<?php
include_once PATH_SYSTEM_SRC.'model/crud/Sql_Class.php';

class Update_Class extends Sql_Class{
    
    /**
     * {@inheritDoc}
     * @see Sql_Class::setSql()
     */
    public function setSql(){
        $this->sql = "UPDATE {$this->getTable()} \nSET {$this->getValues()} \n{$this->getWhere()}";
        //var_dump($this->sql);die;
    }
    
    /**
     * {@inheritdoc}
     * @return string
     */
    private function getValues(){$res = array();
        $res = array();
        //var_dump($this->_fields);die;
        foreach ($this->_fields as $field):
            if($field->isSetted())
                $res[] = "{$field->getName()}='{$field->getValue()}'";
        endforeach;
        return implode(",\n\t", $res);
    }
    
}