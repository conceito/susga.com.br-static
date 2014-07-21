<?php

if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

/*
 * CONTROLLER RESERVADO PARA FRONT-END
 * Sites em português: extends MX_Controller
 * Sites multiligue: extends Multilang_Controller (Ler configurações no controller)
 */

class Frontend_Controller extends MY_Controller
{

	public $footer;
	public $header;
	private $JSfixo = array('jquery-1.10.2.min', 'bootstrap.min', 'page.all'); // assets précarregados
    private $CSSfixo = array('bootstrap', 'base-layout', 'module-template'); // assets précarregados
    public $JS = array(); // JS na pasta assets/js
    public $JSlibs = array(); // JS na pasta libs/jquery
    public $json_vars = array(); // JSON variaveis
    public $CSS = array(); // CSS na pasta assets/css
    public $body_class = ''; // classe na tag body
    public $title = ''; // title intetado nos metadados
    public $corpo = ''; // view do corpo da página
    private $tmp = array(); // array de dados do template principal
    public $uri_seg = array(); // guardará todos os segmentos da uri
    public $modulo = null; // dados do módulo (CMS)
    public $pagina = null; // dados da pagina (cms_conteudo)

    function __construct()
    {
        parent::__construct();

        if (ENVIRONMENT == 'development' && !IS_AJAX)
        {
            $this->output->enable_profiler(true);

        }
        else
        {

            // admin logado não faz cache            
            if ($this->phpsess->get('logado', 'cms') != true)
            {
                // ativa cache
                //                $this->output->cache(60);
            }

        }
        /*
         * Carrega classes para todos os controller */
//        $this->load->library(array('site_utils'));
        $this->load->helper('moeda');

        /*
         * Identifica o controller usado */
        $this->uri_seg = $this->uri->segment_array();



    }

    /**
     * Gera dados e monta o template
     */
    public function templateRender($template = 'template/main')
    {

        /*
         * Seta a classe CSS para a tag BODY no template
         */
        if ($this->uri_seg == false || $this->uri_seg[1] == 'inicio')
        {
            $this->body_class = 'home';
        }
        else
        {
            $this->body_class = 'page';
            $this->body_class .= ' ' . implode(' ', $this->uri_seg);
        }

        /*
         * MONTA AS PARTES DO TEMPLATE
         */
        // título
        $this->tmp['title'] = ($this->title != '') ? $this->title . ' &gt; ' . $this->config->item('title') : $this->config->item('title');

        // conteúdo principal
        $this->tmp['header'] = $this->renderHeader();
        $this->tmp['body'] = $this->corpo;
        $this->tmp['footer'] = $this->footer;

        // scripts adicionais
        $this->tmp['page_scripts'] = (isset($this->pagina['scripts'])) ? $this->pagina['scripts'] : '';

        /*
         * Dados para SEO
         */
        $this->tmp['metatags'] = $this->metatags($this->tmp['title'], $this->pagina['resumo'], $this->pagina['tags'], '');

        /*
         * COMBINA ARRAYS
         */
        $JS  = array_merge($this->JSfixo, $this->JS);
        $CSS = array_merge($this->CSSfixo, $this->CSS);
        $this->json_vars(null, array(
            'base_url' => base_url(),
            'site_url' => trim(site_url(), '/') . '/'
        ));
        $this->tmp['json_vars'] = json_encode($this->json_vars);

        if (ENVIRONMENT == 'development')
        {
            $this->tmp['scripts'] = $this->scripts($JS, 'assets/js');
            $this->tmp['scripts'] .= $this->scripts($this->JSlibs, 'libs/jquery');
            $this->tmp['estilos'] = $this->estilos($CSS, 'assets/css');
        }
        else
        {
            $this->tmp['scripts'] = minify('js', $JS, 'assets/js');
            $this->tmp['scripts'] .= minify('js', $this->JSlibs, 'libs/jquery');
            $this->tmp['estilos'] = minify('css', $CSS, 'assets/css');
        }

        /*
         * DESCARREGA NO TEMPLATE
         */
        $this->load->view($template, $this->tmp);
    }


	/**
	 * render header
	 */
	public function renderHeader()
	{
		$v['titleSite'] = $this->config->item('title');
		return $this->load->view('header', $v, true);
	}

    // -------------------------------------------------------------------------
    /**
     * Método de exemplo do como gerar menu do módulo de páginas.
     * @return type
     */
    public function generate_menu()
    {

        $config['overload']        = true;
        $config['modulo_id']       = 6;
        $config['html']            = true;
        $config['child_pages']     = true;
        $config['main_controller'] = '';
        $config['append']          = array(
            array(
                'titulo' => 'Contato', 'uri' => 'contato'
            ),
            array(
                'titulo' => 'Notícias', 'uri' => 'noticias'
            ),
            array(
                'titulo' => 'Cursos', 'uri' => 'cursos'
            ),
            array(
                'titulo' => 'Loja', 'uri' => 'loja'
            ),
            array(
                'titulo' => 'Pesquisar', 'uri' => 'pesquisa'
            ),
            array(
                'titulo' => 'Files', 'uri' => 'file/index'
            )
        );

        return $this->cms_conteudo->generate_menu($config);
    }


