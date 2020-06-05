<?php
include_once PATH_SYSTEM_SRC.'model/crud/Sql_Class.php';

class Delete_Class extends Sql_Class{
    
    /**
     * {@inheritDoc}
     * @see Sql_Class::setSql()
     */
    public function setSql(){
        $this->sql = "DELETE FROM {$this->getTable()} {$this->getWhere()}";
    }

}

