<div class="expedients form">
<?php echo $this->Form->create('Expedient');?>
	<fieldset>
 		<legend><?php __('Edit Expedient'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('reference');
		echo $this->Form->input('password');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Expedient.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Expedient.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Expedients', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Actions', true), array('controller' => 'actions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Action', true), array('controller' => 'actions', 'action' => 'add')); ?> </li>
	</ul>
</div>