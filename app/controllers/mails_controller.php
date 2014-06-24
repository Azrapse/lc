<?php
class MailsController extends AppController {
    var $name = 'Mails';
    var $components = array('RequestHandler', 'Email');
    var $uses = array('ExpedientAddress', 'Action', 'Document', 'Configuration', 'Expedient', 'ImportedAction');
    var $mailAddress = "{mail.legalecloud.com:143/novalidate-cert}INBOX";
    var $mailAccount = "expedients@legalecloud.com";
    var $mailPassword = "53rn45k1";
    var $mailHost = "legalecloud.com";
    var $checkFrequency = 30;
    var $emailPattern = "/(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";
    function post_bind_email_to_expedient()
    {
        $id = $this->params['form']['id'];
        $this->layout = false;
        $newAddress = array(
            'ExpedientAddress' => array(
                'address'=>md5(uniqid('', true))."@".$this->mailHost,
                'expedient_id' => $id
            )
        );
        $this->ExpedientAddress->deleteAll(array(
            'ExpedientAddress.expedient_id' => $id
        ), false);
        $this->ExpedientAddress->create();
        if($this->ExpedientAddress->save($newAddress))
        {
            $this->set(array(
                    'result'=>$newAddress,
                    '_serialize'=>array('result')
                )
            );
        }
        else
        {
            $this->set(array(
                    'result'=>false,
                    '_serialize'=>array('result')
                )
            );
        }
        $this->render('process');
    }
    // This function processes all incoming expedient mails and transform them into expedient-actions
    function process()
    {
        $this->layout = false;
        $mailAddress = $this->mailAddress;
        $mailAccount = $this->mailAccount;
        $mailPassword = $this->mailPassword;
        // Check if 30 seconds have passed since last time
        $lastTime = $this->get_last_mail_process_time();
        $lastTimeInSec = strtotime($lastTime);
        $lastTimePlusPeriod = $lastTimeInSec + $this->checkFrequency;
        $now = time();
        if($now < $lastTimePlusPeriod)
        {
            $json = array(
                'new_messages' => 0,
                'message' => 'Last checked not so long ago. Check again in '.($lastTimePlusPeriod-$now).' seconds.'
            );
        }
        else
        {
            $result = $this->process_messages($mailAddress, $mailAccount, $mailPassword);
            $json = array(
                'new_messages' => sizeof($result)
            );
        }
        // Return the data
        $this->set(array(
                'result'=>$json,
                '_serialize'=>array('result')
            )
        );
    }
    function process_messages($mailAddress, $mailAccount, $mailPassword)
    {
        $this->set_last_mail_process_time(date('c'));
        // Attempt to connect to the mail server and get a connection
        $mbox = imap_open($mailAddress, $mailAccount, $mailPassword);
        if(!$mbox)
        {
            return false;
        }
        // This array will hold the new incoming messages
        $messages=array();
        // Get the mail overviews
        $MC=imap_check($mbox);
        $MN=$MC->Nmsgs;
        if(!$MN)
        {
            return $messages;
        }
        $overview=imap_fetch_overview($mbox,"1:$MN",0);
        $size=sizeof($overview);
        // Iterate over the overviews to discard those that don't belong to any expedient
        $processingKind = array();
        $addresses = array();
        $addressesDict = array();
        for($i=$size-1;$i>=0;$i--)
        {
            $val=$overview[$i];
            $to=$val->to;
            $deleted = $val->deleted;
            if($deleted)
            {
                continue;
            }
            $matches = array();
            preg_match_all($this->emailPattern, $to, $matches);
            // Add all addresses to the addresses array to consult which ones actually are linked to an expedient
            foreach($matches[0] as $email){
                $addresses[] = $email;
                $addressesDict[$email] = $val;
            }
        }
        if(sizeof($addresses) == 0)
        {
            imap_close($mbox);
            return $messages;
        }
        $matchingExpedients = $this->ExpedientAddress->find('all', array(
            'conditions' => array(
                'ExpedientAddress.address' => $addresses
            ),
            'recursive'=>-1
        ));
        // Make a dictionary with the valid addresses that match an expedient, and which expedient
        $validAddressesDict = array();
        foreach($matchingExpedients as $expContainer)
        {
            $expedient_id = $expContainer['ExpedientAddress']['expedient_id'];
            $address = strtolower($expContainer['ExpedientAddress']['address']);
            $validAddressesDict[$address] = $expedient_id;
        }
        // Process now all mails
        for($i=$size-1;$i>=0;$i--)
        {
            $val=$overview[$i];
            $msg=$val->msgno;
            $from=$val->from;
            $date=$val->date;
            $subj=$val->subject;
            $to=$val->to;
            // Determine if the "to" address is invalid, then reply to the senders of their mails that they
            // aren't valid. Then delete them.
            // The To: may contain several receivers. Get them all and check them all.
            $tos = $this->extract_emails_from($to);
            $isValid = false;
            foreach($tos as $toAddress)
            {
                $toAddress = strtolower($toAddress);
                if(!array_key_exists($toAddress, $validAddressesDict))
                {
                    // This is not valid, try next
                    continue;
                }
                // This is valid, then create the action and documents
                $isValid = true;
                // Decompose the mail in parts
                $structure = imap_fetchstructure($mbox, $msg);
                $parts = $this->create_part_array($structure);
                $message = array(
                    'from' => $from,
                    'to' => $toAddress,
                    'date' => $date,
                    'subject' => imap_utf8($subj),
                    'htmls' => array(),
                    'plains' => array(),
                    'attachments' => array()
                );
                // Search for the ones that are text or attachments
                foreach($parts as $part)
                {
                    $data = imap_fetchbody($mbox, $msg, $part['part_number']);
                    $data = $this->decode_part($part['part_object'], $data);
                    if($data['type'] == 'text/PLAIN')
                    {
                        $message['plains'][] = $data['contents']; 
                    }
                    if($data['type'] == 'text/HTML')
                    {
                        $message['htmls'][] = $data['contents'];
                    }
                    if($data['isAttachment'] )
                    {
                        $message['attachments'][] = array(
                            'name' => $data['name'],
                            'type' => $data['type'],
                            'encoding' => $data['encoding'],
                            'contents' => $data['contents']
                        );
                    }
                }
                // Create an action in that expedient
                $matchingExpedientId = $validAddressesDict[$toAddress];
                $Date = new DateTime($date); //create dateTime object
                if(sizeof($message['plains']) > 0)
                {
                    $body = $message['plains'][0];
                }
                else
                {
                    $body = $message['htmls'][0];
                }
                $action = array(
                    'Action' => array(
                        'expedient_id' => $matchingExpedientId,
                        'concept' => imap_utf8($subj),
                        'comments' => $body,
                        'status_id' => 4,
                        'date'=> $Date->format('Y/m/d')
                    )
                );
                $this->Action->create();
                if(!$this->Action->save($action))
                {
                    debug("error saving action");
                    debug($action);
                }
                else
                {
                    // Create the documents from the attachments
                    foreach($message['attachments'] as $attachment)
                    {
                        $this->create_document_from_attachment($attachment, $this->Action->id);
                    }
                }
                // Note down in the message which user, expedient and action it relates to
                $expedient = $this->Expedient->find('first', array(
                    'conditions' => array(
                        'Expedient.id' => $matchingExpedientId
                    ),
                    'recursive' => 0,
                    'fields' => array('Expedient.id', 'Expedient.user_id', 'User.id', 'User.lawyer_id')
                ));
                $message['expedient_id'] = $expedient['Expedient']['id'];
                $message['user_id'] = $expedient['User']['lawyer_id'];
                $message['action_id'] = $this->Action->id;
                $messages[] = $message;

                // Create Imported Action
                $this->ImportedAction->create();
                $data = array(
                    'ImportedAction' => array(
                        'user_id' => $message['user_id'],
                        'expedient_id' => $message['expedient_id'],
                        'action_id' => $message['action_id'],
                        'from' => $message['from'],
                        'date' => date('c')
                    )
                );
                $this->ImportedAction->save($data);

                // As the action is created, no need to keep checking the other addresses in the To: header
                break;
            }
            // This previous foreach loop might have ended because two reasons: action was created, then isValid=true;
            // Or no valid address was found in the To: header that matches that of an expedient, then isValid=false.
            // In that case, reply with invalid mail
            if(!$isValid)
            {
                $this->reply_invalid($val);
            }
            // In any case, mark the mail as deleted. As it has been processed.
            imap_delete($mbox, $msg);
        }
        // Perform the deletion of the messages.
        imap_expunge($mbox);
        imap_close($mbox);
        return $messages;
    }
    function extract_emails_from($string){
        $matches = array();
        preg_match_all($this->emailPattern, $string, $matches);
        return $matches[0];
    }
    function create_document_from_attachment($attachment, $action_id)
    {
        $name = $attachment['name'];
        $type = $attachment['type'];
        $encoding = $attachment['encoding'];
        $contents = $attachment['contents'];
        $data = array(
            'Document' => array(
                'action_id' => $action_id,
                'reference' => uniqid(),
                'filename' => $name,
                'description' => $name
            )
        );
        $config = $this->Configuration->findByName('filesPath');
        $uploadDir = $config['Configuration']['value'];
        $uploadSubdir = $this->getEmptiestSubdir($uploadDir);
        $uploadPath = $uploadSubdir.DS.'a'.$data['Document']['action_id'].'_'.$data['Document']['reference'];
        $uploadPath = str_replace('//', '/', $uploadPath);
        $fp = fopen($uploadPath, 'w');
        if(!$fp)
        {
            debug("Error opening file for writing: ".$uploadPath);
            return;
        }
        fwrite($fp, $contents);
        fclose($fp);
        $this->Document->create();
        $this->Document->save($data);
    }
    function reply_invalid($mailOverview)
    {
        /* Opciones SMTP*/
        /*$this->Email->smtpOptions = array(
            'port'=>'25',
            'timeout'=>'10',
            'host' => 'mail.legalecloud.com',
            'username'=>'expedients@legalecloud.com',
            'password'=>'53rn45k1');*/
        /* Configurar método de entrega */
        /* $this->Email->delivery = 'smtp';*/
        $msg=$mailOverview->msgno;
        $from=$mailOverview->from;
        $date=$mailOverview->date;
        $subj=$mailOverview->subject;
        $to=$mailOverview->to;
        $replySubj = "Invalid address/Email incorrecto '".$subj."'";
        $this->Email->to = $from;
        $this->Email->subject = $replySubj;
        $this->Email->replyTo = 'no-reply@legalecloud.com';
        $this->Email->from = 'Notificaciones LegaleCloud <notificador@legalecloud.com>';
        $this->Email->template = 'invalid_expedient_address'; // note no '.ctp'
        //Send as 'html', 'text' or 'both' (default is 'text')
        $this->Email->sendAs = 'both'; // because we like to send pretty mail
        //Set view variables as normal
        $this->set('to', $to);
        $this->set('from', $from);
        $this->set('date', $date);
        $this->set('subj', $subj);
        //Do not pass any args to send()
        $this->Email->send();
        if($this->Email->smtpError)
        {
            debug($this->Email->smtpError);
        }
        $this->Email->reset();
    }
    function create_part_array($structure, $prefix="") {
        $part_array= array();
        if (sizeof($structure->parts) > 0) {    // There some sub parts
            foreach ($structure->parts as $count => $part) {
                $this->add_part_to_array($part, $prefix.($count+1), $part_array);
            }
        }else{    // Email does not have a seperate mime attachment for text
            $part_array[] = array('part_number' => $prefix.'1', 'part_object' => $structure);
        }
        return $part_array;
    }
    // Sub function for create_part_array(). Only called by create_part_array() and itself.
    function add_part_to_array($obj, $partno, & $part_array) {
        $part_array[] = array('part_number' => $partno, 'part_object' => $obj);
        if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type
            //print_r($obj);
            if (sizeof($obj->parts) > 0) {    // Check to see if the email has parts
                foreach ($obj->parts as $count => $part) {
                    // Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
                    if (sizeof($part->parts) > 0) {
                        foreach ($part->parts as $count2 => $part2) {
                            $this->add_part_to_array($part2, $partno.".".($count2+1), $part_array);
                        }
                    }else{    // Attached email does not have a seperate mime attachment for text
                        $part_array[] = array('part_number' => $partno.'.'.($count+1), 'part_object' => $obj);
                    }
                }
            }else{    // Not sure if this is possible
                $part_array[] = array('part_number' => $partno.'.1', 'part_object' => $obj);
            }
        }else{    // If there are more sub-parts, expand them out.
            if (property_exists($obj, 'parts') and sizeof($obj->parts) > 0) {
                foreach ($obj->parts as $count => $p) {
                    $this->add_part_to_array($p, $partno.".".($count+1), $part_array);
                }
            }
        }
    }
    function decode_part($part, $message)
    {
        $name = "noname";
        if(is_array($part->parameters)){
            $name = imap_utf8($part->parameters[0]->value);
        }

        $type = $part->type;
        $isAttachment = ($type >= 2 and $type <= 7);
############## type
        if ($type == 0)
        {
            $type = "text/";
        }
        elseif ($type == 1)
        {
            $type = "multipart/";
        }
        elseif ($type == 2)
        {
            $type = "message/";
        }
        elseif ($type == 3)
        {
            $type = "application/";
        }
        elseif ($type == 4)
        {
            $type = "audio/";
        }
        elseif ($type == 5)
        {
            $type = "image/";
        }
        elseif ($type == 6)
        {
            $type = "video";
        }
        elseif($type == 7)
        {
            $type = "other/";
        }
        $type .= $part->subtype;
######## Type end
        $header1=("Content-type: ".$type);
        $header2=("Content-Disposition: attachment; filename=".$name);
######## coding
        $coding = $part->encoding;
        $encoding = 'unknown';
        if ($coding == 0)
        {
            $message = imap_8bit($message);
            $encoding='7bit';
        }
        elseif ($coding == 1)
        {
            $message = imap_8bit($message);
            $encoding='8bit';
        }
        elseif ($coding == 2)
        {
            $message = imap_binary($message);
            $encoding='binary';
        }
        elseif ($coding == 3)
        {
            $message = imap_base64($message);
            $encoding='base64';
        }
        elseif ($coding == 4)
        {
            $message = quoted_printable_decode($message);
            $encoding='quoted_printable';
        }
        elseif ($coding == 5)
        {
            $message = $message;
        }
        return array(
            'contents' => $message,
            'type' => $type,
            'name' => $name,
            'isAttachment' => $isAttachment,
            'encoding' => $encoding
        );
    }
}