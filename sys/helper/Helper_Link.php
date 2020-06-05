<?php

class Helper_Link
{

    private $ctrl;
    private $ctrl_alias;
    private $action;
    private $params;
    private $texto;
    private $title;
    private $link;
    private $icon;
    private $icon_path;
    private $newWindow;
    private $isModal;
    private $isBotao;
    private $class;
    private $class_button;
    private $showIfNoPermition;
    private $showToolTip;
    private $permitions;
    private $specialButtons;

    /**
     * Define parâmetros para gerar um código HTML para executar uma ação
     *
     * @param string $link
     *            ex: ctrl=trb&act=udt&id=99
     * @param string $texto
     *            ex: TITULO DO TRABALHO
     * @param string $title
     *            ex: Exclui tema do trabalho
     * @param array $permitions
     *            array(0, 1, 5);
     * @param boolean $isModal
     * @param boolean $isBotao
     */
    public function __construct($ctrl, $ctrl_alias, $act, array $params = NULL)
    {
        $this->ctrl = $ctrl;
        $this->ctrl_alias = $ctrl_alias;
        $this->action = $act;
        $this->params = $params;
        
        $this->texto = $ctrl_alias;
        $this->title = "{$this->getActionAlias()} {$this->ctrl_alias}";
        $this->showToolTip = TRUE;
        
        $this->newWindow = FALSE;
        $this->isBotao = FALSE;
        $this->isModal = FALSE;
        
        $this->class = NULL;
        $this->class_button = NULL;
        
        $this->showIfNoPermition = TRUE;
        $this->permitions = HelperAuth::getPermitionByType(PERMITION_LEVEL_ADMIN);
        
        $this->icon_path = 'layout/img/icons/';
        $this->specialButtons = Helper_Button::listButtonClasses();
    }

    public function __toString()
    {
        $this->makeLink();
        if (! empty($this->link) and ! is_null($this->link))
            $res = $this->getOpenLink() . $this->getCloseLink();
        else
            $res = (! $this->isBotao ? $this->texto : '');
        
        return $res;
    }

    private function makeLink()
    {
        if (isset($this->link))
            return TRUE;
        
        if (URL_FRIENDLY)
            $this->link = URI . "{$this->ctrl}/{$this->action}{$this->getArgs()}";
        else
            $this->link = "?ctrl={$this->ctrl}&act={$this->action}{$this->getArgs()}";
    }

    private function getArgs()
    {
        $args = array();
        
        if (! is_null($this->params)) {
            if (URL_FRIENDLY) {
                foreach ($this->params as $param => $value)
                    $args[] = "{$param}/{$value}";
                
                    $args = (!empty($args) ? '/' : NULL) . implode('/', $args);
            } else {
                foreach ($this->params as $param => $value)
                    $args[] = "{$param}={$value}";
                
                $args = (!empty($args) ? '&' : NULL) . implode('&', $args);
            }
        } else
            $args = null;
        
        return $args;
    }

    private function getActionAlias()
    {
        $alias = array(
            'index' => 'Lista',
            'view' => 'Visualiza',
            'detalhe' => 'Visualiza',
            'add' => 'Cadastra',
            'del' => 'Exclui',
            'udt' => 'Edita',
        );
        return (key_exists($this->action, $alias) ? $alias[$this->action] : $this->action);
    }

    private function getOpenLink()
    {
        if ($this->hasPermition()) {
            $link = "\n\t<a ";
            $link .= "href=\"{$this->link}\" ";
            $link .= "class=\"{$this->getClass()}".($this->isBotao ? " {$this->class_button} button" : NULL)."\" ";
            if (! $this->showToolTip)
                $link .= "title=\"$this->title\" ";
            if ($this->newWindow)
                $link .= "target=\"_blanck\"";
            $link .= ">\n\t\t";
        } else
            $link = '';
        
        return $link . $this->getContentLink();
    }

    private function getCloseLink()
    {
        return ($this->hasPermition() ? "\n\t</a>\n" : '');
    }

