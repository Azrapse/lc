<?php
	class Document extends AppModel 
	{    
		var $name = 'Document';
		var $displayField = 'reference';
		var	$belongsTo = 'Action';
	}
?>