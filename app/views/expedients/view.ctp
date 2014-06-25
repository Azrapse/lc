<?=$layout->blockStart('viewhead');?>
	<? echo $this->Html->script("expedient_view"); ?>
    <? echo $this->Html->script("jquery.qrcode-0.7.0.min"); ?>
    <?php echo $this->Calendar->scripts(); ?>
<?=$layout->blockEnd();?>

<? if(($isLawyer || $isAdmin) && !$noCustomer) { ?>

<div style="float: right;">
	<?php echo $this->Html->link($multilang->text('IrA').' '.$expedient['User']['fullname'], array('controller' => 'users', 'action' => 'lawyers_customer_view', $expedient['User']['id']), array('class'=>'actions')); ?>
</div>
<? } ?>

<? if(($isCustomer || $isLawyer || $isAdmin) && count($expedients) > 0 && !$noCustomer) { ?>
	<div style="clear:right; float: right; width: 220px; margin: 10px 10px 10px 10px; ">
		<fieldset>
			<legend>
				<? $multilang->__("OtrosExpedientes")?>
				<? if($isLawyer || $isAdmin) { ?>
					<? $multilang->__("delMismoTitular")?>					
				<? } ?>
			</legend>
			<ul>
			<? foreach($expedients as $otherExpedient): ?>
				<li>
				<? echo $html->link($otherExpedient['Expedient']['reference'], array('controller'=>'expedients', 'action'=>'view', $otherExpedient['Expedient']['id']), array('title'=>$otherExpedient['Expedient']['description'])); ?>
				</li>
			<? endforeach; ?>
			</ul>
		</fieldset>
	</div>
<? } ?>

<div>
<h3><? $multilang->__("Expediente")?></h3>
<? if($isAdmin || $isLawyer){ ?>
	<b><? $multilang->__("Titular")?>:</b>
	
	<div id='editorOwner' class='inplaceEditor'>
		<? 
			if ($expedient['User']['fullname'] != null) {
				echo $expedient['User']['fullname'];
			} else {
				$multilang->__('ClienteNoAsignado'); 
			}
		?>
	</div>
	<? $userlist = array();
		foreach ($otherUsers as $key => $value) {
			$userlist[] = array($key, $value);
		}
	?>
	<?		echo $ajax->editor(
				'editorOwner', 
				array('controller'=>'expedients', 'action'=>'ajaxOwnerChange', $expedient['Expedient']['id']),
				array('okControl'=>'link', 'okText' => $multilang->text('Guardar'), 'cancelControl'=>'link', 'cancelText' => $multilang->text('Cancelar'), 'submitOnBlur'=>'true', 'savingText'=>$multilang->text('Guardando'), 'clickToEditText'=>$multilang->text('PulseParaEditar'),
					'collection' => $userlist					
				)
			); 		
	?>
	
	<br />
<? } ?>
<b><?$multilang->__('Referencia')?>:</b>
	<?php 
		if(!$isAdmin && !$isLawyer){
			echo $expedient['Expedient']['reference']; 
		} else if ($isAdmin || $isLawyer){
	?>
			<div id='editorReference' class='inplaceEditor'><?echo $expedient['Expedient']['reference'];?></div>
	<?		echo $ajax->editor(
				'editorReference', 
				array('controller'=>'expedients', 'action'=>'ajaxEdit', $expedient['Expedient']['id'], 'reference'),
				array('okControl'=>'false', 'cancelControl'=>'false', 'submitOnBlur'=>'true', 'savingText'=>$multilang->text('Guardando'), 'clickToEditText'=>$multilang->text('PulseParaEditar'))
			); 
		}
	?>

<br />
    <? if(($isLawyer || $isAdmin) && !$noCustomer) { ?>
        <div class="expedientPassContainer" data-textChange="<?php $multilang->__("Cambiar")?>" data-textCreate="<?php $multilang->__("Crear")?>">
            <div class="link">
                <fieldset>
				<legend><?$multilang->__("DireccionAcceso")?></legend>
                <?php if(sizeof($expedient['ExpedientPass']) > 0)
                {?>
                    <input class="expedientPass" type="text" readonly="readonly"
                        value="<?php echo "https://$_SERVER[HTTP_HOST]".$this->Html->url(array('controller'=>'users', 'action'=>'loginPass',$expedient['ExpedientPass'][0]['pass']))?>"
                        data-baseUrl="<?php echo "https://$_SERVER[HTTP_HOST]".$this->Html->url(array('controller'=>'users', 'action'=>'loginPass'))?>" />
                    <?php
                    echo $this->Html->link($multilang->text("Cambiar"), array('controller' => 'expedient_passes', 'action' => 'post_bind_pass_to_expedient'), array('class'=>'actions expedientPassLink', 'id'=>$expedient['Expedient']['id'], 'reference'=>$expedient['Expedient']['reference']));
                }
                else
                {?>
                    <input class="expedientPass" type="text" readonly="readonly" value=""
                           data-baseUrl="<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'loginPass'))?>"/>
                    <?php
                    echo $this->Html->link($multilang->text("Crear"), array('controller' => 'expedient_passes', 'action' => 'post_bind_pass_to_expedient'), array('class'=>'actions expedientPassLink', 'id'=>$expedient['Expedient']['id'], 'reference'=>$expedient['Expedient']['reference']));
                }
                ?>
				</fieldset>
            </div>
            <div class="qr" style="display: none">
            </div>
        </div>

    <div class="expedientAddressContainer" data-textChange="<?php $multilang->__("Cambiar")?>" data-textCreate="<?php $multilang->__("Crear")?>">
        <div class="link">
			<fieldset>
            <legend><?$multilang->__("Email")?>:</legend>
            <?php if(sizeof($expedient['ExpedientAddress']) > 0)
            {?>
                <span class="expedientAddress"><?php echo $expedient['ExpedientAddress'][0]['address'] ?></span>

                <?php
                echo $this->Html->link($multilang->text("Cambiar"), array('controller' => 'mails', 'action' => 'post_bind_email_to_expedient'), array('class'=>'actions expedientAddressLink', 'id'=>$expedient['Expedient']['id'], 'reference'=>$expedient['Expedient']['reference']));
            }
            else
            {?>
                <span class="expedientAddress"></span>
            <?php
                echo $this->Html->link($multilang->text("Crear"), array('controller' => 'mails', 'action' => 'post_bind_email_to_expedient'), array('class'=>'actions expedientAddressLink', 'id'=>$expedient['Expedient']['id'], 'reference'=>$expedient['Expedient']['reference']));
            }
            ?>
			</fieldset>
        </div>
    </div>
<? } ?>

