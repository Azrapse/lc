<?php
	class Translation extends AppModel 
	{    
		var $name = 'Translation';
                var $belongsTo = array(
		    'Text', 'Language'
		);
		var $displayField = 'text';		
	}
?>
