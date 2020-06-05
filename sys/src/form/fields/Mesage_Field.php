<?php
include_once PATH_SYSTEM_SRC.'form/Field_Class.php';

final class Mesage_Field extends Field_Class{
    
    private $text;
    
    /**
     * Gera um texto a ser exibido no formu~lário
     * @param string $fieldId
     * @param string $text
     */
    public function __construct($fieldId, $text){
        parent::__construct($fieldId);
        $this->text = $text;
    }
    
    /**
     * {@inheritDoc}
     * @see Field_Class::setFieldBody()
     */
    protected function setFieldBody(){
        $this->fieldBody = "\n\t\t<p>{$this->text}</p>";
    }
}