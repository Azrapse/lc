<?php
	class UserItem extends AppModel 
	{    
		var $name = 'UserItem';
		var $belongsTo = array( 
			'ItemType', 
			'User'
		);
		var $hasMany = array(
			'Transaction' => array(
				'className' => 'Transaction',
				'foreignKey' => 'user_item_id',
				'order' => 'Transaction.modified DESC',
				'limit' => 1
			)
		);		
		
	}
?>
