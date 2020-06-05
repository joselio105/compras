<?php

final class model_card extends Helper_Card{
    
    public function __construct($model){
        $this->setCardTitle($model['name']);
        parent::__construct($model);
    }
    
    protected function setContent(){
        $this->content = array();
    }

    protected function setMenu(){
       $this->menu = array();
    }
}