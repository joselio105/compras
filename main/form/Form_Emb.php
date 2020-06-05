<?php
/**
 * @version 02/05/2019 13:28:52
 * @author jose_helio@gmail.com
 *
 */

final class Form_Emb extends Form_Class{

    /**
     * {@inheritDoc}
     * @see Form_Class::setFormId()
     */
    protected function setFormId(){
        $this->id = 'Form_Emb';
    }
    
    /**
     * {@inheritDoc}
     * @see Form_Class::setFormFields()
     */
    protected function setFormFields(){
        $unidades = new Model_Und();
        $tipos = new Model_Emb_Tipo();
        
        $this->fields = array(
            new Input_Number_Field('capacidade', 'Capacidade'),
            new Select_Field('unidade', $unidades->readList('sigla'), 'Unidade'),
            new Select_Field('tipo', $tipos->readList('nome'), 'Tipo'),
            new Input_Submit_Field('enviar'),
        );
        $this->fields[0]->setAutofocus();
    }
}