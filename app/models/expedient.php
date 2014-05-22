<?php
	class Expedient extends AppModel 
	{    
		var $name = 'Expedient';
		var $hasMany = 'Action';
		var $belongsTo = 'User';
		var $displayField = 'reference';
		
		 function manageMysqlResult() {
                $db =& ConnectionManager::getDataSource( $this->useDbConfig );
                $lastResult = mysqli_next_result( $db->connection );
        }
	}
?>