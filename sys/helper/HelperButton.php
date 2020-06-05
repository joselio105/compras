<?php

class Helper_Button extends Helper_Link{
    
    private static $cssFile;
    private static $configFile;
    
    /**
     * Cria um objeto botão
     * @param string $act
     * @param string $ctrl_alias
     * @param array $params
     */
    public function __construct($act, $ctrl_alias, array $params=NULL){
        $ctrl = HelperNavigation::getController();
        $class_button = $act;
        
        parent::__construct($ctrl, $ctrl_alias, $act, $params);
        $this->setIsBotao();
        $this->setClass_Button($class_button);
    }
    
    /**
     * Lista os arquivos SVG na pasta icons/
     * @return string[]
     */
    public static function listSvgIcons(){
        $res = array();
        foreach (glob(PATH_IMG."icons/*.svg") as $file)
            $res[$file] = HelperFile::getSvgIcon(basename($file, '.svg'));
        
        return $res;
    }
    
    public static function listButtonClasses(){
        $res = array();
        
        foreach (HelperFile::jsonRead(self::$configFile) as $bt)
            $res[] = ".{$bt['class']}";
        
        return $res;
    }
    
    public static function prepareButtons(){
        self::$configFile = PATH_MAKER."config/buttons.json";
        self::$cssFile = substr(PATH_CSS, strlen(URI))."_mkr_buttons.css";
        
        if(file_exists(self::$configFile)){
            if(!file_exists(self::$cssFile))
                self::refreshCss();
            elseif (file_exists(self::$cssFile) AND (filemtime(self::$cssFile)<filemtime(self::$configFile))){
                unlink(self::$cssFile);
                self::refreshCss();
            }
        }
    }
    
    private static function refreshCss(){        
        $handle = fopen(self::$cssFile, 'a');
        
        if($handle)
            fwrite($handle, self::cssFileContent());
        else
            HelperView::setAlert("Não foi possível abrir o arqiuvo");
        
        fclose($handle);
    }
    
    private static function cssFileContent(){
        $line = '';
        $list = HelperFile::jsonRead(PATH_MAKER."config/buttons.json");
        $keys = '.'.implode(', .',array_keys($list));
        $line = "
@charset \"UTF-8\";

{$keys}{
    background-repeat: no-repeat;
	background-position: center;
	background-size: 40px 40px;
	border-radius: 50%;
	width: 50px;
}";
foreach ($list as $bt):
$line.= "
 .{$bt['class']}{background-image: url(../img/icons/{$bt['class']}.svg);}";
endforeach;

return $line;
    }
}