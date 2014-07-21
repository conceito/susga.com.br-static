<?php

/**
 * Injeta arquivos para criar edição no front-end. 
 */
class Cms_adminbar{
    
    private $ci = NULL;
    private $user_admin = FALSE;
    
    public function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->helper('cms/cmshelper');
        $this->ci->load->model(array('cms/sessao_model'));
    }
    
    public function generate(){
        
        $this->user_admin = $this->ci->cms_conteudo->admin_verify();
        
        /*
         * Verifica status do admin, mas não bloqueia. Bloqueio é feito nos controllers
         */
        if($this->user_admin === FALSE){
            $this->ci->sessao_model->check_cookie_login();
            return false;
        }
        // dados do admin
        $view['user'] = $this->user_admin;
        $view['modulos'] = $this->get_modulos();
//        mybug($view['modulos']);
        // insere assets
        echo $this->scripts(array('adminbar'));
        echo $this->scripts(array('jquery.cookie'), 'libs/jquery');
        echo $this->estilos(array('adminbar'));
        
        // carrega barra de edição
        $this->ci->load->view('cms/adminbar', $view);
        
        //var_dump( $this->logado);
        
    }
    
    /**
     * Retorna os módulos que tem tabela cms_conteudo.
     * 'id' => string '6' (length=1)
      'ordem' => string '8' (length=1)
      'label' => string 'Páginas' (length=8)
      'uri' => string 'cms/paginas/index/co:6' (length=22)
      'tabela' => string 'cms_conteudo' (length=12)
      'tipo' => string '1' (length=1)
      'grupo' => string '0' (length=1)
      'acao' => string '' (length=0)
      'ico' => string '' (length=0)
      'destaques' => string '0' (length=1)
      'comments' => string '0' (length=1)
      'ordenavel' => string '1' (length=1)
      'inscricao' => string '0' (length=1)
      'multicontent' => string 'Alguma coisa,Outra aba' (length=22)
      'rel' => string '6|7' (length=3)
      'pasta_img' => string '4' (length=1)
      'pasta_arq' => string '5' (length=1)
      'pasta_ajuda' => string '0' (length=1)
      'extra' => string '' (length=0)
      'status' => string '1' (length=1)
      'novo' => string 'cms/paginas/novo/co:6' (length=21)
     * @return type 
     */
    public function get_modulos(){
        
        
        $this->ci->db->where('tabela', 'cms_conteudo');
        $this->ci->db->where('status', 1);
        $this->ci->db->where('grupo', 0);
        $this->ci->db->order_by('ordem');
        $this->ci->db->where('tipo >=', $this->ci->phpsess->get('admin_tipo', 'cms'));
        $result = $this->ci->db->get('cms_modulos');
        
        $return = array();
        
        foreach($result->result_array() as $mod){
            
            $nova_uri = str_replace('index', 'novo', $mod['uri']);
            $mod['novo'] = $nova_uri;
            
            $return[] = $mod;
            
        }
        
        return $return;
        
    }

        
    
    /**
     * Se acionada carrega scripts JS dentro da pasta "js" na raiz do site,
     * ou outro local passado na variavel '$local'.
     *
     * @param array $lista : nome dos scripts sem extenção (js)
     * @param string $local : pasta padrão [js]
     * @return string
     */
    private function scripts($lista, $local = 'ci_itens/js') {
        if (!is_array($lista)) {
            $lista = array($lista); // se não for, transforma em array
        }
        if (count($lista) == 0) {
            return '';
        }
        
        $lista = array_unique($lista);
        
        // $pasta = site_url('js');
        $pasta = base_url() . app_folder() . $local;

        $saida = '';
        foreach ($lista as $nomejs) {
            $saida .= "<script type=\"text/javascript\" src=\"" . $pasta . "/" . $nomejs . ".js\"></script>\n";
        }

        return $saida;
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
    private function estilos($lista, $local = 'ci_itens/css', $media = 'screen') {
        if (!is_array($lista)) {
            $lista = array($lista); // se não for, transforma em array
        }
        if (count($lista) == 0) {
            return '';
        }
        $lista = array_unique($lista);
        $pasta = base_url() . app_folder() . $local;
        $saida = '';
        foreach ($lista as $nomes) {
            $saida .= "<link href=\"" . $pasta . "/" . $nomes . ".css\" rel=\"stylesheet\" type=\"text/css\" media=\"$media\" />\n";
        }

        return $saida;
    }
    
}