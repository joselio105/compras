<?php

final class HelperFile{
    private static $model;
    private static $proprietys;
    private static $name;
    private static $path;
    private static $error_msg;
    private static $logComplement;
    private static $logKey;
    
    /**
     * Incorpora ao código HTML o ícone SVG escolhido
     * @param string $icon_name
     * @param string $path
     * @return string|NULL
     */
    public static function getSvgIcon($icon_name, $path=NULL){
        $path = (is_null($path) ? PATH_IMG.'icons/' : $path);
        $filename = "{$path}".basename($icon_name, '.svg').".svg";
        
        if(file_exists($filename))
            return "\n\t".implode("\t", file($filename));
        else 
            return NULL;
    }
    
    /**
     * Cria um caminho caso ele ou parte dele não exista
     * @param string $path
     */
    public static function create_path($path){
        $brk = explode('/', $path);
        $new_dir = "";
        foreach ($brk as $dir){
            if($dir!='')
                $new_dir .= "{$dir}/";
                if(!file_exists($new_dir))
                    mkdir($new_dir);
        }
    }
    
    /**
     * Retorna a mensagem referente ao erro ocorrido com o upload do arquivo
     * @param integer $error
     * @return string
     */
    public static function getError($error){
        self::$error_msg = array(
            1=>'UPLOAD_ERR_INI_SIZE: O arquivo enviado excede o limite definido na diretiva upload_max_filesize do php.ini.',
            2=>'UPLOAD_ERR_FORM_SIZE: O arquivo excede o limite definido em MAX_FILE_SIZE no formulário HTML. ',
            3=>'UPLOAD_ERR_PARTIAL: O upload do arquivo foi feito parcialmente. ',
            4=>'UPLOAD_ERR_NO_FILE: Nenhum arquivo foi enviado.',
            6=>'UPLOAD_ERR_NO_TMP_DIR: Pasta temporária ausênte. ',
            7=>'UPLOAD_ERR_CANT_WRITE: Falha em escrever o arquivo em disco. ',
            8=>'UPLOAD_ERR_EXTENSION: Uma extensão do PHP interrompeu o upload do arquivo. O PHP não fornece uma maneira de determinar qual extensão causou a interrupção. Examinar a lista das extensões carregadas com o phpinfo() pode ajudar.',
        );
        return self::getFileError($error);
    }
    
    /**
     * Retorna os métodos de uma dada classe
     * @param string $class
     * @return array
     */
    public static function getMethods($class) {
        $classes = array('__construct', '__toString');
        $res = array();
        
        $maker = 'mkr_';
        if(substr($class, 0, strlen($maker))==$maker)
            $filename = PATH_MAKER."controller/{$class}_controller.php";
        else
            $filename = PATH_CONTROLLER."{$class}_controller.php";
                
        if(file_exists($filename)){
            include_once $filename;
            if(class_exists($class)){
                $res = get_class_methods($class);
                        
                foreach ($classes as $noClass):
                    if(is_numeric(array_search($noClass, $res)))
                        unset($res[array_search($noClass, $res)]);
                endforeach;
            }
        }
        return $res;
    }
    
    /**
     * Transfere todos os aquivos em um dada pasta(origem) para outra (destino)
     * @param string $origem
     * @param string $destino
     */
    public static function move_pasta($origem, $destino){
        self::create_path($destino);
        $arquivos = glob($origem."*.*");
        if(file_exists($origem)){
            foreach ($arquivos as $f):
                $file_name = substr($f, strlen($origem));
                rename($f, $destino.$file_name);
            endforeach;
            rmdir($origem);
        }
    }
    
