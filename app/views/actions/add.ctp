<?=$layout->blockStart('viewhead');?>
	<?php echo $this->Calendar->scripts(); ?>
<?=$layout->blockEnd();?>

<h3><? $multilang->__("AnadirActuacion") ?></h3>
<?php	
	echo $form->create('Action', array('action' => 'add'));
	echo $form->input('expedient_id', array('type'=>'hidden'));
	echo $form->input('relevance', array('type'=>'hidden'));
	echo $form->input('concept', array('label' => $multilang->text('Concepto'), 'type'=>'text'));
	echo $form->input('comments', array('label' => $multilang->text('Comentarios')));
	echo $javascript->link('fckeditor');
	echo $fck->load('ActionComments', 900, 400);
	echo $form->input('date', array('label'=>$multilang->text('Fecha'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Calendar->set('ActionDate');
	echo $form->input('status_id', array('label' => $multilang->text('Estado')));
	echo $form->end($multilang->text('Guardar'));	
?>
<? $multilang->__('AddActionFoot') ?>