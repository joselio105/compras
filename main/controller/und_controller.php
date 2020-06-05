<?php
/**
 * @version 02/05/2019 13:45:08
 * @author jose_helio@gmail.com
 *
 */

final class und extends Controller_Class{
    
    public function main(){
        $view = array();
        
        HelperView::setViewData($view);
    }
    
    
    /**
     * {@inheritDoc}
     * @see Controller_Class::setModel()
     */
    protected function setModel(){
        $this->_model = new Model_Und();
    }

    
    /**
     * {@inheritDoc}
     * @see Controller_Class::setForm()
     */
    protected function setForm(){
        $this->_form = new Form_Und();
    }

    //NEW_METHOD
        
        
    
}