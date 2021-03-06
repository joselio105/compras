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
            'produto_tipo' => new Model_Pdt_Tipo(),
            'unidade' => new Model_Und(),
            'embalagem_tipo'=> new Model_Emb_Tipo(),
            'embalagem'=> new Model_Emb(),
            'produto'=> new Model_Pdt(),
            'mercadoria'=> new Model_Mcd(),
            'compra'=> new Model_Hst()
        );
    }
    
    public function main(){
        $view = array();
        
        $view['form'] = $this->_form;
        
        if($this->_form->isSubmitedForm()){
            $file = $this->_form->readFieldForm('file');
            
            if($file['type']==FILE_TYPE_CSV){
                $filename = preg_replace('/[a-zA-Z]*\.*\s*/', '', $file['name']);
                $brk = explode('-', substr($filename, 1));
                $filename = "20{$brk['2']}-{$brk['1']}-{$brk['0']}";
                
                $param['file'] = $filename;
                $filename = PATH_CONTENT."csv/{$filename}.csv";
                move_uploaded_file($file['tmp_name'], $filename);
                HelperNavigation::redirect('csv', 'read', $param);
            }else 
                HelperView::setAlert('O arquivo deve ser no formato CSV');            
        }
        
        HelperView::setViewData($view);
    }
    
    public function read(){        
        $view = array();
        
        $file = HelperNavigation::getParam('file');
        $level = HelperNavigation::getParam('level');
        
        if(!is_null($file)){
            $fileJson['dataStructure'] = PATH_CONTENT."csv/{$file}.json";
            $filename = PATH_CONTENT."csv/{$file}.csv";
            foreach(file($filename) as $line)
				$content[] = str_replace("\r\n", "", $line);
				
            $labels = explode(',', $content[0]);
			//var_dump($labels, $content);die;
                
            array_shift($content);
            array_pop($content);
            
            if(is_null($level)){
                
                foreach($content as $i=>$line):
					//Troca vírgula por ponto nas células numéricas
                    $line = preg_replace('/(\d+)(\,)(\d+)/', '$1.$3', $line);
					//Retira as aspas delimitadoras de valores
                    $line = str_replace('"', '', $line);
					//Retira o símbolo de moeda
                    $line = str_replace('R$ ', '', $line);
                    
                    $itens = explode(',', $line.',');
                    foreach ($labels as $labelKey=>$labelName)
                        $view['ds'][$i][$labelName] = $itens[$labelKey];
                endforeach;
				
                HelperFile::jsonWrite($fileJson['dataStructure'], $view['ds'], TRUE);
            }
            
            if($level==1){
                $view['ds'] = HelperFile::jsonRead($fileJson['dataStructure']);
                
                $tables = array(
                    'produto_tipo' => 'Categoria',
                    'unidade' => 'Unidade',
                    'embalagem_tipo' => 'Embalagem'
                );
                
                foreach ($labels as $toList):
                    $modelName = array_search($toList, $tables);
                    foreach($view['ds'] as $i=>$line):                        
                        $view['ds'][$i][$toList] = $line[$toList];
                        if($modelName)
                            $view['ds'][$i][$toList.'_id'] = $this->getId($modelName, "nome = '{$line[$toList]}'");                 

                    endforeach;
                endforeach;
                    
                unlink($fileJson['dataStructure']);
                HelperFile::jsonWrite($fileJson['dataStructure'], $view['ds'], TRUE);
            }
            
            if($level==2){
                $view['ds'] = HelperFile::jsonRead($fileJson['dataStructure']);
                
                foreach ($view['ds'] as $i=>$line):
                    foreach (array(
                        'produto'=>"nome='{$line['Item']}' AND tipo={$line['Categoria_id']}",
                        'embalagem'=>"capacidade='{$line['Capacidade']}' AND unidade='{$line['Unidade_id']}' AND tipo='{$line['Embalagem_id']}'"
                        ) as $modelName=>$where):
                        $id = $this->getId($modelName, $where);
                        if(!is_null($id))
                            $view['ds'][$i][$modelName.'_id'] = $id;
                        else{
                            $view['ds'][$i][$modelName.'_id'] = $this->addLineOnBd($modelName, $where);
                        }
                    endforeach;
                endforeach;
                    
                unlink($fileJson['dataStructure']);
                HelperFile::jsonWrite($fileJson['dataStructure'], $view['ds'], TRUE);
            }
            
            if($level==3){
                $view['ds'] = HelperFile::jsonRead($fileJson['dataStructure']);
                
                foreach ($view['ds'] as $i=>$line):

                    $modelName = 'mercadoria';
                    $where = "produto='{$line['produto_id']}' AND embalagem='{$line['embalagem_id']}'";

                    $id = $this->getId($modelName, $where);
                    if(!is_null($id))
                        $view['ds'][$i][$modelName.'_id'] = $id;
                    else
                        $view['ds'][$i][$modelName.'_id'] = $this->addLineOnBd($modelName, $where);                
                endforeach;
                    
                unlink($fileJson['dataStructure']);
                HelperFile::jsonWrite($fileJson['dataStructure'], $view['ds'], TRUE);
            }
            
            
            if($level==4){
                $view['ds'] = HelperFile::jsonRead($fileJson['dataStructure']);
                $data = date('Y-m-d', strtotime($file));
                
                foreach ($view['ds'] as $i=>$line):
                    $modelName = 'compra';
                    $where = "mercadoria='{$line['mercadoria_id']}' AND quantidade='{$line['Quantidade']}' AND preco='{$line['ValUnit']}' AND data='$data'";
                    $id = $this->getId($modelName, $where);
                    if(!is_null($id))
                        $view['ds'][$i][$modelName.'_id'] = $id;
                    else
                        $view['ds'][$i][$modelName.'_id'] = $this->addLineOnBd($modelName, $where);
                endforeach;
                        
                unlink($fileJson['dataStructure']);
                HelperFile::jsonWrite($fileJson['dataStructure'], $view['ds'], TRUE);
            }
            $view['columns'] = array_keys($view['ds'][0]);
            $view['nextLevel'] =  new Helper_Link('csv', 'Importar lista', 'read', array('file'=>$file,'level'=>$level+1));
            $view['nextLevel']->setTexto("Próxima Etapa");
            $view['nextLevel']->setIsBotao();
            $view['nextLevel']->setTitle("Segue para a próxima etapa da importação");
            $view['nextLevel']->setPermitions(HelperAuth::getPermitionByType(PERMITION_LEVEL_PUBLIC));
            
            if($level>=4)
                $view['nextLevel'] = NULL;
            
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
    private function xlistContent(array $content, $toList){
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

    protected function setForm(){
        $this->_form = new Form_File();
    }


}

