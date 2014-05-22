<?php
	class ItemType extends AppModel 
	{    
		var $name = 'ItemType';		
		var $hasMany = array(
			'UserItem',
			'ItemOffer'
		);
		var $displayField = 'name';		
	}
?>
