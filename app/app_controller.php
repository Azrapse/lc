<?php
class AppController extends Controller {
	var $components = array('Auth', 'Session', 'Security', 'Email', 'Cookie');		
	var $deniedAccessFallbackUrl = array('controller'=>'home', 'action'=>'index');
	var $deniedAccessFlashMessage = 'Permiso denegado.';
	var $isAdmin = false;
	
	function tt($ttid)
	{
		$this->loadModel('Text');
		$current_user = $this->Auth->user();
		$langId = 2;
		if($current_user != null){
			$langId = $current_user['User']['language_id'];
		}
		$text = $this->Text->find('first', array('conditions' => array('identifier'=>$ttid)));
		if($text == null){
			return '(Missing '.$ttid.')';
		}
		switch($langId){
			case 1:
				return $text['Text']['eng'];
			break;
			case 2:
				return $text['Text']['spa'];
			break;
			case 3:
				return $text['Text']['por'];
			break;
		}
	}
	
	function getCurrentUserId(){
	    $current_user = $this->Auth->user();
	    return $current_user['User']['id'];
	}

	function getCurrentUserRole(){
		$user = $this->Auth->user();
		$this->loadModel('Role');
		$role = $this->Role->find('first', array('conditions' => array('id' => $user['User']['role_id']), 'recursive' => -1));
		
		return $role;
	}
	
	// This makes these helpers available in all views by default
	function beforeRender(){
		$this->helpers[] = 'Layout';
		
		
		$this->loadModel('LocalizedText');
		$user = $this->Auth->user();		
		$langid = $user['User']['language_id'];
		if($langid == null){			
			$browserLanguage = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
			$languages = array('en'=>1, 'es'=>2, 'pt'=>3);
			$langPriority = array();
			foreach(array_keys($languages) as $lang){
				$langPos = strrpos($browserLanguage, $lang);
				if(!$langPos){
					continue;
				}
				$langPriority[$langPos] = $lang;
			}
			if(!empty($langPriority)){
				$maxPriority = min(array_keys($langPriority));
				$maxPriorityLang = $langPriority[$maxPriority];			
				$maxPriorityLangId = $languages[$maxPriorityLang];
			}
			if(!isset($maxPriorityLangId) or is_null($maxPriorityLangId)){
				$maxPriorityLangId = 1;
			}
			$langid = $maxPriorityLangId;
		}
		$texts = $this->LocalizedText->find('list', array('conditions'=>array('langid'=>$langid), 'fields' => array('identifier','message')));		
		$this->helpers['Multilang']=array('texts' => $texts);				
	}
	
	// This checks for the current user having the controller required permissions before proceeding
	function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->loginError = "Credenciales incorrectas.";    
		$this->Auth->authError = "Necesita identificarse para acceder.";
		$role = $this->getCurrentUserRole();		
		$this->set('currentUserRole', $role);		
		$this->_setupSecurity();
		
		$this->send_notifications();
	}
	
	function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
		return (substr($haystack, -$length) === $needle);
	}
		
	function _setupSecurity() {
		$this->Security->blackHoleCallback = '_badRequest';
		//if(Configure::read('forceSSL')) {
			$this->Security->requireSecure('*');
		//}
	}

	/**
	* The main SecurityComponent callback.
	* Handles both missing SSL problems and general bad requests.
	*/

	function _badRequest() {
		if(/*Configure::read('forceSSL') &&*/ !$this->RequestHandler->isSSL()) {
			$this->_forceSSL();
		} else {
			$this->cakeError('error400');
		}
		exit;
	}

	/**
	* Redirect to the same page, but with the https protocol and exit.
	*/

	function _forceSSL() {
		$this->redirect('https://' . env('SERVER_NAME') . $this->here);
		exit;
	}
	
	function send_notifications()
	{	
	
		$this->loadModel('Configuration');
		$this->loadModel('Action');
		$lastSent = $this->Configuration->find('first', array('conditions'=>array('name'=> 'last_notification_time')));
		
		if($lastSent == null)
		{
			return;
		}
		$lastSentDate = $lastSent['Configuration']['value'];		
		$this->Action->Behaviors->attach('Containable');			
		$dateNow = date("c");
		
		$actions = $this->Action->find('all', array(
			'conditions'=>array(
				'notify_date >' => $lastSentDate, 
				'notify_date <=' => $dateNow
			),
			'recursive'=>3,
			'contain' => array('Expedient' => array( 'User' => 'Lawyer'))
		));
		
		if(count($actions)==0)
		{
			return;
		}
		foreach($actions as $action)
		{			
			$actionConcept = $action['Action']['concept'];
			$actionComments = $action['Action']['comments'];
			$expedientReference = $action['Expedient']['reference'];
			$userName = $action['Expedient']['User']['Lawyer']['fullname'];
			$userEmail = $action['Expedient']['User']['Lawyer']['email'];
			
			if($userEmail==null)
			{
				continue;
			}
			
			$this->Email->to = $userEmail;
			//$this->Email->bcc = array('azrap.se@gmail.com');
			$this->Email->subject = 'LegaleCloud: Notificación '.$expedientReference.' '.$actionConcept;
			$this->Email->replyTo = 'no-reply@legalecloud.com';
			$this->Email->from = 'Notificaciones LegaleCloud <notificador@legalecloud.com>';
			$this->Email->template = 'simple_message'; // note no '.ctp'
			//Send as 'html', 'text' or 'both' (default is 'text')
			$this->Email->sendAs = 'both'; // because we like to send pretty mail
			//Set view variables as normal
			$this->set('userName', $userName);
			$this->set('expedientReference', $expedientReference);
			$this->set('actionConcept', $actionConcept);
			$this->set('actionComments', $actionComments);
			
			//Do not pass any args to send()
			$this->Email->send();
			$this->Email->reset();
		}
		$lastSent['Configuration']['value'] = $dateNow;
		$this->Configuration->save($lastSent);
		
		
	}
}
?>