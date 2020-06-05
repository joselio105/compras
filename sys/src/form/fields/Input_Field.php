<?php
include_once PATH_SYSTEM_SRC.'form/Field_Class.php';

class Input_Field extends Field_Class{
    
    private $placeholder;
    private $type;
    private $maxlenght;
    private $min;
    private $max;
    private $step;
    private $pattern;
    
    /**
     * Gera um campo input de formulário
     * @param string $fieldId
     * @param string $label
     */
    public function __construct($fieldId, $label=NULL){
        parent::__construct($fieldId, $label);
    }
    
    /**
     * {@inheritDoc}
     * @see Field_Class::setFieldBody()
     */
    protected function setFieldBody(){
        $this->fieldBody = "\n\t\t<input";
        $this->fieldBody.= " type=\"{$this->getType()}\"";
        $this->fieldBody.= (!is_null($this->getValue()) ? " value=\"{$this->getValue()}\"" : NULL);
        $this->fieldBody.= $this->getFieldId();
        $this->fieldBody.= $this->getAutofocus();
        $this->fieldBody.= $this->getRequired();
        $this->fieldBody.= $this->getFieldTitle();
        $this->fieldBody.= $this->getMaxlenght();
        $this->fieldBody.= $this->getMax();
        $this->fieldBody.= $this->getMin();
        $this->fieldBody.= $this->getStep();
        $this->fieldBody.= $this->getPattern();
        $this->fieldBody.= " placeholder=\"{$this->getPlaceholder()}\"";
        $this->fieldBody.= "/>";
    }
    
    /**
     * Retorna o tipo de input
     * @return string
     */
    public function getType(){
        return (isset($this->type) ? $this->type : 'text');
    }
    
    /**
     * Retorna o placeholder do input
     * @return string
     */
    protected function getPlaceholder(){
        return (isset($this->placeholder) ? $this->placeholder : $this->getLabelContent());
    }
    
    /**
     * Retorna o número máximo de caracteres do input
     * @return string|NULL
     */
    protected function getMaxlenght(){
        return (isset($this->maxlenght) ? " maxlenght=\"{$this->maxlenght}\"" : NULL);
    }
    
    /**
     * Retorna o valor numérico ou data máximos do input
     * @return string|NULL
     */
    protected function getMax(){
        return (isset($this->max) ? " max=\"{$this->max}\"" : NULL);
    }
    
    /**
     * Retorna o valor numérico ou data mínimos do input
     * @return string|NULL
     */
    protected function getMin(){
        return (isset($this->min) ? " min=\"{$this->min}\"" : NULL);
    }
    
    /**
     * Retorna a variação entre cada valor numérico ou de data
     * @return string|NULL
     */
    protected function getStep(){
        return (isset($this->step) ? " step=\"{$this->step}\"" : NULL);
    }
    
    /**
     * Retorna o padrão aceito pelo input
     * @return string|NULL
     */
    protected function getPattern(){
        return (isset($this->pattern) ? " pattern=\"{$this->pattern}\"" : NULL);
    }
    
    /**
     * Determina o tipo de input
     * @param string $type
     */
    public function setType($type){
        $types = array('hidden', 'email', 'date', 'password', 'checkbox', 'file', 'number', 'radio', 'range', 'search', 'submit', 'tel', 'text', 'time', 'url', 'week');
        
        $this->type = (in_array($type, $types) ? $type : 'text');
    }
    
    /**
     * Determina o placeholder do input
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder){
        $this->placeholder = $placeholder;
    }
    
    /**
     * Determina o número máximo de caracteres do input
     * @param string $maxlenght
     */
    public function setMaxlenght($maxlenght){
        $this->maxlenght = $maxlenght;
    }
    
    /**
     * Determina o valor numérico ou data máximos do input
     * @param string $min
     */
    public function setMin($min){
        $this->min = $min;
    }
    
    /**
     * Determina o valor numérico ou data mínimos do input
     * @param string $max
     */
    public function setMax($max){
        $this->max = $max;
    }
    
    /**
     * Determina a variação entre cada valor numérico ou de data
     * @param string $step
     */
    public function setStep($step){
        $this->step = $step;
    }
    
    /**
     * Determina o padrão aceito pelo input
     * @param string $pattern
     */
    public function setPattern($pattern){
        $this->pattern = $pattern;
    }
}