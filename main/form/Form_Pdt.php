<?php
/**
 * @version 02/05/2019 13:28:15
 * @author jose_helio@gmail.com
 *
 */

final class Form_Pdt extends Form_Class{

    /**
     * {@inheritDoc}
     * @see Form_Class::setFormId()
     */
    protected function setFormId(){
        $this->id = 'Form_Pdt';
    }
    
    /**
     * {@inheritDoc}
     * @see Form_Class::setFormFields()
     */
    protected function setFormFields(){
        $model = new Model_Pdt_Tipo();
        
        $this->fields = array(
            new Input_Field('nome', 'Produto'),
            new Select_Field('tipo', $model->readList('nome', NULL, 'Escolha'), 'Tipo'),
            new Input_Submit_Field('enviar'),
        );
        $this->fields[0]->setAutofocus();
    }
}