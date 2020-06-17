<?php
//include_once PATH_SYSTEM_SRC.'model/crud/Sql_Class.php';

class Read_Class extends Sql_Class{    
    
    private $limit;
    private $order;
    
    /**
     * Determina o limite da consulta
     * @param integer|NULL $limit
     * @param integer|NULL $offset
     */
    public function setLimit($limit, $offset){
        $this->limit = NULL;
        if(!is_null($limit) AND is_numeric($limit)){
            $this->limit = "LIMIT {$limit}";
            if(!is_null($offset) AND is_numeric($offset))
                $this->limit.= " OFFSET {$offset}";
        }
        $this->setSql();
    }
    
    /**
     * Determina a string que ordena a consulta
     * @param string $orderBy
     * @param boolean $orderDesc
     */
    public function setOrder($orderBy, $orderDesc=FALSE){
        $this->order = NULL;
        if(!is_null($orderBy)){
            $this->order = "ORDER BY {$orderBy} ";
            $this->order.= ($orderDesc ? 'DESC' : 'ASC');
        }
        $this->setSql();
    }
    
    /**
     * {@inheritdoc}
     * Retorna o resultado da consulta
     * @return array|string
     */
    public function result(){
        $res = array();
        
        if (parent::result())
            $reg = $this->stmt->fetchAll(PDO::FETCH_OBJ);
        else
            HelperView::setAlert("Erro ao executar consulta em {$this->tbl}<br>{$this->stmt->queryString}");
                
        foreach ($reg as $line => $element) :
            foreach ($element as $field => $value)
                $res[$line][$field] = trim($value);
            endforeach;
            
        return $res;
    }
    
    /**
     * {@inheritDoc}
     * Determina a string de SQL
     * @see Sql_Class::setSql()
     */
    public function setSql(){
        if($this->model->getCount())
            $this->sql = "SELECT COUNT(*) AS total FROM {$this->getTable()} {$this->getWhere()}";
        elseif(!is_null($this->model->getSum()))
            $this->sql = "SELECT SUM({$this->model->getSum()}) AS total FROM {$this->getTable()} {$this->getWhere()}";
        else 
            $this->sql = "SELECT {$this->getFields()} 
                          FROM {$this->getTable()}
                          {$this->getJoin()}
                          {$this->getWhere()}   
                          {$this->getOrder()}   
                          {$this->getLimit()}  
            ";
    }
    
    /**
     * {@inheritDoc}
     * Retorna os campos a serem consultados
     * @see Sql_Class::getFields()
     * @return string
     */
    protected function getFields(){
        $fields = array();
        $fields[] = 'tbl.id AS id';
        
        foreach (parent::getFields() as $field)
            $fields[] = "tbl.{$field} AS {$field}";
        if(!is_null($this->model->getJoin())){
            $key = count($fields);
            foreach ($this->model->getJoin() as $join):
                foreach ($join['fields'] as $fieldName=>$fieldAlias):
                    $fieldName = (is_numeric($fieldName) ? $fieldAlias : $fieldName);
                    $fields[$key] = "{$join['table']}.{$fieldName} AS {$fieldAlias}";
                    $key++;
                endforeach;
            endforeach;
        }
        if(!is_null($this->model->getConcat())){
            $key = count($fields);
            foreach ($this->model->getConcat() as $concat):
                $fields[$key] = $concat;
                $key++;
            endforeach;
        }
        if(!is_null($this->model->getSubSql())){
            $key = count($fields);
            foreach ($this->model->getSubSql() as $subSql):
                $fields[$key] = $subSql;
                $key++;
            endforeach;
        }
        
        return implode(', ', $fields);
    }
    
    /**
     * {@inheritDoc}
     * Retorna a tabela principal da consulta
     * @see Sql_Class::getTableName()
     */
    protected function getTable(){
        return parent::getTable().' AS tbl';
    }
    
    /**
     * Retorna os joins feitos na consulta
     * @return string
     */
    private function getJoin(){
        $res = array();
        
        if(!is_null($this->model->getJoin())){
            foreach ($this->model->getJoin() as $join)
                $res[] = "{$join['type']} {$join['table']} ON {$join['on']}";
        }
        
        return implode("\n\t", $res);
    }
    
    /**
     * Retorna a string que limita a consulta
     * @return string|NULL
     */
    private function getLimit(){
        return $this->limit;
    }
    
    /**
     * Retorna a string que ordena a consulta
     * @return string|NULL
     */
    private function getOrder(){
        return $this->order;
    }
    
}

