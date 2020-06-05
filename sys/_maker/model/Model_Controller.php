<?php
include_once PATH_MAKER.'model/MKR_Model_Class.php';

final class Model_Controller extends MKR_Model_Class{
    
    private $act_set_form;
    private $act_set_model;
    
    /**
     * 
     * @param array $values
     * @param string $classType
     */
    public function __construct(array $values){        
        $classType = 'controller';
        $this->setClassName(strtolower($values['nome']));
        $this->setFileName(PATH_CONTROLLER."{$this->getClassName()}_controller.php");
        $repositoryFile = PATH_MAKER."repository/Controller_Repository.php";
        
        if(!file_exists($this->getFileName())){
            if(copy($repositoryFile, $this->getFileName())){
                HelperFile::replaceInFile($this->getFileName(), 'DATA_CRIACAO', date('d/m/Y H:i:s'));
                $this->setReturn(TRUE);
            }else{
                $this->setReturn(FALSE);
                HelperView::setAlert("Falha ao criar {$classType}<br />Erro ao copiar o arquivo!");
            }
        }else{
            $this->setReturn(FALSE);
            HelperView::setAlert("Falha ao criar {$classType}<br />Já existe um arquivo chamado <b>{$this->getFileName()}</b>!");
        }
        
        if($this->getReturn()){
            HelperFile::create_path(PATH_VIEW.$values['nome']);
            $extends = !(empty($values['model_name']) AND empty($values['form_name']));
            $this->setAct_set_model($values['model_name']);
            $this->setAct_set_form($values['form_name']);
            if($extends){
                HelperFile::replaceInFile($this->getFileName(), 'CLASS_NAME', $this->getClassName().' extends Controller_Class');
                HelperFile::replaceInFile($this->getFileName(), '//NEW_METHOD', $this->act_set_model);
                HelperFile::replaceInFile($this->getFileName(), '//NEW_METHOD', $this->act_set_form);
                $this->setReturn(TRUE);
            }else {
                HelperFile::replaceInFile($this->getFileName(), 'CLASS_NAME', $this->getClassName());
                $this->setReturn(TRUE);
            }
        }
    }
    
    /**
     * Determina o método set_form
     * @param string $formName
     */
    private function setAct_set_form($formName){
        $formName = (!empty($formName) ? $formName : 'NULL');
        $this->act_set_form = "
    /**
     * {@inheritDoc}
     * @see Controller_Class::setForm()
     */
    protected function setForm(){
        \$this->_form = new {$formName}();
    }

    //NEW_METHOD
        ";
    }

    /**
     * Determina o método set_model
     * @param string $modelName
     */
    private function setAct_set_model($modelName){
        $modelName = (!empty($modelName) ? $modelName : 'NULL');
        $this->act_set_model = "
    /**
     * {@inheritDoc}
     * @see Controller_Class::setModel()
     */
    protected function setModel(){
        \$this->_model = new {$modelName}();
    }

    //NEW_METHOD
        ";
    }
    
}