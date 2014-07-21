<?php

/**
 * Classe para fazer pesquisa.
 * 
 * # Seta as configurações e retorna o resultado.
 *   O termo de busca deve estar na url "q="
 *   $results = $this->cms_pesquisa->get($config);
 * 
 * # Retorna o total de resultados
 *   $this->cms_pesquisa->total();
 * 
 * 
 */
class Cms_pesquisa {

    private $ci = NULL;
    public $ignore_module = array(0, 1, 29, 37, 40); // Ignora os módulos
    public $ignore = false; // Ignora os registros com ID
    public $module = false; // restringe aos módulos, FALSE == todos
    public $group = false;  // restringe aos grupos buscados, FALSE == todos
    private $page_module = array(6);// módulos sobre 'paginas'
    private $post_module = array(7);// módulos sobre 'posts'
    private $cale_module = array(21);// módulos sobre 'calendario'
    // Por padrão a pesquisa é realizada apenas em 'cms_conteudo', 
    // para incluir outras tabelas, use: [arquivos|usuarios|enquete]
    public $add_table = false;
    public $cache = false;  // usa cache para salvar resultados
    public $cookie = false; // salva termos do usuário em cookie
    public $campos = array('id', 'dt_ini', 'nick', 'full_uri', 'titulo', 'resumo', 'modulo_id', 'rel'); // quais campos retornar
    public $campos_busca = array('titulo', 'resumo'); // quais campos fazer busca
    public $lang = 'pt';   // idioma @todo: resgatar valor do FW
    public $terms = '';
    public $de = false;    // data de início    
    public $ate = false;   // data limite
    public $ordem = 'dt_ini desc';
    public $total = 0;     // quantidade de resultados
    public $per_page = 10;     // paginação
    private $set_pagination = false;
    public $highlight_open = '<strong class="highlight">';
    public $highlight_close = '</strong>';
    public $base_url = 'pesquisa/r';

    public function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->helper('text');
        
        if($this->ci->config->item('modulo_paginas')){
            $this->page_module = $this->ci->config->item('modulo_paginas');
        }
        if($this->ci->config->item('modulo_posts')){
            $this->post_module = $this->ci->config->item('modulo_posts');
        }
        if($this->ci->config->item('modulo_calendario')){
            $this->cale_module = $this->ci->config->item('modulo_calendario');
        }
        
