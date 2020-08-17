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
            $this->setJoin('mercadoria', 'tbl.mercadoria=mercadoria.id', array('produto', 'embalagem'));
            $this->setJoin('produto', 'mercadoria.produto=produto.id', array('nome'=>'produto_nome', 'tipo'));
            $this->setJoin('produto_tipo', 'produto.tipo=produto_tipo.id', array('nome'=>'tipo_nome'));
            $this->setJoin('embalagem', 'mercadoria.embalagem=embalagem.id', array('capacidade', 'tipo'=>'emb_tipo', 'unidade'));
            $this->setJoin('unidade', 'embalagem.unidade=unidade.id', array('sigla'));
            $this->setJoin('embalagem_tipo', 'embalagem.tipo=embalagem_tipo.id', array('nome'=>'emb_tipo_nome'));
            $this->setSubQuery('SELECT quantidade FROM compra WHERE data IS NOT NULL AND compra.mercadoria=tbl.mercadoria ORDER BY data DESC LIMIT 1', 'lastQtd');
            $this->setSubQuery('SELECT preco FROM compra WHERE data IS NOT NULL AND compra.mercadoria=tbl.mercadoria ORDER BY data DESC LIMIT 1', 'lastPreco');
            $this->setSubQuery('SELECT data FROM compra WHERE data IS NOT NULL AND compra.mercadoria=tbl.mercadoria ORDER BY data DESC LIMIT 1', 'lastDate');
            $this->concat(array('produto.nome', 'embalagem_tipo.nome', 'embalagem.capacidade', 'unidade.sigla'), 'nome', ' ');
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