<?php

final class controller_card extends Helper_Card{
    
    public function __construct($controller){
        $this->setCardTitle($controller['name']);
        //var_dump($controller);die;
        parent::__construct($controller);
    }
    
    protected function setContent(){
        $this->content['actions'] = new ContentCard($this->item['actions'], 'ul');
    }

    protected function setMenu(){
        $this->menu['action'] = new MenuCard('mkr_index', 'action', 'add', 'add_action', array('what'=>'action', 'controller'=>$this->item['name']));
        $this->menu['action']->setTitle("Gera uma nova action para o controller");
        $this->menu['action']->setIsModal();
        $this->menu['action']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        $this->menu['view'] = new MenuCard('mkr_index', 'view', 'add', 'add_view', array('what'=>'view', 'controller'=>$this->item['name']));
        $this->menu['view']->setTitle("Gera um novo arquivo de view para o controller");
        $this->menu['view']->setIsModal();
        $this->menu['view']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
    }
}