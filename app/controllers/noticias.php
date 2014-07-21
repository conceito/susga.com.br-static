<?php

/**
 * Controller para gerir módulo de Notícias 
 */
class Noticias extends Frontend_Controller {

    public function __construct() {
        parent::__construct();

//        $this->load->library('cms_conteudo'); # carregadas no Front_Controller
    }

    /**
     * 
     */
    public function index() {

        // breadcrumb
        $this->breadcrumb->add('Notícias');
        
        // exibe lista de posts baseado nos argumentos abaixo:
        $view['posts'] = $this->cms_posts->get(array(
            'modulo_id' => 7,
            'per_page' => 2,
            'base_url' => 'noticias',
            'gallery_tag' => 1,
            'gettags' => true,
            'ordem' => 'cont.prioridade'
        ));
        // string de navegação para injetar na view
        $view['pagination'] = $this->cms_posts->pagination();
        $view['total'] = $this->cms_posts->get_total();
//        mybug($view['posts'], true);
        
        
        $this->title = 'Posts';
        $this->corpo = $this->load->view('site_add/looping-links', $view, true);

        $this->templateRender();
    }
    
    // -------------------------------------------------------------------------
    /**
     * Método padrão para exibir módulo "notícias".
     * Ver: $route['noticias/(:any)'] = "noticias/display/$1";
     * @param int $page_id
     */
    public function display(){
        
        // shortcodes devem ser inicializados primeiro
        $this->cms_conteudo->shortcode_reg(array('slide'));
        
        
        
        // retorna dados da tabela cms_conteudo parseado
        $this->cms_conteudo->set_page();
        $this->pagina = $this->cms_conteudo->get_page();
        // retorna galeria
        $this->pagina['galeria'] = $this->cms_conteudo->get_page_gallery();
        // retorna os arquivos anexos
        $this->pagina['anexos'] = $this->cms_conteudo->get_page_attachments();
        // retorna dados do módulo
        $this->pagina['modulo'] = $this->cms_conteudo->set_get_modulo();
        
        // retorna as páginas, ou grupos a que pertencem para breadcrumb
        $this->pagina['hierarchy'] = $this->cms_conteudo->get_hierarchy();
//        mybug($this->pagina['hierarchy']);
        
        
        // breadcrumb
        $this->breadcrumb->add('Notícias', 'noticias');
//        $this->breadcrumb->add($this->pagina['titulo']);
        
        $this->breadcrumb->add($this->pagina['hierarchy']);
        
        // disponibiliza dados do conteúdo via JS
        $this->json_vars($this->pagina);
        
        $view['post'] = '';        

        $this->title = $this->pagina['titulo'];
        $this->corpo = $this->load->view('site_add/post-content', $view, true);

        $this->templateRender();
        
    }
    
    // ------------------------------------------------------------------------
    /**
     * Navegação pelos grupos de posts
     */
    public function categoria(){
        
        // retorna dados da tabela cms_conteudo parseado
        $this->cms_conteudo->set_page();
        $this->pagina = $this->cms_conteudo->get_page();
//        mybug($this->pagina);
        // exibe lista de posts baseado nos argumentos abaixo:
        $view['posts'] = $this->cms_posts->get(array(
            'modulo_id' => 7,
            'per_page' => 1,
            'grupo_id' => $this->pagina['id'],
            'base_url' => 'noticias/c/'.$this->pagina['nick'],
            'gallery_tag' => false,
            'gettags' => true
        ));
        // string de navegação para injetar na view
        $view['pagination'] = $this->cms_posts->pagination();
//        mybug($view['posts'], true);
        
        
        // breadcrumb
        $this->breadcrumb->add('Notícias', 'noticias');
        $this->breadcrumb->add($this->pagina['titulo']);
        
        
        $view['post'] = 'Grupo';        

        $this->title = 'Grupo - ' . $this->pagina['titulo'];
        $this->corpo = $this->load->view('site_add/looping-links', $view, true);

        $this->templateRender();
        
    }

}