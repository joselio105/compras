<?php
include_once PATH_DEFAULT.'form/Form_Delete.php';

abstract class Controller_Class{
    
    protected $_model;
    protected $_form;
    protected $id;
    protected $item;
    protected $permitions;
    private $msgDel;
        
    /**
     * Controla as interações entre usuário e sistema
     */
    public function __construct(){
        $this->setPermitions();
        $this->loadFiles();
        $this->setModel();
        $this->setForm();
        $this->setMsgDel();
        
        if(in_array(HelperNavigation::getAction(), array('add', 'udt')))
            $this->_form->setAction(HelperNavigation::getController(), HelperNavigation::getAction(), HelperNavigation::getParams());
        if(!in_array(HelperNavigation::getAction(), array('add', 'main')))
            $this->id = HelperNavigation::getParam('id');
    }
    
    public abstract function main();
    
    /**
     * Cadastra um item na tabela do banco de dados
     */
    public function add(){
        $this->protect();
        if(key_exists('render', HelperNavigation::getParams()))
            HelperView::setRenderFalse();
        $view = array();
        
        $view['form'] = $this->_form;
        
        if($this->_form->isSubmitedForm()){
            $this->id = $this->_model->create($this->getValues());
            if($this->id){
                $this->item = $this->_model->readOne("tbl.id={$this->id}");
                HelperFile::writeLog('action', $this->item);
            }
            $this->finish();
        }
        
        HelperView::setViewData($view);
    }
    
    /**
     * Edita um item na tabela do banco de dados
     */
    public function udt(){
        $this->protect();
        if(key_exists('render', HelperNavigation::getParams()))
            HelperView::setRenderFalse();
        $view = array();
        
        $this->item = $this->_model->readOne("tbl.id={$this->id}");
        
        $this->_form->populate($this->item);
        $view['form'] = $this->_form;
        
        if($this->_form->isSubmitedForm()){
            $res = $this->_model->update($this->getValues(), "id={$this->id}");
            if($res){
                $this->item = $this->_model->readOne("tbl.id={$this->id}");
                HelperFile::writeLog('action', $this->item);
            }
            $this->finish();
        }            
        
        HelperView::setViewData($view);
    }
    
    /**
     * Exclui um item na tabela do banco de dados
     */
    public function del(){
        $this->protect();
        if(key_exists('render', HelperNavigation::getParams()))
            HelperView::setRenderFalse();
        $view = array();
        
        $view['form'] = new Form_Delete($this->getMsgDel());
        $view['form']->setAction(HelperNavigation::getController(), HelperNavigation::getAction(), HelperNavigation::getParams());
        if($view['form']->isSubmitedForm()){
            $this->item = $this->_model->readOne("tbl.id={$this->id}");
            if($view['form']->readFieldForm('bt_yes')=='Sim'){
                if($this->_model->delete('id='.$this->id))
                    HelperFile::writeLog('action', $this->item);                
            }
            $this->finish();                
        }
        HelperView::setViewData($view);
    }
    
    /**
     * Finaliza a ação
     */
    protected function finish(){
        HelperNavigation::redirect(HelperNavigation::getController());
        die;
    }
    
    /**
     * Adapata os dados recebidos pelo forumário aos campos do model
     * @return array
     */
    protected function getValues(){
        return $this->_form->readForm();
    }
    
    /**
     * Determina a mensagem a ser exibida no forumulário de exclusão
     * @param string $msg
     */
    protected function setMsgDel($msg='Deseja excluir esse item?'){
        $this->msgDel = $msg;
    }
    
    /**
     * Recupera a mensagem a ser exibida no formulário de exclusão
     * @return string
     */
    protected function getMsgDel(){
        return $this->msgDel;
    }
    
    /**
     * Faz o include de todos os models de formulários
     */
    protected function loadFiles(){
        //include models
        foreach (glob(PATH_MODEL."Model_*.php") as $file)
            include_once $file;
        
        //include forms
        foreach (glob(PATH_FORM."Form_*.php") as $file)
            include_once $file;
    }
    
    /**
     * Define o nível de permissão para cada ação da classe
     */
    protected function setPermitions(){
        $this->permitions = array(
            'main'=>HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC),
            'add'=>HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC),
            'udt'=>HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC),
            'del'=>HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC),
        );
    }
    
    /**
     * Retorna o nível de permissão da ação atual
     * @return array|NULL
     */
    protected function getPermition(){
        return $this->permitions[HelperNavigation::getAction()];
    }
    
    /**
     * Proteje a função de acordo com sua permissão
     */
    protected function protect(){
        if(!is_null($this->permitions[HelperNavigation::getAction()]))
            HelperAuth::auth($this->getPermition());
    }
    
    /**
     * Retorna um botão
     * @param string $act
     * @param string $ctrl_alias
     * @param array|NULL $params
     * @param boolean $isModal
     * @param string|NULL $ctrl
     * @param array|NULL $permitions
     * @param string|NULL $class_button
     * @return Helper_Link
     */
    protected function getNewButton($act, $ctrl_alias, array $params=NULL, $class_button=null, $isModal=FALSE, $ctrl=NULL, array $permitions=NULL){
        $ctrl = (is_null($ctrl) ? HelperNavigation::getController() : $ctrl);
        $class_button = (is_null($class_button) ? $act : $class_button);
        
        $button = new Helper_Link($ctrl, $ctrl_alias, $act);
        $button->setIsBotao();
        $button->setClass_Button($class_button);
        if($isModal)
            $button->setIsModal();
        if(!is_null($params))
            $button->setParams($params);
        if(!is_null($permitions))
            $button->setPermitions($permitions);
        
        return $button;
    }
    
    /**
     * Determina o model a ser usado
     */
    protected abstract function setModel();
    
    /**
     * Determina o formulário a ser usado
     */
    protected abstract function setForm();
    
}
