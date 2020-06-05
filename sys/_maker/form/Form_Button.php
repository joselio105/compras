<?php

final class Form_Button extends Form_Class{
    
    protected function setFormFields(){
        $this->fields = array(
            new Input_Field('file', 'Arquivo'),
            new Input_Field('nome', 'Classe'),
            new Input_Submit_Field('enviar')
        );
        $this->fields[0]->setAutofocus();
        $this->fields[0]->setType('file');
    }

    protected function setFormId(){
        $this->id = 'form_mkr_icon'; 
    }

}

