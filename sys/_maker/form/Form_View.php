<?php

final class Form_View extends Form_Class{
    
    private $controller;
    private $actions;
    
    public function __construct($controller, array $actions){
        $this->controller = $controller;
        $this->actions = $actions;
        parent::__construct();
    }
    
    protected function setFormFields(){
        $this->fields = array(
            new Input_Field('controller', 'Controller'),
            new Select_Field('nome', $this->actions, 'Action'),
            new Input_Submit_Field('enviar')
        );
        $this->fields[0]->setType('hidden');
        $this->fields[0]->setValue($this->controller);
        $this->fields[1]->setAutofocus();
    }

    protected function setFormId(){
        $this->id = 'form_mkr_view';
    }
}