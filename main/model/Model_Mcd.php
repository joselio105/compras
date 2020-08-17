<?php
/**
 * @version 02/05/2019 13:18:56
 * @author jose_helio@gmail.com
 *
 */

final class Model_Mcd extends Model_Class{
    
    public function __construct($join=FALSE){
        parent::__construct();
        if($join){
            $this->setJoin('produto', 'tbl.produto=produto.id', array('nome'=>'produto_nome', 'tipo'));
            $this->setJoin('produto_tipo', 'produto.tipo=produto_tipo.id', array('nome'=>'tipo_nome'));
            $this->setJoin('embalagem', 'tbl.embalagem=embalagem.id', array('capacidade', 'tipo'=>'emb_tipo', 'unidade'));
            $this->setJoin('unidade', 'embalagem.unidade=unidade.id', array('sigla'));
            $this->setJoin('embalagem_tipo', 'embalagem.tipo=embalagem_tipo.id', array('nome'=>'emb_tipo_nome'));
            $this->concat(array('produto.nome', 'embalagem_tipo.nome', 'embalagem.capacidade', 'unidade.sigla'), 'nome', ' ');
        }
    }
    
    /**
     * {@inheritDoc}
     * @see Model_Class::setTableName()
     */
    protected function setTableName(){
        $this->_table = 'mercadoria';
    }

    /**
     * {@inheritDoc}
     * @see Model_Class::setFields()
     */
    protected function setFields(){
        $this->_fields = array(
            new FieldTable('produto'),
            new FieldTable('embalagem')
        );
    }
}