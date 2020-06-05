<?php

final class Form_Action extends Form_Class{
    
    private $classType;
    
    public function __construct($what){
        $this->classType = ucfirst($what);
        parent::__construct();
    }
    
    protected function setFormFields(){
        $this->fields = array(
            new Input_Field('nome', $this->classType),
            new Input_Submit_Field('enviar')
        );
        $this->fields[0]->setAutofocus();
    }

    protected function setFormId(){
        $this->id = 'form_mkr_action';
    }
}