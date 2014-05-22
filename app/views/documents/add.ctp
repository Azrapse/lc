<?php echo $this->Form->create('Document', array('type'=>'file', 'action'=>'add'));?>
	<fieldset>
 		<legend><? $multilang->__('AdjuntarDocumento'); ?></legend>
	<?php		
		echo $form->input('description', array('label' => $multilang->text('Description')));
		echo $form->file('Document.submittedfile');
		echo $form->input('action_id', array('type'=>'hidden'));
	?>
	</fieldset>
<?php echo $this->Form->end($multilang->text('Adjuntar'));?>
