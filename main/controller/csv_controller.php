<?php
/**
 * @version 05/06/2020 16:33:00
 * @author jose_helio@gmail.com
 *
 */
final class csv extends Controller_Class{
    
    public function main(){
        $view = array();
        
        $lista = glob(PATH_CONTENT.'csv/*.csv');
        
        foreach ($lista as $l):
            $view['lista'][$l] = basename($l, '.csv');
        endforeach;
        
        HelperView::setViewData($view);
    }
    
    public function read(){
        $view = array();
        
        $file = HelperNavigation::getParam('file');
        if(!is_null($file)){
            $filename = PATH_CONTENT."csv/{$file}.csv";
            $content = file($filename);
            $labels = explode(',', $content[0]);
            //var_dump($labels);die;
            array_shift($content);
            array_pop($content);
            foreach($content as $i=>$line):
                $line = preg_replace('/(\d+)(\,)(\d+)/', '$1.$3', $line);
                $line = str_replace('"', '', $line);
                $line = str_replace('R$ ', '', $line);
                
                $itens = explode(',', $line);
                foreach ($labels as $labelKey=>$labelName)
                    $view['content'][$i][$labelName] = $itens[$labelKey];
            endforeach;
            var_dump($view['content']);die;
        }else 
            HelperView::setAlert("É necessário definir o arquivo a ser analizado!");
        
        HelperView::setViewData($view);
    }
    
    protected function setModel(){}

    protected function setForm(){}

}

