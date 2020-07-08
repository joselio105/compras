<?php
/**
 * @version 02/05/2019 13:24:45
 * @author jose_helio@gmail.com
 *
 */

final class Model_Hst extends Model_Class{
    
    public function __construct($join=FALSE){
        parent::__construct();
        if($join){
            $this->setJoin('lcp_mcd', 'tbl.mercadoria=lcp_mcd.id', array('produto', 'embalagem'));
            $this->setJoin('lcp_pdt', 'lcp_mcd.produto=lcp_pdt.id', array('nome'=>'produto_nome', 'tipo'));
            $this->setJoin('lcp_pdt_tp', 'lcp_pdt.tipo=lcp_pdt_tp.id', array('nome'=>'tipo_nome'));
            $this->setJoin('lcp_emb', 'lcp_mcd.embalagem=lcp_emb.id', array('capacidade', 'tipo'=>'emb_tipo', 'unidade'));
            $this->setJoin('lcp_und', 'lcp_emb.unidade=lcp_und.id', array('sigla'));
            $this->setJoin('lcp_emb_tp', 'lcp_emb.tipo=lcp_emb_tp.id', array('nome'=>'emb_tp_nome'));
            $this->setSubQuery('SELECT quantidade FROM lcp_hst WHERE data IS NOT NULL AND lcp_hst.mercadoria=tbl.mercadoria ORDER BY data DESC LIMIT 1', 'lastQtd');
            $this->setSubQuery('SELECT preco FROM lcp_hst WHERE data IS NOT NULL AND lcp_hst.mercadoria=tbl.mercadoria ORDER BY data DESC LIMIT 1', 'lastPreco');
            $this->setSubQuery('SELECT data FROM lcp_hst WHERE data IS NOT NULL AND lcp_hst.mercadoria=tbl.mercadoria ORDER BY data DESC LIMIT 1', 'lastDate');
            $this->concat(array('lcp_pdt.nome', 'lcp_emb_tp.nome', 'lcp_emb.capacidade', 'lcp_und.sigla'), 'nome', ' ');
        }
    }
    
    /**
     * {@inheritDoc}
     * @see Model_Class::setTableName()
     */
    protected function setTableName(){
        $this->_table = 'compra';
    }

    /**
     * {@inheritDoc}
     * @see Model_Class::setFields()
     */
    protected function setFields(){
        $this->_fields = array(
            new FieldTable('mercadoria'),
            new FieldTable('quantidade', FALSE),
            new FieldTable('preco', FALSE),
            new FieldTable('data', FALSE)
        );
    }
}