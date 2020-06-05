<?php
foreach (glob(PATH_MAKER.'card/*.php') as $file)
    include_once $file;
foreach (glob(PATH_MAKER.'form/*.php') as $file)
    include_once $file;
foreach (glob(PATH_MAKER.'model/*.php') as $file)
    include_once $file;    

final class mkr_index{
    
    private $configInfo;
    
    public function __construct(){
        define('ROOT', URI);
        $this->configInfo = array(
            'DB'=>array(
                'file'=>FILE_CONFIG_DB,
                'empty'=>array(
                    'db_host'=>NULL,
                    'db_name'=>NULL,
                    'db_user'=>NULL,
                    'db_pswd'=>NULL,
                ),
            ),
            'Site'=>array(
                'file'=>FILE_CONFIG_SITE,
                'empty'=>array(
                    'site_title'=>NULL,
                    'site_subtitle'=>NULL,
                    'site_author'=>NULL,
                    'description'=>NULL,
                    'key_words'=>NULL,
                    'site_prefix'=>NULL,
                ),
            ),
        );
    }
    
    public function main(){
        $view = array();
        
        //Menus
        $view['menus'] = array('public', 'restrict');
        
        foreach ($view['menus'] as $menuType):
            $view['link']['add'][$menuType] = new Helper_Link('mkr_index', '', 'add_menu_item', array('type'=>$menuType));
            $view['link']['add'][$menuType]->setIsBotao();
            $view['link']['add'][$menuType]->setIsModal();
            $view['link']['add'][$menuType]->setClass_Button('add');
            $view['link']['add'][$menuType]->setTitle("Cria um novo item para o menu {$menuType}");
            $view['link']['add'][$menuType]->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
            
            $view['card']['menu'][$menuType] = array();
            $filename = PATH_MENU."{$menuType}.json";
            if(file_exists($filename)){
            foreach (HelperFile::jsonRead($filename) as $id=>$item) 
                $view['card']['menu'][$menuType][$id] = new menu_item_card($item);
            }
        endforeach;
        
        //Configurações
        $view['configs'] = array('Site', 'DB');
        
        foreach ($view['configs'] as $config):
            $fileConfig = HelperFile::jsonRead($this->configInfo[$config]['file']);
            $view['config'][$config] = (!empty($fileConfig) ? HelperFile::jsonRead($this->configInfo[$config]['file']) : $this->configInfo[$config]['empty']);
            
            foreach ($view['config'][$config] as $name=>$value):
                $view['link'][$config][$name] = new Helper_Link('mkr_index', '', 'udt_config', array('type'=>$config,'item'=>$name));
                $view['link'][$config][$name]->setIsBotao();
                $view['link'][$config][$name]->setIsModal();
                $view['link'][$config][$name]->setClass_Button('udt');
                $view['link'][$config][$name]->setTitle("Altera o valor de {$name}");
                $view['link'][$config][$name]->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
            endforeach;
        endforeach;
        
        //Permitions
        foreach (HelperAuth::getPermition() as $type=>$value)
            $view['permition'][$type] = (!is_null($value) ? implode(', ', $value) : 'PUBLIC');
        
            $view['link']['permition'] = new Helper_Link('mkr_index', '', 'add_permition');
            $view['link']['permition']->setIsBotao();
            $view['link']['permition']->setIsModal();
            $view['link']['permition']->setClass_Button('add');
            $view['link']['permition']->setTitle("Cria um novo nível de permissão");
            $view['link']['permition']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
      
        HelperView::setViewData($view);
    }
    
    
    public function memoria(){
        $view = array();
        $view['pasta'] = (! is_null(HelperNavigation::getParam('pasta')) ? str_replace('-', '/', HelperNavigation::getParam('pasta')) : ROOT);
        $categ = $files = $pastas = $bytes = array();

        foreach (glob($view['pasta'] . '*/', GLOB_ONLYDIR) as $content) :
            $categ[$content] = $content;
        endforeach
        ;

        foreach ($categ as $path) :
            if (! is_array($path)) {
                if (file_exists($path))
                    $files[$path] = HelperFile::getFilesInfo($path);
            } else {
                foreach ($path as $subPath) :
                    if (! is_array($subPath)) {
                        if (file_exists($subPath))
                            $files[$subPath] = HelperFile::getFilesInfo($subPath);
                    } else {
                        foreach ($subPath as $ssPath) :
                            if (file_exists($ssPath))
                                $files[$ssPath] = HelperFile::getFilesInfo($ssPath);
                        endforeach
                        ;
                    }
                endforeach
                ;
            }
        endforeach
        ;
        foreach ($files as $path => $info) :
            $bytes[$path] = 0;
            $pastas[$path] = array();
            foreach ($info as $f) :
                $bytes[$path] += $f['size'];
                if (key_exists($f['path'], $pastas[$path])) {
                    $pastas[$path][$f['path']] += $f['size'];
                } else
                    $pastas[$path][$f['path']] = $f['size'];
                $view['link'][$path] = new Helper_Link(HelperNavigation::getController(), $path, HelperNavigation::getAction(), array(
                    'pasta' => str_replace('/', '-', $path)
                ));
                $view['link'][$path]->setNoToolTip();
                $view['link'][$path]->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
            endforeach
            ;
        endforeach
        ;
        $view['link'][0] = new Helper_Link(HelperNavigation::getController(), '../', HelperNavigation::getAction(), array(
            'pasta' => '..-'
        ));
        $view['link'][0]->setNoToolTip();
        $view['link'][0]->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));

        // ordenando por tamanho
        arsort($bytes, SORT_NUMERIC);

        $view['total'] = array_sum($bytes);
        $view['pastas'] = $pastas;
        $view['bytes'] = $bytes;

        foreach ($bytes as $path => $size)
            $view['percent'][$path] = number_format(100 * $size / $view['total'], 2, ',', '.') . '%';

        HelperView::setViewData($view);
    }
    
    public function teste(){
        $view = array();
                
        HelperView::setViewData($view);
    }
    
    public function add(){
        HelperView::setRenderFalse();
        $view = array();
        //$res = FALSE;
        $what = HelperNavigation::getParam('what');        
        $controller = HelperNavigation::getParam('controller');
        
        if($what=='controller')
            $form = new Form_Controller(HelperFile::listClasses(PATH_MODEL), HelperFile::listClasses(PATH_FORM));
        elseif($what=='model')
            $form = new Form_Model();
        elseif($what=='view'){
            $actions = array();
            foreach (HelperFile::getMethods($controller) as $method):
                if(!file_exists(PATH_VIEW."{$controller}/{$method}.phtml"))
                    $actions[$method] = $method;
            endforeach;
            $form = new Form_View($controller, $actions);
        }else
            $form = new Form_Action($what);
        
        $form->setAction(NULL, NULL, HelperNavigation::getParams());
        
        if($form->isSubmitedForm()){
            if($what=='controller')
                new Model_Controller($form->readForm());
            elseif($what=='model')
                new Model_Model($form->readForm());
            elseif($what=='action'){
                $res = HelperFile::replaceInFile(PATH_CONTROLLER."{$controller}_controller.php", '//NEW_METHOD', $this->getActionRepository($form->readFieldForm('nome')));
                if(!$res AND !is_numeric($res))
                    HelperView::setAlert("Erro ao criar a action <b>{$form->readFieldForm('nome')}</b><br />Falha ao ler o arquivo!");
                elseif(!$res AND is_numeric($res))
                    HelperView::setAlert("Erro ao criar a action <b>{$form->readFieldForm('nome')}</b><br />Não há marcação no arquivo!");
            }else 
                new MKR_Model_Class($form->readForm(), $what);
            $action = (in_array($what, array('action', 'view')) ? 'controller' : $what);
            
            HelperNavigation::redirect('mkr_custom', $action);
        }
        
        $view['form'] = $form;
        
        HelperView::setViewData($view);
    }
    
    public function add_menu_item(){
        HelperView::setRenderFalse();
        $type = HelperNavigation::getParam('type');
        $view = array();
        $form = new Form_Menu_Item();
        $form->setAction(NULL, NULL, HelperNavigation::getParams());
        
        $view['form'] = $form;
        
        if($form->isSubmitedForm()){
            $brk = explode('/', $form->readFieldForm('act'));
            $info = array($form->readFieldForm('name')=>array(
                'name'=>$form->readFieldForm('name'),
                'ctlr'=>$brk[0],
                'act'=>$brk[1],
                'title'=>$form->readFieldForm('title'),
                'permitions'=>HelperAuth::getPermitionByType($form->readFieldForm('permition')),
            ));
            
            HelperFile::jsonWrite(PATH_MENU."{$type}.json", $info);
            HelperNavigation::redirect(HelperNavigation::getController());
        }
        
        HelperView::setViewData($view);
    }
    
    public function add_permition(){
        HelperView::setRenderFalse();
        $view = array();
        $form = new Form_Permition();
        $form->setAction(NULL, NULL, HelperNavigation::getParams());
        
        $view['form'] = $form;
        
        if($form->isSubmitedForm()){
            
            //Criar/ Alterar JSON de AccessCode
            $permition = strtolower($form->readFieldForm('nome'));            
            $access = HelperAuth::getAccessCode();
            if(!array_search($permition, $access)){
                array_push($access, $permition);
                HelperFile::jsonWrite(FILE_CONFIG_ACCESS_CODE, $access);
                
                //Permissões
                $accessCode = array_search($permition, HelperAuth::getAccessCode());
                $newPermitions = $form->readFieldForm('acessos');
                $permitions = HelperAuth::getPermition();
                $permitions['admin'] = array_merge($permitions['admin'], array($accessCode));
                if(!key_exists($permition, $permitions)){
                    $permitions[$permition] = ($newPermitions ? ($newPermitions + array($accessCode)) : array($accessCode));
                    HelperFile::jsonWrite(FILE_CONFIG_PERMITIONS, $permitions);
                }else
                    HelperView::setAlert("As permissões desse nível de acesso <b>{$permition}</b> já existem!");
            }else
                HelperView::setAlert("O nível de acesso <b>{$permition}</b> já existe!");
            HelperNavigation::redirect(HelperNavigation::getController());
        }
        
        HelperView::setViewData($view);
    }
    
    public function udt_config(){
        HelperView::setRenderFalse();
        $view = array();
        $item = HelperNavigation::getParam('item');
        $type = HelperNavigation::getParam('type');
        
        $form = new Form_Action($item);
        $form->setAction(NULL, NULL, HelperNavigation::getParams());
        
        if($form->isSubmitedForm()){
            $filename = $this->configInfo[$type]['file'];
            if(file_exists($filename))
                $info = HelperFile::jsonRead($filename);
            else 
                $info = $this->configInfo[$type]['empty'];
            $info[$item] = $form->readFieldForm('nome');
            
            HelperFile::jsonWrite($filename, $info);
            HelperNavigation::redirect(HelperNavigation::getController());
        }
        
        $view['form'] = $form;
        
        HelperView::setViewData($view);
    }
    
    public function close(){
        HelperView::setAlert(NULL);
        $controller = HelperNavigation::getParam('ctlr_back');
        $action = HelperNavigation::getParam('act_back');
        $params = HelperNavigation::getParams();
        unset($params['ctlr_back']);
        unset($params['act_back']);
        
        HelperNavigation::redirect($controller, $action, $params);
    }
    
    private function getActionRepository($actionName) {
        $res = "
    public function {$actionName}(){
        \$view = array();

        HelperView::setViewData(\$view);
    }

    //NEW_METHOD
    
        ";
        
        return $res;
    }
    
}