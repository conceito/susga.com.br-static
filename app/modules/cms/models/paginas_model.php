<?php

/**
 * Paginas_model
 *
 * @package
 * @author Bruno
 * @copyright Copyright (c) 2010
 * @version $Id$
 * @access public
 * */
class Paginas_model extends CI_Model {

    public $all_pages = false;
    public $arquitetura = false;
    public $modulo = false;
    public $vars = false;
    private $modo_filtro = false;// quando algum filtro está sendo usado


    function __construct() {
        parent::__construct();
    }
    
    /**
     * Armazena os dados do módulo
     * @param array $modulo 
     */
    public function set_modulo($modulo){
        $this->modulo = $modulo;
    }
    
    /**
     * Armazena as variáveis
     * @param array $vars 
     */
    public function set_vars($vars){
        $this->vars = $vars;
    }

        
    /**
     * Retorna todos das páginas ordenadas
     * @return array 
     */
    public function get_all_pages(){
        
        $vars = $this->vars;
        
        // opções de filtro
        $uri_filters = $this->set_page_filters();
        
        $this->db->select('*');       
              
        $this->db->where('lang', get_lang());
        $this->db->where('modulo_id', $this->vars['co']);
        $this->db->where('tipo', 'conteudo');
        $this->db->order_by('ordem');        

        $sql = $this->db->get('cms_conteudo');        
        
        return $sql->result_array();
    }
    
    // ----------------------------------------------------------------------
   
    /**
     * Parseia $_POST e URI para saber se existem filtros ativos
     * Retorna URI para paginação.
     * 
     * @return string
     */
    private function set_page_filters(){
        
        // define os campos que serão usados no filtro
        $campos_usados[] = array('campo' => 'titulo', 'type' => 'like');
        $campos_usados[] = array('campo' => 'show', 'type' => 'int');
        $campos_usados[] = array('campo' => 'destaque', 'type' => 'int');
        $campos_usados[] = array('campo' => 'dt_ini', 'type' => 'date');
        $campos_usados[] = array('campo' => 'status', 'type' => 'int');
        $campos_valorados = array();
        
//        mybug($this->input->post());
        
        // uri de filtros para paginação
        $return = '';        
        
        // verifica se veio pelo POST ou URI
        foreach($campos_usados as $row){
            
            $campo = $row['campo'];
            $type  = $row['type'];
            $uri = $this->uri->to_array('filter_'.$campo);
            
            // tem post?
            if(isset($_POST['filter_'.$campo])){
                $valor = $_POST['filter_'.$campo];
            } 
            // tem na URI
            else if($uri['filter_'.$campo] != '') {                
                $valor = $uri['filter_'.$campo];
            } else {
                $valor = '';
            }
            
            // acrescenta o valor
            $row['valor'] = $valor;
            $campos_valorados[] = $row;
            
        }
//        mybug($campos_valorados);
        // faz pesquisa
        foreach($campos_valorados as $row){
            
            if($row['valor'] != ''){
                $this->modo_filtro = true;
                $campo = $row['campo'];
                $type  = $row['type'];
                $valor = $row['valor'];
                
                // se for data
                if($type == 'date' && strlen($valor) == 10){
                    $valor = formaSQL($valor);
                }
                
                if($type == 'like'){
                    $this->db->like(''.$campo, $valor);
                } else {
                    $this->db->where(''.$campo, $valor);
                }

                // incrementa uri
                $return .= '/filter_'.$campo.':'.$valor;
                
                
            }
        }
        
        
//        mybug($return);
        return $return;
    }
    
    /**
     * Faz busca pelas páginas recursivamente.
     * Pode retornar um array simples, ou multidimensional
     * 
     * @param type $multidimensional
     * @return type 
     */
    public function get_arquitetura($multidimensional = true) {        

        $this->arquitetura = array();
        // se os grupos ainda não estão armazenados
        if($this->all_pages === false || count($this->all_pages) == 0){
            $this->all_pages = $this->get_all_pages();
        }
        
        
        
        if($this->modo_filtro === FALSE){
            
            foreach ($this->all_pages as $row) {

                if($row['grupo'] == 0){// apenas de primeiro nível

                    $row['level'] = 0;
                    $row['uri'] = $row['nick'];

                    // se for multidimensional
                    if($multidimensional){

                        $sub = $this->_get_arquitetura_recursive($row['id'], 0, $row['uri'], $multidimensional);
                        $row['sub'] = $sub; // false ou array
                        $this->arquitetura[] = $row;

                    } else {

                        $this->arquitetura[] = $row;                
                        $this->_get_arquitetura_recursive($row['id'], 0, $row['uri'], $multidimensional);                    

                    }

                }

            }
        } else {
            $this->arquitetura = $this->all_pages;
        }
        
        
        
        $saida = array(
            'ttl_rows' => count($this->all_pages),
            'rows' => $this->parse_arquitetura($this->arquitetura)
        );
        
        // paginação
        // #paginação removina nas páginas, mas a library precisa ser instanciada 
        // por compatibilidade
        $this->load->library('pagination');

        return $saida;
    }

 
    /**
     * Pesquisa recursivamente pelos grupos do módulo.
     * @param int $grupo_id
     * @param int $level
     * @param array $multidimensional
     * @return mixed 
     */
    private function _get_arquitetura_recursive($grupo_id, $level = 0, $uri = '', $multidimensional = true) {


        $saida = array();
        $level++;
        foreach ($this->all_pages as $row) {

            // se o grupo é filho... inclui
            if($row['grupo'] == $grupo_id){
                
                $row['level'] = $level;
                $row['uri'] = $uri.'/'.$row['nick'];
                              
                // se for multimensional retorna um array
                if($multidimensional){
                
                    $sub = $this->_get_arquitetura_recursive($row['id'], $level, $row['uri'], $multidimensional);
                    $row['sub'] = $sub; // false ou array   
                    $saida[] = $row;
                    
                } else {
                    // senão... incrementa o array principal
                    $this->arquitetura[] = $row;                
                    $this->_get_arquitetura_recursive($row['id'], $level, $row['uri'], $multidimensional);
                }
                
                
                
            } else {
                if($multidimensional){
                    // senão... ignora e continua o looping
                    continue;   
                }
                                           
            }            
            
        }
        // se for multidimensional retorna o array ou FALSE
        if($multidimensional){
            if(count($saida) > 0){
                return $saida;
            } else {
                return false;
            } 
        }
               
    }
    

