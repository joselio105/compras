<?php

final class HelperView{
    private static $render = true;
    private static $view_data;    
    private static $order;
    private static $table;    
    
    /**
     * Impede a renderização da página, renderizando somente a View
     */
    public static function setRenderFalse(){
        self::$render = false;
    }
    
    /**
     * Recupera o status de renderização da action
     * @return boolean
     */
    public static function getRender(){
        return self::$render;
    }
    
    /**
     * Recupera a mensagem de alerta devidamente configurada
     * @return string
     */
    public static function printAlert(){
        $params = HelperNavigation::getParams();
        $params['ctlr_back'] = HelperNavigation::getController();
        $params['act_back'] = HelperNavigation::getAction();
        
        $link = new Helper_Link('mkr_index', 'Close', 'close', $params);
        $link->setIsBotao();
        $link->setTitle('Fechar Alerta!');
        $link->setClass_Button('close');
        $link->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
        
        if(isset($_SESSION[SITE_PREFIX.'alert']) and !empty($_SESSION[SITE_PREFIX.'alert'])){
            return 	"<div class=\"alerta\">\n\t<p>{$_SESSION[SITE_PREFIX.'alert']}</p>{$link}</div>";
        }
    }
    
    /**
     * Define a mensagem de alerta
     * @param string $msg
     */
    public static function setAlert($msg){
        $_SESSION[SITE_PREFIX.'alert'] = $msg;
    }
    
    /**
     * Recupera a mensagem de erro
     * @return string
     */
    public static function getAlertMsg(){
        return $_SESSION[SITE_PREFIX.'alert'];
    }
    
    /**
     * Carrega o arquivo da view
     * @param string $view_file
     */
    public static function load_view($view_file){
        if (file_exists($view_file))
            include_once $view_file;
            else
                include_once '_cmv/view/_error/no_file.phtml';
    }
    
    /**
     * Recupera os dados passados pela action
     * @return array
     */
    public static function getViewData(){
        return self::$view_data;
    }
    
    /**
     * Define os dados passados para a view
     * @param mixed $view
     */
    public static function setViewData($view){
        self::$view_data = $view;
    }
    
    /**
     * Ordena a tabela passada de acordo com parâmetros contidos no array order
     * @param array $table
     * @param array $order
     * @return array
     */
    public static function orderTable(array $table, array $order){
        if(count($table)<1)
            return $table;
        
        self::$table = $table;
        self::$order = $order; 
        
        self::planifica(); 
        self::transversaliza();
        //var_dump(self::$order, self::$table);die;
        if(!key_exists(self::$order['orderBy'], self::$table))
            self::escapa("Campo desconhecido", $table);
        
        self::order();
        self::transversaliza();
        unset(self::$table[self::$order['orderBy']]);
        
        return self::$table;
    }
    
    /**
     * Retorna a tabela original com uma mensagem de erro
     * @param string $msg
     * @param array $table
     * @return array
     */
    private static function escapa($msg, array $table=null){
        self::setAlert('Não foi possível ordenar a tabela!: '.$msg);
        return (is_null($table)?self::$table:$table);
    }
    
    /**
     * Verifica se o campo a ser buscado é um array e o trata para que seja ordenado
     */
    private static function planifica(){
        $orderBy = self::$order['orderBy'];
        
        foreach (self::$table as $i=>$element):
            if(is_array($element[$orderBy])){
                if(key_exists('nome', $element[$orderBy]))
                    self::$table[$i][$orderBy.'_planned'] = $element[$orderBy]['nome'];                    
                else
                    self::escapa("Campo incompatível");
            }else
                self::$table[$i][$orderBy.'_planned'] = $element[$orderBy];
        endforeach; 

        self::$order['orderBy'] = $orderBy.'_planned';
    }
    
    /**
     * Altera as chaves do array
     */
    private static function transversaliza(){
        $transTable = array();
        $table = self::$table;
        
        foreach ($table as $i=>$element):
            foreach ($element as $field=>$value)
                $transTable[$field][$i] = $value;
        endforeach;
        
        self::$table = $transTable;
    }
    
    /**
     * Ordena a tabela
     */
    private static function order(){
        $table = self::$table;
        $tableOrd = $tableOrd2 = array();
        $sortFlag = (self::$order['orderBy']=='id_planned'?SORT_NUMERIC:SORT_LOCALE_STRING);
        
        if(self::$order['order']=='ASC')
            asort($table[self::$order['orderBy']], $sortFlag);
        else
            arsort($table[self::$order['orderBy']], $sortFlag);   
        
        //Ordena demais campos
        foreach ($table[self::$order['orderBy']] as $i=>$v):
            foreach ($table as $id=>$val):
                if($id!=self::$order['orderBy'] AND key_exists($i, $val))
                    $tableOrd[$id][] = $val[$i];
            endforeach;            
        endforeach;
        
        //corrige o tamanho dos arrays
        foreach ($tableOrd as $id=>$tOrd)
            $tableOrd2[$id] = array_pad($tOrd, count($table[self::$order['orderBy']]), null);
          
        //Une tabelas
        foreach ($table[self::$order['orderBy']] as $i=>$v)
            $tableOrd2[self::$order['orderBy']][] = $v;
        
        self::$table = $tableOrd2;
    }
}