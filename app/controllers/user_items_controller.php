<?php
class UserItemsController extends AppController {
    var $scaffold;	
	var $components = array('Session');	
	var $helpers = array('Html');
	var $name = 'UserItems';
	
	var $businessId = 'JKGRRRJJ3UKYS';
	//var $businessId = 'HGFE5XYMXJYYN';
	var $paypalUrl = "https://www.paypal.com/cgi-bin/webscr"; 
	//var $paypalUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr"; 
		
	function beforeFilter() 
	{
		parent::beforeFilter();
		$actionName = $this->params['action'];
	
		$role = $this->getCurrentUserRole();
		$roleCodename = $role['Role']['codename'];
		if ($roleCodename == 'VIEWER' || $roleCodename == 'CUST')
		{
			$this->Session->setFlash('Acceso denegado.');
			$this->redirect($this->Auth->logout());
		}
		
		if ($roleCodename == 'LAWYER' && !in_array($actionName, array('index')))
		{
			$this->Session->setFlash('Acceso denegado. Operación no permitida a despachos.');
			$this->redirect($this->Auth->logout());
		}
		
		$this->Auth->allow(array('notify'));
		
	}
	
	function index(){
		$lawyerId = $this->getCurrentUserId();
				
		$items = $this->UserItem->find('all', array(
				'conditions' => array(
					'UserItem.user_id' => $lawyerId					
				),
				'recursive' => 1,				
				'fields' => array( 'UserItem.amount', 'UserItem.usable', 'ItemType.name')				
			)
		);
		
		for($i=0; $i< count($items); $i++){
			$item = $items[$i];
			if (!$item['UserItem']['usable']) {
				$notesStatus = '';
				$notesReason = '';				
				switch($item['Transaction'][0]['status']) {
					case 'Canceled_Reversal':
						$notesStatus = 'Devolución Cancelada';
						break;
					
					case 'Completed':
						$notesStatus = 'Completado';
						break;
					
					case 'Denied':
						$notesStatus = 'Pago denegado';
						break;
					
					case 'Expired':
						$notesStatus = 'Autorización de Pago Caducada';
						break;
					
					case 'Failed':
						$notesStatus = 'Pago desde cuenta bancaria fracasado';
						break;
					
					case 'Pending':
						$notesStatus = 'Pendiente de pago';
						break;
					
					case 'Refunded':
						$notesStatus = 'Devuelto por el vendedor';
						break;
					
					case 'Reversal':
						$notesStatus = 'Pago revertido al comprador';
						break;
					
					default: 
						$notesStatus = $item['Transaction'][0]['status'];
						break;
				}
				if ($item['Transaction'][0]['status'] == 'Pending') {
					switch($item['Transaction'][0]['reason']) {
						case 'address':
							$notesReason = "El cliente no ha incluído un domicilio al pagar.";
							break;
						case 'authorization':
							$notesReason = "El vendedor no ha autorizado aún el pago.";
							break;
						case 'echeck':
							$notesReason = "El pago se hizo con un cheque y aún no se ha resuelto.";
							break;
						case 'intl':
							$notesReason = "El vendedor no ha aceptado o denegado aún el pago.";
							break;
						case 'multi-currency':
							$notesReason = "Se ha pagado con moneda extranjera y el vendedor no ha aceptado o denegado aún el pago.";
							break;
						case 'paymentreview':
							$notesReason = "Paypal está revisando el pago por posibles riesgos.";
							break;
						case 'unilateral':
							$notesReason = "La dirección de pago no ha sido registrada o confirmada.";
							break;
						case 'upgrade':
							$notesReason = "Contacte con el vendedor para que haga una mejora.";
							break;
						case 'verify':
							$notesReason = "Contacte con el vendedor para que haga una verificación.";
							break;
						default:
							$notesReason = $item['Transaction'][0]['reason'];
							break;
					}
				}
				$item['UserItem']['notes'] = $notesStatus." - ".$notesReason;
				$items[$i] = $item;
			}
		}
		
		
		
		
		$this->set('lawyerId', $lawyerId);
		$this->set('items', $items);
		// Check how many expedient slots we have left
		$userId = $this->getCurrentUserId();
		$userWalletInfo = $this->UserItem->User->Expedient->query('call sp_expedient_wallet_info_get('.$userId.');', false);
		$this->UserItem->User->Expedient->manageMysqlResult();
		$userUsedExpedientSlots = $userWalletInfo[0][0]['expedientCount'];
		$userExpedientSlots = $userWalletInfo[0][0]['expedientTokenCount'];
		$userInfinitySlots = $userWalletInfo[0][0]['infinityTokenCount'];
		
		$this->set('usedExpedientSlots', $userUsedExpedientSlots);
		$this->set('availableExpedientSlots', $userInfinitySlots != null ? "∞" : $userExpedientSlots - $userUsedExpedientSlots);
		$this->set('userId', $userId);
		$this->set('infiniteSlotsItemId',2);
		
		$itemOffers = $this->UserItem->ItemType->ItemOffer->find('all', array('recursive' => 0));
		$this->set('itemOffers', $itemOffers);
		
		$invoiceId = uniqid();
		$this->set('invoiceId', $invoiceId);
		App::import('Helper', 'Html'); // loadHelper('Html'); in CakePHP 1.1.x.x
        $html = new HtmlHelper();
		$this->set('notifyUrl', $html->url(array('controller'=>'user_items', 'action'=>'notify'), true));
		$this->set('returnUrl', $html->url(array('controller'=>'user_items', 'action'=>'index'), true));
		$this->set('businessId', $this->businessId);		
		$this->set('paypalUrl', $this->paypalUrl);
		
	}
    
