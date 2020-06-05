<?php

final class Boot{
    
    private $site_title;    
    private $site_subtitle;    
    private $title_prepend;    
    private $title_append;    
    private $meta;

    public function __construct()
    {
        header('Content-Type: text/html; charset=UTF-8');
        setlocale(LC_TIME, 'pt_BR', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        session_start();
        $this->getConfig();
    }

    /**
     * Exibe as metatags registradas
     */
    public function __toString()
    {
        $meta = $this->meta;
        $tags = (isset($meta['author']) ? "<meta name=\"author\" content=\"{$meta['author']}\"/>\n\t\t" : null);
        $tags .= (isset($meta['description']) ? "<meta name=\"description\" content=\"{$meta['description']}\"/>\n\t\t" : null);
        $tags .= (isset($meta['key_words']) ? "<meta name=\"keywords\" content=\"{$meta['key_words']}\"/>" : null);
        return $tags;
    }

    /**
     * Exibe o título do site
     */
    public function print_title()
    {
        $title = $this->site_title;
        $title = (isset($this->title_prepend) ? $this->title_prepend . " | {$title}" : $title);
        $title = (isset($this->title_append) ? "{$title} - " . $this->title_append : $title);
        echo "<title>{$title}</title>\n";
    }

    /**
     * Seta o prefixo do título
     *
     * @param String $title_prepend
     */
    public function set_title_prepend($title_prepend)
    {
        $this->title_prepend = $title_prepend;
    }

    /**
     * Seta o sufixo do título
     *
     * @param String $title_append
     */
    public function set_title_append($title_append)
    {
        $this->title_append = $title_append;
    }

    // GETs
    /**
     * Retorna o título do site
     *
     * @return String
     */
    public function get_site_title()
    {
        return $this->site_title;
    }

    /**
     * Retorna o subtítulo do site
     *
     * @return String
     */
    public function get_site_subtitle()
    {
        return $this->site_subtitle;
    }

    /**
     * Retorna as metatags do site
     *
     * @return array
     */
    public function get_metas()
    {
        return $this->meta;
    }
    
    /**
     * Coleta os atributos do site
     */
    private function getConfig(){
        $this->site_title = 'Nome do Site';
        $this->site_subtitle = 'Breve descrição';
        $this->title_prepend = null;
        $this->title_append = null;
        $this->meta['description'] = null;
        $this->meta['key_words'] = '';
        $site_prefix = 'zero_';
        
        if (file_exists(FILE_CONFIG_SITE)) {
            $info = HelperFile::jsonRead(FILE_CONFIG_SITE);
            $this->site_title = $info['site_title'];
            $this->site_subtitle = $info['site_subtitle'];
            $this->meta['author'] = $info['site_author'];
            $this->meta['description'] = $info['description'];
            $this->meta['key_words'] = $info['key_words'];
        }else
            HelperView::setAlert("O arquivo ".FILE_CONFIG_SITE." não existe!");
         
    }
}