    /**
     * Copia um arquivo (ou o conteÃºdo de uma pasta) para um destino especificado
     * @param string $origem
     * @param string $destino
     */
    public static function copy($origem, $destino){
        $log = array();
        $copy = array();
        $fileName = array();
        
        if(is_file($origem)){
            $copy[0] = $origem;
            $fileName[0] = null;  
            $bkr = explode('/', $destino);
            $file = $bkr[count($bkr)-1];
            $path = substr($destino, 0, -(strlen($file)));
            self::create_path($path);
        }
        if(is_dir($origem)){
            $origem = (substr($origem, 0, -1)!='/'?$origem.'/':$origem);
            $destino = (substr($destino, 0, -1)!='/'?$destino.'/':$destino);
            self::create_path($destino);
            
            $copy = glob($origem."*.*");
            $log['origem'] = $origem;
            /*$log['destino']= $destino;
            $log['copy'] = $copy;*/
            foreach ($copy as $i=>$c)
                $fileName[$i] = substr($c, strlen($origem));
        }
        
        foreach ($copy as $i=>$c):
            $log[$i] = "Copiando {$c} para {$destino}{$fileName[$i]}";
            
            if(!copy($c, $destino.$fileName[$i]))
                HelperView::setAlert("ERRO AO COPIAR ARQUIVO: {$origem}");
            else 
                $log[$i].= " - OK";
        endforeach;
        
        if(strpos($origem, 'index.php'))
            return $log;
        else 
            return TRUE;
    }
    
    /**
     * Transfere o arquivo recebido (caminho completo) para a pasta tcc/_trash
     * @param string $file
     */
    public static function trash_file($file){
        $file = self::getFileName($file);
        $destino = str_replace('tcc/', 'tcc/_trash/', $file['path']);
        self::create_path($destino);
        
        rename("{$file['path']}/{$file['name']}", "{$destino}/{$file['name']}");
    }
    
    /**
     * Substitui o conteÃºdo de um arquivi
     * @param string $file
     * @param string $search - REGEX
     * @param string $replace
     * @return
     */
    public static function replaceInFile($file, $search, $replace){
        $count = array();
        $arquivo = fopen($file, "r+");
        
        if ($arquivo) {
            $novo_buffer = '';
            $line = 0;
            
            while (!feof($arquivo)) {
                $buffer = fgets($arquivo, filesize($file));
                $novo_buffer .= str_replace($search, $replace, $buffer, $count[$line]);
                //$novo_buffer .= preg_replace($search, $replace, $buffer, -1, $count);
                $line++;
            }
            //var_dump($novo_buffer, $count);die;
            ftruncate($arquivo, 0);
            rewind($arquivo);
            fwrite($arquivo, $novo_buffer);
            fclose($arquivo);
            
            return array_sum($count);
        }
    }
    
