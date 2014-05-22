<?php
	class ActionStatus extends AppModel 
	{    
		var $name = 'ActionStatus';
		var $hasMany = array(
			'Action' => array(
				'foreignKey' => 'status_id', 
				'className' => 'Action'
			)
		);
	}
?>