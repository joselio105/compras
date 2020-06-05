<?php
/**
 * @version 07/05/2019 15:08:45
 * @author jose_helio@gmail.com
 *
 */

final class Form_Add_Mcd extends Form_Class{

    /**
     * {@inheritDoc}
     * @see Form_Class::setFormId()
     */
    protected function setFormId(){
        $this->id = 'Form_Add_Mcd';
    }
    
    /**
     * {@inheritDoc}
     * @see Form_Class::setFormFields()
     */
    protected function setFormFields(){
        $model = new Model_Mcd();
        
        $this->fields = array(
            new InputAutoComplete_Field('mercadoria', $model->readList('nome'), 'Mercadoria'),
            new Input_Submit_Field('enviar'),
        );
        $this->fields[0]->setAutofocus();
    }
}