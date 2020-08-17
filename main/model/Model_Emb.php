<?php
/**
 * @version 02/05/2019 13:17:21
 * @author jose_helio@gmail.com
 *
 */

final class Model_Emb extends Model_Class{
    
    public function __construct($join=FALSE){
        parent::__construct();
        if($join){
            $this->setJoin('unidade', 'tbl.unidade=unidade.id', array('nome'=>'unidade_nome', 'sigla'));
            $this->setJoin('embalagem_tipo', 'tbl.tipo=embalagem_tipo.id', array('nome'=>'tipo_nome'));
            $this->concat(array('embalagem_tipo.nome', 'capacidade', 'sigla'), 'nome', ' ');
        }
    }
    
    /**
     * {@inheritDoc}
     * @see Model_Class::setTableName()
     */
    protected function setTableName(){
        $this->_table = 'embalagem';
    }

    /**
     * {@inheritDoc}
     * @see Model_Class::setFields()
     */
    protected function setFields(){
        $this->_fields = array(
            new FieldTable('capacidade'),
            new FieldTable('unidade'),
            new FieldTable('tipo')
        );
    }
}