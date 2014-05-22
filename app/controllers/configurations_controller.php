<?php
class ConfigurationsController extends AppController {

	var $name = 'Configurations';

	function beforeFilter(){
		parent::beforeFilter();
		
		$role = $this->getCurrentUserRole();
		if ($role['Role']['codename'] != 'ADMIN')
		{
			$this->Session->setFlash('Acceso denegado.');
			$this->redirect($this->Auth->logout());
		}	
	}

	
	function index() {
		$this->Configuration->recursive = 0;
		$this->set('configurations', $this->paginate());
	}

	function add() {
		if (!empty($this->data)) {
			$this->Configuration->create();
			if ($this->Configuration->save($this->data)) {
				$this->Session->setFlash(__('The configuration has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The configuration could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid configuration', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Configuration->save($this->data)) {
				$this->Session->setFlash(__('The configuration has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The configuration could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Configuration->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for configuration', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Configuration->delete($id)) {
			$this->Session->setFlash(__('Configuration deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Configuration was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>