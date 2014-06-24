<?php
class DocumentsController extends AppController {

	var $name = 'Documents';
	var $helpers = array('Html','Ajax','Javascript', 'Calendar', 'Js'=>array('Prototype'));
	var $components = array( 'RequestHandler' );
	
	function beforeFilter(){
		parent::beforeFilter();
		if($this->params['action'] != 'download'){
			$role = $this->getCurrentUserRole();
			if ($role['Role']['codename'] == 'VIEWER')
			{
				$this->Session->setFlash('Acceso denegado.');
				$this->redirect($this->Auth->logout());
			}
		}
	}
	
	function index() {
		$this->Document->recursive = 0;
		$this->set('documents', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid document', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('document', $this->Document->read(null, $id));
	}

	function ajax_list($actionId){
		$this->Document->recursive = -1;
		$documents = $this->Document->FindAllByActionId($actionId);
		$this->set(compact('documents', 'actionId'));
		$this->layout = false;
	}
	
	function add($action_id = null) {
		if (!empty($this->data)) {			
			$fileData = $this->params['data']['Document']['submittedfile'];
			$this->loadModel('Configuration');
			$config = $this->Configuration->findByName('filesPath');
			$uploadDir = $config['Configuration']['value'];			
			$uploadSubdir = $this->getEmptiestSubdir($uploadDir);
			$this->data['Document']['reference'] = uniqid();
			$uploadPath = $uploadSubdir.DS.'a'.$this->data['Document']['action_id'].'_'.$this->data['Document']['reference'];
			$uploadPath = str_replace('//', '/', $uploadPath);
			if($this->isUploadedFile($fileData)){
				//move_uploaded_file($fileData['tmp_name'], $uploadPath);
				if (rename($fileData['tmp_name'], $uploadPath) == true){	
					$this->Session->setFlash("Fichero ".$fileData['name']." adjuntado.");
					$this->data['Document']['filename'] = $fileData['name'];			
					$this->Document->create();			
					if ($this->Document->save($this->data)) {
						$this->Session->setFlash(__('Documento registrado.', true));
						$this->redirect(array('controller' => 'actions', 'action' => 'edit', $action_id));
					} else {
						$this->Session->setFlash(__('El documento no ha podido ser registrado. Reinténtelo más tarde.', true));
						$this->redirect(array('controller' => 'actions', 'action' => 'edit', $action_id));
					}
				} else {
					$this->Session->setFlash("Error moviendo fichero ".$fileData['name']." a ".$uploadPath);
				}
			} else {
				$this->Session->setFlash("Error subiendo fichero ".$fileData['name'].".");
			}						
		} else {
			$this->data['Document']['action_id'] = $action_id;
		}				
	}
	
	function ajax_add($action_id = null) {
		if (!empty($this->data)) {						
			$fileData = $this->params['data']['Document']['submittedfile'];
			$this->loadModel('Configuration');
			$config = $this->Configuration->findByName('filesPath');
			$uploadDir = $config['Configuration']['value'];			
			$uploadSubdir = $this->getEmptiestSubdir($uploadDir);
			$this->data['Document']['reference'] = uniqid();
			$uploadPath = $uploadSubdir.DS.'a'.$this->data['Document']['action_id'].'_'.$this->data['Document']['reference'];
			$uploadPath = str_replace('//', '/', $uploadPath);			
			$action_id = $this->data['Document']['action_id'];
			if($this->isUploadedFile($fileData)){
				//move_uploaded_file($fileData['tmp_name'], $uploadPath);
				if (rename($fileData['tmp_name'], $uploadPath) == true){	
					$this->Session->setFlash("Fichero ".$fileData['name']." adjuntado.");
					$this->data['Document']['filename'] = $fileData['name'];					
					$this->Document->create();			
					if ($this->Document->save($this->data)) {
						$this->Session->setFlash(__('Documento registrado.', true));						
					} else {
						$this->Session->setFlash(__('El documento no ha podido ser registrado. Reinténtelo más tarde.', true));						
					}					
				} else {
					$this->Session->setFlash("Error moviendo fichero ".$fileData['name']." a ".$uploadPath);					
				}
			} else {
				$this->Session->setFlash("Error subiendo fichero ".$fileData['name'].".");				
			}
			$this->redirect(array('controller'=>'actions', 'action' => 'edit', $action_id));
		} else {
			$this->data['Document']['action_id'] = $action_id;
		}
		$this->layout = false;		
	}

	function isUploadedFile($fileData){	
		if ((isset($fileData['error']) && $fileData['error'] == 0) || (!empty( $fileData['tmp_name']) && $fileData['tmp_name'] != 'none')) {
			return is_uploaded_file($fileData['tmp_name']);
		}
		return false;
	}
	

	
	function findInSubdirs($path, $file){
		$subdirs = $this->getDirectories($path);
		foreach($subdirs as $subdir){
			$filepath = $subdir.DS.$file;
			$filepath = str_replace('//', '/', $filepath);
			if(file_exists($filepath)){
				return $filepath;
			}
		}
		return null;
	}
	
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid document', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Document->save($this->data)) {
				$this->Session->setFlash(__('The document has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The document could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Document->read(null, $id);
		}
		$actions = $this->Document->Action->find('list');
		$this->set(compact('actions'));
	}
		
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Identificador de documento no válido.', true));
			$this->redirect(array('controller' => 'actions', 'action' => 'edit', $action_id));
		}
		
