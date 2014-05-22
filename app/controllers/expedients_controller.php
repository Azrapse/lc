<?php

class ExpedientsController extends AppController {
    var $scaffold;
	var $helpers = array('Html','Ajax','Javascript', 'Calendar', 'Js'=>array('Prototype'));
	var $components = array( 'RequestHandler' );
	
    function beforeFilter(){
		parent::beforeFilter();
		if($this->params['action'] != 'viewByReference' && $this->params['action'] != 'view'){
			$role = $this->getCurrentUserRole();			
			if ($role['Role']['codename'] == 'VIEWER')
			{				
				$this->Session->setFlash('Acceso denegado.');
				$this->redirect($this->Auth->logout());
			}
		}
	}
	
	function index(){
		$this->Expedient->recursive = 0;
		$this->set('expedients', $this->paginate());
	}
	
	function array_value_recursive(array $arr){
		$val = array();
		array_walk_recursive($arr, function($v, $k) use(&$val){
			array_push($val, $v);
		});
		return $val;
	}

	function view($id){
		$role = $this->getCurrentUserRole();		
		if($role['Role']['codename'] == 'VIEWER' && $id != $this->Session->read('allowed_expedient_id')){
			$this->Session->setFlash('Acceso denegado. Debe proporcionar una referencia y contraseña específica para ese expediente.');			
			$this->redirect($this->Auth->logout());
			return;
		}
		$this->Expedient->Behaviors->attach('Containable');
		$expedient = $this->Expedient->find('first', array(
			'conditions'=>array(
				'Expedient.id'=>$id
			), 
			'contain' => array(
				'Action' => array(
					'Status', 
					'Document', 
					'order'=>'Action.date'
				), 
				'User' => array(
					'id', 
					'fullname'
				)
			)
		));		
		
		
		$auth = $this->Session->read('Auth');
		
		// Viewers can only see the expedient they gave credentials for
		if ($role['Role']['codename'] != 'VIEWER') {
		
			// Customers can only see their own expedients
			if ($role['Role']['codename'] == 'CUST' && $expedient['Expedient']['user_id'] != $auth['User']['id']) {
				$this->Session->setFlash('Acceso denegado. Identifíquese como el titular del expediente indicado para poder visualizarlo, o bien como su representante legal.');			
				$this->redirect($this->Auth->logout());
				return;
			}
			
			//Lawyers can only see their customers' expedients.
			if($role['Role']['codename'] == 'LAWYER') {
				
				$customerIds = $this->Expedient->User->find('all', array(
					'conditions' => array('User.lawyer_id' => $auth['User']['id']),
					'fields' => array( 'User.id' ),
					'recursive' => -1
				));
				
				if (!in_array($expedient['Expedient']['user_id'], $this->array_value_recursive($customerIds))) {
					$this->Session->setFlash('Acceso denegado. No representa al titular del expediente. Identifíquese como el titular o su representante legal.');								
					$this->redirect($this->Auth->logout());
					return;
				}
			}
			$noCustomer = $expedient['User']['id'] == null;
			$this->set('noCustomer', $noCustomer);
			
			// We retrieve all other expedients from this same customer
			$expedients = $this->Expedient->find('all', array(
				'conditions' => array('user_id' => $expedient['Expedient']['user_id'], 'id <>' => $id), 
				'fields' => array('id', 'reference', 'description'),
				'recursive' => -1
			)); 
			$this->set('expedients', $expedients);
			$this->set('currentUser', $auth['User']);
			
			// Lawyers and Admin can change the expedient owner
			if ($role['Role']['codename'] == 'LAWYER') {
				$otherUsers = $this->Expedient->User->find('list', array(					
					'conditions' => array('Role.codename' => 'CUST', 'Lawyer.id' => $auth['User']['id']),					
					'recursive' => 0
				));
				$this->set('otherUsers', $otherUsers);
				
			} else if ($role['Role']['codename'] == 'ADMIN') {
				
				$otherUsers = $this->Expedient->User->find('list', array(					
					'conditions' => array('Role.codename' => 'CUST'),					
					'recursive' => 0
				));
				$this->set('otherUsers', $otherUsers);				
			}
		}
		
		$this->set('expedient', $expedient);
		
		$this->set('isAdmin', $role['Role']['codename'] == 'ADMIN');
		$this->set('isCustomer', $role['Role']['codename'] == 'CUST');
		$this->set('isViewer', $role['Role']['codename'] == 'VIEWER');
		$this->set('isLawyer', $role['Role']['codename'] == 'LAWYER');
		
	}

