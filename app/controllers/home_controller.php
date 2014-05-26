<?php

class HomeController extends AppController {
    var $components = array('Session', 'RequestHandler');
	var $uses = array();
	var $helpers = array('Html', 'Fck', 'Ajax', 'Pretty' /*, 'Modalbox', 'Crumb'*/);
	
	function index()
	{
		$auth = $this->Session->read('Auth');
		$this->set('userlongname', $auth['User']['fullname']);
		
		$role = $this->getCurrentUserRole();
		
		// If viewer user, sent to login for expedient_login.
		if($role['Role']['codename'] == 'VIEWER'){
			$this->set('isViewer', true);
			$this->redirect(array('controller' => 'users', 'action' => 'login'));
		}
		else if ($role['Role']['codename'] == 'CUST') {
			$this->set('isCustomer', true);
			$this->redirect(array('controller' => 'home', 'action' => 'customer_home'));
		}
		else if ($role['Role']['codename'] == 'LAWYER') {
			$this->set('isLawyer', true);
			$this->redirect(array('controller' => 'home', 'action' => 'lawyer_home'));
		}
		else if ($role['Role']['codename'] == 'ADMIN') {
			$this->set('isAdmin', true);			
		}		
		
	}
	
	function customer_home()
	{
		$auth = $this->Session->read('Auth');
		$this->set('userlongname', $auth['User']['fullname']);
		
		$role = $this->getCurrentUserRole();
		// If not the right role, redirect to index to be determined the right home page.
		if ($role['Role']['codename'] != 'CUST') {
			$this->redirect(array('controller' => 'home', 'action' => 'index'));
			return;
		}
		$this->loadModel('Expedient');
		$this->paginate = array(
			'conditions' => array('Expedient.user_id' => $auth['User']['id']), 
			'fields' => array('Expedient.id', 'Expedient.reference', 'Expedient.description'),
			'recursive' => -1,
			'limit' => 10,
			'order' => 'Expedient.reference'
		); 
		$this->data = $this->paginate('Expedient');
		$this->set('user', $auth['User']);
	}
	
	function lawyer_home()
	{
		$auth = $this->Session->read('Auth');
		$this->set('userlongname', $auth['User']['fullname']);
		
		$role = $this->getCurrentUserRole();
		// If not the right role, redirect to index to be determined the right home page.
		if ($role['Role']['codename'] != 'LAWYER') {
			$this->redirect(array('controller' => 'home', 'action' => 'index'));
			return;
		}
		
		// Get customers of this lawyer		
		$this->loadModel('User');
		$this->loadModel('Expedient');
		$this->User->Behaviors->attach('Containable');
		$customers = $this->User->find('all', array(
			'conditions' => array('User.lawyer_id' => $auth['User']['id'], 'Role.codename' => 'CUST'), 
			'contain' => array('Role', 'Expedient'),
			'fields' => array('User.id', 'User.fullname', 'Role.id', '(SELECT COUNT(*) FROM expedients AS Expedient WHERE Expedient.user_id = User.id) AS expedient_count'),
			'order' => array('User.fullname'),
			'recursive' => 0
		)); 
		$this->set('customers', $customers);
		
		
		// Check how many expedient slots we have left
		$userId = $this->getCurrentUserId();
		$userWalletInfo = $this->Expedient->query('call sp_expedient_wallet_info_get('.$userId.');', false);
		$this->Expedient->manageMysqlResult();
		$userUsedExpedientSlots = $userWalletInfo[0][0]['expedientCount'];
		$userExpedientSlots = $userWalletInfo[0][0]['expedientTokenCount'];
		$userInfinitySlots = $userWalletInfo[0][0]['infinityTokenCount'];
		$this->set('usedExpedientSlots', $userUsedExpedientSlots);
		$this->set('availableExpedientSlots', $userInfinitySlots != null ? "∞" : $userExpedientSlots - $userUsedExpedientSlots);
		
		$this->paginate = array(
			'conditions' => array(
				'User.lawyer_id' => $auth['User']['id']
			),
			'fields' => array('Expedient.id', 'Expedient.reference', 'Expedient.description', 'User.id', 'User.fullname', 'User.lawyer_id'),
			'recursive' => 0,
			'limit' => 10,
			'order' => 'Expedient.reference',
		);

		$this->data = $this->paginate('Expedient');
		
		$this->set('user', $auth['User']);
	}
		
	function maintenance()
	{
		$this->set('info', phpinfo());
	}
		
		
	
}

?>