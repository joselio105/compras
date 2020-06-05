<?php
/**
 * @version 02/05/2019 12:22:57
 * @author jose_helio@gmail.com
 *
 */

final class Model_Und extends Model_Class{
    
    /**
     * {@inheritDoc}
     * @see Model_Class::setTableName()
     */
    protected function setTableName(){
        $this->_table = 'lcp_und';
    }

    /**
     * {@inheritDoc}
     * @see Model_Class::setFields()
     */
    protected function setFields(){
        $this->_fields = array(
            new FieldTable('nome'),
            new FieldTable('sigla')
        );
    }
}