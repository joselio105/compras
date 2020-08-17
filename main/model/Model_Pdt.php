<?php
/**
 * @version 02/05/2019 12:26:54
 * @author jose_helio@gmail.com
 *
 */

final class Model_Pdt extends Model_Class{
    
    public function __construct($join=FALSE){
        parent::__construct();
        if($join)
            $this->setJoin('produto_tipo', 'tbl.tipo=produto_tipo.id', array('nome'=>'tipo_nome'));
    }
    
    /**
     * {@inheritDoc}
     * @see Model_Class::setTableName()
     */
    protected function setTableName(){
        $this->_table = 'produto';
    }

    /**
     * {@inheritDoc}
     * @see Model_Class::setFields()
     */
    protected function setFields(){
        $this->_fields = array(
            new FieldTable('nome'),
            new FieldTable('tipo')
        );
    }
}