        log_message('debug', "Cms_pesquisa Library Initialized");
    }

    // -------------------------------------------------------------------------
    /**
     * Parseia as configurações iniciais e seta pariáveis
     * @param       array       $config 
     * @access      private
     * @return      void
     */
    public function initialize($params) {

        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    /**
     * Método público para instanciar classe e recuperar o resultado.
     * 
     * @param       string      $terms
     * @param       array       $config
     * @return      array
     */
    public function get($config) {

        // instancia variáveis do usuário
        if (count($config) > 0) {
            $this->initialize($config);
        }

        // prerara termos de busca
        $this->terms = $this->get_terms_from_url();
        if($this->terms === FALSE)          {
            redirect('pesquisa');
        }
           
        // executa query principal
        return $this->query();
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna os termos de busca da url, via GET ou parâmetro.
     * @return string
     */
    public function get_terms_from_url(){
        
        if($this->ci->input->get('q')){
            
            if(strlen(trim($this->ci->input->get('q'))) == 0){
                redirect('pesquisa');
            }
            return $this->parse_terms($this->ci->input->get('q'));
        } else {
            return $this->ci->uri->segment(3);
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna os termos de busca da memória, como array ou string
     * @param       type        $as_array
     * @return      array|string
     */
    public function get_terms($as_array = false){
        
        $t1 = explode('_', $this->terms);
        $result = ($as_array) ? array() : '';
        
        foreach($t1 as $t){
            
            if($as_array){
                $result[] = str_replace('-', ' ', $t);
            } else {
                
                // se encontrar '-' deve envelopar com " "
                if(strpos($t, '-')){ $result .= '"'; }
                
                $result .= str_replace('-', ' ', $t);
                
                if(strpos($t, '-')){ $result .= '"'; }
                
                $result .= ' ';
            }
            
        }
        
        return ($as_array) ? $result : trim($result);
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna o total de registros encontrados.
     * @return int
     */
    public function get_total(){
        return $this->total;
    }

    // -------------------------------------------------------------------------
    /**
     * Organiza todo processamento de acordo com as opções.
     */
    private function query() {

        // estabelece $offset para paginação
        $v = $this->ci->uri->to_array('pag');
        if ($v['pag'] == '') {
            $offset = 0;
        } else {
            $offset = ($v['pag'] - 1) * $this->per_page;
        }


        $this->ci->db->distinct();
        // conteúdo
        $this->ci->db->from('cms_conteudo as cont');
        $campos = $this->prep_campos($this->campos);
        // grupo
        $this->ci->db->join('cms_conteudo as grupo', 'grupo.id = cont.grupo', 'left');
        $campos .= ', grupo.titulo as grupo_nome, grupo.nick as grupo_nick, grupo.id as grupo_id';
        // módulo
        $this->ci->db->join('cms_modulos as modu', 'modu.id = cont.modulo_id', 'left');
        $campos .= ', modu.front_uri, modu.label as modulo_titulo';

        // ignora os módulos
        $this->ci->db->where_not_in('cont.modulo_id', $this->ignore_module);
        
        $this->ci->db->where('cont.tipo', 'conteudo');
        $this->ci->db->where('cont.status', 1);
//        $this->ci->db->where('cont.grupo !=', 0);
        $this->ci->db->order_by('cont.' . $this->ordem);
        $this->ci->db->limit($this->per_page, $offset);
        $this->ci->db->select('SQL_CALC_FOUND_ROWS '.$campos, false);
        $this->ci->db->where('cont.lang', $this->lang);

        // trata buscas FULLTEXT
        $this->fulltext();

        $result = $this->ci->db->get();

//        mybug($this->ci->db->last_query(), true);
//        mybug($result->result_array());

        if ($result->num_rows() == 0) {
            return FALSE;
        }
        
        $query = $this->ci->db->query('SELECT FOUND_ROWS() AS `Count`');
        $this->total = $query->row()->Count;
        

        // se o total de registros for maior que o resultado limitado,
        // ativa pagination
        if($this->total > $result->num_rows()){            
            $this->set_pagination();
        } 
        
        
        $resultados = $result->result_array();
        // limpa memória
        $result->free_result();
        
        return $this->query_parse($resultados);
    }
    
    
    // -------------------------------------------------------------------------
    /**
     * Parseia resultados para adicionar highlight.
     * @param       type         $query
     * @return      array
     */
    private function query_parse($query){
        
        $return = array();
        $terms = $this->get_terms(true);
//        mybug($terms);
        
        foreach ($query as $row){
            
            // percorre o titulo
            if(in_array('titulo', $this->campos)){
                $res = $row['titulo'];
                foreach ($terms as $t){
                    $res = $this->highlight($t, $res);
                }
                $row['titulo'] = $res;
            }            
            
            // percorre o resumo
            if(in_array('resumo', $this->campos)){
                $res = $row['resumo'];
                foreach ($terms as $t){
                    $res = $this->highlight($t, $res);
                }
                $row['resumo'] = $res;
            }
            
            // percorre o txt
            if(in_array('txt', $this->campos)){
                $res = $row['txt'];
                foreach ($terms as $t){
                    $res = $this->highlight($t, $res);
                }
                $row['txt'] = $res;
            }
            
            
            // insere uri do conteúdo
            if($row['full_uri'] != ''){
                $row['uri'] = $row['full_uri'];                
            }
            // modelo antigo que busca a hierarquia de grupos
            // @tip: futuramente esta parte de baixo pode ser removida
            else {
                
                // se for página, instancia e recupera a hierarquia
                if(in_array($row['modulo_id'], $this->page_module)){
                    $page = $this->ci->cms_conteudo->get_page($row['id']);
                    $hierarchy = $this->ci->cms_conteudo->get_hierarchy();

                    $row['uri'] = $hierarchy[count($hierarchy)-1]['uri'];
                } 
                // para os outros módulos usa roteamento padrão do módulo
                else {                

                    // reinicia variáveis para o módulo do conteúdo
                    $this->ci->cms_posts->initialize(array(
                        'modulo_id' => $row['modulo_id']
                    ));
                    // monta uri baseado na arquitetura de grupos
                    $row['uri'] = $this->ci->cms_posts->get_post_uri($row);                

                }
                
            }
            
            
            $return[] = $row;
        }
        
        return $return;
    }

    // -------------------------------------------------------------------------

    /**
     * Algorítimos para limpar a query e passar por filtros avançados.
     * Retorna termos no formado "aaa|bbb-cc|ddddd"
     * @return array
     */
    public function parse_terms($terms) {

        $output = array();
        $output2 = array();

        $arr = explode('"', trim($terms));

        for ($i = 0; $i < count($arr); $i++) {
            if ($i % 2 == 0) {
                // remove pontuação
                $ret = preg_replace("/[[:punct:]]/", "", $arr[$i]);
                $output = array_merge($output, explode(" ", $ret));
            } else {
                // remove pontuação
                $ret = preg_replace("/[[:punct:]]/", "", $arr[$i]);
                $output[] = $ret;
            }
        }
        foreach ($output as $word) {
            if (trim($word) != "") {
                $output2[] = $word;
            }
        }

        // certifica-se de ter um termo
        if (empty($output2)) {
            $output2 = array($terms);
        }
        
        // transforma no padrão para CI sem caracteres especiais
        $ci_like = '';
        foreach ($output2 as $term){
           $ci_like .= $this->limpa_caracteres($term).'_'; 
        }
        
        $ci_like = trim($ci_like, '_');

//        mybug($ci_like);
        return $ci_like;
    }
    
    // ------------------------------------------------------------------------
    /**
     * Limpa uma String de caracteres especiais, espaços e pontuação.
     *
     * @param        string         $texto 
     * @return       string
     */
    function limpa_caracteres($texto = '') {

        $texto = str_replace(' ', '-', convert_accented_characters($texto));
        
        return $texto;

    }

    // -------------------------------------------------------------------------
    /**
     * Prepara string para ER
     * @param type $str
     * @return type
     */
//    private function parse_er($str){
//        
//        $str = str_replace('u', '[uú]', $str);
//        
//        return $str;
//        
//    }
    // -------------------------------------------------------------------------
    /**
     * Adiciona SQL para busca pelos termos
     */
    private function fulltext() {


        $like = ' (';        

        // concatena os campos buscados
        $concat = "CONCAT(";
        
        foreach ($this->campos_busca as $field) {
            $concat.="cont.$field,";
        }
        $concat.="'')";

        $termos = $this->get_terms(true);
        $total = count($termos);
        
        for ($x = 0; $x < $total; $x++) {
            $like .= $concat . " LIKE '%" . $termos[$x] . "%' ";
//            $like .= 'cont.'.$this->campos_busca[$x] . " REGEXP '".  $termos."' ";

            if ($x < $total - 1) {
                $like .= 'OR ';
            }
        }

        $like .= ')';

        $this->ci->db->where($like, NULL, FALSE);
    }

    // -------------------------------------------------------------------------
    /**
     * Gera a paginação na view.
     * @return string
     */
    public function pagination() {
        if ($this->set_pagination) {
            return $this->ci->pagination->create_links();
        } else {
            return '';
        }
    }
    
    // -------------------------------------------------------------------------
    /**
     * Inicializa a paginação.
     * @param array $post
     */
    private function set_pagination($post = false){
        
        $this->ci->load->library('pagination');
        $this->set_pagination = true;
//            
//        mybug(urldecode('d%C3%AA'));
//        mybug($this->terms);
        // inclui os termos da pesquisa na paginação
        $this->base_url .= '/'.$this->terms;
        
        // configura paginação ----------------------------------
        $config['base_url'] = site_url($this->base_url);
        $config['total_rows'] = $this->total;
        $config['per_page'] = $this->per_page;
//        $config['suffix'] = 'q:234';
        $config['uri_segment'] = 3;
        $config['num_links'] = 1; // quantas páginas são mstradas antes de depois na paginação
        $config['full_tag_open'] = '<div class="pagination pagination-centered"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['first_link'] = 'primeira';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['next_link'] = '»';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '«';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['last_link'] = 'última';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
//        $config['display_pages'] = false;
        $this->ci->pagination->initialize($config);        
  
        
    }

    // -------------------------------------------------------------------------
    /**
     * Recebe uma strins nnnn, nnnn, nnnn e retorna a referência da tabela cont.*
     * @param type $string
     * @return type
     */
    private function prep_campos($campos) {

        if (!is_array($campos)) {
            $campos = explode(',', $campos);
        }

        $return = array();
        foreach ($campos as $c) {

            $return[] = 'cont.' . trim($c);
        }

        return implode(',', $return);
    }
    
    // -------------------------------------------------------------------------
    /**
     * As duas funções abaixo fazem o highlight independente dos acentos.
     * @param type $str
     * @param type $delim
     * @return type
     */
    private function prepare_search_term($str,$delim='#') {
        $search = preg_quote($str,$delim);

        $search = preg_replace('/[aàáâãåäæ]/iu', '[aàáâãåäæ]', $search);
        $search = preg_replace('/[eèéêë]/iu', '[eèéêë]', $search);
        $search = preg_replace('/[iìíîï]/iu', '[iìíîï]', $search);
        $search = preg_replace('/[oòóôõöø]/iu', '[oòóôõöø]', $search);
        $search = preg_replace('/[uùúûü]/iu', '[uùúûü]', $search);
        $search = preg_replace('/[cç]/iu', '[cç]', $search);
        // add more characters...

        return $search;
    }
    private function highlight($searchtext, $text) {
        $search = $this->prepare_search_term($searchtext);
        return preg_replace('#' . $search . '#iu', $this->highlight_open.'$0'.$this->highlight_close, $text);
    }

}