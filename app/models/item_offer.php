<?php
	class ItemOffer extends AppModel 
	{    
		var $name = 'ItemOffer';
		var $belongsTo = array( 
			'ItemType'
		);				
	}
?>
