<?php

class Pesquisa extends Frontend_Controller{
    
    public function __construct() {
        parent::__construct();
        $this->load->library('cms_pesquisa');
    }
    
    public function index(){
        
        $this->breadcrumb->add('Pesquisar');
        
        $this->title = 'Pesquisa';
        $this->corpo = $this->load->view('site_add/pesquisa', '', true);
        
        $this->templateRender();
        
    }
    
    public function results($query = ''){
          
         
        
        $config = array(
            'per_page' => 3,
            'campos_busca' => array('titulo', 'resumo', 'txt')
        );
        
        $view['result'] = $this->cms_pesquisa->get($config);
        $view['total'] = $this->cms_pesquisa->get_total();
        // string de navegação para injetar na view
        $view['pagination'] = $this->cms_pesquisa->pagination();
        
        $this->breadcrumb->add('Resultado para: ' . $this->cms_pesquisa->get_terms());
        
        $this->title = 'Resultado para '.$this->cms_pesquisa->get_terms();
        $this->corpo = $this->load->view('site_add/pesquisa', $view, true);
        
        $this->templateRender();
        
    }
    
}