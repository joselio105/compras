<?php

final class button_card extends Helper_Card{
    
    public function __construct($button){
        $this->setCardTitle($button['class']);
        parent::__construct($button);
    }
    
    protected function setContent(){
        $this->content = array(
            'icon'=>new ContentCard($this->item['icon'], 'svg'),
        );
    }

    protected function setMenu(){
        /*$this->menu['action'] = new MenuCard('mkr_index', 'action', 'add', 'add_action', array('what'=>'action', 'controller'=>$this->item['name']));
        $this->menu['action']->setTitle("Gera uma nova action para o controller");
        $this->menu['action']->setIsModal();
        $this->menu['action']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        $this->menu['view'] = new MenuCard('mkr_index', 'view', 'add', 'add_view', array('what'=>'view', 'controller'=>$this->item['name']));
        $this->menu['view']->setTitle("Gera um novo arquivo de view para o controller");
        $this->menu['view']->setIsModal();
        $this->menu['view']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));*/
    }
}