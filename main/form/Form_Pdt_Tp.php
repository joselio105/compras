<?php
/**
 * @version 02/05/2019 13:27:58
 * @author jose_helio@gmail.com
 *
 */

final class Form_Pdt_Tp extends Form_Class{

    /**
     * {@inheritDoc}
     * @see Form_Class::setFormId()
     */
    protected function setFormId(){
        $this->id = 'Form_Pdt_Tp';
    }
    
    /**
     * {@inheritDoc}
     * @see Form_Class::setFormFields()
     */
    protected function setFormFields(){
        $this->fields = array(
            new Input_Field('nome', 'Tipo de Produto'),
            new Input_Submit_Field('enviar'),
        );
        $this->fields[0]->setAutofocus();
    }
}