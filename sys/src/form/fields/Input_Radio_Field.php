<?php
include_once PATH_SYSTEM_SRC.'form/Field_Class.php';

final class Input_Radio_Field extends Field_Class{
    
    private $values;
    private $id;
    private $labelGroup;
    private $isRadio;
    
    /**
     * Gera um campo input de formulário do tipo radio
     * @param string $fieldId
     * @param array $values
     * @param string $label
     */
    public function __construct($fieldId, array $values, $label){
        $this->values = $values;
        $this->id = $fieldId;
        $this->labelGroup = $label;
        $this->setLineClass('checkboxes');
        $this->setFieldName($fieldId);
        $this->required = TRUE;
        $this->autofocus = FALSE;
        $this->isRadio = TRUE;
    }
    
    /**
     * {@inheritDoc}
     * @see Field_Class::setFieldBody()
     */
    protected function setFieldBody(){
        $this->fieldBody = "\n\t\t<h3>{$this->labelGroup}</h3>";
        $this->fieldBody.= "\n\t\t<ul>";
        $k=0;
        foreach ($this->values as $value=>$label):
            $id = $this->id.$value;
            $this->fieldBody.= "\n\t\t\t<li>";
            $this->fieldBody.= "\n\t\t\t\t<input type=\"{$this->getType()}\"";
            $this->fieldBody.= " value=\"{$value}\"";
            $this->fieldBody.= " id=\"{$id}\"";
            $this->fieldBody.= " name=\"{$this->getFieldName()}\"";
            $this->fieldBody.= ((!is_null($this->getValue()) AND $this->getValue()==$value) ?" checked" :NULL);
            $this->fieldBody.= ">";
            $this->fieldBody.= "\n\t\t\t\t<label for=\"{$id}\">{$label}</label>{$label}";
            $this->fieldBody.= "</li>";
            
            $k++;
        endforeach;
        $this->fieldBody.= "\n\t\t</ul>";
    }
    
    /**
     * Define o campo como tipo checkbox
     * @param boolean $is
     */
    public function setIsCheckbox($is=TRUE){
        $this->isRadio = !$is;
    }
    
    /**
     * Recupera o tipo do campo
     * @return string
     */
    public function getType(){
        return ($this->isRadio ? 'radio' : 'checkbox');
    }
    
    /**
     * Recupera o nome do campo de acordo com o tipo da input
     * {@inheritDoc}
     * @see Field_Class::getFieldName()
     */
    public function getFieldName(){
        if($this->isRadio)
            return parent::getFieldName();
        else 
            return parent::getFieldName().'[]';
    }
    
}