    function lista_paginas($vars, $modulo = array()) {

        /**
         * Opções de pesquisa e filtros 
         */
        $pps = $this->config->item('pagination_limits');
        $pp = ($vars['pp'] == '') ? $pps[0] : $vars['pp']; // por página
        $b = $vars['b'];
        // se foi feita uma busca
        if (strlen(trim($this->input->post('q'))) > 0) {
            $b = $this->cms_libs->limpa_caracteres(trim($this->input->post('q')));
            $b = ($b == 'busca') ? '' : $b; // prevenir contra falsa busca
            $offset = 0;
        }
        // se foi feita bsca avançada ------------------------
        if (strlen(trim($this->input->post('ativo'))) > 0) {
            $stt = $this->input->post('ativo');
            $offset = 0;
        } else {
            $stt = $vars['stt'];
        }
        // se foi feita seleção com grupos
        if (strlen(trim($this->input->post('grupos'))) > 0) {
            $g = $this->input->post('grupos');
        } else {
            $g = ($vars['g'] == '') ? 0 : $vars['g'];
        }
        // pelas datas
        if (strlen(trim($this->input->post('dt1'))) > 0) {
            $dt1 = formaSQL($this->input->post('dt1'));
        } else {
            $dt1 = $vars['dt1'];
        }
        if (strlen(trim($this->input->post('dt2'))) > 0) {
            $dt2 = formaSQL($this->input->post('dt2'));
        } else {
            $dt2 = $vars['dt2'];
        }

        /**
         * Montagem da SQL 
         */
        $this->db->select('raiz.*, pags.*');
        $this->db->from('cms_conteudo as raiz');
        $this->db->join('cms_conteudo as pags', 'pags.grupo = raiz.id');

        // ordenação
        $this->db->order_by('raiz.ordem, pags.ordem');

        // filtros
        if ($dt1 != '' && $dt2 == '') {
            $this->db->where('pag.dt_ini', $dt1);
        } else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('pag.dt_ini >=', $dt1);
            $this->db->where('pag.dt_ini <=', $dt2);
        }

//        if ($stt != '') {
//            $this->db->where('pags.status', $stt);
//        }

        if ($b != '') {
            $this->db->like('raiz.titulo', $b);
            $this->db->or_like('raiz.resumo', $b);
            $this->db->or_like('pags.resumo', $b);
            $this->db->or_like('pags.resumo', $b);
            
        }
        /*
        if ($g == 0) {
            $this->db->where('pag.grupo !=', 0); // todos conteudos
        } else {
            $this->db->where('pag.grupo', $g); // conteudos do grupo
        }
         * 
         */

        $this->db->where('raiz.tipo', 'conteudo');
//        $this->db->where('pags.tipo', 'conteudo');
        $this->db->where('raiz.modulo_id', $vars['co']);
        $this->db->where('raiz.lang', get_lang());
        $sql = $this->db->get();
        $resultado = $sql->result_array();
        $total = $sql->num_rows();
        
//        mybug($resultado);

        // paginação
        // #paginação removina nas páginas, mas a library precisa ser instanciada 
        // por compatibilidade
        $this->load->library('pagination');

        $saida = array(
            'ttl_rows' => $total,
            'rows' => $this->parse_lista_conteudos($resultado, $modulo)
        );

