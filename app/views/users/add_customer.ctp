<div style="float: right;">
	<?php echo $this->Html->link($multilang->text('CancelarYVolver'), array('controller' => 'home', 'action' => 'index'), array('class'=>'actions')); ?>
</div>
<div>
<h3>
<?$multilang->__('CrearNuevoClienteDe', $currentUser['User']['fullname'])?>
</h3>
<? 
	echo $this->Form->create('User');
	echo $this->Form->input('fullname', array('label'=>$multilang->text('NombreCompleto'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Form->input('username', array('label'=>$multilang->text('NombreDeAcceso'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Form->input('password', array('label'=>$multilang->text('ContrasenaDeAcceso'), 'type'=>'text', 'autocomplete'=>'off'));
	if ($lawyerId != null) {
		echo $this->Form->hidden('lawyer_id');
	} else {
		echo $this->Form->input('lawyer_id', array('options' => $lawyers, 'selected' => $this->data['User']['lawyer_id'], 'label'=>$multilang->text('DespachoPertenece')));
	}
	echo $this->Form->end($multilang->text('Crear'));	
?>
<?$multilang->__('PodraAnadir')?>
</div>