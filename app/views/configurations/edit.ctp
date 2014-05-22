<?php echo $this->Form->create('Configuration');?>
<fieldset>
	<legend><?php __('Editar Configuración'); ?></legend>
<?php
	echo $this->Form->input('id', array('type'=>'hidden'));
	echo $this->Form->input('name', array('label'=>'Nombre'));
	echo $this->Form->input('value', array('label'=>'Valor'));
?>
</fieldset>
<?php echo $this->Form->end(__('Guardar', true));?>
