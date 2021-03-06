<?=$layout->blockStart('viewhead');?>
	<?php echo $this->Calendar->scripts(); ?>
<?=$layout->blockEnd();?>

<fieldset>
	<legend><? $multilang->__("ActionInfo")?></legend>
<?php	
	echo $form->create('Action', array('action' => 'edit'));
	echo $form->input('expedient_id', array('type'=>'hidden'));
	echo $form->input('relevance', array('type'=>'hidden'));
	echo $form->input('concept', array('label' => $multilang->text('Concepto'), 'type'=>'text'));
	echo $form->input('comments', array('label' => $multilang->text('Comentarios')));
	echo $javascript->link('fckeditor');
	echo $fck->load('ActionComments', 860, 400);
	echo $form->input('date', array('label'=>$multilang->text('Fecha'), 'type'=>'text', 'autocomplete'=>'off'));
	echo $this->Calendar->set('ActionDate');
	echo $form->input('status_id', array('label' => $multilang->text('Estado')));
	echo $form->end($multilang->text('Guardar'));	
?>
<?$multilang->__("SaveBeforeDoc");?>

</fieldset>

<fieldset>
	<legend><? $multilang->__("ActionDocs")?></legend>
	<div id="documentContainer"></div>
	<script type="text/javascript">//<![CDATA[
			(function ($){
				var url = "<?php echo $this->Html->url(array('controller' => 'documents', 'action' => 'ajax_list', $actionId))?>";
				$("#documentContainer").load(url, {});
			})(jQuery);			
		//]]></script>		
</fieldset>