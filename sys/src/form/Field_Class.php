<?php

abstract class Field_Class{
    
    private $lineId;
    private $lineClass;
    private $fieldId;
    private $fieldName;
    private $fieldClass;
    private $fieldTitle;
    private $label;
    protected  $fieldBody;
    private $value;
    private $autofocus;
    private $required;
    
    /**
     * Gera um campo de formulário
     * @param string $fieldId
     * @param string $label
     */
    public function __construct($fieldId, $label=NULL){
        $this->fieldId = $fieldId;
        $this->fieldName = $fieldId;
        $this->label = $label;
        $this->required = TRUE;
        $this->autofocus = FALSE;
    }
    
    /**
     * Exibe o código HTML do campo de formulário
     * @return string
     */
    public function __toString(){
        $this->setFieldBody();
        $res = '';
        $res.= "\n\t<div";
        $res.= $this->getLineId();
        $res.= $this->getLineClass();
        $res.= ">";
        $res.=$this->getLabel();
        $res.=$this->fieldBody;
        $res.= "\n\t</div>\n";
        
        return $res;
    }
    
    /**
     * Determina o código HTML do campo
     */
    protected abstract function setFieldBody();
    
    /**
     * Retorna o id do conteiner do campo
     * @return string|NULL
     */
    private function getLineId(){
        return (isset($this->lineId) ? " id=\"{$this->lineId}\"" : NULL);
    }
    
    /**
     * Retorna a classe do conteiner do campo
     * @return string|NULL
     */
    private function getLineClass(){
        return (isset($this->lineClass) ? " class=\"{$this->lineClass}\"" : NULL);
    }
    
    /**
     * Retorna o id e o name do campo
     * @return string|NULL
     */
    protected function getFieldId(){
        return (isset($this->fieldId) ? " id=\"{$this->fieldId}\" name=\"{$this->fieldId}\"" : NULL);
    }
    
    /**
     * Retorna a classe do campo
     * @return string|NULL
     */
    protected function getFieldClass(){
        return (isset($this->fieldClass) ? "class=\"{$this->fieldClass}\"" : NULL);
    }
    
    /**
     * Retorna o title do campo
     * @return string|NULL
     */
    protected function getFieldTitle(){
        return (isset($this->fieldTitle) ? "title=\"{$this->fieldTitle}\"" : NULL);
    }
    
    /**
     * Mostra se o campo é de preenchimento obrigatório
     * @return string|NULL
     */
    protected function getRequired(){
        return ($this->required ? ' required' : NULL);
    }
    
    /**
     * Mostra se o campo deve estar em foco quando a página for carregada
     * @return string|NULL
     */
    protected function getAutofocus(){
        return ($this->autofocus ? ' autofocus' : NULL);
    }
    
    /**
     * Retorna a tag completa com o label do campo
     * @return string|NULL
     */
    private function getLabel(){
        return (!is_null($this->label) ? "\n\t\t<label for=\"{$this->fieldId}\">{$this->label}</label>" : NULL);
    }
    
    /**
     * Retorna somente o conteúdo do label do campo
     * @return string
     */
    protected function getLabelContent(){
        return $this->label;
    }
    
    /**
     * Determina o id do conteiner
     * @param string $lineId
     */
    protected function setLineId($lineId){
        $this->lineId = $lineId;
    }
    
    /**
     * Determina o valor do campo
     * @param string $value
     */
    public function setValue($value){
        $this->value = $value;
    }
    
    /**
     * Determina a classe do conteiner do campo
     * @param string $lineClass
     */
    public function setLineClass($lineClass){
        $this->lineClass = $lineClass;
    }
    
    /**
     * Determina o id do campo
     * @param string $fieldId
     */
    public function setFieldId($fieldId){
        $this->fieldId = $fieldId;
    }
    
    /**
     * Determina a classe do campo
     * @param string $fieldClass
     */
    public function setFieldClass($fieldClass){
        $this->fieldClass = $fieldClass;
    }
    
    /**
     * Determina o título do campo
     * @param string $fieldTitle
     */
    public function setFieldTitle($fieldTitle){
        $this->fieldTitle = $fieldTitle;
    }
    
    /**
     * Determina o rótulo do campo
     * @param string $label
     */
    public function setLabel($label){
        $this->label = $label;
    }
    
    /**
     * Determina que o campo não tem preenchimento obrigatório
     */
    public function setNoRequired(){
        $this->required = FALSE;
    }
    
    /**
     * Determina se o campo deve estar em foco ao carregar a página
     */
    public function setAutofocus(){
        $this->autofocus = TRUE;
    }
    
    /**
     * Retorna o valor do campo
     * @return string
     */
    public function getValue(){
        return $this->value;
    }
    
    /**
     * Retorna o id do campo
     * @return string
     */
    public function getFieldIdContent(){
        return $this->fieldId;
    }
    
    /**
     * Retorna o name do campo
     * @return string
     */
    public function getFieldName(){
        return $this->fieldName;
    }

    /**
     * Determina o name do campo
     * @param string $fieldName
     */
    public function setFieldName($fieldName){
        $this->fieldName = $fieldName;
    }


}