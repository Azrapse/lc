<?$multilang->__("Bienvenido", $userlongname) ?>
<br />

<div id="expedientUserPanel">
	<div id="expedientPanel">
		<fieldset>
			<legend>
				<?$multilang->__("Expedientes")?>
			</legend>
		<?$multilang->__("BusquedaDeExpedientes")?>
		<? 	// Searchbox
			echo $this->Form->create('Expedient', array('action'=>'searchString'));
			echo $this->Form->input('query', array('label'=> false, 'div'=>false, 'type'=>'text', 'style' => 'width: 80%;'));
			echo $this->Form->end(array('label'=>$multilang->text('Buscar'), 'div'=>false));
		?>

		<p><?$multilang->__("ExpedientsExplanation")?></p>
		
		<table cellpadding="0" cellspacing="0">
		<tr>			
				<th><?php echo $this->Paginator->sort($multilang->text('Referencia'), 'Expedient.reference');?></th>			
				<th><?php echo $this->Paginator->sort($multilang->text('Descripcion'), 'Expedient.description');?></th>
				<th><?php echo $this->Paginator->sort($multilang->text('Cliente'), 'User.fullname');?></th>		
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
			<td><?php echo $this->Html->link($expedient['User']['fullname'], array('controller' => 'users', 'action' => 'lawyers_customer_view', $expedient['User']['id'])); ?>&nbsp;</td>				
		</tr>
		<?php endforeach; ?>
		</table>
		<?php
		echo $this->Paginator->counter(array(
		'format' => $multilang->text('PaginatorFooterText')
		));
		?>

		<div class="paging">
			<?php echo $this->Paginator->prev('<< ' .  $multilang->text('Anterior'), array(), null, array('class'=>'disabled'));?>
		 | 	<?php echo $this->Paginator->numbers();?>
		|
			<?php echo $this->Paginator->next( $multilang->text('Siguiente') . ' >>', array(), null, array('class' => 'disabled'));?>
		</div>
		<br />
		</fieldset>
	</div>
	
	<div id="userPanel">
		<fieldset>
			<legend><?$multilang->__("Clientes")?></legend>
		<p><?$multilang->__("ClientesExplanation")?></p>
		<br />
		<table>
			<tr>
				<th>
					<?$multilang->__("Cliente")?>
				</th>
				<th>
					<?$multilang->__("Expedientes")?>
					<span class="expedient_slots_info">
						(<span class="used">
							<? echo $usedExpedientSlots; ?>
						</span> 
						<?$multilang->__("usados")?>
						<span class="available">
							<? echo $availableExpedientSlots; ?>
						</span>
						<?$multilang->__("disponibles")?>)
					</span>
				</th>
			</tr>
		<? foreach($customers as $customer): ?>
			<tr>
				<td>
					<? echo $html->link($customer['User']['fullname'], array('controller'=>'users', 'action'=>'lawyers_customer_view', $customer['User']['id'])); ?>
				</td>
				<td>
					<? echo $customer[0]['expedient_count']; ?>
				</td>
			</tr>
		<? endforeach; ?>
		</table>

		<br />
		<? echo $this->Html->link($multilang->text('CrearNuevoCliente'), array('controller' => 'users', 'action' => 'add_customer', $user['id']), array('class' => 'actions')); ?>
		
		</fieldset>
	</div>

</div>