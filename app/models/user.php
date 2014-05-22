<?php
	class User extends AppModel 
	{    
		var $name = 'User';
		var $belongsTo = array(
			'Lawyer' => array (
				'className' => 'User',
				'foreignKey' => 'lawyer_id'
			), 
			'Role',
			'Language'
		);
		var $hasMany = array(
			'Client' => array(
				'className' => 'User',
				'foreignKey' => 'lawyer_id'
			),
			'Expedient',
			'UserItem'
		);
		var $displayField = 'fullname';
		var $validate = array(        
			'username' => array(            
				'alphaNumeric' => array(                
					'rule' => 'alphaNumeric',                
					'required' => true,                
					'message' => 'Sólo caracteres y dígitos.'
				),
				'between' => array(                
					'rule' => array('between', 5, 50),                
					'message' => 'Entre 5 y 50 caracteres.'            
				),
				'notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vacío.')
			),
			'email' => array(        
				'email'=>array(
					'rule' => array('email', true),        
					'message' => 'Debe ser una dirección de correo electrónico válida.'    
				),
				'notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vacío.')
			),
			'password' => array(
				'between' => array(
					'rule' => array('between', 5, 50),
					'message' => "Entre 5 y 50 caracteres"
				),
				'notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vacío.')
			),
			// 'address' =>  array(            
				// 'notEmpty' => array(                
					// 'rule' => 'notEmpty',                
					// 'required' => true,                
					// 'message' => 'Este campo no puede dejarse vacío.',
					// 'allowEmpty' => false
				// )
			// ),
			// 'town' => array('notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vacío.')),
			// 'province' => array('notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vacío.')),
			// 'postal_code' => array('notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vacío.')),
			// 'college_number' => array('notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vacío.')),
			// 'fullname' => array(
				// 'between' => array(
					// 'rule' => array('between', 5, 256),
					// 'message' => "Entre 5 y 256 caracteres"
				// ),
				// 'notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vacío.')
			// )
		);
	}
?>
