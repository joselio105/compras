<?php

final class _error{
    
    private $msg;

    public function __construct($errorType, $object){
        $error_msg = array(
            "O arquivo <b>{$object}</b> do Controller não localizado!",
            "A classe <b>{$object}</b> do Controller não existe!",
            "O arquivo <b>{$object}</b> da View não localizado!",
            "O método <b>{$object}</b> da Action não existe!"
        );
        
        $this->msg = $error_msg[$errorType];
    }
    
    public function index(){
        HelperView::setViewData($this->msg);
    }
}