        return $saida;
    }

    

    /**
     * Faz busca pelos grupos recursivamente
     * Não tem paginação
     */
    function lista_grupos($uriVars) {

        $params = array();// init
        
        if(isset($uriVars['status'])){
            $this->db->where('status', $uriVars['status']);
            $params['status'] = $uriVars['status'];
        }
        $this->db->where('grupo', 0);
        $this->db->where('rel', 0); // primeiro nível        
        $this->db->where('lang', get_lang());
        $this->db->where('modulo_id', $uriVars['co']);
        $this->db->where('tipo', 'conteudo');
        $this->db->order_by('ordem');

        $sql = $this->db->get('cms_conteudo');
        
        $saida = array();
        foreach ($sql->result_array() as $row) {

            $row['level'] = 0;
            $sub = $this->_get_recursive_grupos($row['id'], 0, $params);
            $row['sub'] = $sub; // false ou array            

            $saida[] = $this->_parse_grupo($row);
        }

        
        
        return $saida;
    }

    /**
     * Pesquisa recursivamente pelos grupos do módulo
     * @param int $grupo_id
     */
    function _get_recursive_grupos($grupo_id, $level = 0, $params = array()) {

        if(isset($params['status'])){
            $this->db->where('status', $params['status']);
        }
        
        $this->db->where('rel', $grupo_id); // sub nível
//        $this->db->where('grupo', 0);
        $this->db->order_by('ordem');
        $sql = $this->db->get('cms_conteudo');

        if ($sql->num_rows() == 0) {
            return false;
        }

        $saida = array();
        $level++;
        foreach ($sql->result_array() as $row) {

            $row['level'] = $level;
            $sub = $this->_get_recursive_grupos($row['id'], $level, $params);
            $row['sub'] = $sub; // false ou array
            $saida[] = $this->_parse_grupo($row);
        }

        return $saida;
    }

    function lista_tags($uriVars) {

        $this->db->where('grupo', 0);
        $this->db->where('rel', 0); // primeiro nível
        $this->db->where('tipo', 'tag');
        $this->db->where('lang', get_lang());
        $this->db->where('modulo_id', $uriVars['co']);
        $this->db->order_by('ordem');

        $sql = $this->db->get('cms_conteudo');

        $saida = array();
        foreach ($sql->result_array() as $row) {

            $row['level'] = 0;

            $saida[] = $this->_parse_grupo($row);
        }

        return $saida;
    }

    /**
     * Monta a hierarquia dos grupos e monta um combobox
     */
    function getGrupoComboHierarchy($uriVars, $controller = false) {
                
        // se for controller "paginas" o funcionamento do grupo foi redirecionado
        // para aa própria hierarquia das páginas
        if($controller == 'paginas'){
            
            $this->set_vars($uriVars);
            $paginas = $this->get_arquitetura(false);          
            
            
            
            $gruposFlated = $paginas['rows'];
            
        }
        // para os outros módulos os grupos funcionam da forma padrão
        else {
            $uriVars['status'] = 1;
            $hierarchy = $this->lista_grupos($uriVars);
            $gruposFlated = $this->flatMultidimensionalArray($hierarchy, 'sub');
        }
//        mybug($hierarchy);
        
        // prepara dados para combobox
        $option = array();

        $combo = '<select name="grupos" class="input-combo " id="grupos">';

        // só apresenta opção se for um Grupo, se for conteúdo não
        if (substr($this->uri->segment(3), 0, 5) == 'grupo') {
            $combo .= '<option value="0">Nenhum</option>';
        } else if($controller == 'paginas'){
            $combo .= '<option value="0">[v] Raiz</option>';
        }

        foreach ($gruposFlated as $row) {

            // pega o nível para montar hierarquia no combo box
            $lev = $row['level'];

            $label = str_hierarchy($lev) . $row['titulo'];

            if (isset($uriVars['relacionamento'])) {
                $selected = ( $uriVars['relacionamento'] == $row['id']) ? 'selected="selected"' : '';
            } else {
                $selected = '';
            }

            if (isset($uriVars['id'])) {
                $disabled = ( $uriVars['id'] == $row['id']) ? 'disabled="disabled"' : '';
            } else {
                $disabled = '';
            }

            $combo .= '<option value="' . $row['id'] . '" ' . $selected . ' ' . $disabled . '>' . $label . '</option>';
        }

        $combo .= '</select>';

//        $combo = $this->cms_libs->cb($uriVars['relacionamento'], $option, 'rel', false, '', array('Nenhum' => '0'));

        return $combo;
    }

    /**
     * Recebe um array multidimensional e retorna um array
     * Trabalha junto com o $this->flatMultidimensionalArrayRecursive()
     *
     * @param <type> $multiArray array multi que será fletado
     * @param <type> $indexArray nome do indice do array que é o array multi
     * @return array
     */
    function flatMultidimensionalArray($multiArray, $indexArray) {

        $this->saida = array();

        foreach ($multiArray as $arr) {

            // vefifica se o indice é um array, separa o array
            $new = $arr[$indexArray];
            unset($arr[$indexArray]);

            $this->saida[] = $arr;

            // se for array continua
            if (is_array($new)) {
                $this->saida[] = $this->flatMultidimensionalArrayRecursive($new, $indexArray);
            }
        }

        /*
         * Segundo loop ára remover elementos vazios
         */
        $saida = array();
        foreach ($this->saida as $arr) {

            if (is_array($arr)) {
                $saida[] = $arr;
            }
        }

        return $saida;
    }

    /**
     * Trabalha para o método $this->flatMultidimensionalArray()
     * @param <type> $multiArray
     * @param <type> $indexArray
     */
    function flatMultidimensionalArrayRecursive($multiArray, $indexArray) {

        foreach ($multiArray as $arr) {

            // vefifica se o indice é um array, separa o array
            $new = $arr[$indexArray];
            unset($arr[$indexArray]);

            if ($arr)
                $this->saida[] = $arr;

            if (is_array($new)) {
                $this->saida[] = $this->flatMultidimensionalArrayRecursive($new, $indexArray);
            }
        }
    }

    function getGrupoParents($grupo_id, $modulo_id) {

        // se for modelo 'paginas' usa hierarquia de forma diferente
        if($this->uri->segment(2) == 'paginas'){
            $hierarchy = $this->get_arquitetura(false);
            $hierarchy = $hierarchy['rows'];
            $indexArray = 'rel';
        } 
        // posts, calendario, loja
        else {
            $hierarchy = $this->lista_grupos(array('co' => $modulo_id));
            $indexArray = 'sub';
        }
        
        
//        mybug($hierarchy['rows'], true);
        $gruposFlated = $this->flatMultidimensionalArray($hierarchy, $indexArray);

        $reverseOrder = array_reverse($gruposFlated);

        // percorre todos os grupos para encontrar o selecionado
        $parents = array();
        $onTrail = false;
        $levelGrupo = 0;
        foreach ($reverseOrder as $gru) {

            if ($onTrail === true) {// rastreamento iniciado
                // se for de nível inferior, e menor que zero,
                // senão já passou para outro grau de parentesco
                if ($levelGrupo > $gru['level']) {
                    $parents[] = $gru;

                    if ($gru['level'] == 0) {
                        break;
                    }
                } else if ($gru['level'] == $levelGrupo) {// mesmo nível
                    continue;
                } else {
                    // então pode parar
//                    $gru['level'] = 0;
                    break;
                }
            }

            if ($gru['id'] == $grupo_id) {// ao encontrar começa rastrear parentesco
                $parents[] = $gru;
                $onTrail = true;
                $levelGrupo = $gru['level'];
            }
        }

        // reinverte para direção normal
        $normalParents = array_reverse($parents);
        return $normalParents;
    }
    
    // -----------------------------------------------------------------------
    /**
     * Recebe ID e NICK do post, ID do módulo e ID do grupo e monta a URI
     * completa para salvar no BD.
     * 
     * @param array $post
     * @param int $grupo_id
     * @param int $modulo_id
     * @return string 
     */
    public function get_full_uri($post, $grupo_id, $modulo_id){
        
        // configurações passadas para outros métodos
        $model = $this->cms_libs->dados_menus_raiz($modulo_id);
        $post_uri = $model['front_uri'];
        
        // pega grupo com seus parentes, se houver
        $grupoParents = $this->getGrupoParents($grupo_id, $modulo_id);

//        mybug($gruposFlated);
        // monta uri
        // usa o grupo_nick?
        if(strpos($post_uri, '[grupo]') !== FALSE){
            
            // pega o último grupo do array, o grupo mais próximo do post
            $trocapor = $grupoParents[count($grupoParents)-1];            
            
            $post_uri = str_replace('[grupo]', $trocapor['nick'], $post_uri);
            
        } else if(strpos($post_uri, '[grupos]') !== FALSE){
            
            $trocapor = '';
            /** depreciado **
            if(isset($grupoParents)){
                foreach($grupoParents as $grupo){
                    $trocapor .= $grupo['nick'].'/';
                }
                $trocapor = trim($trocapor, '/');
            }
             * 
             */
            // pega simplespente a página mais próxima, pois já vem com 
            // uri de get_arquitetura()
            $ParenteMaisProximo = end($grupoParents);
            $trocapor = $ParenteMaisProximo['uri'];
            
            $post_uri = str_replace('[grupos]', $trocapor, $post_uri);
        }
        // usa o nick do conteúdo?
        if(strpos($post_uri, '[nick]') !== FALSE){           
            $post_uri = str_replace('[nick]', $post['nick'], $post_uri);
        }
        // usa o ID do conteúdo?
        if(strpos($post_uri, '[id]') !== FALSE){           
            $post_uri = str_replace('[id]', $post['id'], $post_uri);
        }
        
        
//        mybug($post_uri);
        
        return trim($post_uri, '/');
        
    }

    /**
     * Parseia dados do grupo individualmente
     * @param <type> $arrayDoGrupo
     * @return <type>
     */
    function _parse_grupo($arrayDoGrupo) {

        if (count($arrayDoGrupo) == 0)
            return false;

        $saida = array();
        foreach ($arrayDoGrupo as $index => $val) {

            if ($index == 'status') {

                if ($val == 1) {
                    $val = 'ativo';
                } else if ($val == 0) {
                    $val = 'inativo';
                } else if ($val == 2) {
                    $val = 'editando';
                }
            }

            if ($index == 'img') {
                $cores = $this->get_grupo_cores($val);
                $saida['grupoCor1'] = $cores['cor1'];
                $saida['grupoCor2'] = $cores['cor2'];
            }

            $saida[$index] = $val;
        }

        return $saida;
    }
    
    /**
     * Prepara as páginas para informações de exibição
     *
     * @param mixed $array
     * @return
     */
    private function parse_arquitetura($array) {
        
        if (count($array) == 0)
            return false;
        
        $modulo = $this->modulo;
        
        // percorre array
        $saida = array();


        foreach ($array as $row) {
            if ($row['status'] == 1)
                $row['status'] = 'ativo';
            else if ($row['status'] == 0)
                $row['status'] = 'inativo';
            else if ($row['status'] == 2)
                $row['status'] = 'editando';

            // se for grupo de conteúdo
            if (isset($row['level']) && $row['level'] == 0) {
                // Obtêm cores do crupo
                // trata as cores
                $cores = $this->get_grupo_cores($row['img']);
                $row['grupoCor1'] = $cores['cor1'];
                $row['grupoCor2'] = $cores['cor2'];
            }
            

            // se existe comentários pesquisa a quantidade
            if (isset($modulo['comments']) && $modulo['comments'] == 1) {
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 1);
                $sqlA = $this->db->get('cms_comentarios');
                $row['comm_ttl'] = $sqlA->num_rows();
                // novos
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 2);
                $sqlN = $this->db->get('cms_comentarios');
                $row['comm_new'] = $sqlN->num_rows();
            }
            // se existe inscrição pesquisa a quantidade
            if (isset($modulo['inscricao']) && $modulo['inscricao'] == 1) {
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 1);
                $sqlA = $this->db->get('cms_inscritos');
                $row['insc_ttl'] = $sqlA->num_rows();
                // novos
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 2);
                $sqlN = $this->db->get('cms_inscritos');
                $row['insc_new'] = $sqlN->num_rows();
            }
            
            $saida[] = $row;
        }
        
        return $saida;
    }

    /**
     * Prepara pesquisa de item de menu
     *
     * @param mixed $array
     * @return
     */
    function parse_lista_conteudos($array, $modulo = array()) {
        
        if (count($array) == 0)
            return false;
        
        $modulo = $this->modulo;
        
        // percorre array
        $saida = array();


        foreach ($array as $row) {
            if ($row['status'] == 1)
                $row['status'] = 'ativo';
            else if ($row['status'] == 0)
                $row['status'] = 'inativo';
            else if ($row['status'] == 2)
                $row['status'] = 'editando';

            // se for grupo de conteúdo
            if ($row['grupo'] == 0) {
                $row['grupo'] = 'Grupo';

                // Obtêm cores do crupo
                // trata as cores
                $cores = $this->get_grupo_cores($row['img']);
                $row['grupoCor1'] = $cores['cor1'];
                $row['grupoCor2'] = $cores['cor2'];
            }
            // se for conteúdo
            else {

                // pega grupo com seus parentes, se houver
                $grupoParents = $this->getGrupoParents($row['grupo'], $modulo['id']);
                $row['grupoParents'] = $grupoParents;
                $row['grupoParents'] = false;
            }

            // se existe comentários pesquisa a quantidade
            if (isset($modulo['comments']) && $modulo['comments'] == 1) {
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 1);
                $sqlA = $this->db->get('cms_comentarios');
                $row['comm_ttl'] = $sqlA->num_rows();
                // novos
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 2);
                $sqlN = $this->db->get('cms_comentarios');
                $row['comm_new'] = $sqlN->num_rows();
            }
            // se existe inscrição pesquisa a quantidade
            if (isset($modulo['inscricao']) && $modulo['inscricao'] == 1) {
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 1);
                $sqlA = $this->db->get('cms_inscritos');
                $row['insc_ttl'] = $sqlA->num_rows();
                // novos
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 2);
                $sqlN = $this->db->get('cms_inscritos');
                $row['insc_new'] = $sqlN->num_rows();
            }
            // coloca no array
            // $saida[] = array('id' => $row['id'],
            // 'titulo' => $row['titulo'],
            // 'tipo' => $tipo,
            // 'status' => $att);
            $saida[] = $row;
        }
