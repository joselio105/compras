<?php
/**
 * @version 02/05/2019 13:30:12
 * @author jose_helio@gmail.com
 *
 */

final class Form_Hst extends Form_Class{

    /**
     * {@inheritDoc}
     * @see Form_Class::setFormId()
     */
    protected function setFormId(){
        $this->id = 'Form_Hst';
    }
    
    /**
     * {@inheritDoc}
     * @see Form_Class::setFormFields()
     */
    protected function setFormFields(){
        $this->fields = array(
            new Input_Submit_Field('enviar'),
        );
    }
}