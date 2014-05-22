<?php
	class Language extends AppModel 
	{    
		var $name = 'Language';
		var $hasMany = array(
		    'Translation'
		);
		var $displayField = 'name';		
	}
?>
