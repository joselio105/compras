<?php
/**
 * @version 02/05/2019 13:30:12
 * @author jose_helio@gmail.com
 *
 */

final class Form_Hst extends Form_Class{
    
    private $pdt_id;
    private $mcd_id;
    private $mcd_model;
    
    public function __construct(){
        $this->mcd_model = new Model_Mcd(TRUE);
        $this->pdt_id = HelperNavigation::getParam('pdt_id');
        $this->mcd_id = HelperNavigation::getParam('mcd_id');;
        parent::__construct();
    }

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
        $where = (!is_null($this->pdt_id) ? "produto={$this->pdt_id}": NULL);
        $this->fields = array(
            new Select_Field('mercadoria', $this->mcd_model->readList('nome', $where), 'Mercadoria'),
            new Input_Number_Field('quantidade', 'Quantidade'),
            new Input_Number_Field('preco', 'Preço'),
            //new Input_Date_Field('data', 'Data'),
            new Input_Submit_Field('enviar'),
            );
        
        $this->fields[0]->setAutofocus();
        $this->fields[0]->setValue($this->mcd_id);
        $this->fields[1]->setMin(0);
        $this->fields[1]->setStep(.001);
        $this->fields[1]->setValue(1);
        $this->fields[2]->setMin(0);
        $this->fields[2]->setStep(.01);
        //$this->fields[3]->setValue(date('Y-m-d'));
    }
}