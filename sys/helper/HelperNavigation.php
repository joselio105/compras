<?php

final class HelperNavigation extends Start{
    
    /**
     * Recupera o controller
     * @return string
     */
    public static function getController() {
        return parent::getController();
    }
    
    /**
     * Recupera a action
     * @return string
     */
    public static function getAction() {
        return parent::getAction();
    }
    
    /**
     * Recupera todos os par�metros passados via URL
     * @return string[]
     */
    public static function getParams(){
        return parent::getParams();
    }
    
    /**
     * Recupera o valor de um dado par�metro
     * @param string $param
     * @return string|NULL
     */
    public static function getParam($param){
        $params = parent::getParams();
        
        if(key_exists($param, $params))
            return $params[$param];
        else 
            return NULL;
    }
    
    /**
     * Redireciona o site para os parâmetros passados
     * @param string $controller
     * @param string $action
     * @param array $params
     */
    public static function redirect($controller, $action=null, array $params=null){
        $goto = self::getUrl($controller, $action, $params);
        header("Location: {$goto}");
    }
    
    /**
     * Retorna a URL para onde redirecionar o site
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return string
     */
    private static function getUrl($controller, $action=null, array $params=null){
        $parameters = "";
        if(!is_null($params)){
            foreach ($params as $key=>$value):
                if(URL_FRIENDLY)
                    $parameters.= "{$key}/{$value}/";
                else
                    $parameters.= "&$key}={$value}";
            endforeach;
        }
        $action = (!is_null($action)?$action:'main');
        
        if(URL_FRIENDLY)
            return URI."{$controller}/{$action}/{$parameters}";
        else 
            return URI."?ctrl={$controller}&act={$action}{$parameters}";
    }
    
}

