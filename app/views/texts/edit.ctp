<div style="float: right;">
	<?php echo $this->Html->link('Cancelar y volver', array('controller' => 'texts', 'action' => 'index'), array('class'=>'actions')); ?>
</div>
<div>
<h3>
Añadir texto
</h3>
<?	
	echo $this->Form->create('Text',array('action' => 'edit'));
        echo $this->Form->input('id', array('type'=>'hidden'));
	echo $this->Form->input('identifier');	
	$i=0;
        foreach($languages as $language){
            $i++;
            echo $this->Form->input('lang'.$i, array('label'=>$language, 'type'=>'textarea', 'autocomplete'=>'off'));
	    echo $this->Form->input('langid'.$i, array('type'=>'hidden'));
        }	
	echo $this->Form->end('Guardar');	
?>
</div>
