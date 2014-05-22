<?php

class UsersController extends AppController {
    var $scaffold;
	var $name = 'Users';    
	var $components = array('RequestHandler', 'Cookie');	
	var $helpers = array('Html', 'Ajax', 'Javascript');
	
	function beforeFilter() 
	{
		parent::beforeFilter();
		$actionName = $this->params['action'];
		if(!in_array($actionName, array( 'login', 'expedient_login', 'uni_login', 'logout', 'terms', 'create_account'))){
			$role = $this->getCurrentUserRole();
			$roleCodename = $role['Role']['codename'];
			if ($roleCodename == 'VIEWER')
			{
				$this->Session->setFlash('Acceso denegado.');
				$this->redirect($this->Auth->logout());
			}
			
			if ($roleCodename == 'LAWYER' && !in_array($actionName, array('lawyers_customer_view', 'view', 'add_customer', 'ajaxEdit', 'ajaxChangeLanguage')))
			{
				$this->Session->setFlash('Acceso denegado. Operación no permitida a despachos.');
				$this->redirect($this->Auth->logout());
			}
			
			if ($roleCodename == 'CUST' && !in_array($actionName, array('view', 'ajaxChangeLanguage')))
			{
				$this->Session->setFlash('Acceso denegado. Operación no permitida a clientes.');
				$this->redirect($this->Auth->logout());
			}
		}	
	
		// Esto permite ejecutar el código en login() antes de que se haga la redirección.
		//$this->Auth->autoRedirect = false;
		
		$this->Auth->allow(array('expedient_login','terms', 'create_account', 'uni_login'));
		//$this->Auth->autoRedirect = false;
	}
	
    function login($redirectUrl=null) 
	{	
		//if($this->Auth->User() and $redirectUrl) {
		//	$this->redirect($redirectUrl, null, true);
		//}
    }
		
	function uni_login()
	{		
		if(!empty($this->data)) {
			// Although User has not such fields, the form that is sent does.
			$identificator = $this->data['User']['identificator'];
			$wordofpass = $this->data['User']['wordofpass'];
			$this->loadModel('Expedient');
			
			// Check if it's an user to log in
			$user = $this->User->find('first', array('conditions' => array('username' => $identificator, 'password' => $this->Auth->password($wordofpass)), 'recursive' => -1));
			if(!empty($user)){				
				$this->Auth->login($user);				
				$this->redirect(array('controller' => 'home', 'action' => 'index'));
				return;
			} 			
			
			// Check if it's an expedient to log in
			$expedient = $this->Expedient->find('first', array('conditions' => array('reference' => $identificator, 'password' => $wordofpass), 'recursive' => -1));
			if(!empty($expedient)){
				$this->loadModel('Role');
				$viewerRole = $this->Role->findByCodename('VIEWER');
				$viewerUser = $this->User->findByRoleId($viewerRole['Role']['id']);
				$this->Auth->login($viewerUser);
				$this->Session->write('allowed_expedient_id', $expedient['Expedient']['id']);
				$this->redirect(array('controller' => 'expedients', 'action' => 'view', $expedient['Expedient']['id']));
				return;
			} 			
		}
		$this->Session->setFlash('Identificación y/o contraseña incorrectas.');
		$this->redirect('login');
	}

    function logout() 
	{
		$this->Auth->logoutRedirect = array('controller' => 'home', 'action' => 'index');	
        $this->redirect($this->Auth->logout());
		$this->Cookie->write('loggedUserId',null, false);
		$this->Cookie->write('loggedUserName',null, false);
		$this->Cookie->write('loggedUserLogin',null, false);
    }

	
	function lawyers_customer_view($userId)
	{
		$role = $this->getCurrentUserRole();
		
		$this->User->Behaviors->attach('Containable');
		$customer = $this->User->find('first', array(
			'conditions' => array(
				'User.id' => $userId
			),
			'recursive' => 1,
			'contain' => array('Expedient')
		));
		if ($role['Role']['codename'] != 'ADMIN') {		
			$currentUser = $this->Auth->user();
			if ($customer['User']['lawyer_id'] != $currentUser['User']['id'])
			{
				$this->Session->setFlash('Acceso denegado. Identifíquese como el representante de dicho cliente.');
				$this->redirect($this->Auth->logout());
				return;
			}
		}
		$this->set('customer', $customer);		
		// Check how many expedient slots we have left
		$userId = $this->getCurrentUserId();
		$userWalletInfo = $this->User->Expedient->query('call sp_expedient_wallet_info_get('.$userId.');', false);
		$this->User->Expedient->manageMysqlResult();
		$userUsedExpedientSlots = $userWalletInfo[0][0]['expedientCount'];
		$userExpedientSlots = $userWalletInfo[0][0]['expedientTokenCount'];
		$userInfinitySlots = $userWalletInfo[0][0]['infinityTokenCount'];
		$this->set('usedExpedientSlots', $userUsedExpedientSlots);
		$this->set('availableExpedientSlots', $userInfinitySlots != null ? "∞" : $userExpedientSlots - $userUsedExpedientSlots);
	}
	
