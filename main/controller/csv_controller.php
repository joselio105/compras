<?php
/**
 * @version 05/06/2020 16:33:00
 * @author jose_helio@gmail.com
 *
 */
final class csv extends Controller_Class{
    
    private $models;
    
    public function __construct(){
        parent::__construct();
        $this->models = array(
            'pdt_tp' => new Model_Pdt_Tipo(),
            'und' => new Model_Und(),
            'emb_tp'=> new Model_Emb_Tipo(),
            'emb'=> new Model_Emb(),
            'pdt'=> new Model_Pdt()
        );
    }
    
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
         * Salvar lista base
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
            $fileJson['lvl1'] = PATH_CONTENT."csv/json/{$file}_lvl1.json";
            $fileJson['dataStructure'] = PATH_CONTENT."csv/json/{$file}_ds.json";
            if(!file_exists($fileJson['lvl1'])){
                $tables = array(
                    'pdt_tp' => 'Categoria',
                    'und' => 'Unidade',
                    'emb_tp' => 'Embalagem'
                );
                foreach ($tables as $modelName=>$toList):
                    $lista = $this->listContent($view['content'], $toList);
                    foreach ($lista as $i=>$name):
                        $id = $this->getId($modelName, "nome = '{$name}'");
                        $view['lvl1'][$toList][$id]['nome'] = $name;
                        $view['lvl1'][$toList][$id]['id'] = $id;
                    endforeach;
                endforeach;
                
                //Estrutura de Dados
                foreach ($labels as $toList):
                    $modelName = array_search($toList, $tables);
                    //var_dump($modelName, $toList, $tables);die;
                    foreach($view['content'] as $i=>$line):                        
                        $view['ds'][$i][$toList] = $line[$toList];
                        if($modelName)
                            $view['ds'][$i][$toList.'_id'] = $this->getId($modelName, "nome = '{$line[$toList]}'");                 
                    endforeach;
                endforeach;
                
                HelperFile::jsonWrite($fileJson['dataStructure'], $view['ds']);
                HelperFile::jsonWrite($fileJson['lvl1'], $view['lvl1']);
                
            }else 
                $view['lvl1'] = HelperFile::jsonRead($fileJson['lvl1']);
            
            //Nível 2
            $fileJson['lvl2'] = PATH_CONTENT."csv/json/{$file}_lvl2.json";
            $view['ds'] = HelperFile::jsonRead($fileJson['dataStructure']);
            if(file_exists($fileJson['lvl2']))
                $view['lvl2'] = HelperFile::jsonRead($fileJson['lvl2']);
            else{
                /*
                 * Embalagem (emb)
                 * capacidade=>ds.Quantidade
                 * unidade=>ds.Unidade_id
                 * tipo=>ds.Embalagem_id
                 * 
                 * Produto (pdt)
                 * nome=>ds.Item
                 * tipo=>ds.Categoria_id
                 */
                
                foreach ($view['ds'] as $i=>$line):
                    foreach (array(
                        'pdt'=>"nome='{$line['Item']}' AND tipo={$line['Categoria_id']}",
                        'emb'=>"capacidade='{$line['Quantidade']}' AND unidade='{$line['Unidade_id']}' AND tipo='{$line['Embalagem_id']}'"
                    ) as $modelName=>$where):
                        $id = $this->getId($modelName, $where);
                        if(!is_null($id))
                            $view['ds'][$i][$modelName.'_id'] = $id;
                        else{
                            $view['ds'][$i][$modelName.'_id'] = $this->addLineOnBd($modelName, $where);
                        }
                    endforeach;
                endforeach;
                
                HelperFile::jsonWrite($fileJson['dataStructure'], $view['ds']);
                var_dump($view['ds']);die;
            }
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
    
    /**
     * Recupera o id de um elemento no banco de dados
     * @param string $modelName
     * @param string $where
     * @return NULL|integer
     */
    private function getId($modelName, $where){
        $response = array();
        
        $response = $this->models[$modelName]->readOne($where);
        
        return (!is_null($response) ? $response['id'] : NULL);
    }
    /**
     * Registra o item no banco de dados
     * @param string $modelName
     * @param string $values
     * @return boolean|number
     */
    private function addLineOnBd($modelName, $values){
        foreach (explode(' AND ', $values) as $value):
            $brk = explode('=', $value);
            $brk[1] = str_replace("'", "", $brk[1]);
            $newValues[$brk[0]] = $brk[1];
        endforeach;
        $response = $this->models[$modelName]->create($newValues);
        
        return $response;
    }
    
    protected function setModel(){}

    protected function setForm(){}


}

