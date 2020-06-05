<?php
include_once PATH_HELPER.'Helper_Link.php';

final class MenuCard extends Helper_Link{
    
    private $hide;
    
    public function __construct($ctlr, $text, $action, $classButton, array $params=NULL){
        parent::__construct($ctlr, $text, $action, $params);
        $this->hide = FALSE;
        $this->setIsBotao();
        $this->setShowIfNoPermition(FALSE);
        $this->setClass_Button($classButton);
        if($action=='del')
            $this->setClass('delete');
    }
    
    public function hideIfPublic(){
        if(!is_null(HelperAuth::getAuth()))
            $this->hide = TRUE;
    }
    
    public function __toString(){
        if($this->hide)
            return '';
        else 
            return parent::__toString();
    }
}

