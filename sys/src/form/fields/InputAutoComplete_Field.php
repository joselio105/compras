<?php
include_once PATH_SYSTEM_SRC.'form/Field_Class.php';

class InputAutoComplete_Field extends Field_Class{

    private $values;
    
    /**
     * Gera um campo autocomplete no formulário
     * @param string $fieldId
     * @param array $values
     * @param string|NULL $label
     */
    public function __construct($fieldId, array $values, $label=NULL){
        parent::__construct($fieldId, $label);
        $this->values = $values;
    }
    
    /**
     * {@inheritDoc}
     * @see Field_Class::setFieldBody()
     */
    protected function setFieldBody(){
        $this->fieldBody = "\n\t\t<input type=\"hidden\" class=\"autocomplete\"";
        $this->fieldBody.= (!is_null($this->getValue()) ? " value=\"{$this->values[$this->getValue()]}\"" : NULL);
        $this->fieldBody.= $this->getFieldId();
        $this->fieldBody.= " />";        
        $this->fieldBody.= "\n\t\t<input type=\"text\"";
        $this->fieldBody.= " id=\"auto_search\"";
        $this->fieldBody.= $this->getAutofocus();
        $this->fieldBody.= " />";
        $this->fieldBody.= "\n\t\t<select id=\"auto_value\" size=\"5\">";
        foreach ($this->values as $id=>$value):
            $this->fieldBody.= "\n\t\t\t<option";
            $this->fieldBody.= " value=\"{$id}\"";
            $this->fieldBody.= ((!is_null($this->getValue()) AND $this->getValue()==$id) ? " selected" : NULL);
            $this->fieldBody.= ">";
            $this->fieldBody.= $value;
            $this->fieldBody.= "</option>";
        endforeach;
        $this->fieldBody.= "</select>";
    }

}

