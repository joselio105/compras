<?php
/**
 * @version 09/05/2019 14:40:58
 * @author jose_helio@gmail.com
 *
 */

final class Form_Hst_Check extends Form_Class{
    
    private $pdt_id;
    private $mcd_id;
    private $mcd_model;
    
    public function __construct($pdt_id, $mcd_id){
        $this->mcd_model = new Model_Mcd();
        $this->pdt_id = $pdt_id;
        $this->mcd_id = $mcd_id;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     * @see Form_Class::setFormId()
     */
    protected function setFormId(){
        $this->id = 'Form_Hst_Check';
    }
    
    /**
     * {@inheritDoc}
     * @see Form_Class::setFormFields()
     */
    protected function setFormFields(){
        $this->fields = array(
            new Select_Field('mercadoria', $this->mcd_model->readList('nome', "produto={$this->pdt_id}"), 'Mercadoria'),
            new Input_Number_Field('quantidade', 'Quantidade'),
            new Input_Number_Field('preco', 'Preço'),
            new Input_Date_Field('data', 'Data'),
            new Input_Submit_Field('enviar'),
        );
        
        $this->fields[0]->setAutofocus();
        $this->fields[0]->setValue($this->mcd_id);
        $this->fields[1]->setMin(0);
        $this->fields[1]->setStep(.001);
        $this->fields[1]->setValue(1);
        $this->fields[2]->setMin(0);
        $this->fields[2]->setStep(.01);
        $this->fields[3]->setValue(date('Y-m-d'));
    }
}