<?php
include_once PATH_SYSTEM_SRC.'form/Field_Class.php';

final class Select_Field extends Field_Class{
    
    private $values;
    private $id;
    private $labelGroup;
    
    /**
     * Gera um campo select de formulário
     * @param string $fieldId
     * @param array $values
     * @param string $label
     */
    public function __construct($fieldId, array $values, $label){
        parent::__construct($fieldId, $label);
        $this->values = $values;
    }
    
    /**
     * {@inheritDoc}
     * @see Field_Class::setFieldBody()
     */
    protected function setFieldBody(){
        $this->fieldBody = "\n\t\t<select ";
        $this->fieldBody.= $this->getFieldId();
        $this->fieldBody.= $this->getFieldClass();
        $this->fieldBody.= $this->getFieldTitle();
        $this->fieldBody.= $this->getAutofocus();
        $this->fieldBody.= $this->getRequired();
        $this->fieldBody.= ">";
        
        $k=0;
        foreach ($this->values as $key=>$value):
            $this->fieldBody.= "\n\t\t\t<option";
            $this->fieldBody.= " value=\"{$key}\"";
            $this->fieldBody.= ((!is_null($this->getValue()) AND $this->getValue()==$key) ? " selected" : NULL);
            $this->fieldBody.= ">";
            $this->fieldBody.= $value;
            $this->fieldBody.= "</option>";
            
            $k++;
        endforeach;
        $this->fieldBody.= "\n\t\t</select>";
    }
    
}