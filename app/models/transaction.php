<?php
	class Transaction extends AppModel 
	{    
		var $name = 'Transaction';
		var $belongsTo = array( 
			'UserItem' => array(
				'className' => 'UserItem',
				'foreignKey' => 'user_item_id'
			)
		);
		var $displayField = 'description';			
	}
?>
