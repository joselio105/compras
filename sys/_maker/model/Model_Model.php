<?php
include_once PATH_MAKER.'model/MKR_Model_Class.php';

final class Model_Model extends MKR_Model_Class{
    
    /**
     * 
     * @param array $values
     * @param string $classType
     */
    public function __construct(array $values){        
        parent::__construct($values, 'model');
        if($this->getReturn()){
            HelperFile::replaceInFile($this->getFileName(), 'TABLE_NAME', $values['tabela']);
            $this->setReturn(TRUE);
        }
    }
    
}