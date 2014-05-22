<?$multilang->__("Bienvenido", $userlongname) ?>
<fieldset>
	<legend><?$multilang->__("Expedientes")?></legend>
<p><?$multilang->__("ExpedientsExplanation")?></p>
<br />
<? 
	echo $this->Form->create('Expedient', array('action'=>'searchString', 'method'=>'GET'));
	echo $this->Form->input('query', array('label'=>false, 'div'=>false, 'type'=>'text', 'style' => 'width: 80%;'));
	echo $this->Form->end(array('label'=>'Buscar', 'div'=>false));
?>
<table cellpadding="0" cellspacing="0">
<tr>			
		<th><?php echo $this->Paginator->sort($multilang->text('Referencia'), 'Expedient.reference');?></th>			
		<th><?php echo $this->Paginator->sort($multilang->text('Descripcion'), 'Expedient.description');?></th>		
</tr>
<?php
$i = 0;
foreach ($this->data as $expedient):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
<tr<?php echo $class;?>>		
	<td><?php echo $this->Html->link($expedient['Expedient']['reference'], array('controller' => 'expedients', 'action' => 'view', $expedient['Expedient']['id'])); ?>&nbsp;</td>			
	<td><?php echo $expedient['Expedient']['description']; ?>&nbsp;</td>	
</tr>
<?php endforeach; ?>
</table>
<br />
<?php
echo $this->Paginator->counter(array(
'format' => $multilang->text('PaginatorFooterText')
));
?>	</p>

<div class="paging">
	<?php echo $this->Paginator->prev('<< ' . $multilang->text('Anterior'), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
|
	<?php echo $this->Paginator->next($multilang->text('Siguiente') . ' >>', array(), null, array('class' => 'disabled'));?>
</div>
</fieldset>