    private function getContentLink()
    {
        if ($this->isBotao) {
            $link = '<img src="'.URI.PATH_IMG.'transparency.png" alt="transparency.png">';
            if ($this->showToolTip)
                $link .= "<span class=\"ttp_text\"><b>{$this->texto}</b>{$this->title}</span>";
        } else {
            if ($this->hasPermition()){
                $link = "{$this->getIcon()}<span class=\"link_text\">{$this->texto}</span>";
                if ($this->showToolTip)
                    $link .= "<span class=\"ttp_text\"><b>{$this->texto}</b>{$this->title}</span>";
            }else {
                $link = ($this->showIfNoPermition ? $this->texto : '');
            }
        }
        
        return $link;
    }

    private function getClass()
    {
        $class = array();
        if ($this->isModal)
            $class[] = 'modal';
        if (! is_null($this->class))
            $class[] = $this->class;
        if ($this->showToolTip)
            $class[] = 'tooltip';
        return implode(' ', $class);
    }

    private function getText()
    {
        $res = (is_null($this->class_button) ? $this->texto : (! in_array($this->class_button, $this->specialButtons) ? $this->texto : ''));
        
        return $res;
    }

    public function hasPermition()
    {
        $auth = (is_null(HelperAuth::getAuth()) ? 999 : HelperAuth::getAuth());
        if (is_null($this->permitions))
            $res = TRUE;
        else {
            if (is_array($this->permitions))
                $res = in_array($auth, $this->permitions);
            else
                $res = is_numeric($this->permitions) and $this->permitions == $auth;
        }
        
        return $res;
    }

    /**
     * Modifica um parâmetro pré estabelecido
     * Os parâmetros são: texto, title, link, newWindow, isModal, isBotao, permitions, class, class_button
     *
     * @param string $param
     * @param string|boolean $value
     */
    public function changePreset($param, $value)
    {
        $this->$param = $value;
    }

    /**
     *
     * @return multitype:number
     */
    public final function getPermitions()
    {
        return $this->permitions;
    }

    /**
     *
     * @param mixed $ctrl
     */
    public function setCtrl($ctrl)
    {
        $this->ctrl = $ctrl;
    }

    /**
     *
     * @param mixed $ctrl_alias
     */
    public function setCtrl_alias($ctrl_alias)
    {
        $this->ctrl_alias = $ctrl_alias;
    }

    /**
     *
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     *
     * @param string $texto
     */
    public final function setTexto($texto)
    {
        $this->texto = $texto;
    }

    /**
     *
     * @param string $title
     */
    public final function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @param string $link
     */
    public final function setLink($link)
    {
        $this->link = $link;
    }

    /**
     *
     * @param boolean $newWindow
     */
    public final function setNewWindow($newWindow = TRUE)
    {
        $this->newWindow = $newWindow;
    }

    /**
     *
     * @param boolean $show
     */
    public function setNoToolTip($show = FALSE)
    {
        $this->showToolTip = $show;
    }

    /**
     *
     * @param boolean $isModal
     */
    public final function setIsModal($isModal = TRUE)
    {
        $this->isModal = $isModal;
    }

    /**
     *
     * @param boolean $isBotao
     */
    public final function setIsBotao($isBotao = TRUE)
    {
        $this->isBotao = $isBotao;
    }

    /**
     *
     * @param array|NULL $permitions
     */
    public final function setPermitions($permitions)
    {
        $this->permitions = $permitions;
    }

    /**
     *
     * @param string $class
     */
    public final function setClass($class)
    {
        $this->class = $class;
    }

    /**
     *
     * @param string $class_button
     */
    public final function setClass_Button($class_button)
    {
        $this->class_button = $class_button;
    }

    /**
     *
     * @param boolean $showIfNoPermition
     */
    public function setShowIfNoPermition($showIfNoPermition)
    {
        $this->showIfNoPermition = $showIfNoPermition;
    }

    /**
     *
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }
    
    private function getIcon(){
        return HelperFile::getSvgIcon("act_{$this->icon}");
    }
    
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
}

