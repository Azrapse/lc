<ul>
    <?php foreach($newActions as $action): ?>
    <li>
        <a class="expedientLink"
           href="<?php echo $this->Html->url(array('controller'=>'expedients', 'action'=>'view', $action['ImportedAction']['expedient_id'])); ?>#<?php echo $action['ImportedAction']['action_id']?>"
           title="<?php echo $action['Action']['concept'] ?>">
            <?php echo $action['Expedient']['reference'] ?>
        </a>
        <a class="deleteImportedActionLink" href="<?php echo $this->Html->url(array('controller'=>'imported_actions', 'action'=>'delete', $action['ImportedAction']['id'])); ?>.json">
            <img src="<?php echo $this->webroot; ?>img/tick.png" />
        </a>
    </li>
    <?php endforeach; ?>
</ul>