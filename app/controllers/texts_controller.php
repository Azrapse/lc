<?php
class TextsController extends AppController {
        var $scaffold;
	var $name = 'Texts';
        
        function add(){
            $languages = $this->Text->Translation->Language->find('list');            
            $this->set('languages', $languages);
            if (!empty($this->data)){
		    $this->Text->create();
                    if($this->Text->save($this->data, false)) {
                        $engText = $this->data['Text']['lang1'];
                        $eng = array('Translation' => array('text_id' => $this->Text->id, 'language_id'=>1, 'message' => $engText));
                        $spaText = $this->data['Text']['lang2'];
                        $spa = array('Translation' => array('text_id' => $this->Text->id, 'language_id'=>2, 'message' => $spaText));
                        $porText = $this->data['Text']['lang3'];
                        $por = array('Translation' => array('text_id' => $this->Text->id, 'language_id'=>3, 'message' => $porText));
                        $this->Text->Translation->create();
                        $this->Text->Translation->save($eng);
                        $this->Text->Translation->create();
                        $this->Text->Translation->save($spa);
                        $this->Text->Translation->create();
                        $this->Text->Translation->save($por);
                        $this->redirect(array('controller'=>'texts', 'action'=>'index'));
                    } else {
                        $this->Session->setFlash("Error al crear texto.");
                    }                    
		} else {			
			
		}
            
        }
	
	function edit($textId){
            $languages = $this->Text->Translation->Language->find('list');            
            $this->set('languages', $languages);
            if (!empty($this->data)){		    
                    if($this->Text->save($this->data, false)) {
                        $engText = $this->data['Text']['lang1'];
			$engId = $this->data['Text']['langid1'];
                        $eng = array('Translation' => array('text_id' => $this->Text->id, 'language_id'=>1, 'message' => $engText));
                        $spaText = $this->data['Text']['lang2'];
			$spaId = $this->data['Text']['langid2'];
                        $spa = array('Translation' => array('text_id' => $this->Text->id, 'language_id'=>2, 'message' => $spaText));
                        $porText = $this->data['Text']['lang3'];
			$porId = $this->data['Text']['langid3'];
                        $por = array('Translation' => array('text_id' => $this->Text->id, 'language_id'=>3, 'message' => $porText));
                        if($engId == null){
				$this->Text->Translation->create();
			}else{
				$this->Text->Translation->id = $engId;
			}
                        $this->Text->Translation->save($eng);
                        if($spaId == null){
				$this->Text->Translation->create();
			}else{
				$this->Text->Translation->id = $spaId;
			}
                        $this->Text->Translation->save($spa);
                        if($porId == null){
				$this->Text->Translation->create();
			}else{
				$this->Text->Translation->id = $porId;
			}
                        $this->Text->Translation->save($por);
                        $this->redirect(array('controller'=>'texts', 'action'=>'index'));
                    } else {
                        $this->Session->setFlash("Error al crear texto.");
                    }                    
		} else {
			$this->Text->id = $textId;
			$text = $this->Text->find('first', array('conditions'=>array('id'=>$textId)));
			$this->data = $text;
			$eng = $this->Text->Translation->find('first', array('conditions'=>array('text_id'=>$textId, 'language_id'=>1)));
			$spa = $this->Text->Translation->find('first', array('conditions'=>array('text_id'=>$textId, 'language_id'=>2)));
			$por = $this->Text->Translation->find('first', array('conditions'=>array('text_id'=>$textId, 'language_id'=>3)));
			$this->data['Text']['lang1']=$eng['Translation']['message'];
			$this->data['Text']['lang2']=$spa['Translation']['message'];
			$this->data['Text']['lang3']=$por['Translation']['message'];
			$this->data['Text']['langid1']=$eng['Translation']['id'];
			$this->data['Text']['langid2']=$spa['Translation']['id'];
			$this->data['Text']['langid3']=$por['Translation']['id'];
		}            
        }
}
?>