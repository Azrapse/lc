<table cellpadding="0" cellspacing="0">
<tr>			
		<th><?php echo $this->Paginator->sort('Concepto de la Actuación', 'concept');?></th>			
		<th><?php echo $this->Paginator->sort('Fecha', 'date');?></th>
		<th><?php echo $this->Paginator->sort('Estado', 'status_id');?></th>
		<th><?php echo $this->Paginator->sort('Relevancia', 'relevance');?></th>
		<th><?php echo $this->Paginator->sort('Expediente', 'expedient_id');?></th>
		<th class="actions"><?php __('Acciones');?></th>
</tr>
<?php
$i = 0;
foreach ($actions as $action):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
<tr<?php echo $class;?>>		
	<td><?php echo $action['Action']['concept']; ?>&nbsp;</td>		
	<td><?php echo date('d/m/Y', strtotime($action['Action']['date'])); ?>&nbsp;</td>
	<td><?php echo $action['Status']['name']; ?></td>
	<td>
		<div id='action<? echo $action['Action']['id']; ?>RelevanceControl'></div>
		<script type="text/javascript">//<![CDATA[
			<?php echo $ajax->remoteFunction(array(         
					'url' => array( 
						'controller' => 'actions', 
						'action' => 'ajax_viewRelevance', $action['Action']['id']),
						'update' => 'action'.$action['Action']['id'].'RelevanceControl'
					) 
				); 
			?>
		//]]></script>				
	</td>
	<td>
		<?php echo $this->Html->link($action['Expedient']['reference'], array('controller' => 'expedients', 'action' => 'view', $action['Expedient']['id'])); ?>
	</td>
	<td class="actions">
		<?php echo $this->Html->link(__('Editar', true), array('action' => 'edit', $action['Action']['id'])); ?>
		<?php echo $this->Html->link(__('Eliminar', true), array('action' => 'delete', $action['Action']['id']), null, __('La eliminación es permanente y todos los documentos adjuntos a la actuación serán inaccesibles. ¿ Está seguro de que desea eliminar esta actuación?', true)); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Página %page%/%pages%, actuaciones %start% a %end% de %count%.', true)
));
?>	</p>

<div class="paging">
	<?php echo $this->Paginator->prev('<< ' . __('Anterior', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
|
	<?php echo $this->Paginator->next(__('Siguiente', true) . ' >>', array(), null, array('class' => 'disabled'));?>
</div>
