<?php
include_once 'sys/start.class.php';
Start::run(TRUE);
include_once FILE_ERROR;
$error = NULL;

$controller = HelperNavigation::getController();
$action = HelperNavigation::getAction();

if(!file_exists(PATH_CONTROLLER."{$controller}_controller.php") AND $controller=='index')
    HelperNavigation::redirect('mkr_index', 'main');

$viewDefault = PATH_DEFAULT."view/{$action}.phtml";    
if(substr($controller, 0, 3)=='mkr'){
    $ctrlFile = PATH_MAKER."controller/{$controller}_controller.php";
    $viewFile = PATH_MAKER."view/{$controller}/{$action}.phtml";
}else{
    $ctrlFile = PATH_CONTROLLER."{$controller}_controller.php";
    $viewFile = PATH_VIEW."{$controller}/{$action}.phtml";
}

if(!file_exists($ctrlFile))
    $error = new _error(ERROR_NO_CONTROLLER_FILE, $ctrlFile);
else{
    include_once $ctrlFile;
    if(!class_exists($controller))
        $error = new _error(ERROR_NO_CONTROLLER_CLASS, $controller);
    else{
        $ctlr = new $controller;
        if(!method_exists($ctlr, $action))
            $error = new _error(ERROR_NO_ACTION, $action);
        else{
            if(!file_exists($viewFile)){
                if(file_exists($viewDefault)){
                    $ctlr->$action();
                    define('VIEW_FILE', $viewDefault);
                    
                    if(HelperView::getRender())
                        include_once FILE_LAYOUT;
                    else
                        include_once VIEW_FILE;
                }else{
                    $error = new _error(ERROR_NO_VIEW_FILE, $viewFile);
                }
            }else{
                $ctlr->$action();
                define('VIEW_FILE', $viewFile);
                if(HelperView::getRender())
                    include_once FILE_LAYOUT;
                else
                    include_once VIEW_FILE;
            }
        }
    }
}
if(!is_null($error)){
    $error->index();
    define('VIEW_FILE', FILE_VIEW_ERROR);
    include_once FILE_LAYOUT;
}