	function notify(){
		$this->layout = false;
		$this->autoRender = false;
		 
		// STEP 1: Read POST data
		 
		// reading posted data from directly from $_POST causes serialization 
		// issues with array data in POST
		// reading raw POST data from input stream instead. 
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
		  $keyval = explode ('=', $keyval);
		  if (count($keyval) == 2)
			 $myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
		   $get_magic_quotes_exists = true;
		} 
		foreach ($myPost as $key => $value) {        
		   if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
				$value = urlencode(stripslashes($value)); 
		   } else {
				$value = urlencode($value);
		   }
		   $req .= "&$key=$value";
		}
				 
		// STEP 2: Post IPN data back to paypal to validate
		 
		$ch = curl_init($this->paypalUrl);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		 
		// In wamp like environments that do not come bundled with root authority certificates,
		// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path 
		// of the certificate as shown below.
		// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
		if( !($res = curl_exec($ch)) ) {
			// error_log("Got " . curl_error($ch) . " when processing IPN data");
			curl_close($ch);
			exit;
		}
		curl_close($ch);
		
		
		// STEP 3: Inspect IPN validation result and act accordingly
		 
		if (strcmp ($res, "VERIFIED") == 0) {
			// check whether the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
			
			// assign posted variables to local variables
			$item_name = $_POST['item_name'];
			$item_number = $_POST['item_number'];
			$quantity = $_POST['quantity'];
			$custom = $_POST['custom'];
			$payment_status = $_POST['payment_status'];
			$payment_amount = $_POST['mc_gross'];
			$payment_currency = $_POST['mc_currency'];
			$invoice = $_POST['invoice'];
			$txn_id = $_POST['txn_id'];
			$receiver_email = $_POST['receiver_email'];
			$payer_email = $_POST['payer_email'];
			
			$reason = 'none';
			if(array_key_exists('pending_reason', $_POST)){
				$reason = $_POST['pending_reason'];
			}
			
			/*************************************************************************************************************/			
			// We have got a notification. 
			// We register it for the specified item. 
			
			// Look for an item with [invoice] = $invoice
			$userItem = $this->UserItem->find('first', array(
				'conditions' => array(
					'UserItem.invoice' => $invoice
					)				
				)
			);
			
			// If no item has been found, they we must create it first
			if(!$userItem) {
				$userItem = array(
					'UserItem'=> array (
						'user_id' => $custom,
						'item_type_id' => $item_number,
						'amount' => $quantity,
						'usable' => 1,
						'invoice' => $invoice
						)
					);
				$this->UserItem->create();				
				$this->UserItem->save($userItem);				
				$userItemId = $this->UserItem->id;
			} else {
				$userItemId = $userItem['UserItem']['id'];
			}
			
			// Now, we register the notification as a transaction			
			$transaction = array(
				'Transaction' => array(
					'user_item_id' => $userItemId,
					'status' => $payment_status,					
					'description' => $raw_post_data,
					'modified' => date('Y-m-d H:i:s'),
					'reason' => $reason
					)
				);
			$this->UserItem->Transaction->create();			
			$transactionId = $this->UserItem->Transaction->save($transaction);
			
			// Finally, set the usable field of the item to True or False depending on the status of the last transaction
			$this->UserItem->id = $userItemId;									
			if ($transaction['Transaction']['status'] != 'Completed') {				
				$this->UserItem->saveField('usable', 0);				
			} else {
				$this->UserItem->saveField('usable', 1);				
			}	
			//$this->log($this->UserItem->getDataSource()->getLog(false, false), LOG_DEBUG);			
			
			/*************************************************************************************************************/
			
		} else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
		}
	}	

	function admin_wallet($lawyerId) {
		$userItems = $this->UserItem->find('all', array(
				'conditions' => array(
					'UserItem.user_id' => $lawyerId
				),
				'recursive' => 0
			)
		);
		$user = $this->UserItem->User->find('first', array('conditions' => array('User.id' => $lawyerId)));
		$this->set('user', $user);
		$this->set('userItems', $userItems);		
		$itemTypes = $this->UserItem->ItemType->find('list');		
		$this->set('itemTypes', $itemTypes);
	}
	
	function admin_add_item($lawyerId, $itemTypeId, $amount) {
		$this->layout = false;
		$this->autoRender = false;		
	}
	
	function admin_remove_item($itemId) {
		$this->layout = false;
		$this->autoRender = false;		
	}
	
	function admin_modify_item() {
	    $this->layout = false;
	    $this->autoRender = false;
	    $data = $this->params['form'];
	    
	    $data = array('UserItem'=>
			array(
			    'item_type_id' => $data['typeId'],
			    'id' => $data['itemId'],
			    'amount' => $data['amount'],
			    'usable' => $data['usable']
			)
		    );
	    
	    $this->UserItem->id = $data['UserItem']['id'];
	    $this->UserItem->save($data);
	    
	}
	
	function admin_get_details($itemId) {
		$this->layout = false;
	}
	
}

?>