<?php

abstract class DB_Connect_Class{
    
    private $_DB_HOST;    
    private $_DB_NAME;    
    private $_DB_USER;    
    private $_DB_PSWD;
    private $_db;

    /**
     * Conecta o site ao banco de dados
     * @return boolean
     */
    public function __construct(){
        $this->setAttrs();
        //var_dump($this->checkAttrs());die;
        if($this->checkAttrs())
            return $this->connect();
        else 
            return FALSE;
    }
    
    /**
     * Retorna o objeto PDO
     * @return PDO
     */
    public function getDb(){
        if(!is_object($this->_db))
            var_dump($this->_db, "ERRO ao conectar com o banco de dados!");
        return $this->_db;
    }
    
    /**
     * Retorna o nome do banco de dados
     * @return string
     */
    protected function getDbNAme(){
        return $this->_DB_NAME;
    }
    
    /**
     * Coleta os atributos para conexão no arquivo de configuração do sistema
     */
    private function setAttrs(){
        if (file_exists(FILE_CONFIG_DB)) {
            foreach (HelperFile::jsonRead(FILE_CONFIG_DB) as $name=>$value)
                $this->$name = $value;
        }else{
            $this->_DB_HOST = 'localhost';
            $this->_DB_USER = 'root';
            $this->_DB_NAME = 'lista_compras';
            $this->_DB_PSWD = '';
            //HelperView::setAlert("O arquivo ".FILE_CONFIG_DB." não existe!");
        }
    }
    
    /**
     * Verifica se todos os atributos para conexão foram passados
     * @return boolean
     */
    private function checkAttrs(){
        $msg = NULL;
        
        if(!isset($this->_DB_HOST))
            $msg = "O host do banco de dados não foi determinado!";
        if(!isset($this->_DB_NAME))
            $msg = "O nome do banco de dados não foi determinado!";
        if(!isset($this->_DB_USER))
            $msg = "O usuário do banco de dados não foi determinado!";
        if(!isset($this->_DB_PSWD))
            $msg = "A senha do banco de dados não foi determinada!";
        
        if(!is_null($msg))
            HelperView::setAlert($msg);
        
        return is_null($msg);
    }
    
    /**
     * Conecta ao banco de dados
     * @return boolean
     */
    private function connect() {
        $dsn = "mysql:dbname={$this->_DB_NAME};host={$this->_DB_HOST}";
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        );
        
        $this->_db = new PDO($dsn, $this->_DB_USER, $this->_DB_PSWD, $options);
        $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return is_object($this->_db);
    }
}

