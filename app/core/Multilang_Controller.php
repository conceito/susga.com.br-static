<?php

if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

/**
 * Controller utilizado para quando o site necessitar ser Multilingue.
 * Como configurar:
 * 1) O controller principal deve extender este controller (Multilang_controller)
 * 2) Ativar as rotas em config/routes.php
 * 3) Configurar o array $languages com as línguas utilizadas
 * 4) Adicionar os dicionários nas respectivas pastas dentro de 'app/language/'
 * 5) Atraves da função lang('chave') o dicionário é atualizado.
 * 6) Habilitar o helper 'MY_url_helper.php'
 */
class Multilang_Controller extends MY_Controller
{
    /*
     * Configurar neste array as línguas disponíveis.
     */
    public $languages = array(
        'pt' => array('pt-BR', 'Português'),
        'en' => array('english', 'English'),
        'fr' => array('french', 'France'),
        'es' => array('spanish', 'Español')
    );
    /*
     * Variável que contém a chave da língua ativa. 'pt' é padrão
     */
    public $lng = 'pt';

    function __construct()
    {
        parent::__construct();

        /*
         * Carregando a língua
         */
        $this->lng = $this->getLangKey();
    }

    /**
     * Esta funçao é responsável por carregar os dicionários.
     * Todos os dicionários devem ser listados como o exemplo:
     * $this->lang->load('NOMEDODICIONARIO', $this->languages[$this->getLang()]);
     * Os dicionários ficam na pasta: app/language/"pasta_idioma"/dic_lang.php
     *
     * *IMPORTANTE: Esta função deverá ser chamada no construtor de todos os controllers.
     *
     * @return string
     */
    public function setLang()
    {
        $this->load->helper('language');
        $langFolder = $this->languages[$this->getLangKey()][0];

        $this->lang->load('dic', $langFolder);
        $this->lang->load('form_validation', $langFolder);

        /*
         * Definindo setLocale de acordo com a língua ativa
         */
        if ($this->getLangKey() == 'pt')
        {
            setlocale(LC_ALL, "pt_BR.utf-8", "ptb", "ptb.utf-8");
        }
        else
        {
            if ($this->getLangKey() == 'en')
            {
                setlocale(LC_ALL, "En-Us");
            }
            else
            {
                if ($this->getLangKey() == 'fr')
                {
                    setlocale(LC_ALL, "fr_FR");
                }
                else
                {
                    if ($this->getLangKey() == 'es')
                    {
                        //            setlocale(LC_TIME, "esp");
                        setlocale(LC_ALL, 'es-ES', "esp");
                    }
                }
            }
        }

    }

    /**
     * Retorna as letras do idioma. Se não existe seta "pt" como padrão.
     *
     * @return string
     */
    public function getLangKey()
    {

        $langSeg = $this->uri->segment(1);

        if (strlen($langSeg) != 2)
        {
            $langSeg = 'pt';
        }

        return $langSeg;
    }

    /**
     * Retorna o nome da língua ativa: Português, English...
     * @return string
     */
    public function getLangName()
    {

        return $this->languages[$this->getLangKey()][1];

    }


    /**
     * <code>
     * return array(
     *  'folder'      => 'pt-BR',
     *  'label'       => 'Português',
     *  'home_url'    => 'http://site.com/pt',
     *  'current_url' => 'http://site.com/pt/contato'
     * );
     * </code>
     * @return array
     */
    public function getLanguages()
    {
        $langs  = $this->languages;
        $return = array();
        foreach ($langs as $k => $l)
        {
            $return[$k] = array(
                'folder'      => $l[0],
                'label'       => $l[1],
                'home_url'    => base_url() . $k,
                'current_url' => $this->changeLangCurrentUrlTo($k)
            );
        }

        return $return;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function changeLangCurrentUrlTo($key = 'pt')
    {
        $current   = current_url();
        $actualKey = $this->getLangKey();

        $url = str_replace('/' . $actualKey, '/' . $key, $current);

        // check if has lang string
        $aUrl = explode('/', $url);

        if (!in_array($key, $aUrl))
        {
            $url = trim($url, '/') . '/' . $key;
        }

        return $url;
    }

}