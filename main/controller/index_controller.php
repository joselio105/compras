<?php
/**
 * @version 26/11/2018 10:22:05
 * @author jose_helio@gmail.com
 *
 */

final class index extends Controller_Class{
    
    private $mercadoria;
    private $produto;
    
    public function __construct(){
        parent::__construct();
        $this->mercadoria = HelperNavigation::getParam('mcd_id');
        $this->produto = HelperNavigation::getParam('pdt_id');
    }
    
   public function main(){
        $view = array();
        
        $view['lista'] = $this->_model->read("quantidade IS NULL", 'nome');
        //var_dump($view['lista']);die;
        
        $ctrl_alias = 'Lista de Compras';
        $view['link']['add'] = new Helper_Button('add', $ctrl_alias);
        $view['link']['add']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        $view['link']['hst'] = new Helper_Button('history', $ctrl_alias);
        $view['link']['hst']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        foreach ($view['lista'] as $l):
            foreach (array('del', 'check') as $act):
            
                $params = array('id'=>$l['id']);
                if($act=='check'){
                    $params['pdt_id'] = $l['produto'];
                    $params['mcd_id'] = $l['mercadoria'];
                }
                
                $view['link'][$act][$l['id']] = new Helper_Button($act, $ctrl_alias, $params);
                $view['link'][$act][$l['id']]->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
                if($act=='check')
                    $view['link'][$act][$l['id']]->setTitle('Marca Item como Comprado');
                
            endforeach;
        endforeach;
        
        HelperView::setViewData($view);
    }
    
    public function history(){
        $view = array();
        
        $ctrl_alias = 'Lista de Compras';
        foreach ($this->_model->read("quantidade>0", 'nome') as $l):
            $view['lista'][$l['mercadoria']] = $l;
        
            foreach (array('check', 'view') as $act):            
                $params = array(
                    'id'=>$l['id'],
                    'pdt_id'=>$l['produto'],
                    'mcd_id'=>$l['mercadoria']
                );
                $view['link'][$act][$l['id']] = new Helper_Button($act, $ctrl_alias, $params);
                $view['link'][$act][$l['id']]->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
                if($act=='check'){
                    $view['link'][$act][$l['id']]->setTitle('Atualiza Item Comprado');
                    $view['link'][$act][$l['id']]->setClass_Button('udt');
                }
            endforeach;
        endforeach;
        
        HelperView::setViewData($view);
    }
    
    /**
     * {@inheritDoc}
     * @see Controller_Class::add()
     */
    public function add(){
        $this->_form = new Form_Add_Mcd();
        parent::add();
    }
    
    public function add_list(){
        $view = array();
        
        $values = array(
            'mercadoria'=>$this->id
        );
        if ($this->_model->create($values))
            HelperNavigation::redirect(HelperNavigation::getController());
        
        HelperView::setViewData($view);
    }
    
    public function check(){
        $this->_form = new Form_Hst_Check($this->produto, $this->mercadoria);
        $this->_form->setAction(HelperNavigation::getController(), HelperNavigation::getAction(), HelperNavigation::getParams());
        parent::udt();
    }
    
    /**
     * {@inheritDoc}
     * @see Controller_Class::setPermitions()
     */
    protected function setPermitions(){
        parent::setPermitions();
        $this->permitions['check'] = HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC);
    }
    
    /**
     * {@inheritDoc}
     * @see Controller_Class::setModel()
     */
    protected function setModel(){
        $this->_model = new Model_Hst();
    }

    /**
     * {@inheritDoc}
     * @see Controller_Class::setForm()
     */
    protected function setForm(){
        $this->_form = new Form_Hst();
    }

    //NEW_METHOD
    
        
    
    
}