<b><?$multilang->__("Descripcion")?>:</b><br />
	<?php 
		if(!$isAdmin && !$isLawyer){
			echo nl2br($expedient['Expedient']['description']); 
		} else if ($isAdmin || $isLawyer){
	?>
			<div id='editorDescription' class='inplaceEditor'><?echo nl2br($expedient['Expedient']['description']);?></div>
	<?		echo $ajax->editor(
				'editorDescription', 
				array('controller'=>'expedients', 'action'=>'ajaxEdit', $expedient['Expedient']['id'], 'description'),
				array('okControl'=>'false', 'cancelControl'=>'false', 'submitOnBlur'=>'true', 'savingText'=>$multilang->text('Guardando'), 'clickToEditText'=>$multilang->text('PulseParaEditar'), 'rows'=>5)
			); 
		}
	?>
<br /><br />

<h3><?$multilang->__("Actuaciones")?></h3>
<?php
	foreach($expedient['Action'] as $action):
?>
<a name="<?php echo $action['id']; ?>"></a>
<fieldset>
	<legend><?php echo $action['concept']; ?></legend>
	
	<? if ($isAdmin || $isLawyer): ?>
	<div style="float:right; text-align:right;">
		<div id='action<? echo $action['id']; ?>RelevanceControl'>
		<?
			echo $this->requestAction(array('controller' => 'actions', 'action' => 'ajax_viewRelevance'), array('pass' => array($action['id'])));
		?>
		</div>

		<? echo $html->link($multilang->text('Editar'), array('controller' => 'actions', 'action'=>'edit', $action['id'])); ?>
		<br />
		<? echo $html->link($multilang->text('Eliminar'), array('controller' => 'actions', 'action'=>'delete', $action['id']), array(), $multilang->text("DeleteWarning")); ?>
	</div>
	<? endif; ?>
	
	<b><?$multilang->__("Fecha")?>:</b> <? echo date("d/m/Y", strtotime($action['date'])); ?>
	&nbsp;
	<b><?$multilang->__("Estado")?>:</b> <span title="<? echo $action['Status']['description']?>"><? echo $action['Status']['name']; ?></span>
	<? if ($isAdmin || $isLawyer): 
		$notifyId = "notify".$action['id'];
		$notifyUrl = $html->url(array('controller'=>'actions', 'action'=>'ajaxEditNotify', $action['id']));
		$notifyIconId = "notifyIcon".$action['id'];
		?>
		<b><?$multilang->__("Notificar")?>:</b> 
		<input type="text" id="<? echo $notifyId;?>" value="<? echo $action['notify_date']; ?>" class="inlineEditor" data-notifyUrl="<? echo $notifyUrl;?>" data-iconId="<? echo $notifyIconId; ?>"/>
		<div id="<? echo $notifyIconId; ?>" class="notifyIcon">&nbsp;</div>
		<?  $onClose = 'function(cal){jQuery("#'.$notifyId.'").change(); cal.hide();}';
			echo $this->Calendar->set($notifyId, true, "%Y-%m-%d", $onClose); ?>		
	<? endif; ?>
	<br />
	<b><?$multilang->__("Comentarios")?>:</b><br /><? echo $action['comments']; ?>


	<?
		if(count($action['Document']) > 0):
	?>	
	<table cellpadding="0" cellspacing="0">
		<tr>			
				<th><?$multilang->__("Documento")?></th>			
				<th><?$multilang->__("Descripcion")?></th>		
		</tr>
		<?php
		$i = 0;
		foreach ($action['Document'] as $document):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>		
			<td><?php echo $this->Html->link($document['filename'], array('controller' => 'documents', 'action'=>'download', $document['id'])); ?>&nbsp;</td>			
			<td><?php echo $document['description']; ?>&nbsp;</td>	
		</tr>
		<?php endforeach; ?>
	</table>
	<?
		endif;
	?>
</fieldset>
<?
	endforeach;
?>
<? 
	if ($isAdmin || $isLawyer) {
		echo $html->link($multilang->text('AnadirActuacion'), array('controller'=>'actions', 'action'=>'add', $expedient['Expedient']['id']), array('class'=>'actions'));
	}
?>
</div>
