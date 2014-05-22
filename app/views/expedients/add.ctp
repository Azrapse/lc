<h3>
<? $multilang->__("CrearExpediente")?>
<? 
	if ($fixedCustomer) {
		echo $multilang->text('para', $customerName);
	}
?>
</h3>
<? 
	echo $this->Form->create('Expedient');
	echo $this->Form->input('reference', array('label'=>$multilang->text('Referencia'), 'type'=>'text', 'autocomplete'=>'off'));
	if ($subfix != '') {
		$multilang->__("referenceSubfixWarning", $subfix);		
	}
	echo $this->Form->input('password', array('label'=>$multilang->text('Contrasena'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Form->input('description', array('label'=>$multilang->text('Descripcion'), 'type'=>'text', 'autocomplete'=>'off'));
	if ($fixedCustomer == true) {
		echo $this->Form->hidden('user_id');		
	} else {
		echo $this->Form->input('user_id', array('label'=>'Titular'));
	}
	echo $this->Form->end($multilang->text('Crear'));
?>

