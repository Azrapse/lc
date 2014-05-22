<?php

class RolesController extends AppController {
    var $scaffold;
	
		function beforeFilter(){
		parent::beforeFilter();
		
		$role = $this->getCurrentUserRole();
		if ($role['Role']['codename'] != 'ADMIN')
		{
			$this->Session->setFlash('Acceso denegado.');
			$this->redirect($this->Auth->logout());
		}	
	}

	
}

?>