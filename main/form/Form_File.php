<?php

final class Form_File extends Form_Class{
    
    protected function setFormFields(){
        $this->fields = array(
            new Input_Field('file', 'Arquivo CSV'),
            new Input_Submit_Field('enviar')
        );
        $this->fields[0]->setAutofocus();
        $this->fields[0]->setType('file');
    }

    protected function setFormId(){
        $this->id = 'form_field';
    }

}