    /**
     * Faz o download do arquivo recebido (caminho completo)
     * @param string $file
     * @param string $downAs
     */
    public static function downloadFile($file, $downAs=null){
        $file = self::getFileName($file);
        $downAs = (is_null($downAs)?"TCC-ARQ-UFSC-DOWNLOAD_{$file['name']}":$downAs);
        
        //header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="'.$downAs.'"');
        header('Content-Type: '.mime_content_type($file['fullPath']));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file['fullPath']));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        
        var_dump(readfile($file['fullPath'])); // lÃª o arquivo
    }
    
    /**
     * Recupera o nome do arquivo em um caminho completo
     * @param string $file
     * @return array
     */
    public static function getFileName($file){
        $res = array();
        
        $brk = explode('/', $file);
        $res['fullPath'] = $file;
        $res['name'] = $brk[count($brk)-1];
        unset($brk[count($brk)-1]);
        $res['path'] = implode('/', $brk);
        
        $brk = explode('.', $res['name']);
        $res['extension'] = $brk[count($brk)-1];
        
        return $res;
    }
    
    /**
     * Lista Classes em um dado diretório, informando nome do arquivo e nome da classe
     * @param string $dir
     * @return array
     */
    public static function listClassesOnDir($dir){
        $class = array();
        
        foreach (self::readDir($dir) as $i=>$model):
            $class[$i]['file'] = $model;
            $class[$i]['name'] = HelperFile::getClassName($model);
        endforeach;
        
        return $class;
    }
    
    /**
     * Lista Classes em um dado diretório
     * @param string $dir
     * @param string $firstElement
     * @return string[]
     */
    public static function listClasses($dir, $firstElement='Nenhum'){
        $res = array();
        
        if(!is_null($firstElement))
            $res[''] = $firstElement;
            
        foreach (self::readDir($dir) as $class)
            $res[self::getClassName($class)] = self::getClassName($class);
                
        return $res;
    }
    
    public static function listAllControllers(){
        $res = array();
        
        $res = self::listClasses(PATH_CONTROLLER, NULL);
        /*$res['maker'] = self::listClasses(PATH_MAKER.'controller/', NULL);
         $res = array_merge($res['custom'], $res['maker']);*/
        
        return $res;
    }
    
    public static function listAllActions(){
        $res = array();
        
        foreach (self::listAllControllers() as $controller):
        foreach (self::getMethods($controller) as $action)
            $res["{$controller}/{$action}"] = "{$controller}/{$action}";
            endforeach;
            
            return $res;
    }
    
    /**
     * Retorna um array com todos os arquivos .php de um dado diretório
     * @param string $dir
     * @return array
     */
    public static function readDir($dir){
        return glob($dir.'*.php');
    }
    
    /**
     * Retorna o nome da classe de um dado arquivo
     * @param string $filename
     * @return string
     */
    public static function getClassName($filename){
        foreach (file($filename) as $line):
        if(strstr($line, 'class '))
            $brk = explode(' ', $line);
            endforeach;
            
            $terms = array('abstract', 'final', 'class');
            foreach ($terms as $term):
            if(is_numeric(array_search($term, $brk)))
                unset($brk[array_search($term, $brk)]);
                endforeach;
                
                $res = trim(array_shift($brk));
                
                return (substr($res, -1)=='{' ? substr($res, 0, -1) : $res);
    }
    
    /**
     * Retorna um array com informaÃ§Ãµes sobre os arquivos de um caminho especificado e dos arquivos em suas subpastas
     * @param string $path - O Caminho a ser analizado
     * @return string[][]
     */
    public static function getFilesInfo($path){
        $dirIt = new RecursiveTreeIterator(new RecursiveDirectoryIterator($path));
        
        
        $k = 0;
        $files = array();
        foreach ($dirIt as $fileName=>$dirTree):
            if(is_file($fileName)){
                $files[$k] = self::getFileInfo($fileName);
                $k++;
        }
        endforeach;
        
        return $files;
    }
    
    /**
     * Retorna localizaÃ§Ã£o, nome, tamanho e data de modificaÃ§Ã£o de um dado arquivo
     * @param string $fileName - Nome de um arquivo, incluindo sua localizaÃ§Ã£o
     * @return string[]
     */
    public static function getFileInfo($fileName){
        $fileName = str_replace('\\', '/', $fileName);
        $file = explode('/', $fileName);
        $length = -1 * strlen($file[count($file)-1]);
        
        $files = array(
            'nulo'=>$fileName,
            'path'=>substr($fileName, 0, $length),
            'name'=>$file[count($file)-1],
            'size'=>filesize($fileName),
            'chng'=>filemtime($fileName),
        );
        
        return $files;
    }
    
    /**
     * Retorna uma listagem das pastas de um dado caminho, exceto aquelas listadas no parÃ¢metro $prohibed
     * @param string $path
     * @param string[]
     * @return string[]
     */
    public static function getDirs($path, array $prohibed=null){
        $dirIt = new RecursiveTreeIterator(new RecursiveDirectoryIterator($path));        
        
        $k = 0;
        $files = array();
        foreach ($dirIt as $fileName=>$dirTree):
        if(is_dir($fileName) AND !self::isDot($fileName)){
            $files[$k] = str_replace('\\','/', $fileName);
            $k++;
        }
        endforeach;        
        
        if(!is_null($prohibed)){
            $dirs = $files;
            $files = array();
            
            foreach ($prohibed as $p):
                $pattern = "/^({$p}{1})(.*)/";
                foreach ($dirs as $d):
                    if(preg_match($pattern, $d)!=1)
                        $files[] = $d;
                endforeach;
            endforeach;
        }
        
        return $files;
    }
    
    /**
     * Escreve um log
     * @param string $type - tipo de log
     *        Tipos disponÃ­veis:
     *            - download
     * @param array $data
     * @return string
     */
    public static function writeLog($type, array $data, $controller=NULL, $action=NULL){
        $info = array();
        $filePath = PATH_LOGS;
        $fileName = $type.'.json';
        $usr = HelperAuth::getUser();        
        $when = date('YmdHis');
        $k = (isset(self::$logKey) ? count(self::$logKey) : 0);
        self::$logKey[$k] = $when.$usr['id'].$k;
        $key = self::$logKey[$k];
        $info[$key] = array(
            'when'=>$when,
            'who'=>"{$usr['nome']} ({$usr['id']})",
            'IP'=>$_SERVER['REMOTE_ADDR']
        );
        $controller = (!is_null($controller) ? $controller : HelperNavigation::getController());
        $action = (!is_null($action) ? $action : HelperNavigation::getAction());
        switch ($type){
            case 'action':
                /*
                 * Algumas inconsistÃªncias no ler e escrever o JSON
                 * Ao testar com addTrb o Log estÃ¡ OK, mas no complemento, o addTrb_Usr nÃ£o Ã© registrado no LOG
                 */
                $res = self::getLogInfo($data, $controller, $action);
                
                $info[$key]['action']       = $action;
                $info[$key]['controller']   = $controller;
                $info[$key]['acao']         = $res['acao'];
                $info[$key]['item']         = $res['item'];
                $info[$key]['ident']        = $res['ident'];
                break;
                
            case 'acesso':
                $info[$key]['action'] = (empty($data)?HelperNavigation::getAction():$data[0]);
                break;
                
            case 'download':
                $info[$key]['fileTitle']  = $data['titulo'];
                $info[$key]['fileName']   = $data['img'];
                $info[$key]['filePath']   = "tcc/{$data['sem']}/{$data['trb_id']}/";
                $info[$key]['trb_id']     = $data['trb_id'];
                $info[$key]['trbTitle']   = $data['trabalho'];
                
                break;
                
            case 'sysversion':
                $info = array();
                
                foreach ($data as $d):
                    $info[$when][self::getFileID($d['nulo'])]['nulo']       = $d['nulo'];   
                    $info[$when][self::getFileID($d['nulo'])]['name']       = $d['name'];   
                    $info[$when][self::getFileID($d['nulo'])]['path']       = $d['path'];
                    $info[$when][self::getFileID($d['nulo'])]['size']       = $d['size'];
                    $info[$when][self::getFileID($d['nulo'])]['chng']       = $d['chng'];
                    $info[$when][self::getFileID($d['nulo'])]['situation']  = $d['situation'];
                endforeach;
                
                break;
        }
        
        //Escreve novos registros
        self::jsonWrite($filePath.$fileName, $info);
        
        return $when;        
    }
    
    /**
     * Escreve um array num dado arquivo json
     * @param string $jsonFile
     * @param array $info
     * @return number
     */
    public static function jsonWrite($jsonFile, array $info, $overwrite=FALSE){
        $infoOld = array();
        
        if(file_exists($jsonFile))
            $infoOld = self::jsonRead($jsonFile);
        else
            self::create_path(pathinfo($jsonFile, PATHINFO_DIRNAME).'/');
                
        //Junta os registros atuais com os anteriores
        if(!empty($infoOld) AND !$overwrite)
            array_push($info, $infoOld);
                
        //Transforma o array de registros em jSon
        if(phpversion()<'5.3')
            $jsonData = json_encode($info);
        else
            $jsonData = json_encode($info, JSON_PRETTY_PRINT | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG);
                      
        return file_put_contents($jsonFile, $jsonData);                
                
    }
    
    /**
     * Recupera no formato array os dados de um arquivo json
     * @param string $filename
     * @return array
     */
    public static function jsonRead($filename){
        $res = array();
        $string = '';
        
        if(file_exists($filename)){
            foreach (file($filename) as $line)
                $string.= $line;
        }
        $res = json_decode($string, TRUE);
        
        return (!is_null($res) ? $res : array());
    }
    
    /**
     * Recupera as ocorrÃªncias de um Log
     * @param string $logFile
     * @return array|NULL
     */
    public static function getLog($logFile){
        if(file_exists($logFile))
            return self::formatLog($logFile);
        else 
            return NULL;
    }
    
    /**
     * Lê o arquivo de log especificado
     * @param string $logName
     * @param boolean $recentFirst
     * @return boolean|array
     */
    public static function readLog($logName, $recentFirst=TRUE){
        $res = FALSE;
        
        $filename = PATH_LOGS."{$logName}.json";
        
        if(!file_exists($filename))
            return FALSE;
        else
            $res = self::readJson($filename);
        
        if($recentFirst)
            krsort($res);
        
        return $res;
        
    }
    
    /**
     * Retorna um id em base 64 a partir do caminho e nome do arquivo
     * @param string $filename
     * @return string
     */
    public static function getFileID($filename){
        $bkr = explode('/', $filename);
        unset($bkr[0]);
        
        return base64_encode(implode('', $bkr));           
    }
    
    /**
     * Retorna o tipo MIME do arquivo
     * @param string $file
     * @return boolean|string
     */
    public static function getFileType($file){
        $fileTypes = array(
            'pdf'=>'application/pdf',
            'png'=>'image/png',
            'jpg'=>'image/jpeg'
        );
        $brk = explode('.', $file);
        $extension = $brk[count($brk)-1];
        
        return (key_exists($extension, $fileTypes) ? $fileTypes[$extension] : FALSE);        
    }
    
    /**
     * Retorna dados do usuÃ¡rio
     * @param integer $usr_id
     * @return string
     */
    private static function getUsrToLog($usr_id){
        $model = NULL;//new Model_Usr();
        $usr = $model->read_one("tbl.id={$usr_id}");
        return "<a class=\"tooltip\">{$usr['nome']}</a>";
    }
    
    private static function getWhat($log){
        if(key_exists('action', $log)){
            if(key_exists('controller', $log)){
                $ctrl = self::getControllerInfo($log['controller']);
                $what = array();
                $what[0] = self::getAction($log['action']);
                $what[1] = $ctrl['name'];
                if(!is_null($ctrl['mainField'])){
                    $data = self::getModelElement($log['controller'], $log[$log['controller'].'_id']);
                    $limit = 80;
                    if($data)
                        $what[2] = ' <b>'.(strlen($data[$ctrl['mainField']])>$limit?substr($data[$ctrl['mainField']], 0, $limit).'...':$data[$ctrl['mainField']]).'</b>';
                        else
                            $what[2] = "<b>{$log[$log['controller'].'_id']}</b>";
                }
                //var_dump($log['when'],$log[$ctrl['super'].'_id']);
                if(!is_null($log[$ctrl['super'].'_id'])){
                    if(preg_match('/^([0-9]{4})\-([1|2]{1})$/', $log[$ctrl['super'].'_id'])==1){
                        $what[3] = "do semestre <b>{$log[$ctrl['super'].'_id']}</b>";
                    }else{
                        $ctrlSuper = self::getControllerInfo($ctrl['super']);
                        $dataSuper = self::getModelElement($ctrl['super'], $log[$ctrl['super'].'_id']);
                        $what[3] = " no {$ctrlSuper['name']} <b>{$dataSuper[$ctrlSuper['mainField']]}</b>";
                    }
                }
                if($log['controller']=='bnc' AND $data){
                    $what[1] = '<b>'.self::getBnc_tipo($data[$ctrl['mainField']]).'</b>';
                    unset($what[2]);
                }
                if($ctrl['super']=='bnc' AND $dataSuper){
                    $what[3] = "da <b>".self::getBnc_tipo($dataSuper[$ctrlSuper['mainField']]).'</b>';
                }
                
                $what = implode(' ', $what);
            }else
                $what = ($log['action']=='logout'?'Saiu da ':'Entrou na ')."Ãƒï¿½rea restrita do Site";
        }elseif(key_exists('fileTitle', $log))
        $what = "Baixou o Arquivo {$log['fileName']}";
        else
            $what = "Baixou o Certificado";
            
            return $what;
            
    }
    
    private static function getModelElement($ctrl, $id){
        if(empty($ctrl) OR is_null($ctrl))
            return FALSE;
        $modelName = ucfirst($ctrl).'_Model';
        $model = new $modelName;
        return $model->read_one("tbl.id={$id}");
    }
    
    /**
     * Lê um arquivo JSON e retorna um array desse
     * @param string $filename
     * @return array
     */
    private static function readJson($filename){
        $array = array();
        if(file_exists($filename)){
            $jsonFile = fopen($filename, 'r');
            $string = fread($jsonFile, filesize($filename));
            $array = json_decode($string, TRUE);
            fclose($jsonFile);
        }
        return $array;
    }
    
    private static function formatLog($filename){
        $log = array();
        foreach (self::readJson($filename) as $i=>$l):
            $log[$i]['when'] = HelperData::showData($l['when']);
            $log[$i]['IP'] = $l['IP'];
            $log[$i]['usr'] = self::getUsrToLog($l['who']);
            $log[$i]['what'] = self::getWhat($l);
            $log[$i]['all'] = $l;
        endforeach;
        
        $log = HelperView::orderTable($log, array('orderBy'=>'when', 'order'=>'DESC'));
        
        return $log;
    }
    
    /**
     * Verifica se a pasta enviada Ã© . ou ..
     * @param string $dir
     * @return boolean
     */
    private static function isDot($dir){
        $pattern = '/(.*)([\.]{1,2})$/';
        if(preg_match($pattern, $dir)!=0)
            return TRUE;
        else 
            return FALSE;
        
    }
    
    /**
     * Seta o complemento do log
     * @param array $data
     * @return NULL[]
     */
    private static function getLogComplement(array $data){
        self::$logComplement = array(
            'id'=>0,
            'quantidade'=>0,
            'usr_id'=>NULL,
            'nome'=>NULL,
            'mercadoria'=>0,
            'produto'=>0,
            'produto_nome'=>NULL,
            'preco'=>0,
            'data'=>NULL,
            'capacidade'=>0,
            'tipo'=>NULL,
            'unidade'=>0,
            'produto'=>0,
            'embalagem'=>0,
            'sigla'=>NULL
        );
        
        foreach ($data as $field=>$value):
            if(key_exists($field, self::$logComplement))
                self::$logComplement[$field] = $value;
        endforeach;
        
        return self::$logComplement;
    }
    
    /**
     * Retorna informaÃ§Ãµes relativa a aÃ§Ã£o realizada
     * @param array $data;
     * @return string[]
     */
    private static function getLogInfo(array $data, $controller, $action){
        //var_dump($data);die;
        $complement = self::getLogComplement($data);
        //var_dump($complement);die;
        $actions = array(
            'add'=>'Cadastrou',
            'udt'=>'Alterou',
            'del'=>'Excluiu',
            'check'=>'Comprou',
            'senha'=>'Alterou a senha do',
        );
        
        $controllers = array(
            'index'=>array(
                'name'=>"{$complement['produto_nome']} ({$complement['produto']})",
                'ident'=>" na lista de compras"
            ),
            'emb'=>array(
                'name'=>'embalagem',
                'ident'=>" {$complement['nome']} ({$complement['id']})"
            ),
            'mcd'=>array(
                'name'=>"mercadoria",
                'ident'=>" {$complement['nome']} ({$complement['id']})"
            ),
            'pdt'=>array(
                'name'=>'produto',
                'ident'=>" {$complement['nome']} ({$complement['id']})"
            ),
            'pdt_tp'=>array(
                'name'=>'tipo de produto',
                'ident'=>" {$complement['nome']} ({$complement['id']})"
            ),
            'und'=>array(
                'name'=>"unidade",
                'ident'=>" {$complement['nome']} - {$complement['sigla']} ({$complement['id']})"
            ),
        );
            
        return array(
            'item'=>$controllers[$controller]['name'],
            'acao'=>$actions[$action],
            'ident'=>$controllers[$controller]['ident'],
        );
    }
}
