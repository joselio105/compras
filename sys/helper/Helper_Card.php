<?php

include_once PATH_HELPER.'card_element/ContentCard.php';
include_once PATH_HELPER.'card_element/MenuCard.php';

abstract class Helper_Card{
    
    protected $menu;
    protected $content;
    private $cardTitle;
    private $menuClass;
    private $class;
    protected $item;

    public function __construct(array $item, $class=NULL){
        $this->item = $item;
        $this->setContent();
        $this->setMenu();
        $this->class = (is_null($class)?'card':$class);
            
    }
    
    public function __toString(){
        $card = "<div class=\"{$this->class}\">\n";
        $card.= $this->cardTitle;
        $card.= $this->getMenu();
        $card.= $this->getContent();
        $card.= "</div>\n";
        
        return $card;
    }
    
    abstract protected function setMenu();
    
    abstract protected function setContent();
    
    private function getContent(){
        $content = "";
        
        foreach ($this->content as $attr):
            if(!is_array($attr->getValue())){
                if($attr->getTag()=='svg')
                    $content.= '<div class="tooltip">'.HelperFile::getSvgIcon($attr->getValue())."<span class=\"ttp_text\">{$attr->getValue()}</span></div>";
                elseif($attr->getTag()=='img')
                    $content.= "\t<{$attr->getTag()}{$this->getAttrs($attr->getAttrs())} src=\"{$attr->getValue()}\" >\n";
                else
                    $content.= "\t<{$attr->getTag()}{$this->getAttrs($attr->getAttrs())}>{$attr->getValue()}</{$attr->getTag()}>\n";
            }else{
                $content.= "\t<{$attr->getTag()}{$this->getAttrs($attr->getAttrs())}>\n";
                foreach ($attr->getValue() as $line):
                    $field = $attr->getValueField();
                    if(!is_null($field)){
                        $line_cont = $line[$field];
                        $label = $attr->getLabelField();
                        $line_cont = (!is_null($label) ? "{$line[$label]}: <b>{$line_cont}</b>" : $line_cont);
                    }else 
                        $line_cont = $line;
                    
                    $content.= "\t<li>{$line_cont}</li>\n";
                endforeach;
                
                $content.= "</{$attr->getTag()}>";
            }
        endforeach;
        
        return $content;
    }
    
    private function getAttrs(array $attrList){
        $attr = NULL;
        foreach ($attrList as $item=>$value)
            $attr.= " {$item}=\"{$value}\"";
        
        return $attr;
    }
    
    private function getMenu(){
        $menu = "";
        if(!is_null($this->menu) AND !empty($this->menu)){
            $menu.= "\n\t<nav class=\"{$this->getMenuClass()}\">\n";
            foreach ($this->menu as $item)
                $menu.= "\t{$item}\n";
            $menu.= "\t</nav>\n";
        }
        
        
        return $menu;
    }
    
    private function getMenuClass(){
        return (isset($this->menuClass)?$this->menuClass:'horizontal');
    }
    
    /**
     * Define o título do card
     * @param string $title
     * @param string $tag
     */
    protected function setCardTitle($title, $tag='h3'){
        $this->cardTitle = "\n\t<{$tag}>{$title}</{$tag}>\n";
    }
    
    protected function setMenuClass($menuClass){
        $this->menuClass = $menuClass;
    }

}

