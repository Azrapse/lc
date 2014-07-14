<?php

class ActionsController extends AppController {
    var $scaffold;
	var $helpers = array('Html','Ajax','Javascript', 'Calendar', 'Fck', 'Js'=>array('Prototype'));
	var $components = array( 'RequestHandler' );

	function beforeFilter(){
		parent::beforeFilter();
		$role = $this->getCurrentUserRole();
		if ($role['Role']['codename'] == 'VIEWER')
		{
			$this->Session->setFlash('Acceso denegado.');
			$this->redirect($this->Auth->logout());
		}
	
	}

	
	function index() {
		$role = $this->getCurrentUserRole();
		if ($role['Role']['codename'] != 'ADMIN')
		{
			$this->Session->setFlash('Acceso denegado.');
			$this->redirect($this->Auth->logout());
		}
	
		$this->Action->recursive = 0;
		$this->set('actions', $this->paginate());
	}
	
	function edit($id){
		if(!empty($this->data)){
			$this->data['Action']['date'] = date_format(DateTime::createFromFormat('d/m/Y', $this->data['Action']['date']), 'Y-m-d');
            $this->data['Action']['id'] = $id;
            $this->Action->id = $id;
			$this->Action->save($this->data);
			$this->redirect(array('controller'=>'expedients', 'action'=>'view', $this->data['Action']['expedient_id']));
		} else  {
			$this->data = $this->Action->find('first', array('conditions' => array('id'=>$id), 'recursive'=>-1));
			$this->data['Action']['date'] = date('d/m/Y', strtotime($this->data['Action']['date']));
			//$this->data['Action']['comments'] = htmlspecialchars($this->data['Action']['comments']);
			$statuses = $this->Action->Status->find('list');
			$this->set('statuses', $statuses);				
		}
		$this->set('actionId', $id);
	}
	
	function delete($id){
		
		$this->Action->Behaviors->attach('Containable');
		$action = $this->Action->find('first', array(
			'conditions' => array(
				'Action.id' => $id
			), 
			'recursive' => 2,
			'contain' => array(
				'Expedient' => array(
					'id', 
					'user_id', 
					'User' => array('id', 'lawyer_id')
				)
			)			
		));
		
		$expedient_id = $action['Action']['expedient_id'];
		$deletedDocs = 0;
		$role = $this->getCurrentUserRole();
		// Check if current User is lawyer to this action		
		$auth = $this->Session->read('Auth');
		$currentUserId = $auth['User']['id'];				
		
		
		if ($role['Role']['codename'] == 'ADMIN' || $action['Expedient']['User']['lawyer_id'] == $currentUserId){			
			// Eliminar primero todos los documentos de la actuación
			// **
			$this->loadModel('Configuration');
			App::import('Controller', 'Documents');
			$documentsController = new DocumentsController;
			$config = $this->Configuration->findByName('filesPath');
			$subdirs = $documentsController->getDirectories($config['Configuration']['value']);
			$this->Action->Document->recursive = -1;
			$documents = $this->Action->Document->find('all', array('conditions'=>array('action_id'=>$id), 'fields'=>array('Document.id', 'Document.reference')));
			foreach($documents as $document)
			{				
				// Borramos el fichero de los directorios de documentos.						
				// Lo buscamos en todos los subdirectorios
				foreach($subdirs as $dir){
					$filepath = $dir.'a'.$id.'_'.$document['Document']['reference'];						
					if (file_exists($filepath)){
						unlink($filepath);
						break;
					}
				}				
				$this->Action->Document->delete($document['Document']['id']);
				$deletedDocs++;
			}
			// **
		
			$this->Action->delete($id);
			$this->Session->setFlash('Actuación eliminada. Se eliminaron '.$deletedDocs.' documentos adjuntos.');
		} else {
			$this->Session->setFlash('Permiso denegado.');
		}
		$this->redirect(array('controller'=>'expedients', 'action'=>'view', $expedient_id));		
	}

