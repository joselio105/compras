<?php
/**
 * @version 02/05/2019 13:43:06
 * @author jose_helio@gmail.com
 *
 */

final class mcd extends Controller_Class{
    
    public function main(){
        $view = array();
        
        $view['lista'] = $this->_model->read(NULL, 'produto_nome');
        
        $ctrl_alias = 'Mercadoria';
        $view['link']['add'] = new Helper_Button('add', $ctrl_alias);
        $view['link']['add']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        foreach ($view['lista'] as $l):
            foreach (array('del', 'udt', 'add_list') as $act):
                $view['link'][$act][$l['id']] = new Helper_Button($act, $ctrl_alias, array('id'=>$l['id']));
                $view['link'][$act][$l['id']]->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
                if($act=='add_list'){
                    $view['link'][$act][$l['id']]->setCtrl('index');
                    $view['link'][$act][$l['id']]->setTitle('Acrescenta na Lista de Compras');
                }
            endforeach;
        endforeach;
        
        HelperView::setViewData($view);
    }
    
    
    /**
     * {@inheritDoc}
     * @see Controller_Class::setModel()
     */
    protected function setModel(){
        $this->_model = new Model_Mcd();
    }

    
    /**
     * {@inheritDoc}
     * @see Controller_Class::setForm()
     */
    protected function setForm(){
        $this->_form = new Form_Mcd();
    }

    //NEW_METHOD
        
        
    
}