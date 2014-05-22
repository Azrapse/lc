<div style="float: right;">
	<?php echo $this->Html->link($multilang->text('CancelarYVolver'), array('controller' => 'home', 'action' => 'index'), array('class'=>'actions')); ?>
</div>
<div>
<h3>
	<?$multilang->__('EditarUsuario', $user['User']['fullname'])?>
</h3>
<script  language='javascript'>
	function toggleLawyerDiv() {
		var lawyerDiv = document.getElementById('lawyerDiv');
		var roleSelect = document.getElementById('UserRole');
		var selectedIndex = roleSelect.selectedIndex;
		var selectedText = roleSelect[selectedIndex].text;
		
		if (selectedText == 'Cliente') {			
			lawyerDiv.style.display = 'block';
		} else {			
			lawyerDiv.style.display = 'none';
		}
	}
</script>
<? 
	echo $this->Form->create('User');
	echo $this->Form->input('id', array('type'=>'hidden'));
	echo $this->Form->input('fullname', array('label'=>$multilang->text('NombreCompleto'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Form->input('username', array('label'=>$multilang->text('NombreDeAcceso'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Form->input('email', array('label'=>$multilang->text('Email'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Form->input('oldPassword', array('type'=>'hidden'));
	echo $this->Form->input('newPassword', array('label'=>$multilang->text('CambiarContrasena'), 'type'=>'password', 'autocomplete'=>'off'));
	echo $this->Form->input('confirmNewPassword', array('label'=>$multilang->text('ConfirmarCambiarContrasena'), 'type'=>'password', 'autocomplete'=>'off'));
	echo $this->Form->input('Role', array('label'=>$multilang->text('RolEnSistema'), 'type'=>'select', 'onChange' => 'toggleLawyerDiv()'));
	//echo $this->Form->input('Lawyer', array('label'=>'Despacho al que pertenece', 'div' => array('id' => 'lawyerDiv'), 'type'=>'select')); // It doesn't work well
	echo $this->Form->input('lawyer_id', array('options' => $lawyers, 'selected' => $this->data['User']['lawyer_id'], 'label'=>$multilang->text('DespachoPertenece'), 'div' => array('id' => 'lawyerDiv')));
	echo $this->Form->input('language_id', array('options' => $languages, 'selected' => $this->data['User']['language_id'], 'label'=>$multilang->text('Idioma'), 'div' => array('id' => 'languageDiv')));
	echo $this->Form->end($multilang->text('Guardar'));	
?>
</div>
<script>
	toggleLawyerDiv();
</script>