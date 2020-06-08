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
        /*
         * Ler o arquivo csv
         * Identificar Labels (primeira linha)
         * Remover primeira(labels) e última(totais) linhas
         * Organizar o csv como um array
         * Listar os elementos base (categoria, embalagem tipo e embalagem unidade)
         * Listar os elementos segundo nível (produto e embalagem)
         * Listar os elementos terceiro nível (mercadoria)
         * Tebela final: produto, pdtId, pdt_tp, pdt_tp_id, emb_capacidade, unidade, unsId, emb_tipo, emb_tpId, cmp_quantidade, cmp_data, cmp_preço)
         */
        
        $view = array();
        
        $file = HelperNavigation::getParam('file');
        if(!is_null($file)){
            $filename = PATH_CONTENT."csv/{$file}.csv";
            $content = file($filename);
            $labels = explode(',', $content[0]);
            
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
            
            //Nível Base
            $tables = array(
                'pdt_tp' => 'Categoria',
                'und' => 'Unidade',
                'emb_tp' => 'Embalagem'
            );
            foreach ($tables as $modelName=>$toList):
                $lista = $this->listContent($view['content'], $toList);
                foreach ($lista as $i=>$name):
                    $view['lists'][$toList][$i]['nome'] = $name;
                    $view['lists'][$toList][$i]['id'] = $this->getId($modelName, "nome = '{$name}'");
                endforeach;
            endforeach;
            
            var_dump($view['lists'], $view['database'], $view['content']);die;
        }else 
            HelperView::setAlert("É necessário definir o arquivo a ser analizado!");
        
        HelperView::setViewData($view);
    }
    
    /**
     * Lista elementos obtidos no arquivo importado
     * @param array $content
     * @param string $toList
     * @return string[]
     */
    private function listContent(array $content, $toList){
        $response = array();
        
        foreach ($content as $line)
            $response[$line[$toList]] = $line[$toList];
        
            sort($response, SORT_NATURAL | SORT_FLAG_CASE);
        return $response;
    }
    
    private function listOnDb($modelName){
        $response = array();
        
        $models = array(
            'pdt_tp' => new Model_Pdt_Tipo(),
            'unidade' => new Model_Und(),
            'emb_tp'=> new Model_Emb_Tipo()
        );
        
        $response = $models[$modelName]->read();
        
        return $response;
    }
    
    private function getId($modelName, $where){
        $response = array();
        
        $models = array(
            'pdt_tp' => new Model_Pdt_Tipo(),
            'und' => new Model_Und(),
            'emb_tp'=> new Model_Emb_Tipo()
        );
        
        $response = $models[$modelName]->readOne($where);
        
        return (!is_null($response) ? $response['id'] : NULL);
    }
    
    protected function setModel(){}

    protected function setForm(){}


}

