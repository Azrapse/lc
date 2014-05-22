<?$multilang->__("Bienvenido", $userlongname) ?>
<fieldset>
	<legend><?$multilang->__("ElementosDelSistema")?></legend>
<? echo $html->link($multilang->text('Despachos'), array('controller'=>'users', 'action'=>'index', 'LAWYER')); ?>
<br />
<? echo $html->link($multilang->text('Clientes'), array('controller'=>'users', 'action'=>'index', 'CUST')); ?>
<br />
<? echo $html->link($multilang->text('TodosLosUsuarios'), array('controller'=>'users', 'action'=>'index')); ?>
<br />
<? echo $html->link($multilang->text('Expedientes'), array('controller'=>'expedients', 'action'=>'index')); ?>
<br />
<? echo $html->link($multilang->text('Actuaciones'), array('controller'=>'actions', 'action'=>'index', 'sort'=>'relevance')); ?>
</fieldset>
<fieldset>
	<legend><?$multilang->__("AccesoPorReferencia")?></legend>
<? 
	echo $form->create('Expedient', array('action'=>'viewByReference'));
	echo $form->input('reference', array('label' => $multilang->text('Referencia')));
	echo $form->end($multilang->text('IrAExpediente'));
?>
</fieldset>
<fieldset>
	<legend><?$multilang->__("BusquedaDeExpedientes")?></legend>
	<? 
	echo $this->Form->create('Expedient', array('action'=>'searchString'));
	echo $this->Form->input('query', array('label'=>false, 'div'=>false, 'type'=>'text', 'style' => 'width: 80%;'));
	echo $this->Form->end(array('label'=> $multilang->text('Buscar'), 'div'=>false));
	?>
</fieldset>
<div>
	<? echo $html->link($multilang->text('AjustesDeConfiguracion'), array('controller'=>'configurations', 'action'=>'index')); ?>
</div>
<div>
	<? echo $html->link($multilang->text('Textos'), array('controller'=>'texts', 'action'=>'index')); ?>
</div>