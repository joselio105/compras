<?php
$usr = HelperAuth::getUser();

$itens = HelperFile::jsonRead(PATH_MENU.'restrict.json');
if(!$usr)
    $itens = HelperFile::jsonRead(PATH_MENU.'public.json');
if(substr(HelperNavigation::getController(), 0, 3)=='mkr')
    $itens = HelperFile::jsonRead(PATH_MAKER.'menu/menu.json');

if(!empty($itens)){
    $menu = new Helper_Menu();
    $menu->setMenuItens($itens);
    echo $menu;
}