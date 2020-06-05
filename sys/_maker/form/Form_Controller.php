<?php

final class Form_Controller extends Form_Class{
    
    private $models;
    private $forms;
    
    public function __construct(array $models, array $forms){
        $this->models = $models;
        $this->forms = $forms;
        parent::__construct();
    }
    
    protected function setFormFields(){
        $this->fields = array(
            new Input_Field('nome', 'Controller'),
            new Select_Field('model_name', $this->models, 'Model'),
            new Select_Field('form_name', $this->forms, 'Formulário'),
            new Input_Submit_Field('enviar')
        );
        $this->fields[0]->setAutofocus();
        $this->fields[1]->setNoRequired();
        $this->fields[2]->setNoRequired();
    }

    protected function setFormId(){
        $this->id = 'form_mkr_controller';
    }
}