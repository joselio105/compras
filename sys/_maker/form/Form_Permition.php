<?php

final class Form_Permition extends Form_Class{
    
    protected function setFormFields(){
        $acessos = HelperAuth::getAccessCode(NULL, TRUE);
        $this->fields = array(
            new Input_Field('nome', 'Nome'),
            (!empty($acessos) ? new Input_Radio_Field('acessos', $acessos, 'Acessos') : NULL),
            new Input_Submit_Field('enviar')
        );
        $this->fields[0]->setAutofocus();
        if(!empty($acessos))
            $this->fields[1]->setIsCheckbox();
    }

    protected function setFormId(){
        $this->id = 'form_mkr_permition';
    }
}