	function ajaxEdit($id, $field){
		$value = trim($this->params['form']['value']);
		$customer = $this->User->find('first', array('conditions' => array('User.id' => $id), 'recursive' => -1));
		// Check permissions
		$currentUser = $this->Auth->user();
		$role = $this->getCurrentUserRole();
		$roleCodename = $role['Role']['codename'];
		if ($roleCodename == 'LAWYER') {
			if ($customer['User']['lawyer_id'] != $currentUser['User']['id']) {
				$this->Session->setFlash('Acceso denegado. Identifíquese como el representante de dicho cliente.');
				$this->layout = false;
				$this->render('/ajax/ajax_inplaceeditor');
				return;
			}
		}		
		if ($field == 'password') {			
			$customer['User']['password'] = $this->Auth->password(trim($value));
			$this->set('value', '<i>Contraseña modificada</i>');
		} else {
			$customer['User'][$field] = $value;
			$this->set('value', $value);
		}
		$this->User->save($customer, false);		
		$this->layout = false;
		$this->render('/ajax/ajax_inplaceeditor');
	}
	
	function add_customer($lawyerId = null) {		
		$currentUser = $this->Auth->user();
		$role = $this->getCurrentUserRole();
		$roleCodename = $role['Role']['codename'];
		
		if (!empty($this->data)){
			$role = $this->User->Role->find('first', array('conditions' => array('codename' => 'CUST')));			
			$this->data['User']['role_id'] = $role['Role']['id'];			
			if ($roleCodename != 'ADMIN') {
				$this->data['User']['lawyer_id'] = $currentUser['User']['id'];
			}
			
			$this->data['User']['validated'] = 1;
						
			$this->User->create();							
			if($this->User->save($this->data, false)){				
				$this->redirect(array('controller'=>'users', 'action'=>'lawyers_customer_view', $this->User->id));
			} else {
				$this->Session->setFlash("Error al crear usuario.");				
				$this->set('lawyerId', $this->data['User']['lawyer_id']);
				$this->set('currentUser', $currentUser);
				$this->data['User']['password'] = '';												
			}			
		} else {			
			$this->data['User']['fullname'] = 'Introduzca un nombre descriptivo completo';
			$this->data['User']['username'] = 'introduzcaNombreDeAcceso';
			$this->data['User']['password'] = 'introduzcaContraseñaDeAccesoComo'.uniqid();
			$this->data['User']['lawyer_id'] = $lawyerId;
			$this->set('lawyerId', $lawyerId);
			if($roleCodename == 'ADMIN') {
				$lawyerRole = $this->User->Role->find('first', array('conditions' => array('Role.codename' => 'LAWYER'), 'recursive' => -1));
				$lawyers = $this->User->find('list', array('conditions' => array('User.role_id' => $lawyerRole['Role']['id'])));	
				$this->set('lawyers', $lawyers);
			}
			$this->set('currentUser', $currentUser);			
		}
	}
	
	function add_lawyer() {		
		if (!empty($this->data)){
			$role = $this->User->Role->find('first', array('conditions' => array('codename' => 'LAWYER')));			
			$this->data['User']['role_id'] = $role['Role']['id'];
			$this->User->create();			
			$this->User->save($this->data, false);
			$this->redirect(array('controller'=>'home', 'action'=>'index'));
		} else {
			$currentUser = $this->Auth->user();
			$this->data['User']['fullname'] = 'Introduzca un nombre descriptivo completo';
			$this->data['User']['username'] = 'introduzcaNombreDeAcceso';
			$this->data['User']['password'] = 'introduzcaContraseñaDeAccesoComo'.uniqid();						
		}
	}
	
	function index($roleCodename = null) {
		$conditions = array();
		if ($roleCodename != null) {
			$conditions['Role.codename'] = $roleCodename;
		}
		$fields = array('User.id', 'User.fullname','User.username','User.email', 'User.role_id', 'Role.id', 'Role.codename');
		if ($roleCodename == 'CUST') {
			$fields[] = 'User.lawyer_id';
			$fields[] = 'Lawyer.id';
			$fields[] = 'Lawyer.fullname';
		}
				
		$this->paginate = array(
			'conditions' => $conditions,
			'fields' => $fields,
			'limit' => 10,
			'order' => array('User.fullname' => 'asc')
		);
		$data = $this->paginate('User');
		$this->set('data', $data);
		$this->set('roleCodename', $roleCodename);
		
	
	}
	