	function ajaxEdit($id, $field){
		$value = trim($this->params['form']['value']);
		$expedient = $this->Expedient->find('first', array('conditions' => array('id' => $id), 'recursive' => -1));
		$expedient['Expedient'][$field] = $value;
		$this->set('value', $value);
		// If it's reference, we add the user_id hexed subfix.
		if ($field == 'reference' && !($this->endsWith($expedient['Expedient'][$field], '-'.dechex($expedient['Expedient']['user_id'])))) {
			$moddedReference = $value.'-'.dechex($expedient['Expedient']['user_id']);
			$expedient['Expedient'][$field] = $moddedReference;
			$this->set('value', $moddedReference);
		}		
		$this->Expedient->save($expedient);		
		$this->layout = false;
		$this->render('/ajax/ajax_inplaceeditor');
	}

	
	function ajaxOwnerChange($id){
		$value = trim($this->params['form']['value']);
		
		$expedient = $this->Expedient->find('first', array('conditions' => array('Expedient.id' => $id), 'recursive' => -1));
		$expedient['Expedient']['user_id'] = $value;
		$this->Expedient->save($expedient);
		// We respond with the name of the owner		
		$newOwner = $this->Expedient->User->find('first', array('conditions' => array('User.id' => $value), 'recursive' => -1));
		$this->set('value', $newOwner['User']['fullname']);
		$this->layout = false;
		$this->render('/ajax/ajax_inplaceeditor');
	}
	
	function viewByReference(){		
		$reference = trim($this->params['data']['Expedient']['reference']);
		$this->Expedient->recursive = -1;		
		$expedient = $this->Expedient->findByReference($reference, 'id');
		if (empty($expedient)){
			$this->Session->setFlash('Referencia '.$reference.' no encontrada');
			$this->redirect(array('controller'=>'home', 'action'=>'index'));
		} else {
			$this->redirect(array('action'=>'view', $expedient['Expedient']['id']));
		}
	}

	function add($customerId = null) {
		$subfix = '';
		$auth = $this->Session->read('Auth');
		
		// Check first if there are expedient tokens left
		$userId = $this->getCurrentUserId();
		$userWalletInfo = $this->Expedient->query('call sp_expedient_wallet_info_get('.$userId.');', false);
		$this->Expedient->manageMysqlResult();
		$userUsedExpedientSlots = $userWalletInfo[0][0]['expedientCount'];
		$userExpedientSlots = $userWalletInfo[0][0]['expedientTokenCount'];
		$userInfinitySlots = $userWalletInfo[0][0]['infinityTokenCount'];
		if(!($userUsedExpedientSlots < $userExpedientSlots || $userInfinitySlots)){
			// The user doesn't have enough expedient slots. Redirect to buy page.
			$this->Session->setFlash('No tiene ranuras de expedientes disponibles. Necesita adquirir más.');
			$this->redirect(array('controller'=>'user_items', 'action'=>'index'));
			return;
		}
		
		
		// There are expedient tokens left, create it
		if (!empty($this->data)){				
			if ($customerId != null){
				$this->data['Expedient']['user_id'] = $customerId;				
			}
			$subfix = '-'.dechex($this->data['Expedient']['user_id']);
			$this->data['Expedient']['reference'] = $this->data['Expedient']['reference'].$subfix;
			$customer = $this->Expedient->User->find('first', array('conditions' => array('User.id' => $this->data['Expedient']['user_id']), 'recursive' => -1));
			
			// Keep lawyers from adding expedients to customers that aren't theirs.							
			if ($customer['User']['lawyer_id'] != $auth['User']['id']) {
				$this->Session->setFlash('Cliente no válido.');
				$this->redirect(array('controller'=>'home', 'action'=>'index'));
				return;
			}
			
			$this->Expedient->create();			
			$this->Expedient->save($this->data);
			$this->redirect(array('controller'=>'expedients', 'action'=>'view', $this->Expedient->id));
		} else {
			if ($customerId == null){
				$role = $this->getCurrentUserRole();
				if ($role['Role']['codename'] == 'ADMIN') {
					$customers = $this->Expedient->User->find('list');
					$this->set('users', $customers);					
				} else if ($role['Role']['codename'] == 'LAWYER') {
					
					$customers = $this->Expedient->User->find('list', array('conditions' => array('User.lawyer_id' => $auth['User']['id'])));
					$this->set('users', $customers);					
				}
				$this->set('fixedCustomer', false);
			} else {
				$subfix = '-'.dechex($customerId);
				$this->set('fixedCustomer', true);
				$customer = $this->Expedient->User->find('first', array('conditions'=>array('User.id'=>$customerId), 'recursive' => -1, 'fields' => array('User.id', 'User.fullname', 'User.lawyer_id')));				
				$this->set('customerName', $customer['User']['fullname']);
				
				
				$this->data = array(
					'Expedient' => array(
						'user_id'=>$customerId						
						)
				);			
			}
		}
		$this->set('subfix', $subfix);
	}
	

