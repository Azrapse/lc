<div style="float: right;">
	<?php echo $this->Html->link($multilang->text('CancelarYVolver'), array('controller' => 'home', 'action' => 'index'), array('class'=>'actions')); ?>
</div>
<div>
<h3>
<?$multilang->__('CrearNuevoDespacho')?>
</h3>
<? 
	echo $this->Form->create('User');
	echo $this->Form->input('fullname', array('label'=>$multilang->text('NombreCompleto'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Form->input('username', array('label'=>$multilang->text('NombreDeAcceso'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Form->input('email', array('label'=>$multilang->text('Email'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Form->input('password', array('label'=>$multilang->text('ContrasenaDeAcceso'), 'type'=>'text', 'autocomplete'=>'off'));	
	echo $this->Form->end('Crear');	
?>
<?$multilang->__('DespachoPodra')?>
</div>