<?php
foreach (glob(PATH_MAKER.'card/*.php') as $file)
    include_once $file;
foreach (glob(PATH_MAKER.'form/*.php') as $file)
    include_once $file;

final class mkr_buttons{
    
    public function __construct(){
        define('BUTTONS_CONFIG', PATH_MAKER.'config/buttons.json');
    }
    
    public function main(){
        $view = array();
        
        if(file_exists(BUTTONS_CONFIG)){
            $file = HelperFile::jsonRead(BUTTONS_CONFIG);
            ksort($file);
            foreach ($file as $button)
                $view['card'][] = new button_card($button);
        }
        
        $view['link']['add'] = new Helper_Button('add', 'botão');
        $view['link']['add']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        HelperView::setViewData($view);
    }
    
    public function add(){
        $view = array();
        
        $form = new Form_Button();
        $view['form'] = $form;
        
        if($form->isSubmitedForm()){
            $file = $form->readFieldForm('file');
            if(!in_array($file['type'], array(FILE_TYPE_SVG))){
                HelperView::setAlert("O arquivo deve ser no formato SVG");
                HelperNavigation::redirect(HelperNavigation::getController());
            }
            //Upload do arquivo
            $classname = $form->readFieldForm('nome');
            $filename = PATH_IMG."icons/{$classname}.svg";
            move_uploaded_file($file['tmp_name'], $filename);
            //Registra o botão
            $data = array();
            if(file_exists(BUTTONS_CONFIG))
                $data = HelperFile::jsonRead(BUTTONS_CONFIG);
            $data[$classname] = array(
                'icon'=>$filename,
                'class'=>$classname
            );
            ksort($data);
            HelperFile::jsonWrite(BUTTONS_CONFIG, $data);
            
            HelperNavigation::redirect(HelperNavigation::getController());
        }
        
        HelperView::setViewData($view);
    }
    
    /*public function icons(){
        $view = array();
        
        $view['link']['add'] = new Helper_Button('add', 'ícone', array('what'=>'icon'));
        
        $view['lista'] = glob(PATH_IMG.'icons/*.svg');
        
        HelperView::setViewData($view);
    }
    
    public function classes(){
        $view = $match = array();
        $filename = 'page/css/_mkr_buttons.css';
        
        $view['link']['add'] = new Helper_Button('add', 'ícone');
        
        if(file_exists($filename)){
            foreach (file($filename) as $i=>$l):
                preg_match('/(\.)(\w+)\{(.+)\}/', $l, $match[$i]);            
                if(!empty($match[$i]))
                    $view['lista'][$i] = $match[$i]['2'];
            endforeach;
        }
        
        HelperView::setViewData($view);
    }
    
    protected function finish($what){
        $action = ($what=='icon' ? 'icons' : 'main');
        HelperNavigation::redirect(HelperNavigation::getController(), $action);
    }*/
    
}