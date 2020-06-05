<?php
include_once PATH_SYSTEM_SRC.'model/DB_Connect_Class.php';

foreach (glob(PATH_SYSTEM_SRC.'model/crud/*_Class.php') as $file)
    include_once $file;

abstract class Model_Class extends DB_Connect_Class{
    
    protected $_table;
    protected $_fields;
    private $join;
    private $concat;
    private $count;  
    private $sum;
    private $subSql;
    private $isConnected;
    protected $create;  
    protected $read;
    protected $update;
    protected $delete;
    
    /**
     * Faz interações entre o site e o banco de dados
     */
    public function __construct(){
        $this->isConnected = FALSE;
        if(parent::__construct())
            $this->isConnected = TRUE;
        $this->setTableName();
        $this->setFields();
        $this->count = FALSE;
    }
    
    /**
     * Retorna uma listagem de registros
     * @param string $where
     * @param string $orderBy
     * @param boolean $orderDesc
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function read($where=NULL, $orderBy=NULL, $orderDesc=FALSE, $limit=NULL, $offset=NULL){
        if($this->isConnected){
            $this->read = new Read_Class($this);
            $this->read->setWhere($where);
            $this->read->setOrder($orderBy, $orderDesc);
            $this->read->setLimit($limit, $offset);
            $this->read->setSql();
            
            return $this->read->result();
        }else 
            return array();
    }
    
    /**
     * Retorna um registro específico
     * @param string $where
     * @return array|NULL
     */
    public function readOne($where){
        $res = $this->read($where);
         
        if(count($res)==1){
            $key = array_keys($res);
            return $res[$key[0]];
        }else 
            return NULL;
    }
    
    /**
     * Retorna um array no com o registro do campo como valor e o id como chave
     * @param string $fieldName
     * @param string $where
     * @param null|string $firstElement
     * @return string[]
     */
    public function readList($fieldName, $where=NULL, $firstElement=NULL){
        $res = array();
        if(!is_null($firstElement))
            $res[''] = $firstElement;
        
        foreach ($this->read($where, $fieldName) as $l)
            $res[$l['id']] = $l[$fieldName];
        
        return $res;
    }
    
    /**
     * Retorna o id do último registro cadastrado na tabela
     */
    public function getLastId(){
        $this->read = new Read_Class($this);
        $this->read->setLimit(1, NULL);
        $this->read->setOrder('tbl.id', TRUE);
        $this->read->setSql();
        $res = $this->read->result();
        
        return $res[0]['id'];
    }
    
    /**
     * Insere um novo registro na tabela do banco de dados
     * @param array $values
     * @return boolean|integer
     */
    public function create(array $values){
        if($this->isConnected){
            $this->create = new Create_Class($this);
            
            if($this->create->setValues($values)){
                $this->create->setSql();
                
                if($this->create->result()){
                    $this->loadFile($values);
                    return $this->getLastId();
                }
            }
            return FALSE;
        }
        return FALSE;
    }
    
    /**
     * Altera registros da tabela no banco de dados 
     * @param array $values
     * @param string $where
     * @return boolean
     */
    public function update(array $values, $where){
        if($this->isConnected){
            $res = FALSE;
            $this->update = new Update_Class($this);
            $this->update->setWhere($where);
            if($this->update->setValues($values)){
                $this->update->setSql();
                
                if($this->update->result()){
                    $this->loadFile($values);
                    return TRUE;
                }
            }
            return $res;
        }
        return FALSE;
    }
    
    /**
     * Exclui registros da tabela no banco de dados
     * @param string $where
     * @return boolean
     */
    public function delete($where){
        if($this->isConnected){
            $this->delete = new Delete_Class($this);
            $this->delete->setWhere($where);
            $this->delete->setSql();
            return $this->delete->result();
        }else 
            return FALSE;
    }
    
    /**
     * Retorna o nome da tabela do model
     * @return string
     */
    public function getTableName(){
        return $this->_table;
    }

    /**
     * Retorna os campos do model
     * @return array
     */
    public function getFields(){
        return $this->_fields;
    }
    
    /**
     * Retorna o array com a configuração de junção com outras tabelas
     * @return array
     */
    public function getJoin(){
        return $this->join;
    }
    
    /**
     * Conta o número de registros que atendem à condição passada
     * @param string $where
     * @return integer
     */
    public function count($where=NULL){
        $this->read = new Read_Class($this);
        $this->setCount();
        $this->read->setWhere($where);
        $res = $this->read->result();
        $keys = array_keys($res);
        return intval($res[$keys[0]]['total']);
    }
    
    /**
     * Soma os valores do campo inficado atendendo à condição passada
     * @param string $fieldToSum
     * @param string $where
     * @return float
     */
    public function sum($fieldToSum, $where=NULL){
        $this->read = new Read_Class($this);
        $this->setSum($fieldToSum);
        $this->read->setWhere($where);
        $res = $this->read->result();
        $keys = array_keys($res);
        return floatval($res[$keys[0]]['total']);
    }
    
    /**
     * Retorna as concatenações entre campos
     * @return array
     */
    public function getConcat(){
        return $this->concat;
    }
    
    /**
     * Verifica se a consulta será uma contagem
     * @return boolean
     */
    public function getCount(){
        return $this->count;
    }
    
    /**
     * Verifica se a consulta será um somatório
     * @return boolean
     */
    public function getSum(){
        return $this->sum;
    }
    
    /**
     * Retorna a lista de subqueries
     * @return string[]
     */
    public function getSubSql(){
        return $this->subSql;
    }
    
    /**
     * Configura a junção com outras tabelas
     * @param string $table
     * @param string $on
     * @param array $fields
     * @param string $joinType
     */
    protected function setJoin($table, $on, array $fields, $joinType='INNER JOIN'){
        $this->join[count($this->join)] = array(
            'table'=>$table,
            'on'=>$on,
            'fields'=>$fields,
            'type'=>$joinType
        );
    }
    
    protected function resetJoin(){
        $this->join = array();
    }

    /**
     * Concatena dois ou mais campos em uma consulta
     * @param array $fields
     * @param string $alias
     * @param string $union
     */
    protected function concat(array $fields, $alias, $union='-'){
        $this->concat[count($this->concat)] = "CONCAT(".implode(", '{$union}', ", $fields).") AS {$alias}";
    }

    /**
     * Determina que a consulta fará uma contagem de elementos
     */
    protected function setCount(){
        $this->count = TRUE;
    }
    
    /**
     * Determina um campo para ser somado na consulta fará um somatório
     * @param string $fieldToSum
     */
    protected function setSum($fieldToSum){
        $this->sum = $fieldToSum;
    }
    
    /**
     * Insere uma subquery no query principal
     * @param string $sql
     * @param string $alias
     */
    protected function setSubQuery($sql, $alias){
        $key = count($this->subSql);
        $this->subSql[$key] = "({$sql}) AS {$alias}";
    }
    
    /**
     * Determina o nome da tabela
     */
    protected abstract function setTableName();

    /**
     * Determina os campos da tabela
     */
    protected abstract function setFields();
    
    private function loadFile(array $values){   
        foreach ($values as $name=>$value):
            if(is_array($value)){
                $fieldsArray = (HelperNavigation::getAction()=='add' ? $this->create->getFieldsArray() : $this->update->getFieldsArray());
                $path = (key_exists('path', $value) ? $value['path'] : PATH_CONTENT);
                HelperFile::create_path($path);
                $filename = $fieldsArray[$name];
                move_uploaded_file($value['tmp_name'], $path.$filename);
            }
        endforeach;            
    }
    
}