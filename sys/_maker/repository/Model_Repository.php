<?php
/**
 * @version DATA_CRIACAO
 * @author jose_helio@gmail.com
 *
 */

final class CLASS_NAME extends Model_Class{
    
    /**
     * {@inheritDoc}
     * @see Model_Class::setTableName()
     */
    protected function setTableName(){
        $this->_table = 'TABLE_NAME';
    }

    /**
     * {@inheritDoc}
     * @see Model_Class::setFields()
     */
    protected function setFields(){
        $this->_fields = array();
    }
}