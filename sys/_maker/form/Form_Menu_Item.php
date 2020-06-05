<?php

final class Form_Menu_Item extends Form_Class{
    
    protected function setFormFields(){
        $this->fields = array(
            new Input_Field('name', 'Nome'),
            new Select_Field('act', HelperFile::listAllActions(), 'Ação'),
            new Input_Field('title', 'Título'),
            new Select_Field('permition', HelperAuth::listPermitions(), 'Nível de Permissão'),
            new Input_Submit_Field('enviar')
        );
        
        $this->fields[0]->setAutofocus();
    }

    protected function setFormId(){
        $this->id = 'form_menu_item';
    }
}