<?php
/**
 * @version 02/05/2019 13:28:45
 * @author jose_helio@gmail.com
 *
 */

final class Form_Mcd extends Form_Class{

    /**
     * {@inheritDoc}
     * @see Form_Class::setFormId()
     */
    protected function setFormId(){
        $this->id = 'Form_Mcd';
    }
    
    /**
     * {@inheritDoc}
     * @see Form_Class::setFormFields()
     */
    protected function setFormFields(){
        $produtos = new Model_Pdt(TRUE);
        $embalagens = new Model_Emb(TRUE);
        
        $this->fields = array(
            new Select_Field('produto', $produtos->readList('nome', NULL, 'Escolha'), 'Produto'),
            new Select_Field('embalagem', $embalagens->readList('nome', NULL, 'Escolha'), 'Embalagem'),
            new Input_Submit_Field('enviar'),
        );
        $this->fields[0]->setAutofocus();
    }
}