	function edit($userId = null) {
		if (!empty($this->data)) {
			// Postback: If there is a new password specified
			if ($this->data['User']['newPassword'] != '') {
				// Check that the confirmation is right
				if ( $this->data['User']['confirmNewPassword'] == $this->data['User']['newPassword']) {
					// If it is, hash it
					$this->data['User']['password'] = $this->Auth->password($this->data['User']['newPassword']);
				} else {
					// If it's not, error and reshow
					$this->Session->setFlash('Las nuevas contraseñas no coinciden.');
					$this->redirect(array('action'=>'edit', $userId));					
					return;
				}
			} else {
				// If there is no new password specified, use the old one
				$this->data['User']['password'] = $this->data['User']['oldPassword'];
			}
			
			//Save the changes.
			$this->User->save($this->data, false);
			$this->redirect(array('controller' => 'home', 'action' => 'index'));
		} else {
			// Not postback:
			$user = $this->User->find('first', array(
				'conditions' => array('User.id' => $userId), 
				'recursive' => 0,
				'fields' => array('User.id', 'User.fullname', 'User.username', 'User.email', 'User.password', 'User.role_id', 'User.lawyer_id', 'User.language_id', 'Lawyer.id', 'Role.id'),
			));
			// To prevent the password from being rehashed, we store it in oldPassword
			$user['User']['oldPassword'] = $user['User']['password'];
			$this->set('user', $user);
			$this->data = $user;		
		}
		
		$roles = $this->User->Role->find('list');
		$lawyerRole = $this->User->Role->find('first', array('conditions' => array('Role.codename' => 'LAWYER'), 'recursive' => -1));
		$lawyers = $this->User->find('list', array('conditions' => array('User.role_id' => $lawyerRole['Role']['id'])));
		$languages = $this->User->Language->find('list');
		$this->set('roles', $roles);
		$this->set('lawyers', $lawyers);
		$this->set('languages', $languages);
	}
	
	function terms() {
	}
	
	function create_account() {
		$error_message = "";
		if (!empty($this->data)){			
			// Check if username is already taken
			$existingUsername = $this->User->find('count', array('conditions' => array('User.username'=>$this->data['User']['username'])));
			$existingExpedient = $this->User->Expedient->find('count', array('conditions' => array('Expedient.reference'=>$this->data['User']['username'])));
			if($existingUsername > 0 || $existingExpedient > 0 || $this->data['User']['username'] == ""){
				$error_message = $error_message . 'Ese nombre de usuario no está disponible. Escoja otro nombre de usuario.';
						
				$this->data['User']['username'] = "";				
			}			
			else if($this->data['User']['password'] != $this->Auth->password($this->data['User']['password_confirm'])){
				$error_message = $error_message . 'Las contraseñas no coinciden.';
			} else {
				$this->User->set($this->data);
				if(!$this->User->validates()){
					$error_message = $error_message . 'Revise los datos proporcionados, hay algunos errores.';
				}
			}
			
			if($error_message != ''){
				$this->Session->setFlash($error_message);
				$this->data['User']['password_confirm'] = '';
				$this->data['User']['password'] = '';
				
			}
			else {
				// At this point, everything is all right
				
				$this->User->create();
				if($this->data['User']['fullname'] == '' || $this->data['User']['fullname'] == null) {
					$this->data['User']['fullname'] = $this->data['User']['username'];
				}
				$this->data['User']['role_id'] = 5;
				$this->User->save($this->data);
				$freeExpedients = array(
					'UserItem' => array(
						'user_id'=> $this->User->id,
						'item_type_id' => 1,
						'amount' => 50,
						'usable' => 1,
						'invoice' => uniqid()
					)
				);
				$this->User->UserItem->create();
				$this->User->UserItem->save($freeExpedients);
				$this->Auth->login($this->data);
				$this->redirect(array('controller'=>'home', 'action'=>'lawyer_home'));
			}
			
		} else {			
			
		}
	}
	
	function ajaxChangeLanguage(){
	    $langid=$_POST['langid'];
	    if($langid==null){
		return;
	    }
	    $currentUser = $this->Auth->user();
	    $currentUser['User']['language_id'] = $langid;
	    $this->Session->write('Auth.User.language_id', $langid);
	    $this->User->save($currentUser);	
	    $this->set('value', $currentUser['User']['language_id']);
	    $this->layout = false;
	    $this->render('/ajax/ajax_inplaceeditor');
	        
	}
}

?>