<div style="float: right;">
	<?php echo $this->Html->link($multilang->text('VolverPrincipal'), array('controller' => 'home', 'action' => 'index'), array('class'=>'actions')); ?>
</div>
<div>
<h3><?$multilang->__("Cliente")?></h3>
<b><?$multilang->__("Nombre")?>:</b>
<div id='editorFullname' class='inplaceEditor'><?echo $customer['User']['fullname']; ?></div>
	<?		echo $ajax->editor(
				'editorFullname', 
				array('controller'=>'users', 'action'=>'ajaxEdit', $customer['User']['id'], 'fullname'),
				array('okControl'=>true, 'cancelControl'=>true, 'submitOnBlur'=>true, 'savingText'=>$multilang->text('Guardando'), 'clickToEditText'=>$multilang->text('PulseParaEditar'))
			); 		
	?>
<br />
</div>
<b><?$multilang->__("NombreDeAcceso")?>:</b> 
<? echo $customer['User']['username']; ?>
<br/>
<b><?$multilang->__("Contrasena")?>:</b>
<div id='editorPassword' class='inplaceEditor'><i><?$multilang->__("IntroduzcaNuevaContrasena")?></i></div>
	<?		echo $ajax->editor(
				'editorPassword', 
				array('controller'=>'users', 'action'=>'ajaxEdit', $customer['User']['id'], 'password'),
				array('okControl'=>true, 'cancelControl'=>true, 'submitOnBlur'=>'true', 'savingText'=>$multilang->text('Guardando'), 'clickToEditText'=>$multilang->text('PulseParaEditar'))
			); 		
	?>
</div>
<hr />
<div>
<h3><?$multilang->__("Expedientes")?></h3>
<p>
	<a class="actions" href="<?php echo $this->Html->url(array('controller' => 'expedients', 'action' => 'add', $customer['User']['id'])); ?>">
		<?$multilang->__("CrearExpediente")?>
		<span class="expedient_slots_info">
			(
			<span class="used">
				<? echo $usedExpedientSlots; ?>
			</span> 
			<?$multilang->__("usados")?>
			<span class="available">
				<? echo $availableExpedientSlots; ?>
			</span>
			<?$multilang->__("disponibles")?>)
		</span>
	</a>
</p>
<table cellpadding="0" cellspacing="0">
<tr>			
		<th><?$multilang->__("Referencia")?></th>			
		<th><?$multilang->__("Descripcion")?></th>		
</tr>
<?php
$i = 0;
foreach ($customer['Expedient'] as $expedient):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
<tr<?php echo $class;?>>		
	<td><?php echo $this->Html->link($expedient['reference'], array('controller' => 'expedients', 'action' => 'view', $expedient['id'])); ?>&nbsp;</td>			
	<td><?php echo $expedient['description']; ?>&nbsp;</td>
</tr>
<?php endforeach; ?>
</table>
<p>
	<a class="actions" href="<?php echo $this->Html->url(array('controller' => 'expedients', 'action' => 'add', $customer['User']['id'])); ?>">
		<?$multilang->__("CrearExpediente")?>
		<span class="expedient_slots_info">
			(
			<span class="used">
				<? echo $usedExpedientSlots; ?>
			</span> 
			<?$multilang->__("usados")?>
			<span class="available">
				<? echo $availableExpedientSlots; ?>
			</span>
			<?$multilang->__("disponibles")?>)
		</span>
	</a>
</p>
</div>
