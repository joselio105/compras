<?php

final class Form_Model extends Form_Class{
    
    protected function setFormFields(){
        $this->fields = array(
            new Input_Field('nome', 'Model'),
            new Input_Field('tabela', 'Tabela'),
            new Input_Submit_Field('enviar')
        );
        $this->fields[0]->setAutofocus();
    }

    protected function setFormId(){
        $this->id = 'form_mkr_model';
    }
}