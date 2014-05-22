<?php
// Asegurarse de que el helper Multilang está disponible en todos los controladores que hagan uso de él.
// Por ejemplo añadiendo a AppController 	function beforeRender(){$this->helpers['Multilang']=array('texts' => $texts);}
// Uso en la vista:
//    contenido HTML para un texto "test_text" con contenido "Esto es un {token} mensaje de prueba"
// <? $multilang->__("test_text", array('token'=>'tokenreplacemnt')); ? >

/**
 * MultilangHelper
 * This Helper provides a few functions that can be used to assist with multilanguage.
 * 
 * @author David Esparza
 */

class MultilangHelper extends AppHelper {
	
	var $__textDict = null;
		
        function __construct($options) {
            $this->__textDict = $options['texts'];            
        }
        	
                
	function text($key, $replacements=null) {
            if(array_key_exists($key, $this->__textDict)) {
                $text = $this->__textDict[$key];
                if(isset($replacements)){
                    if(!is_array($replacements)){
                        $replacements = array($replacements);
                    }
                    foreach($replacements as $pattern => $value){
                        $text = str_replace('{'.$pattern.'}', $value, $text);
                    }
                }            
                return $text;
            } else {
                return '{*MISSINGTEXT:'.$key.'*}';
            }
	}
        
        function __($key, $replacements=null) {
            echo $this->text($key, $replacements);
	}                
}

?>