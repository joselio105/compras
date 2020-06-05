<?php

/**
 * @version DATA_CRIACAO
 * @author jose_helio@gmail.com
 *
 */

final class CLASS_NAME extends Helper_Card{
    
    public function __construct($item){
        $this->setCardTitle($item['nome']);
        parent::__construct($item);
    }
    
    protected function setContent(){
        $this->content = array();
    }

    protected function setMenu(){
        $this->menu = array();
    }
}