<? $userKind = ($roleCodename == 'CUST') ? 'clientes' : 'despachos'; 
	if ($roleCodename == '') {
		$userKind = 'todos los usuarios del sistema';
	}
?>
<h3><?php __('Listado de '.$userKind);?></h3>
<?php echo $this->Html->link(__('Despachos', true), array('action' => 'index', 'LAWYER')); ?>&nbsp;
<?php echo $this->Html->link(__('Clientes', true), array('action' => 'index', 'CUST')); ?>&nbsp;
<?php echo $this->Html->link(__('Todos', true), array('action' => 'index')); ?>&nbsp;
<br />
<br />
<br />
<?php echo $this->Html->link(__('Crear despacho', true), array('action' => 'add_lawyer'), array('class'=>'actions')); ?>
<?php echo $this->Html->link(__('Crear cliente', true), array('action' => 'add_customer'), array('class'=>'actions')); ?>
<table cellpadding="0" cellspacing="0">
<tr>			
	<th>
		<?php echo $this->Paginator->sort('Nombre', 'User.fullname');?>
	</th>
<? if ($roleCodename == 'CUST'): ?>
	<th>
		<?php echo $this->Paginator->sort('Despacho', 'Lawyer.fullname');?>
	</th>
<? endif; ?>		
	<th>
		<?php echo $this->Paginator->sort('Login', 'User.username');?>
	</th>
	<th>
		<?php echo $this->Paginator->sort('Correo', 'User.email');?>
	</th>
	<th>
		Acciones
	</th>
</tr>
<?php
$i = 0;
foreach ($data as $user):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = 'altrow';
	}
?>
<tr class="<?php echo $class;?>">		
	<td>
		<?php echo $this->Html->link($user['User']['fullname'], array('action' => 'edit', $user['User']['id'])); ?>
		&nbsp;
	</td>
<? if ($roleCodename == 'CUST'): ?>
	<td>
		<?php echo $this->Html->link($user['Lawyer']['fullname'], array('action' => 'edit', $user['Lawyer']['id'])); ?>
		&nbsp;
	</td>
<? endif; ?>	
	<td>
		<?php echo $user['User']['username']; ?>
		&nbsp;
	</td>
	<td>
		<?php echo $user['User']['email']; ?>
		&nbsp;
	</td>	
	<td>
	<? if ($roleCodename == 'LAWYER'): ?>
		<?php echo $this->Html->link("Editar Cartera", array('controller'=>'user_items', 'action' => 'admin_wallet', $user['User']['id'])); ?>
	<? endif; ?>
	</td>
</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Página %page% de %pages%, '.$userKind.' %start% a %end% de %count%.', true)
));
?>	</p>

<div class="paging">
	<?php echo $this->Paginator->prev('<< ' . __('Anterior', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
|
	<?php echo $this->Paginator->next(__('Siguiente', true) . ' >>', array(), null, array('class' => 'disabled'));?>
</div>
