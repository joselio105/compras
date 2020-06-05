<?php

class FieldTable{
    
    private $name;
    private $value;
    private $type;
    private $required;
    private $unique;
    private $setted;
    
    /**
     * Campo de tabela de banco de dados
     * @param string $name
     * @param boolean $required
     * @param boolean $unique
     * @param boolean $typeNumeric
     */
    public function __construct($name, $required=TRUE, $typeNumeric=FALSE, $unique=FALSE){
        $this->name = $name;
        $this->required = $required;
        $this->unique = $unique;
        $this->type = ($typeNumeric ? 'numeric' : 'string');
        $this->setted = FALSE;
    }
    
    public function __toString(){
        return $this->name;
    }
    
    /**
     * Carrega um valor para o campo, caso esse atenda o tipo determinado
     * @param string $value
     * @return boolean
     */
    public function setValue($value){
        /*if($this->type=='string'){
            if(is_numeric($value))
                return FALSE;
        }else{
            if(!is_numeric($value))
                return FALSE;
        }*/
            
        $this->value = $value;        
        $this->setted = TRUE;
        return TRUE;
    }
    
    /**
     * Verifica se o campo foi setado
     * @return boolean
     */
    public function isSetted(){
        return $this->setted;
    }
    
    /**
     * Retorna o nome do campo
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * Retorna o valor do campo
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * Retorna o tipo de dado do campo
     * @return string
     */
    public function getType(){
        return $this->type;
    }

    /**
     * Informa se o campo é de preenchimento obrigatório
     * @return boolean
     */
    public function getRequired(){
        return $this->required;
    }

    /**
     * Informa se o campo é de valor único
     * @return boolean
     */
    public function getUnique(){
        return $this->unique;
    }

}