    /**
     * Adiciona as metatags no head.
     *
     * @param string $pagina : nome do arquivo com metatags sem extenção (php)
     * @param string $local : pasta padrão [site/metatags]
     * @return string
     */
    public function metatags($title = '', $description = '', $keywords = '', $local = '')
    {

        $dadosPag['title']       = ($title != '') ? $title : $this->config->item('title');
        $dadosPag['description'] = (isset($description) && $description != '') ? $description : $this->config->item('description');
        $dadosPag['keywords']    = (isset($keywords) && $keywords != '') ? $keywords : $this->config->item('keywords');
        $saida                   = $this->load->view($local . '/metatags', $dadosPag, true);

        return $saida;
    }


    /**
     * Se acionada carrega scripts JS dentro da pasta "js" na raiz do site,
     * ou outro local passado na variavel '$local'.
     *
     * @param array $lista : nome dos scripts sem extenção (js)
     * @param string $local : pasta padrão [js]
     * @return string
     */
    function scripts($lista, $local = 'js')
    {
        if (!is_array($lista))
        {
            $lista = array($lista); // se não for, transforma em array
        }
        if (count($lista) == 0)
        {
            return '';
        }

        $lista = array_unique($lista);

        // $pasta = site_url('js');
        $pasta = base_url() . app_folder() . $local;

        $saida = '';
        foreach ($lista as $nomejs)
        {
            $saida .= "<script type=\"text/javascript\" src=\"" . $pasta . "/" . $nomejs . ".js\"></script>\n";
        }

        return $saida;
    }

    // -----------------------------------------------------------------------
    /**
     * Combina os arrays e converte em JSON na view.
     * São variáveis globais para serem usadas via JS nas views.
     * @param       array $array
     * @return      array
     */
    public function json_vars($namespace = null, $array = null)
    {

        if ($namespace !== null)
        {

            if (is_array($namespace))
            {
                $array     = $namespace;
                $namespace = 'conteudo';
            }
            else
            {
                if (!is_array($array))
                {
                    $array = explode(',', $array);
                }
            }

            $array_in = $array;
            unset($array);
            $array[$namespace] = $array_in;
        }

        $this->json_vars = array_merge($this->json_vars, $array);

    }

    /**
     * Se acionada carrega estilos CSS dentro da pasta "css" na raiz do site,
     * ou outro local passado na variavel '$local'.
     *
     * @param array $lista : nome dos estilos sem extenção (css)
     * @param string $local : pasta padrão [css]
     * @param string $media : tipo de css, 'screen' é o padrão
     * @return string
     */
    function estilos($lista, $local = 'css', $media = 'screen')
    {
        if (!is_array($lista))
        {
            $lista = array($lista); // se não for, transforma em array
        }
        if (count($lista) == 0)
        {
            return '';
        }
        $lista = array_unique($lista);
        $pasta = base_url() . app_folder() . $local;
        $saida = '';
        foreach ($lista as $nomes)
        {
            $saida .= "<link href=\"" . $pasta . "/" . $nomes . ".css\" rel=\"stylesheet\" type=\"text/css\" media=\"$media\" />\n";
        }

        return $saida;
    }

    /*
     * ACRESCENTA SCRIPTS PARA SEREM ENVIADOS AO TEMPLATE
     */

    function setNewScript($array = null, $where = 'assets')
    {
        if ($array == null)
        {
            return false;
        }
        else
        {
            if (!is_array($array))
            {
                $array = explode(',', $array);
            }
        }

        if ($where == 'assets')
        {
            $this->JS = array_merge($this->JS, $array);
        }
        else
        {
            if ($where == 'libs')
            {
                $this->JSlibs = array_merge($this->JSlibs, $array);
            }
        }
    }

    /*
     * ACRESCENTA CSSs PARA SEREM ENVIADOS AO TEMPLATE
     */

    function setNewEstyle($array = null)
    {
        if ($array == null)
        {
            return false;
        }
        else
        {
            if (!is_array($array))
            {
                $array = explode(',', $array);
            }
        }

        $this->CSS = array_merge($this->CSS, $array);
    }

    /**
     * Plugins são conjuntos de JS e CSS que são injetados.
     * @param type $pluginName
     */
    public function setNewPlugin($pluginName)
    {

        if (!is_array($pluginName))
        {
            $pluginName = explode(',', $pluginName);
        }

        foreach ($pluginName as $plugin)
        {

            if ($plugin == 'NOMEDOPLUGIN')
            {
                $this->setNewEstyle(array('NOMEDOPLUGIN'));
                $this->setNewScript(array('NOMEDOPLUGIN'));
                $this->setNewJquery(array('NOMEDOPLUGIN'));
            }
            else if ($plugin == 'angular')
            {
                $this->setNewScript(array('angular/1.2.16/angular.min', 'angular/1.2.16/angular-animate.min'));
            }

        }
    }

}