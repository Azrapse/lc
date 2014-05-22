<table>
<tr>
	<th><? $multilang->__("Documento")?></th>
	<th><? $multilang->__("Descripcion")?></th>
	<th><? $multilang->__("Acciones")?></th>
</tr>
<?php
	foreach($documents as $document):
?>
<tr>
	<td>
		<? echo $html->link($document['Document']['filename'], array('action'=>'download', $document['Document']['id'])); ?>
	</td>
	<td>
		<? echo $document['Document']['description']; ?>
	</td>
	<td>
		<? echo $html->link($multilang->text('Eliminar'), array('action'=>'delete', $document['Document']['id'])); ?>
	</td>
</tr>
<?
	endforeach;
?>
</table>
<? echo $ajax->link($multilang->text('AdjuntarDocumento'), array('action'=>'ajax_add', $actionId), array('update'=>'documentContainer')); ?>