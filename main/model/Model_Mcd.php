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
            $this->setJoin('lcp_pdt', 'tbl.produto=lcp_pdt.id', array('nome'=>'produto_nome', 'tipo'));
            $this->setJoin('lcp_pdt_tp', 'lcp_pdt.tipo=lcp_pdt_tp.id', array('nome'=>'tipo_nome'));
            $this->setJoin('lcp_emb', 'tbl.embalagem=lcp_emb.id', array('capacidade', 'tipo'=>'emb_tipo', 'unidade'));
            $this->setJoin('lcp_und', 'lcp_emb.unidade=lcp_und.id', array('sigla'));
            $this->setJoin('lcp_emb_tp', 'lcp_emb.tipo=lcp_emb_tp.id', array('nome'=>'emb_tp_nome'));
            $this->concat(array('lcp_pdt.nome', 'lcp_emb_tp.nome', 'lcp_emb.capacidade', 'lcp_und.sigla'), 'nome', ' ');
        }
    }
    
    /**
     * {@inheritDoc}
     * @see Model_Class::setTableName()
     */
    protected function setTableName(){
        $this->_table = 'lcp_mcd';
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