//        mybug($saida);
        return $saida;
    }

    /**
     * Entra com a string das cores concatenadas e retorna um array
     * 
     * @param string $tagdecor
     * @param string $tipo Para fazer alguma distinção no futuro
     * @return array Array com as duas cores array('grupoCor1' => #cor, 'grupoCor2' => #cor)
     */
    function get_grupo_cores($tagdecor, $tipo = 'conteudo') {
        $tagdecor = trim($tagdecor);
        $cores = (strlen($tagdecor) > 6) ? explode('|', $tagdecor) : array("", "");

        if (!is_array($cores) || count($cores) < 2) {
            $cores = array("", "");
        }

//        mybug($cores);
        $row['cor1'] = $cores[0];
        $row['cor2'] = $cores[1];

        return $row;
    }

    /**
     * Dados de UM omentário de um conteúdo
     * */
    function comentario_dados($id_cont) {
        $this->db->where('id', $id_cont);
        $sql = $this->db->get('cms_comentarios');
        return $sql->row_array();
    }

    /**
     * Todos os comentários de um conteúdo
     * */
    function comentarios_dados($id_cont) {
        $this->db->where('conteudo_id', $id_cont);
        $this->db->order_by('data desc');
        $this->db->order_by('hora desc');
        $sql = $this->db->get('cms_comentarios');
        return $this->parse_comentarios_dados($sql->result_array());
    }

    /**
     * Prepara pesquisa de item de menu
     *
     * @param mixed $array
     * @return
     */
    function parse_comentarios_dados($array) {
        if (count($array) == 0)
            return false;
        // percorre array
        $saida = array();
        foreach ($array as $row) {
            if ($row['status'] == 1
            )
                $row['status'] = 'ativo';
            else if ($row['status'] == 0
            )
                $row['status'] = 'inativo';
            else if ($row['status'] == 2
            )
                $row['status'] = 'editando';

            $saida[] = $row;
        }
        return $saida;
    }

    /**
     * Insere no banco os dados do grupo
     * @param array $var Opções na uri, como: id, co
     * @return bool
     */
    function grupo_salva($var) {
        // - salva os dados do menu principal Raiz
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $txt = trim($this->input->post('txt'));
        $cor1 = trim($this->input->post('cor1'));
        $cor2 = trim($this->input->post('cor2'));
        $rel = $this->input->post('grupos');

        $dados['titulo'] = $titulo;
        $dados['resumo'] = $txt;        
        $dados['img'] = $cor1 . '|' . $cor2;
        $dados['tipo'] = 'conteudo';
        $dados['rel'] = $rel;
        $dados['grupo'] = 0;

        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['dt_ini'] = date("Y-m-d");
            $dados['hr_ini'] = date("H:i:s");
            $dados['lang'] = get_lang();
            $dados['status'] = 1;
            $dados['modulo_id'] = $var['co'];
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);

            $sql = $this->db->insert('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Novo Grupo: <a href=\"" . cms_url('cms/paginas/grupoEdita/co:' . $var['co'] . '/id:' . $this->db->insert_id()) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {

            $nick = $this->input->post('nick_edita');
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);

            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Grupo: <a href=\"" . cms_url('cms/paginas/grupoEdita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }

        return $sql;
    }

    // -------------------------------------------------------------------------
    
    function conteudo_salva($var) {
        // - salva os dados do menu principal Raiz
        $grupo = $this->input->post('grupos');
        $rel = $this->input->post('rel');
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $data = trim($this->input->post('dt1'));
        $status = $this->input->post('status');
        $resumo = trim($this->input->post('resumo'));
        $tags = trim($this->input->post('tags'));
        $txt = trim($this->input->post('txt'));
        $mytags = $this->input->post('mytags');
        $show = $this->input->post('show');
        $scripts = $this->input->post('scripts');


        $dados['titulo'] = $titulo;
        $dados['resumo'] = $resumo;
        $dados['dt_ini'] = formaSQL($data);
        $dados['grupo'] = $grupo;
        $dados['modulo_id'] = $var['co'];        
        $dados['tags'] = $tags;
        $dados['status'] = $status;
        $dados['txt'] = campo_texto_utf8($txt);
        $dados['rel'] = prep_rel_to_sql($rel);
        $dados['atualizado'] = date("Y-m-d H:i:s");

//        mybug($mytags);
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            
            $dados['tipo'] = 'conteudo';
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['hr_ini'] = date("H:i:s");
            $dados['lang'] = get_lang();
            $dados['dt_ini'] = date("Y-m-d");
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            

            $sql = $this->db->insert('cms_conteudo', $dados);
            $esteid = $this->db->insert_id();
            // -- >> LOG << -- //
            $oque = "Nova Página: <a href=\"" . cms_url('cms/paginas/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            
            // antes de salvar, salva última versão
            $this->cms_libs->save_revision($var['id']);

            $nick = $this->input->post('nick_edita');

            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['full_uri'] = $this->get_full_uri(array('id' => $var['id'], 'nick' =>$dados['nick']), $grupo, $var['co']);
            $dados['txtmulti'] = $this->concatenateMultiContents();
            $dados['show'] = $show;
            $dados['scripts'] = $scripts;

            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Página: <a href=\"" . cms_url('cms/paginas/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }

        // faz atualização das tags. precisa do ID, por isso está aqui
        $this->set_tag_conteudo($mytags, $var);
        
        // faz atualização dos metadados
        $this->cms_libs->set_metadados($var['id']);
        
        // escreve rotas dinâmicas
        $this->cms_libs->write_dynamic_routes();


        return $esteid;
    }
    
    // -------------------------------------------------------------------------
    
    public function conteudo_salva_copia($var){
        
        $grupo = $this->input->post('grupos');
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $data = trim($this->input->post('dt1'));
        $status = $this->input->post('status');
        
        $conteudo = $this->conteudo_dados($var);
         
        
        
        $dados['titulo']   = $titulo;
        $dados['ordem']    = $conteudo['ordem'];
        $dados['resumo']   = $conteudo['resumo'];
        $dados['dt_ini']   = formaSQL($data);
        $dados['tags']     = $conteudo['tags'];
        $dados['grupo']    = $grupo;
        $dados['modulo_id'] = $conteudo['modulo_id'];
        $dados['tipo']     = $conteudo['tipo'];
        $dados['status']   = $status;
        $dados['txt']      = $conteudo['txt'];
        $dados['rel']      = $conteudo['rel'];
        $dados['autor']    = $this->phpsess->get('admin_id', 'cms');
        $dados['hr_ini']   = date("H:i:s");
        $dados['lang']     = $conteudo['lang'];
        $dados['txtmulti'] = $conteudo['txtmulti'];
        $dados['show']     = $conteudo['show'];
        $dados['galeria']  = $conteudo['galeria'];
        $dados['destaque'] = $conteudo['destaque'];
        $dados['img']      = $conteudo['img'];
        $dados['semana']   = $conteudo['semana'];
        $dados['extra']    = $conteudo['extra'];
        $dados['nick']     = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
        
        $sql = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();
        // -- >> LOG << -- //
        $oque = "Cópia de Página: <a href=\"" . cms_url('cms/paginas/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
        $this->cms_libs->faz_log_atividade($oque);
        
        $new_tags = array();
        $t = $this->get_conteudo_tags($var['id']);
        
        
        if(count($t) > 0){
            foreach($t as $tag){
                $new_tags[] = $tag['id'];
            }
            
            // faz atualização das tags. precisa do ID, por isso está aqui
            $this->set_tag_conteudo($new_tags, $esteid);
        }        
        
        // escreve rotas dinâmicas
        $this->cms_libs->write_dynamic_routes();


        return $esteid;
        
       
        
    }

    /**
     * Recebe a lista de tag IDs e o ID do conteúdo
     * Remove as tags existentes e insere as novas
     * 
     * @param array $listaTagIds
     * @param array|int $var
     * @return bool
     */
    function set_tag_conteudo($listaTagIds = array(), $var = array()) {

        if (!is_array($var)) {
            $id = $var;
        } else {
            $id = $var['id'];
        }

        if (count($listaTagIds) == 0 || $listaTagIds === false) {
            $this->db->delete('cms_tag_conteudo', array('conteudo_id' => $id));
            return false;
        }

        // remove itens duplicados
        $listaTagIds = array_unique($listaTagIds);

        // 1º) Recupera as tags originais
        $tagsSalvas = $this->get_conteudo_tags($id, 'id');        

        // 2º) percorre as tags salvas e separa
        $tagsToUpdate = array();

        if ($tagsSalvas) {
            
            // combina os arrays, para ter listagem completa de verificação
            $fullArrayTags = array_merge($listaTagIds, $tagsSalvas);
            $fullArrayTags = array_unique($fullArrayTags);
            
            
            
            for ($i = 0; $i < count($fullArrayTags); $i++) {
                
                $tagNova = (isset($listaTagIds[$i])) ? $listaTagIds[$i] : FALSE;
                $tagSalva = (isset($tagsSalvas[$i])) ? $tagsSalvas[$i] : FALSE;

                // se a tag nova não existe nas tags salvas >> update
                if ($tagNova && array_search($tagNova, $tagsSalvas) === FALSE) {
                    $tagsToUpdate[] = $tagNova;
                } 
                // se a tag salva não existe nas novas tags >> update
                if($tagSalva && array_search($tagSalva, $listaTagIds) === FALSE) {                    
                    $tagsToUpdate[] = $tagSalva;
                }
            }
        } else {
            // se não existe tags no conteúdo, só pode salvar as que existirem
            $tagsToUpdate = $listaTagIds;
        }



        // senão percorre a lista atualizando na tabela de relacionamentos
        foreach ($tagsToUpdate as $tagId) {


            // verifica a existência
            $this->db->where('tag_id', $tagId);
            $this->db->where('conteudo_id', $id);
            $result = $this->db->get('cms_tag_conteudo');
            $ttl = $result->num_rows();

            // se existe >> remove
            if ($ttl > 0) {
                $this->db->where('tag_id', $tagId);
                $this->db->where('conteudo_id', $id);
                $this->db->delete('cms_tag_conteudo');
            }
            // senão >> insere
            else {
                // insere tag por tag
                $dados['conteudo_id'] = $id;
                $dados['tag_id'] = $tagId;
                $this->db->insert('cms_tag_conteudo', $dados);
            }
        }

        return true;
    }

    /**
     * Insere no banco os dados da Tag
     * @param array $var Opções na uri, como: id, co
     * @return bool
     */
    function tag_salva($var) {
        // - dados
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $txt = trim($this->input->post('txt'));
        $cor1 = trim($this->input->post('cor1'));
        $cor2 = trim($this->input->post('cor2'));

        $dados['titulo'] = $titulo;
        $dados['resumo'] = $txt;
        $dados['img'] = $cor1 . '|' . $cor2;
        $dados['tipo'] = 'tag';

        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['dt_ini'] = date("Y-m-d");
            $dados['hr_ini'] = date("H:i:s");
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['grupo'] = 0;
            $dados['lang'] = get_lang();
            $dados['status'] = 1;
            $dados['modulo_id'] = $var['co'];

            $sql = $this->db->insert('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Nova Tag: <a href=\"" . cms_url('cms/paginas/tagEdita/co:' . $var['co'] . '/id:' . $this->db->insert_id()) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Tag: <a href=\"" . cms_url('cms/paginas/tagEdita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }

        return $sql;
    }

    /**
     * Combina os campos de texto multicontent e retorna uma string.
     * @return type 
     */
    function concatenateMultiContents() {

        $retorno = '';
        $glue = '<!--breakmulti-->';
        $partes = array();
        foreach ($_POST as $chv => $vlr) {

            if (substr($chv, 0, 9) == 'txtmulti_') {
                $partes[] = $vlr;
            }
        }

        $retorno = implode($glue, $partes);

        return $retorno;
    }

    /**
     * Pega os dados na Library 'cms/cms_libs' e parseia os dados
     *
     * @param mixed $var
     * @return
     */
    function conteudo_dados($var) {
        
//        $sql = $this->db->from('cms_conteudo')
//                     ->where('id', $var['id'])
//                     ->get();
//        // parseia conteudo
//        $dd = array();
//
//        $dadosConteudo = $sql->row_array();
//        
//        foreach ($dadosConteudo as $chv => $vlr) {
//            
//            // identifica se é o campo 'tags' com valor de cor hexadecimal #ffff
//   
//                // os valores de cor nos grupos de usuário estão guardados no campo 'filtro'
//                if ($tb == 'cms_usuarios' && $chv == 'filtro') {
//                    $cores = (strlen($vlr) > 6) ? explode('|', $vlr) : array("", "");
//                    $dd['cor1'] = $cores[0];
//                    $dd['cor2'] = $cores[1];
//                }
//                // os valores de cor nos grupos de conteudo estão guardados no campo 'tags'
//                else if ($tb != 'cms_usuarios' && $chv == 'img') {
//                    $cores = (strlen($vlr) > 6) ? explode('|', $vlr) : array("", "");
//                    $dd['cor1'] = $cores[0];
//                    $dd['cor2'] = $cores[1];
//                }                   
//
//            $dd[$chv] = $vlr;
//        }
//        
        
        $dd = $this->cms_libs->conteudo_dados($var);
        if (!$dd)
            return false;
        // percorre array
        $saida = array();
        foreach ($dd as $chv => $vlr) {
            // data
            if ($chv == 'dt_ini')
                $saida['dt1'] = formaPadrao($vlr);
            if ($chv == 'dt_fim')
                $saida['dt2'] = formaPadrao($vlr);
            // quantidade de imagens na galeria
            if ($chv == 'galeria') {
                if (strlen($vlr) == 0) {
                    $saida['quantGal'] = 0;
                } else {
                    $array = explode('|', $vlr);
                    $saida['quantGal'] = count($array);
                }
            }
            // coloca no array
            $saida[$chv] = $vlr;
        }

        return $saida;
    }

    /**
     * Retorna um array com as tags deste módulo
     *
     * @param int $moduloID
     * @return array
     */
    function get_modulo_tags($moduloID) {

        // pesquisa as tags deste conteúdo

        $this->db->select('*');
        $this->db->order_by('ordem');
        $this->db->where('modulo_id', $moduloID);
        $this->db->where('tipo', 'tag');
        $this->db->where('status', 1);
        $this->db->from('cms_conteudo');

        $sql = $this->db->get();

        $tags = $sql->result_array();

        return $tags;
    }

    /**
     * Entra com o ID do conteúdo e retorna um array das tags com as cores
     * Retorna FALSE caso não exista
     *
     * @param int $conteudoID
     * @return array
     */
    function get_conteudo_tags($conteudoID, $onlyCollumn = false) {


        // pesquisa as tags deste conteúdo e combina com a tabela cms_conteudo
        // para retornar os dados completos das tags
        $this->db->select('*');
//        $this->db->where('cms_tag_conteudo.status', 1);
        $this->db->from('cms_tag_conteudo as tag');
        $this->db->join('cms_conteudo as conteudo', 'conteudo.id = tag.tag_id');
        $this->db->where('tag.conteudo_id', $conteudoID);
        $this->db->where('conteudo.status', 1);
        $this->db->order_by('conteudo.ordem');


        $sql = $this->db->get();
//        mybug($this->db->last_query());
        $tags = $sql->result_array();

        if ($sql->num_rows() == 0) {
            return false;
        }

        // Parseia dados para retornar as cores
        $saida = array();

        foreach ($tags as $tag) {


            $cores = $this->get_grupo_cores($tag['img']);
            $tag['cor1'] = $cores['cor1'];
            $tag['cor2'] = $cores['cor2'];

            // pode retornar um único campo, ou o array conpleto
            if ($onlyCollumn) {
                $saida[] = $tag[$onlyCollumn];
            } else {
                $saida[] = $tag;
            }
        }

        return $saida;
    }

    /**
     * Monta o bloco com as tags do módulo e as tags do conteúdo
     * 
     * @return string
     */
    function get_view_tags() {

        $var = $this->uri->to_array(array('co', 'id'));
        $conteudoID = $var['id'];
        $dados['modulo_tags'] = $this->get_modulo_tags($var['co']);
        $dados['conteudo_tags'] = $this->get_conteudo_tags($conteudoID);

//        mybug($dados);
        // se não existem tags, retorna vazio
        if (!$dados['modulo_tags']) {
            return '';
        }

        // monta a view
        $view = $this->load->view('cms/tags', $dados, true);

        return $view;
    }

}

?>