<?php

/**
 * Controller para gerir módulo de páginas 
 */
class Pages extends Frontend_Controller {

    public function __construct() {
        parent::__construct();

//        $this->load->library('cms_conteudo'); # carregadas no Front_Controller
    }

    /**
     * Identifica a página e os grupos na hierarquia 
     */
    public function index() {

        $v = $this->uri->to_array('id');
        
        

        // Caso a página não seja identificada pela URI, podemos entrar manualmente
//        $this->cms_conteudo->set_page('nick ou ID');

        
//        $hierarquia = $this->cms_conteudo->get_page_hierarchy();
        $this->pagina = $this->cms_conteudo->get_page();
        
//        $ralacionados = $this->cms_conteudo->get_page_relations();
//        
//        $this->pagina['precos'] = $this->cms_conteudo->get_precos();
//        $anexos10 = $this->cms_conteudo->get_page_attachments('agenda-ecemplo');
//        $tags = $this->cms_conteudo->get_page_tags();
//        mybug($this->pagina);

        $view['post'] = '';
        

        $this->title = $this->pagina['titulo'];
        $this->corpo = $this->load->view('site_add/post-content', $view, true);

        $this->templateRender();
    }
    
    // -------------------------------------------------------------------------
    /**
     *  Método padrão para exibir módulo "páginas"
     * @param int $page_id
     */
    public function display($page_id = ''){
        
        // shortcodes devem ser inicializados primeiro
        $this->cms_conteudo->shortcode_reg(array('slide'));
        
        // breadcrumb
        // $this->load->library('breadcrumb'); // autoload        
        
        
        
        
        // retorna dados da tabela cms_conteudo parseado
        $this->pagina = $this->cms_conteudo->get_page($page_id);
        
        if($this->pagina === FALSE){
            redirect();
        }
        
        // retorna galeria
        $this->pagina['galeria'] = $this->cms_conteudo->get_page_gallery();
        // retorna os arquivos anexos
        $this->pagina['anexos'] = $this->cms_conteudo->get_page_attachments();
        // retorna dados do módulo
        $this->pagina['modulo'] = $this->cms_conteudo->set_get_modulo();
        // retorna as páginas filhas
        $this->pagina['children'] = $this->cms_conteudo->get_children(true, array('html' => true));
        // retorna as páginas, ou grupos a que pertencem para breadcrumb
        $this->pagina['hierarchy'] = $this->cms_conteudo->get_hierarchy();
        
        
        
        // breadcrumb
        $this->breadcrumb->add($this->pagina['hierarchy']);
        
        $view['post'] = '';        

        $this->title = $this->pagina['titulo'];
        $this->corpo = $this->load->view('site_add/post-content', $view, true);

        $this->templateRender();
    }

    

}