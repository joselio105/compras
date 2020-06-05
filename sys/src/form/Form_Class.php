<?php
foreach (glob(PATH_SYSTEM_SRC.'form/fields/*_Field.php') as $file)
    include_once $file;

abstract class Form_Class{
    
    protected $id;
    protected $fields;
    private $method;
    private $action;
    private $enctype;

    /**
     * Gera um formulário
     * @param string $formId
     */
    public function __construct(){
        $this->setFormId();
        $this->setFormFields();
        $this->setAction();
    }
    
    /**
     * Determina o id do formulário
     */
    abstract protected function setFormId();
    
    /**
     * Determina os campos do formulário
     */
    abstract protected function setFormFields();
    
    /**
     * Exibe o código HTML do formulário
     * @return string
     */
    public function __toString(){
        $this->checkFileField();
        
        $res = "\n<form ";
        $res.= "id=\"$this->id\" ";
        $res.= "method=\"{$this->getMethod()}\" ";
        $res.= "action=\"$this->action\" ";
        $res.= "enctype=\"{$this->getEnctype()}\" ";
        $res.= ">";
        foreach ($this->fields as $field)
            $res.= $field;
        $res.= "\n</form>\n";
        
        return $res;
    }
    
    /**
     * Faz com que o método de transmissão de dados do formulário seja GET
     */
    protected function setMethodGet(){
        $this->method = 'GET';
    }
    
    /**
     * Determina para onde o formulário deve enviar seus dados
     * @param string $controller
     * @param string $action
     * @param array $params
     */
    public function setAction($controller=NULL, $action=NULL, array $params=NULL){
        $controller = (is_null($controller)?HelperNavigation::getController():$controller);
        $action = (is_null($action)?HelperNavigation::getAction():$action);
        $parameters = "";
        if(!is_null($params)){
            foreach ($params as $param=>$value)
                $parameters.= (URL_FRIENDLY ? "{$param}/{$value}/" : "&{$param}={$value}");
        }
        
        $this->action = (URL_FRIENDLY ? URI."{$controller}/{$action}/{$parameters}" : "?ctrl={$controller}&act={$action}{$parameters}");
    }
    
    /**
     * Faz com que o formulário possa envia arquivos
     */
    protected function setEnctypeFile(){
        $this->enctype = 'multipart/form-data';
    }
    
    /**
     * Insclui um campo ao formulário
     * @param Field_Class $field
     */
    protected function setField(Field_Class $field){
        $key = (isset($this->fields) ? count($this->fields) : 0);
        
        $this->fields[$key] = $field;
    }
    
    /**
     * Determina os campos do formulário
     * @param array $fields
     */
    protected function setFields(array $fields){
        $this->fields = $fields;
    }
    
    /**
     * Retorna o método de transmissão de dados do formulário
     * @return string
     */
    private function getMethod(){
        return (isset($this->method)?$this->method:'POST');
    }
    
    /**
     * Retorna a forma de envio de dados do formulário
     * @return string
     */
    private function getEnctype(){
        return (isset($this->enctype)?$this->enctype:'application/x-www-form-urlencoded');
    }
    
    /**
     * Verifica se o formulário possui campos do tipo file e prepara o formulário para lidar com isso
     */
    private function checkFileField(){
        if($this->hasFileField())
            $this->setEnctypeFile();
    }
    
    /**
     * Verifica a existência de campos do tipo file
     * @return boolean
     */
    public function hasFileField(){
        $res = array();
        
        foreach ($this->fields as $field):
            if(method_exists($field, 'getType') AND $field->getType()=='file')
                $res[] = TRUE;
        endforeach;
            
        return !empty($res);
    }
    
    /**
     * Verifica se o formulário foi enviado
     * @return boolean
     */
    public function isSubmitedForm(){
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }
    
    /**
     * Carrega valores nos campos do formulário
     * @param array $data
     */
    public function  populate(array $data){
        foreach ($this->fields as $field):
            $fieldId = $field->getFieldName();
        
            if(key_exists($fieldId, $data) AND !empty($data[$fieldId]))
                $field->setValue($data[$fieldId]);
        endforeach;
    }
    
    /**
     * Retorna o valor inserido em cada campo do formulário
     * @return string[]
     */
    public function readForm(){
        unset($_POST['submit']);
        $res = array(); 
        
        foreach ($_POST as $key=>$value)
            $res[$key] = $value; 
        foreach ($_FILES as $key=>$value)
            $res[$key] = $value;
            
        return $res;
            
    }
    
    /**
     * Retorna o valor inserido em um dado campo do formulário
     * @param string $fieldId
     * @return boolean|string
     */
    public function readFieldForm($fieldId){
        $formFields = $this->readForm();
        return (key_exists($fieldId, $formFields) ? $formFields[$fieldId] : FALSE);
    }
    
    /**
     * Retorna todos os campos do formulário como objetos
     * @return Field_Class[]
     */
    public function getFields(){
        return $this->fields;
    }
    
    public function getAction(){
        return $this->action;
    }
}