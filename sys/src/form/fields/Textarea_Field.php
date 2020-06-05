<?php
include_once PATH_SYSTEM_SRC.'form/fields/Input_Field.php';

final class Textarea_Field extends Input_Field{
    
    /**
     * Gera um campo textarea de formulário
     * @param string $fieldId
     * @param string $label
     */
    public function __construct($fieldId, $label=NULL){
        parent::__construct($fieldId, $label);
    }
    
    /**
     * {@inheritDoc}
     * @see Input_Field::setFieldBody()
     */
    protected function setFieldBody(){
        $this->fieldBody = "\n\t\t<textarea ";
        $this->fieldBody.= $this->getFieldId();
        $this->fieldBody.= $this->getFieldClass();
        $this->fieldBody.= $this->getFieldTitle();
        $this->fieldBody.= $this->getMaxlenght();
        $this->fieldBody.= $this->getRequired();
        $this->fieldBody.= $this->getAutofocus();
        $this->fieldBody.= " placeholder=\"{$this->getPlaceholder()}\"";
        $this->fieldBody.= ">\n";
        $this->fieldBody.= $this->getValue();
        $this->fieldBody.= "\n\t\t</textarea>";        
    }
    
}