<?php
include_once PATH_SYSTEM_SRC.'form/fields/Input_Field.php';

final class Input_Number_Field extends Input_Field{
    
    /**
     * Gera um campo input de formulário do tipo number
     * @param string $fieldId
     * @param string $label
     */
    public function __construct($fieldId, $label=NULL){
        parent::__construct($fieldId, $label);
        $this->setType('number');
    }
    
}