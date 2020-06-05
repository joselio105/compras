<?php

final class DB_Maneger_Class extends DB_Connect_Class{
    
    private $isConnected;
    private $sql;
    
    public function __construct(){
        $this->isConnected = FALSE;
        if(parent::__construct())
            $this->isConnected = TRUE;
    }
    
    /**
     * Executa uma ação no banco de dados via SQL
     * @param string $sql
     * @return boolean
     */
    public function exec($sql){
        $this->sql = $sql;
        return $this->execute();
    }
    
    /**
     * Executa uma ação no banco de dados via SQL
     * @param string $sql
     * @return array
     */
    public function query($sql){
        $res = $this->getDb()->query($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cria uma tabela no banco de dados
     * @param string $tableName
     * @param ColumnTable_Class[] $columnDescription
     * @return boolean
     */
    public function createTable($tableName, array $columnDescription){
        foreach ($columnDescription as $cols):
            $key = array_search('AUTO_INCREMENT', explode(' ', $cols));
            if($key)
                $colName = $cols->getName();
        endforeach;
        
        $columnDescription[count($columnDescription)] = "PRIMARY KEY ({$colName})";
        $columns = implode(", ", $columnDescription);
        
        $this->sql = "CREATE TABLE IF NOT EXISTS {$tableName} ($columns)";
        return $this->execute();
    }
    
    /**
     * Cria uma tabela idêntica em estrutura ou dados
     * @param string $tableName
     * @param boolean $dump
     * @param string|NULL $newName
     * @return boolean
     */
    public function copyTable($tableName, $dump=FALSE, $newName=NULL){
        $newName = (!is_null($newName) ? $newName : "copy_{$tableName}");
        if($this->tableExists($newName)){
            HelperView::setAlert("Cópia não realizada, já existe uma tabela com o nome {$newName}");
            return FALSE;
        }
        
        $this->sql = "CREATE TABLE {$newName} LIKE {$tableName}";
        if($this->execute()){
            if($dump){
                $res = $this->getDb()->query("INSERT INTO {$newName} SELECT * FROM {$tableName}");
                return $res!=FALSE;
            }
            return TRUE;
        }else
            HelperView::setAlert("Erro ao criar copia da estrutura da tabela!");
    }
    
    /**
     * Retorna um array com as tabelas existentes no banco de dados
     * @return string[]
     */
    public function listTables(){
        $tables = array();
        
        $res = $this->getDb()->query("SHOW tables");
        foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $table):
            $tables[] = $table["Tables_in_{$this->getDbNAme()}"];
        endforeach;
        
        return $tables;
    }
    
    /**
     * Lista as colunas e suas características na tabela
     * @param string $tableName
     * @return array|NULL
     */
    public function listColumns($tableName){
        $res = $this->getDb()->query("DESCRIBE {$tableName}");
        if($this->tableExists($tableName))
            return $res->fetchAll(PDO::FETCH_ASSOC);
        else 
            HelperView::setAlert("A tabela {$tableName} não existe!"); 
        return NULL;
    }
    
    /**
     * Renomeia uma tabela do banco de dados
     * @param string $oldName
     * @param string $newName
     * @return boolean
     */
    public function renameTable($oldName, $newName){
        $this->sql = "RENAME TABLE {$oldName} TO {$newName}";
        return $this->execute();
    }
    
    /**
     * Adiciona uma coluna à tabela do banco de dados
     * @param string $tableName
     * @param ColumnTable_Class $columDescription
     * @param string|NULL $position
     * @return boolean
     */
    public function addColumn($tableName, $columnDescription, $position=NULL){
        $msg = NULL;
        if(!is_null($position)){
            if($this->columnExists($tableName, $position))
                $position = "AFTER {$position}"; 
            else
                $position = ($position=='FIRST' ? $position : NULL);
        }
        
        if($this->tableExists($tableName)){
            if(!$this->columnExists($tableName, $columnDescription->getName())){
                $this->sql = "ALTER TABLE {$tableName} ADD {$columnDescription} {$position}";
                return $this->execute();
            }else
                $msg = "A coluna {$columnDescription->getName()} já existe, não é possível criar outra com esse nome!";
        }else
            $msg = "A tabela {$tableName} não existe, não é possível criar outra com esse nome!";
        
        if(!is_null($msg))
            HelperView::setAlert($msg);
        
        return FALSE;
        
    }
    
    /**
     * Elimina uma coluna da tabela do banco de dados
     * @param string $tableName
     * @param string $columnName
     * @return boolean
     */
    public function delColumn($tableName, $columnName){
        $msg = NULL;
        
        if($this->tableExists($tableName)){
            if($this->columnExists($tableName, $columnName)){
                $this->sql = "ALTER TABLE {$tableName} DROP {$columnName}";        
                return $this->execute();
            }else
                $msg = "A coluna {$columnName} não existe, não é possível excluí-la!";
        }else
            $msg = "A tabela {$tableName} não existe, não é possível excluir um de seus campos!";
            
        if(!is_null($msg))
            HelperView::setAlert($msg);
                
        return FALSE;
        
    }
    
    /**
     * Altera uma dada coluna da tabela do banco de dados
     * @param string $tableName
     * @param string $columnName
     * @param ColumnTable_Class $columDescription
     * @return boolean
     */
    public function udtColumn($tableName, $columnName, $columnDescription){
        $msg = NULL;
        
        if($this->tableExists($tableName)){
            if($this->columnExists($tableName, $columnName)){
                $this->sql = "ALTER TABLE {$tableName} CHANGE {$columnName} {$columnDescription}";
                return $this->execute();
            }else
                $msg = "A coluna {$columnName} não existe, não é possível alterá-la!";
        }else
            $msg = "A tabela {$tableName} não existe, não é possível alterar seus campos!";
            
        if(!is_null($msg))
            HelperView::setAlert($msg);
                
        return FALSE;
    }
    
    /**
     * Gera uma coluna na tabela com o mesmo conteúdo de uma dada tabela
     * @param string $tableName
     * @param string $columnName
     * @param ColumnTable_Class $cloneColumn
     * @return boolean
     */
    public function cloneColumn($tableName, $columnName, $cloneColumn){
        if($this->addColumn($tableName, $cloneColumn, $columnName)){
            $this->sql = "UPDATE {$tableName} SET {$cloneColumn->getName()}={$columnName}";
            return $this->execute();
        }else
            return FALSE;
    }
    
    /**
     * Define a chave primária de uma tabela
     * @param string $tableName
     * @param string $columnName
     * @return boolean
     */
    public function setPrimaryKey($tableName, $columnName){
        $msg = NULL;
        
        if($this->tableExists($tableName)){
            if($this->columnExists($tableName, $columnName)){
                if(is_null($this->getPrimaryKey($tableName))){
                    $this->sql = "ALTER TABLE {$tableName}  ADD PRIMARY KEY ({$columnName})";
                    return $this->execute();
                }else
                    $msg = "A tabela {$tableName} já possui uma chave primária!";
            }else
                $msg = "A coluna {$columnName} não existe, não é possível alterá-la!";
        }else
            $msg = "A tabela {$tableName} não existe, não é possível alterar seus campos!";
            
       if(!is_null($msg))
        HelperView::setAlert($msg);
                
        return FALSE;
    }
    
    /**
     * Recupera a chave primária de uma tabela
     * @param string $tableName
     * @return NULL|string
     */
    public function getPrimaryKey($tableName){
        $res = NULL;
        //var_dump($this->listColumns($tableName));die;
        foreach ($this->listColumns($tableName) as $column):
            if(strlen($column['Key'])>0)
                $res = $column['Field'];
        endforeach;
        
        return $res;
    }
    
    /**
     * Verifica se a coluna existe na tabela
     * @param string $tableName
     * @param string $columnName
     * @return boolean
     */
    public function columnExists($tableName, $columnName){
        $this->sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE (table_name = '{$tableName}') AND (table_schema = '{$this->getDbNAme()}') AND (column_name = '{$columnName}')";
        $res = $this->getDb()->query($this->sql);
        return $res->fetch(PDO::FETCH_COLUMN)>0;
    }
    
    /**
     * Verifica se a tabela existe no banco de dados
     * @param string $tableName
     * @return boolean
     */
    public function tableExists($tableName){
        $this->sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE (table_name = '{$tableName}') AND (table_schema = '{$this->getDbNAme()}')";
        //$this->sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{$this->getDbNAme()}' and table_name = '{$tableName}'";
        $res = $this->getDb()->query($this->sql);
        return $res->fetch(PDO::FETCH_COLUMN)>0;
    }
    
    /**
     * Executa a ação no banco de dados
     * @return boolean
     */
    private function execute(){
        if ($this->isConnected)
            return $this->getDb()->exec($this->sql)>=0;
        else
            return FALSE;
    }

}

