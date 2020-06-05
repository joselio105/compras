<?php

final class menu_item_card extends Helper_Card{
    
    public function __construct($item){
        $this->setCardTitle($item['name'], 'h4');
        parent::__construct($item);
    }
    
    protected function setContent(){
        /*$this->content['itens'] = new ContentCard($this->item, 'ul');*/
        $this->content['ctlr'] = new ContentCard($this->item['ctlr'], 'p', array('class'=>'with_label', 'id'=>'controller'));
        $this->content['act'] = new ContentCard($this->item['act'], 'p', array('class'=>'with_label', 'id'=>'action'));
        $this->content['title'] = new ContentCard($this->item['title'], 'p', array('class'=>'with_label', 'id'=>'título'));
        $this->content['permitions'] = new ContentCard(HelperAuth::getTypePermition($this->item['permitions']), 'p', array('class'=>'with_label', 'id'=>'permissões'));
    }

    protected function setMenu(){
        $this->menu = array();
    }
}