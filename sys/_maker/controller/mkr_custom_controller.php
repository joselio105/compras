<?php
foreach (glob(PATH_MAKER.'card/*.php') as $file)
    include_once $file;

final class mkr_custom{
    
    /**
     * Lista os controllers do site
     */
    public function controller(){
        $view = array();
        $view['cards'] = array();
        
        $view['add'] = new Helper_Link('mkr_index', '', 'add', array('what'=>'controller'));
        $view['add']->setTexto('Novo Controller');
        $view['add']->setIsBotao();
        $view['add']->setIsModal();
        $view['add']->setClass_Button('add');
        $view['add']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        $classes = HelperFile::listClassesOnDir(PATH_CONTROLLER);
        foreach ($classes as $i=>$card):
        $card['actions'] = HelperFile::getMethods($card['name']);
        
        $view['cards'][$i] = new controller_card($card);
        endforeach;
        
        HelperView::setViewData($view);
    }
    
    public function model(){
        $view = array();
        $view['cards'] = array();
        
        $view['add'] = new Helper_Link('mkr_index', '', 'add', array('what'=>'model'));
        $view['add']->setTexto('Novo Model');
        $view['add']->setIsBotao();
        $view['add']->setIsModal();
        $view['add']->setClass_Button('add');
        $view['add']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        $classes = HelperFile::listClassesOnDir(PATH_MODEL);
        foreach ($classes as $i=>$card)
            $view['cards'][$i] = new model_card($card);
            
            HelperView::setViewData($view);
    }
    
    public function formulario(){
        $view = array();
        $view['cards'] = array();
        
        $view['add'] = new Helper_Link('mkr_index', '', 'add', array('what'=>'formulario'));
        $view['add']->setTexto('Novo Formulário');
        $view['add']->setIsBotao();
        $view['add']->setIsModal();
        $view['add']->setClass_Button('add');
        $view['add']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        $classes = HelperFile::listClassesOnDir(PATH_FORM);
        foreach ($classes as $i=>$card)
            $view['cards'][$i] = new model_card($card);
            
            HelperView::setViewData($view);
    }
    
    public function card(){
        $view = array();
        $view['cards'] = array();
        
        $view['add'] = new Helper_Link('mkr_index', '', 'add', array('what'=>'card'));
        $view['add']->setTexto('Novo Card');
        $view['add']->setIsBotao();
        $view['add']->setIsModal();
        $view['add']->setClass_Button('add');
        $view['add']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        $classes = HelperFile::listClassesOnDir(PATH_CARD);
        foreach ($classes as $i=>$card)
            $view['cards'][$i] = new model_card($card);
            
            HelperView::setViewData($view);
    }
    
}