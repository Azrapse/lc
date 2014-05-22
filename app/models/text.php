<?php
	class Text extends AppModel 
	{    
		var $name = 'Text';
		var $hasMany = array(
		    'Translation'
		);		
		
		var $displayField = 'identifier';		
	}
?>
