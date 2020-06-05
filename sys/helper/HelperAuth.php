<?php

final class HelperAuth{
    
    private static $permitions;
    private static $accessCode;
    
    /**
     * Verifica se o usuário tem acesso a action
     * @param array|integer $auth
     */
    public static function auth($auth){
        if(is_null(self::getAuth())){
            self::setGoingTo("http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}");
            HelperNavigation::redirect('auth');
        }
        
        $msg = null;
        
        if(is_array($auth)){
            if(!in_array(self::getAuth(), $auth))
                $msg = "Usuário sem autorização para essa ação!";
        }else{
            if($auth!=self::getAuth() AND !is_null($auth))
                $msg = "Usuário sem autorização para essa ação!";
        }
        
        if(!is_null($msg)){
            HelperView::setAlert($msg);
            HelperNavigation::redirect('auth');
        }
    }
    
    /**
     * Recupera o caminho enviado antes da autenticação do usuário
     * @return string|boolean
     */
    public static function getGoingTo(){
        if(key_exists(HelperView::getPrefix().'goingTo', $_SESSION) OR !is_null($_SESSION[HelperView::getPrefix().'goingTo']))
            return $_SESSION[HelperView::getPrefix().'goingTo'];
        else 
            return false;
    }
    
    /**
     * Determina o valor do goingTo
     */
    public static function setGoingTo($url=null){
        $_SESSION[SITE_PREFIX.'goingTo'] = $url;
    }
    
    /*
     * Registra os dados do usuário logado e seu nível de acesso
     */
    public static function register(array $user){
        $_SESSION[SITE_PREFIX.'auth'] = 1;
        $_SESSION[SITE_PREFIX.'user'] = $user;
    }
    
    /**
     * Apaga os dados do usuário logado, seu nível de acesso e redireciona o site para a página inicial
     */
    public static function unregister(){
        $_SESSION[SITE_PREFIX.'auth'] = null;
        $_SESSION[SITE_PREFIX.'user'] = null;
        HelperNavigation::redirect(CONTROLLER_LOGOUT);
    }
    
    /**
     * Recupera o nível de acesso do usuário logado
     * @return integer|boolean
     */
    public static function getAuth(){
        if(isset($_SESSION[SITE_PREFIX.'auth']))
            return $_SESSION[SITE_PREFIX.'auth'];
        else 
            return NULL;
    }
    
    /**
     * Recupera os dados do usuário logado
     * @return array|boolean
     */
    public static function getUser(){
        if(isset($_SESSION[SITE_PREFIX.'user']))
            return $_SESSION[SITE_PREFIX.'user'];
        else
            return false;
    }
    
    /**
     * Define o nível de acesso
     * @return boolean
     */
    private static function setAccessCode(){
        if(file_exists(FILE_CONFIG_ACCESS_CODE))
            self::$accessCode = HelperFile::jsonRead(FILE_CONFIG_ACCESS_CODE);
        else 
            self::$accessCode = array(1=>'admin');
        
        return isset(self::$accessCode);
    }
    
    /**
     * Recupera um tipo de acesso ou a listagem de acessos cadastradas
     * @param integer $code
     * @return boolean|string|array
     */
    public static function getAccessCode($code=NULL, $noAdmin=FALSE){
        $accessCode = (self::setAccessCode() ? self::$accessCode : array(1=>'admin'));
        $res = array();
        
        if($noAdmin)
            unset($accessCode[1]);
        
        if(!is_null($code))
            $res = (key_exists($code, $accessCode) ? $accessCode[$code] : FALSE);
        else 
            $res = $accessCode;
        
        return $res;
    }
    
    /**
     * Define as permissões
     */
    private static function setPermitions(){
        if(file_exists(FILE_CONFIG_PERMITIONS))
            self::$permitions = HelperFile::jsonRead(FILE_CONFIG_PERMITIONS);
        else 
            self::$permitions = array(
                'public'=>NULL,
                'admin'=>array(1),
            );
    }
    
    /**
     * Recupera a lista de permissões de um nível de acesso
     * @param string $type
     * @return array
     */
    public static function getPermition($type=NULL){
        self::setPermitions();
        if(!is_null($type))
            return (key_exists($type, self::$permitions) ? self::$permitions[$type] : self::$permitions['admin']);
        else 
            return self::$permitions;
    }
    
    /**
     * Recupera um nível de permissão pré definido
     * @param string $type
     * @return number[]
     */
    public static function getPermitionByType($type){
        self::setPermitions();
        if(!key_exists($type, self::$permitions))
            return self::$permitions['admin'];
        else 
            return self::$permitions[$type];
    }
    
    /**
     * Recupera o nome do nível de permissão
     * @param string $permition_level
     * @return string
     */
    public static function getTypePermition($permition_level){
        self::setPermitions();
        $constants = get_defined_constants(TRUE);
        $start = 'PERMITION_LEVEL_';
        $res = '';
        
        foreach ($constants['user'] as $name=>$value):
            if(substr($name, 0, strlen($start))==$start AND self::$permitions[$value]==$permition_level)
                $res = $name;
        endforeach;
        
        return $res;
    }
    
    /**
     * Lista os nomes dos níveis de permissão
     * @return string[]
     */
    public static function listPermitions(){
        self::setPermitions();
        $res = array();
        
        foreach (self::$permitions as $name=>$value)
            $res[$name] = self::getTypePermition($value);
        
        return $res;
    }
    
    /**
     * Esconde um elemento sem permissão para aparecer
     * @param string $type
     * @return string|null
     */
    public static function showIfPermition($type){
        self::setPermitions();
        $usr = self::getUser();
        if(!in_array($usr['acs_tipo_id'], self::$permitions[$type]))
            echo "style=\"display:none;\"";
        
    }
}