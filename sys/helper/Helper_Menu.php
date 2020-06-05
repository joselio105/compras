<?php

final class Helper_Menu
{
    private $menuID;
    private $menuType;
    private $ctlr;
    private $act;
    private $params;
    private $title;
    private $icon;
    private $permitions;
    private $newWindow;
    private $modal;
    private $showIfNoPermition;
    private $itens;
    private $defaultPermitions;
    private $defaultAct;

    public function __construct()
    {
        $this->defaultAct = 'index';
        $this->defaultPermitions = array(
            0
        );
        
        $this->menuType = 'list';
    }

    public function setMenuItens(array $menuItens)
    {
        // var_dump($menuItem);die;
        foreach ($menuItens as $itemName => $menuItem) :
            if (is_array($menuItem))
                $this->setMenuItem($itemName, $menuItem);
        endforeach
        ;
    }

    public function setMenuItem($itemName, array $menuItem)
    {
        if ($this->checkItem($menuItem)) {
            $this->ctlr[$itemName] = $menuItem['ctlr'];
            $this->title[$itemName] = $menuItem['title'];
            $this->act[$itemName] = (key_exists('act', $menuItem) ? $menuItem['act'] : $this->defaultAct);
            $this->params[$itemName] = (key_exists('params', $menuItem) ? $this->setParams($menuItem['params']) : NULL);
            $this->icon[$itemName] = (key_exists('icon', $menuItem) ? $menuItem['icon'] : NULL);
            $this->permitions[$itemName] = (key_exists('permitions', $menuItem) ? $menuItem['permitions'] : $this->defaultPermitions);
            $this->newWindow[$itemName] = (key_exists('newWindow', $menuItem) ? $menuItem['newWindow'] : FALSE);
            $this->modal[$itemName] = (key_exists('modal', $menuItem) ? $menuItem['modal'] : FALSE);
            $this->showIfNoPermition[$itemName] = (key_exists('showText', $menuItem) ? $menuItem['showText'] : FALSE);
        }
    }

    public function setMenuID($id)
    {
        $this->menuID = $id;
    }

    public function __toString()
    {
        if (! $this->setItens())
            HelperView::setAlert("ERRO ao Criar Menu - Nenhum item foi determinado!");
        
        $menu = $this->getMenuTag();
        
        foreach ($this->itens as $item) :
            if ($item->hasPermition()) {
                $menu .= $this->getItemTag();
                $menu .= "\t\t{$item}";
                $menu .= $this->getItemTag(TRUE);
            }
        endforeach
        ;
        $menu .= $this->getMenuTag(TRUE);
        return $menu;
    }

    public function setMenuAsItem()
    {
        $this->menuType = 'itens';
    }

    /**
     * Verifica se o item do menu apresenta os parâmetros necessários
     *
     * @param array $item
     * @return boolean
     */
    private function checkItem(array $item)
    {
        $msg = null;
        if (! key_exists('ctlr', $item))
            $msg = "ERRO ao Criar Menu - É necessário especificar o controller(ctlr) do item!";
        
        if (! key_exists('title', $item))
            $msg = "ERRO ao Criar Menu - É necessário especificar um título para o item!";
        
        if (! is_null($msg)) {
            HelperView::setAlert($msg);
            return FALSE;
        } else
            return TRUE;
    }

    /**
     * Formata os parâmetros do link especificado
     *
     * @param string|array $params
     * @return string
     */
    private function setParams($params)
    {
        return $params;
    }

    /**
     * Checa se os parâmetros foram especificados e constroi os itens do menu
     *
     * @return boolean
     */
    private function setItens()
    {
        if (empty($this->ctlr) or is_null($this->ctlr) or ! isset($this->ctlr))
            return FALSE;
        
        foreach ($this->ctlr as $itemName => $ctlr) :
            $this->itens[$itemName] = new Helper_Link($ctlr, $itemName, $this->act[$itemName], $this->params[$itemName]);
            $this->itens[$itemName]->setTitle($this->title[$itemName]);
            $this->itens[$itemName]->setIcon($this->icon[$itemName]);
            $this->itens[$itemName]->setpermitions($this->permitions[$itemName]);
            $this->itens[$itemName]->setNewWindow($this->newWindow[$itemName]);
            $this->itens[$itemName]->setIsModal($this->modal[$itemName]);
            $this->itens[$itemName]->setShowIfNoPermition($this->showIfNoPermition[$itemName]);
        endforeach
        ;
        
        return TRUE;
    }

    /**
     * Retorna a tag para abrir ou fechar o menu
     *
     * @param boolean $close
     * @return string
     */
    private function getMenuTag($close = FALSE)
    {
        $id = ((!$close AND isset($this->menuID)) ? " id=\"{$this->menuID}\"" : NULL);
        switch ($this->menuType) {
            default:
                $tag = "\n<ul{$id}>\n";
                break;
            
            case 'itens':
                $tag = "\n<menu{$id}>\n";
                break;
        }
        
        return ($close ? str_replace("\n<", '</', $tag) : $tag);
    }

    /**
     * Retorna a tag para abrir ou fechar o item do menu
     *
     * @param boolean $close
     * @return string
     */
    private function getItemTag($close = FALSE)
    {
        switch ($this->menuType) {
            default:
                $tag = "\t<li>\n";
                break;
            
            case 'itens':
                $tag = "<menuitem>\n";
                break;
        }
        
        return ($close ? str_replace('<', '</', $tag) : $tag);
    }
}

