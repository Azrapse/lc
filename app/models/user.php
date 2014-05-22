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
					'message' => 'S�lo caracteres y d�gitos.'
				),
				'between' => array(                
					'rule' => array('between', 5, 50),                
					'message' => 'Entre 5 y 50 caracteres.'            
				),
				'notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vac�o.')
			),
			'email' => array(        
				'email'=>array(
					'rule' => array('email', true),        
					'message' => 'Debe ser una direcci�n de correo electr�nico v�lida.'    
				),
				'notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vac�o.')
			),
			'password' => array(
				'between' => array(
					'rule' => array('between', 5, 50),
					'message' => "Entre 5 y 50 caracteres"
				),
				'notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vac�o.')
			),
			// 'address' =>  array(            
				// 'notEmpty' => array(                
					// 'rule' => 'notEmpty',                
					// 'required' => true,                
					// 'message' => 'Este campo no puede dejarse vac�o.',
					// 'allowEmpty' => false
				// )
			// ),
			// 'town' => array('notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vac�o.')),
			// 'province' => array('notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vac�o.')),
			// 'postal_code' => array('notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vac�o.')),
			// 'college_number' => array('notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vac�o.')),
			// 'fullname' => array(
				// 'between' => array(
					// 'rule' => array('between', 5, 256),
					// 'message' => "Entre 5 y 256 caracteres"
				// ),
				// 'notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Este campo no puede dejarse vac�o.')
			// )
		);
	}
?>
