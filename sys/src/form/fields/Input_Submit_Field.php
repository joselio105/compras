<?php
include_once PATH_SYSTEM_SRC.'form/fields/Input_Field.php';

final class Input_Submit_Field extends Input_Field{
    
    /**
     * Gera um campo input de formulário do tipo submit
     */
    public function __construct(){
        parent::__construct('submit');
        $this->setType('submit');
        $this->setValue('Enviar');
    }
    
}