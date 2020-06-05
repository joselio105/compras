<?php
include_once 'sys/src/form/Form_Class.php';

final class Form_Delete extends Form_Class{

    private $delMsg;
    
    public function __construct($delMsg){
        $this->delMsg = $delMsg;
        parent::__construct();
    }
    
    protected function setFormFields(){
        $field = array();
        
        $field[0] = new Mesage_Field('alerta', $this->delMsg);
        
        $field[1] = new Input_Field('bt_no');
        $field[1]->setType('submit');
        $field[1]->setValue('Não');
        $field[1]->setFieldClass('exclusao');
        
        $field[2] = new Input_Field('bt_yes');
        $field[2]->setType('submit');
        $field[2]->setValue('Sim');
        $field[2]->setFieldClass('exclusao');
        
        $this->fields = $field;
    }

    protected function setFormId(){
        $this->id = 'form_del';
    }

}

