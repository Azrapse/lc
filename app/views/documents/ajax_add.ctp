<fieldset>
	<legend><? $multilang->__('AdjuntarDocumento'); ?></legend>
<?php		
	echo $this->Form->create('Document', array('type'=>'file', 'action'=>'ajax_add'));
	echo $form->input('description', array('label' => $multilang->text('Description'), 'autocomplete'=>'off'));
	echo $form->file('Document.submittedfile');
	echo $form->input('action_id', array('type'=>'hidden'));		
	echo $this->Form->end($multilang->text('Adjuntar'));
?>	
</fieldset>