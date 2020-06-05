<?php
/**
 * @version DATA_CRIACAO
 * @author jose_helio@gmail.com
 *
 */

final class CLASS_NAME extends Form_Class{

    /**
     * {@inheritDoc}
     * @see Form_Class::setFormId()
     */
    protected function setFormId(){
        $this->id = 'CLASS_NAME';
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