<?php
include_once 'sys/boot.php';

class Start{
    
    private static $ctrl;
    private static $act;
    private static $params;
    public static $siteInfo;
    private static $path_helper;
    private static $path_system;
    
    public static function run($friendly_url=FALSE){
        self::$path_helper = 'sys/helper/';
        self::$path_system = 'sys/src/';
        
        $root = substr($_SERVER['SCRIPT_FILENAME'], strlen($_SERVER['DOCUMENT_ROOT']));
        $brk = explode('/', $root);
        $root = substr($root, 0, strlen($brk[count($brk)-1])*-1);
        
        define('URI', 'http://'.$_SERVER['SERVER_NAME'].$root);
        
        if($friendly_url)
            self::ifFriendly();
        else
            self::ifNoFriendly();
        
        self::loadHelpers(1);
        self::constants($friendly_url);
        self::loadHelpers(2);
        
        Helper_Button::prepareButtons();
        
        self::$siteInfo = new Boot();
    }
    
    private static function ifFriendly(){
        if(!file_exists('.htaccess'))
            self::ifNoFriendly();
        
        $server = "http://{$_SERVER['SERVER_NAME']}";
        $domain = substr(URI, strlen($server));
        $filename = substr($_SERVER['REQUEST_URI'], strlen($domain));
        $brk = explode('/', $filename);
        //var_dump($filename, $domain, $server, $_SERVER['REQUEST_URI'], URI);die;
        
        if(key_exists(0, $brk)){
            self::$ctrl = (!empty($brk[0]) ? $brk[0]: 'index');
            unset($brk[0]);
        }else 
            self::$ctrl = 'index';
        
        if(key_exists(1, $brk)){            
            self::$act = (!empty($brk[1]) ? $brk[1]: 'main');;
            unset($brk[1]);
        }else 
            self::$act = 'main';
        
        for ($i=2; $i<=count($brk); $i=$i+2):
            if(key_exists($i+1, $brk))    
                self::$params[$brk[$i]] = $brk[$i+1];
        endfor;
    }
    
    private static function ifNoFriendly(){
        $params = array();
        if(!empty($_SERVER['QUERY_STRING'])):
            foreach (explode('&', $_SERVER['QUERY_STRING']) as $termo):
                $brk = explode('=', $termo);
                $params[$brk[0]] = $brk[1];
            endforeach;
        endif;
        
        if(key_exists('ctrl', $params)){
            self::$ctrl = $params['ctrl'];
            unset($params['ctrl']);
        }else 
            self::$ctrl = 'index';
            
        if(key_exists('act', $params)){
            self::$act = $params['act'];
            unset($params['act']);
        }else
            self::$act = 'main';
        
        self::$params = $params;      
    }
    
    private static function constants($friendly){
        
        define('URL_FRIENDLY', $friendly);
        
        define('PATH_CONTROLLER', 'main/controller/');
        define('PATH_VIEW', 'main/view/');
        define('PATH_MODEL', 'main/model/');
        define('PATH_FORM', 'main/form/');
        define('PATH_CARD', 'main/card/');
        define('PATH_DEFAULT', 'sys/src/default/');
        
        define('FILE_ERROR', 'sys/_error/_error_controller.php');
        define('FILE_VIEW_ERROR', 'sys/_error/view.phtml');
        define('ERROR_NO_CONTROLLER_FILE', 0);
        define('ERROR_NO_CONTROLLER_CLASS', 1);
        define('ERROR_NO_VIEW_FILE', 2);
        define('ERROR_NO_ACTION', 3);
        
        define('CONTROLLER_LOGIN', 'art');
        define('CONTROLLER_LOGOUT', 'auth');
        
        define('PATH_MENU', 'main/config/menu/');
        define('PATH_JS', URI.'sys/js/');
        define('PATH_CSS', URI.'page/css/');
        define('PATH_IMG', 'page/img/');
        define('PATH_FONTS', 'page/fonts/');
        define('PATH_CONTENT', 'content/');
        define('PATH_LOGS', 'content/logs/');
        define('PATH_INCLUDE', 'page/include/');
        define('PATH_EXTRA', URI.'extra/');
        define('PATH_HELPER', self::$path_helper);
        define('PATH_CUSTOM_HELPER', 'main/helper/');
        define('PATH_MAKER', 'sys/_maker/');
        define('PATH_SYSTEM_SRC', self::$path_system);
        define('FILE_MENU', PATH_SYSTEM_SRC.'menu.php');
        define('FILE_LAYOUT', 'page/layout.phtml');
        
        define('FILE_CONFIG_SITE', 'main/config/site.json');
        define('FILE_CONFIG_DB', 'main/config/db.json');
        define('FILE_CONFIG_PERMITIONS', 'main/config/permitions.json');
        define('FILE_CONFIG_ACCESS_CODE', 'main/config/access_code.json');
        
        define('FILE_TYPE_PDF', 'application/pdf');
        define('FILE_TYPE_JPG', 'image/jpeg');
        define('FILE_TYPE_PNG', 'image/png');
        define('FILE_TYPE_SVG', 'image/svg+xml');
        
        define('FILE_SIZE_BYTE', 1);
        define('FILE_SIZE_KILO', 1024);
        define('FILE_SIZE_MEGA', 1024*1024);
        define('FILE_SIZE_GIGA', 1024*1024*1024);
        
        $info = HelperFile::jsonRead(FILE_CONFIG_SITE);
        if(!empty($info) AND key_exists('site_prefix', $info))
            $site_prefix = $info['site_prefix'];
        else 
            $site_prefix = 'noPrefix_';
        define('SITE_PREFIX', $site_prefix);
        
        //Constantes de nível de permissão
        define('PERMITION_LEVEL_PUBLIC', 'public');
        if(file_exists(FILE_CONFIG_ACCESS_CODE)){
            foreach (HelperFile::jsonRead(FILE_CONFIG_ACCESS_CODE) as $access)
                define('PERMITION_LEVEL_'.strtoupper($access), $access);
        }else
            define('PERMITION_LEVEL_ADMIN', 'admin');
    }
    
    private static function loadHelpers($group){
        $helper_group = array(
            1=>array(
                self::$path_helper."HelperFile.php",
            ),
            2=>array(
                self::$path_helper."Helper_Card.php",
                self::$path_helper."Helper_Link.php",
                self::$path_helper."Helper_Menu.php",
                self::$path_helper."HelperButton.php",
                self::$path_helper."HelperAuth.php",
                self::$path_helper."HelperData.php",
                self::$path_helper."HelperNavigation.php",
                self::$path_helper."HelperView.php",
                self::$path_system.'form/Form_Class.php',
                self::$path_system.'model/Model_Class.php',
                self::$path_system.'controller/Controller_Class.php'
            ),
        );
        foreach ($helper_group[$group] as $helper)
            include_once $helper;
    }
    
    protected static function getController(){
        return self::$ctrl;
    }
    
    protected static function getAction(){
        return self::$act;
    }
    
    protected static function getParams(){
        return (is_array(self::$params) ? self::$params : array());
    }
}