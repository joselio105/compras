<?php
include_once PATH_SYSTEM_SRC.'model/crud/FieldTable.php';

final class FileFieldTable extends FieldTable{
    
    private $maxSize;
    private $fileType;
    
    public function __construct($name, array $fileType, $maxSize){
        parent::__construct($name);
        $this->fileType = $fileType;
        $this->setMaxSize($maxSize);
    }
    
    /**
     * {@inheritDoc}
     * Carrega o valor do campo, caso esse atenda os requisitos de tamanho e formato
     * @see FieldTable::setValue()
     */
    public function setValue($value){
        
        $error = NULL;
        
        if(!is_array($value))
            $error = "O valor passado deve ser do tipo array!";
        
        if($value['size']>$this->maxSize)
            $error = "Arquivo maior que o tamanho máximo permitido!";
        if(!in_array($value['type'], $this->fileType) AND $value['size']>0)
            $error = "Formato de arquivo não permitido!";
            
        if(!is_null($error)){
            HelperView::setAlert($error);
            return FALSE;
        }else{
            if($value['size']==0 AND HelperNavigation::getAction()=='add')
                parent::setValue(NULL);
            else
                parent::setValue($this->getFileName($value));
            
            return TRUE;
        }
        
        
    }
    
    /**
     * Determina o tamanho máximo do arquivo
     * @param string|integer $maxSize
     */
    private function setMaxSize($maxSize){
        if(is_numeric($maxSize))
            $this->maxSize = $maxSize;
        if(substr($maxSize, -1, 1)=='M')
            $this->maxSize = substr($maxSize, 0, -1) * 1024 * 1024;
        if(substr($maxSize, -1, 1)=='K')
            $this->maxSize = substr($maxSize, 0, -1) * 1024;
    }
    
    /**
     * Renomeia o arquivo a ser carregado
     * @param array $file
     * @return string
     */
    private function getFileName(array $file){
        $res = '';
        
        $bkr = explode('.', $file['name']);
        $res = date('YmdHis').".{$bkr[count($bkr)-1]}";
        
        return $res;
    }
}
