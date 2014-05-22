<?php
function highlightStr($haystack, $needle, $highlightColorValue) {
     // return $haystack if there is no highlight color or strings given, nothing to do.
    if (strlen($highlightColorValue) < 1 || strlen($haystack) < 1 || strlen($needle) < 1) {
        return $haystack;
    }
    preg_match_all("/$needle+/i", $haystack, $matches);
    if (is_array($matches[0]) && count($matches[0]) >= 1) {
        foreach ($matches[0] as $match) {
            $haystack = str_replace($match, '<span style="background-color:'.$highlightColorValue.';">'.$match.'</span>', $haystack);
        }
    }
    return $haystack;
}
?>

<h3>Búsqueda de Expedientes</h3>

<? 	// Searchbox
	echo $this->Form->create('Expedient', array('action'=>'searchString'));
	echo $this->Form->input('query', array('label'=> false, 'div'=>false, 'type'=>'text', 'style' => 'width: 80%;'));
	echo $this->Form->end(array('label'=>'Buscar', 'div'=>false));
?>
<table cellpadding="0" cellspacing="0">
<tr>			
		<th><?php echo $this->Paginator->sort('Referencia', 'Expedient.reference');?></th>
		<th><?php echo $this->Paginator->sort('Descripción', 'Expedient.description');?></th>		
<? if (!$isCustomer) { ?>
		<th><?php echo $this->Paginator->sort('Cliente', 'User.fullname');?></th>
<? } ?>
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
	<?
		$highlitDesc = $expedient['Expedient']['description'];
		foreach($tokens as $token) {
			$highlitDesc = highlightStr($highlitDesc, $token, '#FF0');
		}		
	?>
	<td><?php echo $highlitDesc; ?>&nbsp;</td>
<? if (!$isCustomer) { ?>
	<td><?php echo $this->Html->link($expedient['User']['fullname'], array('controller' => 'users', 'action' => 'lawyers_customer_view', $expedient['User']['id'])); ?>&nbsp;</td>
<? } ?>
	
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