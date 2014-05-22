<?php
	class Action extends AppModel 
	{    
		var $name = 'Action';
		var $displayField = 'concept';
		var $hasMany = 'Document';
		var $belongsTo = array(
			'Status' => array (
				'className' => 'ActionStatus',
				'foreignKey' => 'status_id'
			), 
			'Expedient'
		);		
	}
?>