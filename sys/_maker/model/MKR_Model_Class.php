<?php

class MKR_Model_Class{
    
    private $className;
    private $fileName;
    private $search;
    private $return;

    /**
     * 
     * @param array $values
     * @param string $classType
     * @return boolean
     */
    public function __construct(array $values, $classType){
        $this->setClassName($this->formatClassName($values['nome']));
        
        switch ($classType):
            default:
                $this->return = FALSE;
                HelperView::setAlert("Falha ao criar {$classType}<br />Tipo de Classe Desconhecido!");
                return;
            case 'controller':
                $this->setClassName(strtolower($this->getClassName()));
                $this->setFileName(PATH_CONTROLLER."{$this->getClassName()}_controller.php");
                $repositoryFile = PATH_MAKER."repository/Controller_Repository.php";
                break;
                
            case 'model':
                $this->setFileName(PATH_MODEL."Model_{$this->getClassName()}.php");
                $this->setClassName("Model_{$this->getClassName()}");
                $repositoryFile = PATH_MAKER."repository/Model_Repository.php";
                break;
                
            case 'view':
                $this->setClassName(strtolower($this->getClassName()));
                //PATH.controller/.action.phtml
                $this->setFileName(PATH_VIEW."{$values['controller']}/{$this->getClassName()}.phtml");
                $repositoryFile = PATH_MAKER."repository/View_Repository.php";
                break;
                
            case 'formulario':
                $this->setFileName(PATH_FORM."Form_{$this->getClassName()}.php");
                $this->setClassName("Form_{$this->getClassName()}");
                $repositoryFile = PATH_MAKER."repository/Form_Repository.php";
                break;
                
            case 'card':                
                $this->setClassName("Card_{$this->getClassName()}");
                $this->setFileName(PATH_CARD."{$this->getClassName()}.php");
                $repositoryFile = PATH_MAKER."repository/Card_Repository.php";
                break;
        endswitch;
        
        if(!file_exists($this->getFileName())){
            if(copy($repositoryFile, $this->getFileName())){
                HelperFile::replaceInFile($this->getFileName(), 'CLASS_NAME', $this->getClassName());
                HelperFile::replaceInFile($this->getFileName(), 'DATA_CRIACAO', date('d/m/Y H:i:s'));
                $this->return = TRUE;
            }else{
                $this->return = FALSE;
                HelperView::setAlert("Falha ao criar {$classType}<br />Erro ao copiar o arquivo!");
            }
        }else{
            $this->return = FALSE;
            HelperView::setAlert("Falha ao criar {$classType}<br />Já existe um arquivo chamado <b>{$this->getFileName()}</b>!");
        }
    }
    
    public function getReturn(){
        return $this->return;
    }
    
    /**
     * Converte os primeiros caracteres da string em caixa alta
     * @param string $className
     * @return string
     */
    private function formatClassName($className){
        $className = strtolower($className);
        $brk = explode('_', $className);
        
        foreach ($brk as $i=>$part)
            $brk[$i] = ucfirst($part);
        
        return (!empty($brk) ? implode('_', $brk) : $className);
    }
    
    /**
     * Determina o valor a ser retornado
     * @param boolean $return
     */
    protected function setReturn($return){
        $this->return = $return;
    }
    
    /**
     * @return string
     */
    protected function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    protected function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    protected function getSearch()
    {
        return $this->search;
    }

    /**
     * @param string $className
     */
    protected function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @param string $fileName
     */
    protected function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param string $search
     */
    protected function setSearch($search)
    {
        $this->search = $search;
    }

    
}