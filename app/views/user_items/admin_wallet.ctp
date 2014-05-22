<? 		
	$this->addScript($this->Html->script('/js/user_items/admin_wallet'));
?> 
<p class="heading">
	<?$multilang->__("ListingWallet", $user['User']['fullname'])?>:
<p> 
<table class="wallet">
	<tr>
		<th>
			<?$multilang->__("Tipo")?>
		</th>
		<th>
			<?$multilang->__("Cantidad")?>
		</th>
		<th>
			<?$multilang->__("Usable")?>
		</th>
		<th>
			<?$multilang->__("Acciones")?>
		</th>
	</tr>
<? 
	foreach($userItems as $item):
?>	
	<tr class="itemRow" itemId="<? echo $item['UserItem']['id']; ?>">
		<td>			
			<? echo $this->Form->input('type', array('empty'=>false, 'options'=>$itemTypes, 'value' => $item['UserItem']['item_type_id'], 'class'=>'type input', 'label'=>false )); ?>
		</td>
		<td>
			<? echo $this->Form->input("amount", array('type'=>'text', 'value'=>$item['UserItem']['amount'], 'label'=>false, 'class' => 'amount input')); ?>
		</td>
		<td>
			<? echo $this->Form->input("usable", array('checked'=>($item['UserItem']['usable']?'checked':false), 'type'=>'checkbox', 'class' => 'usable input', 'label'=>false)); ?>			
		</td>
		<td>
			<a class="action" id="itemModifyLink" href="<?echo $this->Html->url(array('controller'=>'user_items', 'action'=>'admin_modify_item'));?>" data-itemId="<? echo $item['UserItem']['id'] ?>">
				Guardar Cambios
			</a>
			<a class="action" href="<?echo $this->Html->url(array('controller'=>'user_items', 'action'=>'admin_remove_item', $item['UserItem']['id'] ));?>">
				Eliminar
			</a>
		</td>
	</tr>
<?
	endforeach;
?>
</table>