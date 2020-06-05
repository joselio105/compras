<?php
include_once PATH_SYSTEM_SRC.'model/crud/FileFieldTable.php';

abstract class Sql_Class{
    
    private $_db;
    private $_table;
    protected $_fields;
    protected $sql;
    protected $stmt;
    protected $model;
    private $where;
    private $staments;
    
    /**
     * Gera a string de SQL
     * @param Model_Class $model
     */
    public function __construct(Model_Class $model){
        $this->model = $model;
        $this->setFields($model->getFields());
        $this->_table = $model->getTableName();
        $this->_db = $model->getDb();
    }
    
    /**
     * @return string
     */
    public function getSql(){
        $this->setSql();
        return $this->sql;
    }
    
    /**
     * Executa operações de escrita no banco de dados
     * @return boolean
     */
    public function result(){
        $db = $this->getDb();
        
        $this->stmt = $db->prepare($this->sql);
        
        if(!$this->stmt->execute($this->getStaments()))
            HelperView::setAlert("Falha ao adicionar registro!");
        else
            return TRUE;
                
        return FALSE;
    }
    
    /**
     * Determina as condições para a busca
     * @param string $where
     */
    public function setWhere($where){
        
        if (isset($this->staments))
            unset($this->staments);
            
        if (!is_null($where))
            $this->trataWhere($where);
                
        $this->setSql();
    }
    
    /**
     * Determina o valor dos campos
     * @param array $values
     * @return boolean
     */
    public function setValues(array $values){
        $error = array();
        
        foreach ($values as $field=>$value):
            //verifica o tipo de dado (string|numeric|file)
            if(!$this->_fields[$field]->setValue($value))
                $error[] = $field;
                
            //verifica se os campos obrigatorios foram preenchidos
            if($this->_fields[$field]->getRequired() AND is_null($value))
                $error[] = $field;
                      
            //verifica se os campos unicos estão de acordo
            
            if(!$this->_fields[$field]->isSetted())
                unset($this->_fields[$field]);
        endforeach;
        
        /*if(!empty($error))
            HelperView::setAlert("Não foi possível carregar o valor do campo {$error[0]}");*/
                    
        return empty($error);
    }
    
    /**
     * Retorna a tabela em uso
     * @return string
     */
    protected function getTable(){
        return $this->_table;
    }
    
    /**
     * Determina os campos da tabela com seus devidos valores
     * @param FieldTable[] $fields
     */
    protected function setFields(array $fields){
        foreach ($fields as $field)
            $this->_fields[$field->getName()] = $field;
    }

    /**
     * Retorna os campos da tabela
     * @return array
     */
    protected function getFields(){
        return $this->_fields;
    }
    
    /**
     * Retorna o objeto PDO
     * @return PDO
     */
    protected function getDb(){
        return $this->_db;
    }
    
    /**
     * Retorna as condições para a busca
     * @return NULL|string
     */
    protected function getWhere(){
        return $this->where;
    }
    
    /**
     * Retorna os
     * @return array de declaração de valores
     */
    protected function getStaments(){
        return $this->staments;
    }
    
    /**
     * Determina a string de SQL
     */
    public abstract function setSql();
    
    /**
     * Retorna um array com o nome e valor de cada campo
     * @return array
     */
    public function getFieldsArray(){
        $res = array();
        foreach ($this->_fields as $field)
            $res[$field->getName()] = $field->getValue();
        
        return $res;
            
    }
    
    /**
     * Define o banco de dados a ser trabalhado
     * @param string $db
     */
    protected function setDB($db){
        $this->_db = $db;
    }
    
    /**
     * Prepara a where para usar o prepared staments
     * @param string $where
     */
    private function trataWhere($where){
        $posIni = array();
        $posEnd = array();
        $blc = array();
        $match = array();
        
        $pattern = '/^([\(\s]*)';                       //início da condição
        $pattern.= '([[a-z0-9\_\-\.]+)';                //campo
        $pattern.= '([\=\!\<\>\#\s]{1,4})';             //operador de comparação
        $pattern.= '([\"\']{0,1})';                     //abre delimitador de valor
        $pattern.= '([a-zA-Z0-9\_\-\%\@\.\s]+)';        //valor
        $pattern.= '([\"\']{0,1})';                     //fecha delimitador de valor
        $pattern.= '([\)\s]*)$/';                       //fim da condição
        $blcIni = array('[', ' AND ', ' OR ');
        $blcEnd = array(']', ' AND ', ' OR ');
        
        $where = '['.$where.']';
        
        //Localiza início e fim de cada bloco
        foreach (str_split($where) as $pos=>$chr){
            foreach ($blcIni as $ini){
                if(substr($where, $pos, strlen($ini))==$ini)
                    $posIni[] = $pos+strlen($ini);
            }
            foreach ($blcEnd as $end){
                if(substr($where, $pos, strlen($end))==$end)
                    $posEnd[] = $pos;
            }
        }
        
        //Separa os blocos
        foreach ($posIni as $i=>$pos){
            $lenght = $posEnd[$i]-$pos;
            $blc[$i] = trim(substr($where, $pos, $lenght));
            $blc[$i] = str_replace('LIKE', '#', $blc[$i]);
        }
        //Faz as substituições nos blocos
        $blcNew = array();
        foreach ($blc as $i=>$b){
            if(preg_match($pattern, $b, $match[$i])==1){
                if($match[$i][5]!='IS NULL'){
                    $blcNew[$b] = str_replace($match[$i][4].$match[$i][5].$match[$i][6], ':value'.$i, $b);
                    $blcNew[$b] = str_replace('#', 'LIKE', $blcNew[$b]);
                    $this->staments[':value'.$i] = $match[$i][5];
                }
            }
        }
        
        //Faz as substituições na expressão
        foreach ($blcNew as $i=>$b){
            $i = str_replace('#', 'LIKE', $i);
            $where = str_replace($i, $b, $where);
        }
        
        $this->where = 'WHERE '.substr($where, 1, strlen($where)-2);
    }

    
}