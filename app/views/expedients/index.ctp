
<h3><?php __('Listado de Expedientes');?></h3>
<?php echo $this->Html->link(__('Nuevo Expediente', true), array('action' => 'add'), array('class'=>'actions')); ?>
<table cellpadding="0" cellspacing="0">
<tr>			
		<th><?php echo $this->Paginator->sort('Referencia', 'reference');?></th>
		<th><?php echo $this->Paginator->sort('Descripción', 'description');?></th>
		<th class="actions"><?php __('Acciones');?></th>
</tr>
<?php
$i = 0;
foreach ($expedients as $expedient):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
<tr<?php echo $class;?>>		
	<td><?php echo $expedient['Expedient']['reference']; ?>&nbsp;</td>
	<td><?php echo $expedient['Expedient']['description']; ?>&nbsp;</td>
	<td class="actions">
		<?php echo $this->Html->link(__('Ver', true), array('action' => 'view', $expedient['Expedient']['id'])); ?>			
		<?php echo $this->Html->link(__('Borrar', true), array('action' => 'delete', $expedient['Expedient']['id']), null, sprintf(__('¿Está seguro de que desea borrar el expediente %s y todo su contenido?', true), $expedient['Expedient']['reference'])); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Página %page% de %pages%, expedientes %start% a %end% de %count%.', true)
));
?>	</p>

<div class="paging">
	<?php echo $this->Paginator->prev('<< ' . __('Anterior', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
|
	<?php echo $this->Paginator->next(__('Siguiente', true) . ' >>', array(), null, array('class' => 'disabled'));?>
</div>
