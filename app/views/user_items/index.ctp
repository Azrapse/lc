<fieldset>
	<legend>
		<?$multilang->__("ProductosAdquiridos")?>
	</legend>
	<table>
		<tr>	
			<th>
				<?$multilang->__("Producto")?>
			</th>
			<th>
				<?$multilang->__("Usos")?>
			</th>
			<th>
				<?$multilang->__("Notas")?>
			</th>
		</tr>
	<? foreach($items as $item): ?>		
		<tr class="userItem <? echo ($item['UserItem']['usable'] ? 'usable' : 'unusable'); ?>">
			<td class="name">
				<?$multilang->__($item['ItemType']['name'])?>				
			</td>
			<td class="amount">
				<? echo $item['UserItem']['amount']; ?>
			</td>
			<td class="notes">
				
				<? if (!$item['UserItem']['usable']) {					
					echo $item['UserItem']['notes']; 
				} ?>				
			</td>
		</tr>
	<? endforeach; ?>
	</table>
</fieldset>
<fieldset>
	<legend>
		<?$multilang->__("SaldosCartera")?>
	</legend>
	<table>
		<tr>
			<th>
				<?$multilang->__("Elemento")?>
			</th>
			<th>
				<?$multilang->__("Total")?>
			</th>
			<th>
				<?$multilang->__("EnUso")?>
			</th>
			<th>
				<?$multilang->__("Disponible")?>
			</th>
		</tr>
		<tr>			
			<td>
				<?$multilang->__("RanurasDeExpediente")?>
			</td>
			<td>
				<? echo ($usedExpedientSlots+$availableExpedientSlots); ?>
			</td>
			<td>
				<? echo $usedExpedientSlots; ?>
			</td>
			<td>				
				<? echo $availableExpedientSlots; ?>				
			</td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>
		<?$multilang->__("Comprar")?>
	</legend>
	<table>
		<tr>
			<th>
				<?$multilang->__("Producto")?>
			</th>
			<th>
				<?$multilang->__("Usos")?>
			</th>
			<th>
				<?$multilang->__("Importe")?>
			</th>
			<th>
				&nbsp;
			</th>
		</tr>
	<? foreach($itemOffers as $itemOffer): ?>
		<tr>
			<td>
				<?$multilang->__($itemOffer['ItemType']['name'])?>				
			</td>
			<td>
				<? echo $itemOffer['ItemOffer']['amount']; ?>
			</td>
			<td>
				<? echo $itemOffer['ItemOffer']['price']; ?> € (<?$multilang->__("IVAIncluido")?>)
			</td>
			<td>
				<form action="<? echo $paypalUrl; ?>" method="post">
					<input type="hidden" name="cmd" value="_xclick" />
					<input type="hidden" name="custom" value="<? echo $userId; ?>" />
					<input type="hidden" name="item_number" value="<? echo $itemOffer['ItemOffer']['item_type_id']; ?>" />
					<input type="hidden" name="invoice" value="<? echo $invoiceId; ?>" />
					<input type="hidden" name="notify_url" value="<? echo $notifyUrl; ?>" />
					<INPUT TYPE="hidden" name="charset" value="utf-8">		
					<input type="hidden" name="amount" value="<? echo ($itemOffer['ItemOffer']['price'] / $itemOffer['ItemOffer']['amount']); ?>" />
					<input type="hidden" name="quantity" value="<? echo $itemOffer['ItemOffer']['amount']; ?>" />
					<input type="hidden" name="currency_code" value="EUR"/>
					<input type="hidden" name="business" value="<? echo $businessId; ?>">				
					<input type="hidden" name="item_name" value="<? echo $itemOffer['ItemType']['name']; ?>" />		
					<input type="hidden" name="return" value="<? echo $returnUrl; ?>">
					<input type="submit" name="method" value="<?$multilang->__("Comprar")?>">
				</form>
			</td>
		</tr>
	<? endforeach; ?>
		
	</table>
</fieldset>