	function normalize ($string) {
		$table = array(
			'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
			'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
			'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
			'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
			'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
		);
		
		
		return strtr($string, $table);
	}
	
	function searchString() {
		if (!empty($this->data)) {
			$searchString = $this->data['Expedient']['query'];
			$this->redirect(array('action'=>'search', $searchString));
		}
	}
	
	function search($searchString=null) {
		
		$tooCommon = array ('a','ante','bajo','cabe','con','de','desde','contra','en','entre','hacia','hasta',
			'para','por','según','sin','so','sobre','tras', 'mediante', 'durante');
	
		$tokens = explode(' ', $searchString);
		$queryArray = array();
		foreach($tokens as $token) {			
			if (in_array(mb_strtolower($token), $tooCommon)) {
				continue;
			}
			
			$queryArray[] = 'Expedient.description LIKE "%'.$token.'%"';
			$queryArray[] = 'Expedient.reference LIKE "%'.$token.'%"';
			$queryArray[] = 'User.fullname LIKE "%'.$token.'%"';
			
			if (mb_strtolower($token) != $token) {
				$queryArray[] = 'Expedient.description LIKE "%'.mb_strtolower($token).'%"';
				$queryArray[] = 'Expedient.reference LIKE "%'.mb_strtolower($token).'%"';
				$queryArray[] = 'User.fullname LIKE "%'.mb_strtolower($token).'%"';
			}
			if (mb_strtoupper($token) != $token) {
				$queryArray[] = 'Expedient.description LIKE "%'.mb_strtoupper($token).'%"';
				$queryArray[] = 'Expedient.reference LIKE "%'.mb_strtoupper($token).'%"';
				$queryArray[] = 'User.fullname LIKE "%'.mb_strtoupper($token).'%"';
			}
			if ($this->normalize($token) != $token) {
				$queryArray[] = 'Expedient.description LIKE "%'.$this->normalize($token).'%"';
				$queryArray[] = 'Expedient.reference LIKE "%'.$this->normalize($token).'%"';
				$queryArray[] = 'User.fullname LIKE "%'.$this->normalize($token).'%"';
			}
		}
		$conditionsArray = array();
		if (!empty($queryArray)) {
			$conditionsArray['OR'] = $queryArray;
		}		
		
		$role = $this->getCurrentUserRole();
		$auth = $this->Session->read('Auth');
		$isCustomer = false;
		if($role['Role']['codename'] == 'LAWYER') {
			$conditionsArray['User.lawyer_id'] = $auth['User']['id'];
		} else if ($role['Role']['codename'] == 'CUST') {
			$isCustomer = true;
			$conditionsArray['User.id'] = $auth['User']['id'];
		}
						
		$this->paginate = array(
			'conditions' => $conditionsArray,
			'recursive' => 0,
			'fields' => array('Expedient.id', 'Expedient.reference', 'Expedient.description', 'Expedient.user_id', 'User.id', 'User.lawyer_id', 'User.fullname'),
			'limit' => 10,
			'order' => 'Expedient.reference',
		);
		$this->data = $this->paginate('Expedient');
				
		$this->set('isCustomer', $isCustomer);
		$this->set('tokens', $tokens);		
	}
	
}

?>