	function add($expedient_id=null){		
		if (!empty($this->data)){	
			$this->data['Action']['date'] = date_format(DateTime::createFromFormat('d/m/Y', $this->data['Action']['date']), 'Y-m-d');
			$this->Action->create();			
			$this->Action->save($this->data);
			$this->redirect(array('controller'=>'expedients', 'action'=>'view', $this->data['Action']['expedient_id']));
		} else {
			$statuses = $this->Action->Status->find('list');
			$this->set('statuses', $statuses);
			$this->data = array(
				'Action' => array(
					'expedient_id'=>$expedient_id,
					'date' => date('d/m/Y'),
					'relevance' => 0
					)
			);			
		}
	}
	
	function ajax_setRelevance($action_id, $relevance){
		$currentUser = $this->Auth->user();
		$role = $this->getCurrentUserRole();
		if ($role['Role']['codename'] == 'VIEWER' || $role['Role']['codename'] == 'CUST'){
			$this->Session->setFlash('Acceso denegado.');
			$this->redirect($this->Auth->logout());
		}
		$this->Action->Behaviors->attach('Containable');
		$action = $this->Action->find('first', array(
			'conditions' => array(
				'Action.id' => $action_id
			), 
			'recursive' => 2,
			'contain' => array(
				'Expedient' => array(
					'id', 
					'user_id', 
					'User' => array('id', 'lawyer_id')
				)
			)			
		));
		
		if ($role['Role']['codename'] == 'LAWYER'){
			if($action['Expedient']['User']['lawyer_id'] != $currentUser['User']['id']) {
				$this->Session->setFlash('Acceso denegado. Identifíquese como el representante del cliente.');
				$this->redirect($this->Auth->logout());
			}
		}
				
		$action['Action']['relevance'] = $relevance;
		$this->Action->save($action);
		$this->layout = false;
		$this->set('relevance', $relevance);
		$this->set('action_id', $action_id);
		$this->render('/actions/ajax_relevance');
	}
	
	function ajax_viewRelevance($action_id){
		$currentUser = $this->Auth->user();
		$role = $this->getCurrentUserRole();
		if ($role['Role']['codename'] == 'VIEWER' || $role['Role']['codename'] == 'CUST'){
			$this->Session->setFlash('Acceso denegado.');
			$this->redirect($this->Auth->logout());
		}
		$this->Action->Behaviors->attach('Containable');
		$action = $this->Action->find('first', array(
			'conditions' => array(
				'Action.id' => $action_id
			), 
			'recursive' => 2,
			'contain' => array(
				'Expedient' => array(
					'id', 
					'user_id', 
					'User' => array('id', 'lawyer_id')
				)
			)			
		));
		
		if ($role['Role']['codename'] == 'LAWYER'){
			if($action['Expedient']['User']['lawyer_id'] != $currentUser['User']['id']) {
				$this->Session->setFlash('Acceso denegado. Identifíquese como el representante del cliente.');
				$this->redirect($this->Auth->logout());
			}
		}
		
		$relevance = $action['Action']['relevance'];
		$this->layout = false;
		$this->set('relevance', $relevance);
		$this->set('action_id', $action_id);
		$this->render('/actions/ajax_relevance');
	}
	
	function ajaxEdit($id, $field){
		$value = trim($this->params['form']['value']);
		$action = $this->Action->find('first', array('conditions' => array('id' => $id), 'recursive' => -1));
		$action['Action'][$field] = $value;
		$this->set('value', $value);
		
		$this->Action->save($action);
		$this->layout = false;
		$this->render('/ajax/ajax_inplaceeditor');
	}
	
	function ajaxEditNotify($id){
		$value = trim($this->params['form']['value']);
		$this->Action->Behaviors->attach('Containable');
		$action = $this->Action->find('all', array(
			'conditions' => array('Action.id' => $id), 	
			'recursive'=>3,
			'contain' => array('Expedient' => array('User'=>'Lawyer'))
		));
		$action = $action[0];
		
		if($value=='' or $value==null){
			$value = null;
		}
		$action['Action']['notify_date'] = $value;		
		
		$this->Action->save($action);
		$this->layout = false;
		
		$email = $action['Expedient']['User']['Lawyer']['email'];
		$this->set('value', $email);
		$this->render('/ajax/ajax_inplaceeditor');
	}
}

?>