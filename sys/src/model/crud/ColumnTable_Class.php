<?php

final class ColumnTable_Class{
    
    private $name;
    private $type;
    private $size;
    private $autoIncrement;
    private $notNull;
    private $preDefined;
    
    /**
     * Define as características de uma coluna da tabela do banco de dados
     * @param string $name
     * @param string $type
     * @param integer|NULL $size
     * @param boolean $notNull
     * @param boolean $autoIncrement
     * @param mixed $preDefined
     */
    public function __construct($name, $type, $size=NULL, $notNull=TRUE, $autoIncrement=FALSE, $preDefined=NULL){
        $this->name = preg_replace('/\W/', '_', $name);
        $this->type = $type;
        $this->size = $size;
        $this->notNull = $notNull;
        $this->autoIncrement = $autoIncrement;
        $this->preDefined = $preDefined;
    }
    
    /**
     * Retorna a string com as características da coluna
     * @return string
     */
    public function __toString(){
        return "{$this->name} {$this->type}{$this->getSize()}{$this->getNotNull()}{$this->getAutoIncrement()}{$this->getPreDefined()}";
    }
    
    /**
     * Retorna o nome da coluna
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @return string|NULL
     */
    private function getSize(){
        return (!is_null($this->size) ? "({$this->size})" : NULL);
    }

    /**
     * @return string|NULL
     */
    private function getAutoIncrement(){
        if($this->type!='int')
            return NULL;
        return ($this->autoIncrement ? " AUTO_INCREMENT" : NULL);
    }

    /**
     * @return string|NULL
     */
    private function getNotNull(){
        return ($this->notNull ? " NOT NULL" : NULL);
    }

    /**
     * @return string|NULL
     */
    private function getPreDefined(){
        return (!is_null($this->preDefined) ? ($this->preDefined=='CURRENT_TIMESTAMP' ? ' DEFAULT '.$this->preDefined : " DEFAULT '{$this->preDefined}'") : NULL);
    }
    
}

