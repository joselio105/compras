<?php
/**
 * @version 02/05/2019 13:31:56
 * @author jose_helio@gmail.com
 *
 */

final class pdt_tp extends Controller_Class{
    
    public function main(){
        $view = array();
        
        $view['lista'] = $this->_model->read(NULL, 'nome');
        
        $ctrl_alias = 'Tipo de Produto';
        $view['link']['add'] = new Helper_Button('add', $ctrl_alias);
        $view['link']['add']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        foreach ($view['lista'] as $l):
            foreach (array('del', 'udt') as $act):
                $view['link'][$act][$l['id']] = new Helper_Button($act, $ctrl_alias, array('id'=>$l['id']));
                $view['link'][$act][$l['id']]->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
            endforeach;
        endforeach;
        
        HelperView::setViewData($view);
    }
    
    
    /**
     * {@inheritDoc}
     * @see Controller_Class::setModel()
     */
    protected function setModel(){
        $this->_model = new Model_Pdt_Tipo();
    }

    
    /**
     * {@inheritDoc}
     * @see Controller_Class::setForm()
     */
    protected function setForm(){
        $this->_form = new Form_Pdt_Tp();
    }
    
    /*protected function setPermitions(){
        parent::setPermitions();
        foreach ($this->permitions as $act=>$permition)
            $this->permitions[$act] = HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC);
    }*/

    //NEW_METHOD
        
        
    
}