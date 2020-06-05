<?php
include_once PATH_SYSTEM_SRC.'model/crud/Sql_Class.php';

class Create_Class extends Sql_Class{
    
    private $fields;
    
    /**
     * {@inheritDoc}
     * @see Sql_Class::setSql()
     */
    public function setSql(){
        $this->sql = "INSERT INTO {$this->getTable()}({$this->getFields()}) VALUES({$this->getValues()})";
    }
    
    /**
     * {@inheritDoc}
     * @see Sql_Class::getFields()
     */
    protected function getFields(){
        $this->fields = array();
        
        foreach (parent::getFields() as $i=>$fld):
            if(!is_null($fld->getValue()))
                $this->fields[$i] = $fld->getValue();
        endforeach;
        
        return implode(', ', array_keys($this->fields));
    }
    
    /**
     * Retorna o array de valores
     * @return string
     */
    private function getValues(){
        return '"'.implode('", "', $this->fields).'"';
    }

    
}