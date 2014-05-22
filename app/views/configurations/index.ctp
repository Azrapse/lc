	<h3><?php __('Configuraciones');?></h3>
	<?php echo $this->Html->link(__('Añadir Configuración', true), array('action' => 'add'), array('class'=>'actions')); ?>
	<table cellpadding="0" cellspacing="0">
	<tr>			
			<th><?php echo $this->Paginator->sort('Nombre', 'name');?></th>
			<th style="width: 100%;"><?php echo $this->Paginator->sort('Valor', 'value');?></th>
			<th class="actions"><?php __('Acciones');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($configurations as $configuration):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>		
		<td><?php echo $configuration['Configuration']['name']; ?>&nbsp;</td>
		<td><?php echo $configuration['Configuration']['value']; ?>&nbsp;</td>
		<td class="actions">			
			<?php echo $this->Html->link(__('Editar', true), array('action' => 'edit', $configuration['Configuration']['id'])); ?>
			<?php echo $this->Html->link(__('Borrar', true), array('action' => 'delete', $configuration['Configuration']['id']), null, sprintf(__('¿Seguro que deseas eliminar la configuración %s?', true), $configuration['Configuration']['name'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Página %page%/%pages%, configuraciones   %start%-%end%/%count%.', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('Anterior', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('Siguiente', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
