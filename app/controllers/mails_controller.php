<?php

class MailsController extends AppController {    
	var $name = 'Mails';    
	var $components = array('RequestHandler');	
	var $uses = array();
	
	// POST /mails/{username}.json
	function view($username)
	{
		$this->layout = false;
		$mailAddress = "{mail.legalecloud.com:143/novalidate-cert}INBOX";
		$toIdentifier = ".".$username."@";
						
		$result = array();
		// Attempt to connect to the mail server and get a connection
		$mbox = imap_open($mailAddress, "expedients@legalecloud.com", "53rn45k1");
		if(!$mbox)
		{
			$result['success'] = false;
		}
		else
		{
			$result['success'] = true;
			$found = array();
			
			$MC=imap_check($mbox); 
			$MN=$MC->Nmsgs; 
			$overview=imap_fetch_overview($mbox,"1:$MN",0); 
			$size=sizeof($overview); 
			for($i=$size-1;$i>=0;$i--)
			{ 
				$val=$overview[$i]; 
				$msg=$val->msgno; 
				$from=$val->from; 
				$date=$val->date; 
				$subj=$val->subject; 
				$to=$val->to;
				
				// Check if the mail has the $toIdentifier in the To field
				$pos = strpos($to, $toIdentifier);
				if($pos)
				{
					$found[] = "#$msg: From:'$from' To:'$to' Date:'$date' Subject:'$subj'";
				}		
				
				//imap_delete($mbox, $msg);
			}
            $structure = imap_fetchstructure($mbox, $MN);
            $structure = $this->create_part_array($structure);
            debug($structure);
            foreach($structure as $part)
            {
                $data = imap_fetchbody($mbox, $MN, $part['part_number']);
                $data = $this->decode_part($part['part_object'], $data);
                debug($data);
            }

			$result['found']=$found;
			//imap_expunge($mbox);
			imap_close($mbox);		
		}
		// Return the data
		$this->set(array(
			'result'=>$result, 
			'_serialize'=>array('result')
			)
		);
	}

    function create_part_array($structure, $prefix="") {
        //print_r($structure);
        if (sizeof($structure->parts) > 0) {    // There some sub parts
            foreach ($structure->parts as $count => $part) {
                $this->add_part_to_array($part, $prefix.($count+1), $part_array);
            }
        }else{    // Email does not have a seperate mime attachment for text
            $part_array[] = array('part_number' => $prefix.'1', 'part_object' => $obj);
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
                $part_array[] = array('part_number' => $prefix.'.1', 'part_object' => $obj);
            }
        }else{    // If there are more sub-parts, expand them out.
            if (sizeof($obj->parts) > 0) {
                foreach ($obj->parts as $count => $p) {
                    $this->add_part_to_array($p, $partno.".".($count+1), $part_array);
                }
            }
        }
    }

    function decode_part($part, $message)
    {
        $name = imap_utf8($part->parameters[0]->value);
        $type = $part->type;
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
        if ($coding == 0)
        {
            $message = imap_8bit($message);
        }
        elseif ($coding == 1)
        {
            $message = imap_8bit($message);
        }
        elseif ($coding == 2)
        {
            $message = imap_binary($message);
        }
        elseif ($coding == 3)
        {
            $message = imap_base64($message);
        }
        elseif ($coding == 4)
        {
            $message = quoted_printable_decode($message);
        }
        elseif ($coding == 5)
        {
            $message = $message;
        }
        return array('contents'=>$message, 'Content-type'=>$type, 'Content-Disposition'=>$header2);
    }
}