		// Borramos el fichero de los directorios de documentos.
		$this->loadModel('Configuration');
		$config = $this->Configuration->findByName('filesPath');
		$subdirs = $this->getDirectories($config['Configuration']['value']);
		$this->Document->recursive = -1;
		$document = $this->Document->findById($id);
		$action_id = $document['Document']['action_id'];
		// Lo buscamos en todos los subdirectorios
		foreach($subdirs as $dir){
			$filepath = $dir.'a'.$action_id.'_'.$document['Document']['reference'];			
			if (file_exists($filepath)){
				unlink($filepath);
				break;
			}
		}
				
		if ($this->Document->delete($id)) {
			$this->Session->setFlash(__('Documento borrado.', true));
			$this->redirect(array('controller' => 'actions', 'action' => 'edit', $action_id));
		}
		$this->Session->setFlash(__('Documento NO borrado.', true));
		$this->redirect(array('controller' => 'actions', 'action' => 'edit', $action_id));		
	}

	function download($id){
		// Determinar que el usuario efectivamente tiene permiso para descargar este documento.
		// Obtenemos el id de expediente a partir de la actuación, a partir del documento.		
		// Lo comparamos con el id de expediente que se permite ver almacenado en sesión.
		$this->Document->Behaviors->attach('Containable');
		$document = $this->Document->find('first',
			array(
				'conditions' => array(
					'Document.id' => $id
				),
				'contain' => array(
					'Action'=>array(
						'expedient_id'
					)
				)
			)
		);
		
		$role = $this->getCurrentUserRole();
		if($role['Role']['codename'] == 'VIEWER' && $document['Action']['expedient_id'] != $this->Session->read('allowed_expedient_id')){			
			$this->Session->setFlash('Acceso denegado. Debe proporcionar una referencia y contraseña específica para el expediente de ese documento.');						
			$this->redirect($this->Auth->logout());
		}
		
		// Descarga del fichero
		
		$this->loadModel('Configuration');
		$config = $this->Configuration->findByName('filesPath');
		$docsdir = $config['Configuration']['value'];
		$this->autoRender = false;
		
		$filename = 'a'.$document['Document']['action_id'].'_'.$document['Document']['reference'];
		$downloadedFilename = $document['Document']['filename'];
		// Buscamos en qué subdirectorio está
		$filepath = $this->findInSubdirs($docsdir, $filename);
		
		if (is_file($filepath)) {			
			$fileInfo = pathinfo($filepath);
			$params = array(
				'id' => $fileInfo['basename'],
				'download' => true,
				'name' => $fileInfo['filename'],
				//'extension' => $fileInfo['extension'],
				'path' => $fileInfo['dirname'].DS
			);
			$this->set($params);
			Configure::write('debug', 0);
						
			$size = filesize($filepath);
			$type = '';
			if (function_exists('mime_content_type')) {
				$type = mime_content_type($filepath);
			} else if (function_exists('finfo_file')) {
				$info = finfo_open(FILEINFO_MIME);
				$type = finfo_file($info, $filepath);
				finfo_close($info); 
			}
			if ($type == '') {
				$type = "application/force-download";
			}
			
			// Set Headers
			header("Content-Type: $type");
			header("Content-Disposition: attachment; filename=$downloadedFilename");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . $size);
			
			// Download File
			$this->view = 'Media';
			readfile($filepath);
		} else {
			$this->Session->setFlash('Documento inexistente: '.$filepath);
			$this->redirect(array('controller'=>'home', 'action'=>'index'));
		}
	}
}
?>