<?php

/**
 * @dependencies: $this->load->library('site_utils, cms_conteudo').
 * 
 * Biblioteca para manipular listas de páginas/conteúdo do CMS.
 * 
 * Para retornar uma lista de conteúdo. use as configurações abaixo:
 * $posts = $this->cms_posts->get(array(
        'modulo_id'   => 7,
        'base_url'    => 'posts/index',
        'grupo_id'    => false, // false|array()|int
        'per_page'    => 10,
        'start_page'  => false,// sobrepõe 'pag' da uri
        'ignore_pagination' => false,// pega a partir do indice 0 (zero)
        'status'      => 1, // 0|1|2
        'ordem'       => 'cont.dt_ini desc',
        'campos'      => 'id, nick, full_uri, titulo, resumo, dt_ini, galeria, modulo_id',
        'dt_ini'      => false,
        'tipo'        => 'conteudo',
        'destaque'    => false,
        'ignore'      => false, // ID dos posts que serão ignorados
        'tag'         => false,
        'tags'        => false,
        'gettags'     => false,
        'lang'        => 'pt',
        'gallery_tag' => 1,
        'gallery_no_tag' => false
    ));
        
 * Se necessário for a paginação será ativada e é resgatada pelo método:
 * $pagination = $this->cms_posts->pagination();
 */
class Cms_posts{
    
    private $ci = NULL;
    private $user_admin = FALSE;    
    private $set_pagination = false;
    private $modulos_use_precos = array(21, 52); // IDs de módulos que usem preços e descontos
    private $mod_tags = array(); // tags do módulo
    private $this_group_hierarchy = FALSE; // salva os grupos para uri
    public  $this_precos = false; // armazena array dados de preço e descontos, se houver
    public  $total = 0;
    public  $pagination_num_links = 8;// quantas páginas são exibidas
    /*
     * CONFIGURAÇÕES DE PESQUISA
     */
    private $default = array(
        'modulo_id'   => 7,
        'base_url'    => 'posts/index',
        'grupo_id'    => false, // false|array()|int
        'per_page'    => 10,
        'start_page'  => false,// sobrepõe 'pag' da uri
        'ignore_pagination' => false,// pega a partir do indice 0 (zero)
        'status'      => 1, // 0|1|2
        'ordem'       => 'cont.dt_ini desc',
        'campos'      => 'id, nick, full_uri, titulo, resumo, dt_ini, galeria, modulo_id',
        'dt_ini'      => false,
        'tipo'        => 'conteudo',
        'destaque'    => false,
        'ignore'      => false, // ID dos posts que serão ignorados
        'tag'         => false,
        'tags'        => false,
        'gettags'     => false,
        'lang'        => 'pt',
        'gallery_tag' => 1,
        'gallery_no_tag' => false
    );



    public function __construct() {
        $this->ci = &get_instance();
        // se existir, pega referência dos módulos que usam preços
    }
    
    // -------------------------------------------------------------------------
    /**
     * Invocado no Front_controller para instanciar variáveis importantes.
     * @param type $config
     */
    public function config($config){
        $this->lang = $config['lang'];
        $this->user_admin = $this->ci->cms_conteudo->admin_verify();
    }
    
    // -------------------------------------------------------------------------
    /**
     * Parseia as configurações iniciais e seta pariáveis
     * @param       array       $config 
     * @access      private
     * @return      void
     */
    public function initialize($params) {

        foreach($this->default as $key => $val){
            // se variável foi passada
            if(isset($params[$key])){
                $this->$key = $params[$key];
            } 
            // senão usar default
            else {
                $this->$key = $val;
            }
        }
    }

    // -------------------------------------------------------------------------
    /**
     * Método principal que retorna os registros pelos parâmetros passados.
     * Se precisar ativa paginação.
     * Se o módulo tiver injeta os preços e cupons.
     * 
     * @param type $post
     * @return boolean
     */
    public function get($post = false){
        
        // configurações utilizadas neste método       
        $this->initialize($post);
        
        $campos = $this->prep_campos($this->campos);
      
        // trata variaveis 
        if($this->ignore_pagination){
            $offset = 0;
        } 
        // usa paginação da configuração
        else if($this->start_page !== FALSE){
            $offset = ($this->start_page - 1) * $this->per_page;
        }
        // usa paginação da url
        else {
            $v = $this->ci->uri->to_array('pag');
            if($v['pag'] == ''){
                $offset = 0;
            } else {
                $offset = ($v['pag']-1) * $this->per_page;
            }
        }
        
        
        // se for usado o nick da tag, retorna o ID
        $this->tag = $this->get_tag_id($this->tag);
        
        /*
         * Monta Query com relacionamentos
         */
        // a partir desta data. forma SQL
        if ($this->dt_ini){ $this->ci->db->where('cont.dt_ini >=', $this->dt_ini); }            
        
        // de um grupo específico  
        if ($this->grupo_id === FALSE){
            $this->ci->db->where('cont.grupo !=', 0);// todos
        } else if(is_array($this->grupo_id)){
            $this->ci->db->where_in('cont.grupo', $this->grupo_id);
        } else if(is_numeric($this->grupo_id)){
            $this->ci->db->where('cont.grupo', $this->grupo_id);
        }
        
        // ignorar posts
        if($this->ignore){    
            $ignore = explode(',', $this->ignore);
            $this->ci->db->where_not_in('cont.id', $ignore);
        }
        
        // filtro por destaques
        if($this->destaque !== FALSE){ $this->ci->db->where('cont.destaque', $this->destaque);}
        
        // conteúdo
        $this->ci->db->from('cms_conteudo as cont');
        // grupo
        $this->ci->db->join('cms_conteudo as grupo', 'grupo.id = cont.grupo'); 
        $campos .= ', grupo.titulo as grupo_nome, grupo.nick as grupo_nick, grupo.id as grupo_id';
        // módulo
        $this->ci->db->join('cms_modulos as modu', 'modu.id = cont.modulo_id');
        $campos .= ', modu.front_uri';
        // com tag específica        
        if($this->tag){
            $this->ci->db->join('cms_tag_conteudo as tagcon', 'tagcon.conteudo_id = cont.id', 'left');
            $this->ci->db->join('cms_conteudo as tags', 'tagcon.tag_id = tags.id', 'left');
            $this->ci->db->where('tags.id', $this->tag);
            $campos .= ', tagcon.tag_id, tags.titulo as tag_nome, tags.nick as tag_nick';
        }
        // com grupo de tags
        if($this->tags){
            $this->ci->db->join('cms_tag_conteudo as tagcon', 'tagcon.conteudo_id = cont.id', 'left');
            $this->ci->db->join('cms_conteudo as tags', 'tagcon.tag_id = tags.id', 'left');
            $this->ci->db->where_in('tags.id', $this->tags);
     
        }
        // produtos        
        $this->ci->db->join('cms_produtos as produto', 'produto.conteudo_id = cont.id', 'left');
        $campos .= ', produto.codigo, produto.download, produto.estoque, produto.dimensoes, produto.peso, produto.valor_base';
        
        
        $this->ci->db->order_by($this->ordem);
        
        $this->ci->db->where('cont.modulo_id', $this->modulo_id);
        $this->ci->db->where('cont.status', $this->status);
        $this->ci->db->where('cont.tipo', $this->tipo);
        $this->ci->db->limit($this->per_page, $offset);
        $this->ci->db->select('SQL_CALC_FOUND_ROWS '.$campos, false);
        $this->ci->db->where('cont.lang', $this->lang);
        
        $result = $this->ci->db->get();
        
//        mybug($this->ci->db->last_query(), true);
//        mybug($result->result_array());
        
        if($result->num_rows() == 0){
            return FALSE;
        }
               
        
        $query = $this->ci->db->query('SELECT FOUND_ROWS() AS `Count`');
        $this->total = $query->row()->Count;
        
//        mybug($this->total);
        
        // se o total de registros for maio que o resultado limitado,
        // ativa pagination
        if($this->total > $result->num_rows()){
            $this->set_pagination();
        }      
        
        $resultados = $result->result_array();
        // limpa memória
//        $result->free_result();
        
        return $this->post_parse($resultados);
        
        
    }
    
    // -------------------------------------------------------------------------
    
    /**
     * Faz o parseamento dos posts retornados. De acordo com as configurações
     * passadas injeta conteúdos complementares.
     * Ver configurações possíveis.
     * @param       array      $post
     * @return      array
     */
    public function post_parse($post){
        
        // coloca a hierarquia de grupos na memória
        $this->set_groups_hierarchy();
        
        $return = array();
        
        
        foreach ($post as $row){
//            mybug($row);
            // insere imagem de capa
            if($row['galeria'] != ''){
                $row['galeria'] = $this->get_gallery($row['galeria']);                
            }
            
            // verifica se faz uso de preços
            if(in_array($row['modulo_id'], $this->modulos_use_precos)){  
                
                $this->ci->load->helper('moeda');
                
                $precos_cupons = $this->get_precos_descontos($row['id']);
                
                // deixa a lib saber os dados básicos do conteúdo
                $this->ci->cms_conteudo->this_page = $row;
                
                $row['precos'] = $this->ci->cms_conteudo->precos_parser($precos_cupons);
                $row['cupons'] = $this->ci->cms_conteudo->cupons_parser($precos_cupons);
                
                // verifica se existe preço desconto
                $pf = $this->ci->cms_conteudo->preco_final(null, $row['precos']);
                $row['preco_final'] = formaBR($pf);

                
                
            }
            
            // tratamento de data
            if(isset($row['dt_ini'])){
                $row['dt_ini'] = formaPadrao($row['dt_ini']);
            }
            if(isset($row['dt_fim'])){
                $row['dt_fim'] = formaPadrao($row['dt_fim']);
            }
            
            if($this->gettags){
                $row['post_tags'] = $this->get_post_tags($row['id'], $row['modulo_id']);
            }
            
            // insere uri do conteúdo
            // se existe uri na base
            if($row['full_uri'] != ''){
                $row['uri'] = $row['full_uri'];
            } 
            // se não existe, faz pesquisa
            else {
                $row['uri'] = $this->get_post_uri($row);
            }
            //$row['uri'] = $this->get_post_uri($row);
            
            
            // insere adminbar
            $row['adminbar'] = $this->get_adminbar($row);
            
            $return[] = $row;
            
        }
//        
//        mybug($return, true);
        
        return $return;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Recupera os dados sobre preço e descontos.
     * 
     * @param       int         $post_id
     * @return      array
     */
    public function get_precos_descontos($post_id){
        
        // se NÃO estiver na memória, faz a pesquisa.
        
        $this->ci->db->where('conteudo_id', $post_id);
        $this->ci->db->order_by('data');
        $return = $this->ci->db->get('cms_precos'); 
        
        if($return->num_rows() == 0){
            return FALSE;
        }
        
        return $return->result_array();        
        
    }


    // -------------------------------------------------------------------------
    /**
     * Baseado nos dados do post e das configurações, gera o endereço do contéudo
     * @param type $post
     */
    public function get_post_uri($post){
        // configurações passadas para outros métodos
        $post_uri = (isset($post['front_uri']))  ? $post['front_uri']  : 'post/[grupo]/[nick]';
        // monta uri
        // usa o grupo_nick?
        if(strpos($post_uri, '[grupo]') !== FALSE){
            $post_uri = str_replace('[grupo]', $post['grupo_nick'], $post_uri);
        }
        // usa a hierarquia de grupos
        else if(strpos($post_uri, '[grupos]') !== FALSE){
            $arquitetura = $this->get_post_arquitetura($post);
            $last = $arquitetura[count($arquitetura)-1];
            $uri = $last['uri'];
            $post_uri = str_replace('[grupos]', $uri, $post_uri);
        }
        // usa o nick do conteúdo?
        if(strpos($post_uri, '[nick]') !== FALSE){           
            $post_uri = str_replace('[nick]', $post['nick'], $post_uri);
        }
        // usa o ID do conteúdo?
        if(strpos($post_uri, '[id]') !== FALSE){           
            $post_uri = str_replace('[id]', $post['id'], $post_uri);
        }
        
        
        return $post_uri;
    }
    
    // -------------------------------------------------------------------------
    /**
     * Filtra a hierarquia de grupos e retorna a hierarquia do post.
     * @param       array       $post
     * @return      array
     */
    public function get_post_arquitetura($post){
        
        // se os grupos ainda não estão armazenados
        if($this->this_group_hierarchy === false){
            $this->this_group_hierarchy = $this->set_groups_hierarchy();
        }
        
        $reverseOrder = array_reverse($this->this_group_hierarchy);
        
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

            if ($gru['id'] == $post['grupo_id']) {// ao encontrar começa rastrear parentesco
                $parents[] = $gru;
                $onTrail = true;
                $levelGrupo = $gru['level'];
            }
        }

        // reinverte para direção normal
        $normalParents = array_reverse($parents);
        
        return $normalParents;
        
    }


    // -------------------------------------------------------------------------
    /**
     * Faz a transformação do array retonado na pesquisa para a hierarquia
     * correta e altera a variavel original na memória.
     * @param       boolean         $multidimensional
     * @return      type
     */
    public function set_posts_arquitetura($multidimensional = FALSE){
        // inicializa
        $this->arquitetura = array();
        
              
        foreach ($this->this_group_hierarchy as $row) {

            if($row['rel'] == 0){// apenas de primeiro nível
                
                $row['level'] = 0;
                // monta uri. se houver um "main_controller"
                $row['uri'] = '';
                $row['uri'] .= $row['nick'];
                //$row['adminbar'] = $this->adminbar_template('cms/'.$this->_modcontrlr.'/edita/co:'.$row['modulo_id'].'/id:'.$row['id'], 'left');
                
                // se for multidimensional
                if($multidimensional){
                      
                    $sub = $this->_set_arquitetura_recursive($row['id'], 0, $row['uri'], $multidimensional);
                    $row['sub'] = $sub; // false ou array
                    $this->arquitetura[] = $row;
                    
                } else {
                    
                    $this->arquitetura[] = $row;                
                    $this->_set_arquitetura_recursive($row['id'], 0, $row['uri'], $multidimensional);                    
                    
                }
                
            }
            
        }
        
        $this->this_group_hierarchy = $this->arquitetura;
        
        return $this->this_group_hierarchy;
    }
    
    // -------------------------------------------------------------------------
    /**
     * Pesquisa recursivamente pelos grupos do módulo.
     * @param int $grupo_id
     * @param int $level
     * @param array $multidimensional
     * @return mixed 
     */
    private function _set_arquitetura_recursive($grupo_id, $level = 0, $uri = '', $multidimensional = true) {

        
        $saida = array();
        $level++;
        foreach ($this->this_group_hierarchy as $index => $row) {

            // se o grupo é filho... inclui
            if($row['rel'] == $grupo_id){
                
                $row['level'] = $level;
                $row['uri'] = $uri.'/'.$row['nick'];
                //$row['adminbar'] = $this->adminbar_template('cms/'.$this->_modcontrlr.'/edita/co:'.$row['modulo_id'].'/id:'.$row['id'], 'left');
                              
                // se for multimensional retorna um array
                if($multidimensional){
                
                    $sub = $this->_set_arquitetura_recursive($row['id'], $level, $row['uri'], $multidimensional);
                    $row['sub'] = $sub; // false ou array   
                    $saida[] = $row;
                    
                } else {
                    // senão... incrementa o array principal
                    $this->arquitetura[] = $row;                
                    $this->_set_arquitetura_recursive($row['id'], $level, $row['uri'], $multidimensional);
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

    // -------------------------------------------------------------------------
    /**
     * Recebe os IDs e as configurações, retorna um array com os arquivos
     * @param type $ids
     * @return boolean
     */
    private function get_gallery($ids){
        
        $ids = explode('|', $ids);
        
        // se existir o atributo de excluir tags, ele se sobrepõe ao
        // filtro pelas tags de imagens
        if($this->gallery_no_tag){
            $this->ci->db->where('tag_opt !=', $this->gallery_no_tag);
        }
        // filtra imagens tagueadas
        else if($this->gallery_tag !== FALSE){
            $this->ci->db->where('tag_opt', $this->gallery_tag); 
        } 
        
        
        $this->ci->db->where_in('id', $ids);
        $sql = $this->ci->db->get('cms_arquivos');

        if($sql->num_rows() == 0){
            return FALSE;
        } else if($this->gallery_tag !== FALSE){
            return $sql->row_array();
        } else {
            
            // faz a ordenação das imagens
            return $this->ci->cms_conteudo->parse_gallery($sql->result_array(), $ids);
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
     
        // incrementa $base_url se houverem alguns padrões na URI
        $uri = $this->ci->uri->to_array(array('tag'));
        if($this->tag && $uri['tag'] != ''){
            $this->base_url .= '/tag:'.$uri['tag'];
        }
        
        
        // configura paginação ----------------------------------
        $config['base_url'] = site_url($this->base_url);
        $config['total_rows'] = $this->total;
        $config['per_page'] = $this->per_page;
        $config['uri_segment'] = 3;
        $config['num_links'] = ceil($this->pagination_num_links / 2); // quantas páginas são mostradas antes de depois na paginação
        $config['full_tag_open'] = '<div class="pagination pagination-centered clearfix"><ul>';
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
     * Gera a paginação na view.
     * @return string
     */
    public function pagination(){
        if($this->set_pagination){
           return $this->ci->pagination->create_links(); 
        } else {
            return '';
        }
        
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
     * Faz busca pelos grupos deste módulo e coloca na memória.
     * @param       array|boolean       $setup
     * @return      array
     */
    function set_groups_hierarchy($setup = false) {
        
        // se foi passada uma configuração, se sobrepões
        // e faz nova pesquisa
        if($setup){
            $modulo_id = $setup['modulo_id'];
        }
        // se já está na memória
        else if($this->this_group_hierarchy){
            return $this->this_group_hierarchy;
        } 
        // senão faz a busca para salvar hierarquia
        else {
            $modulo_id = $this->modulo_id;
        }
        
        $this->ci->db->where('status', 1);            
        $this->ci->db->where('grupo', 0);       
        $this->ci->db->where('lang', $this->lang);
        $this->ci->db->where('modulo_id', $modulo_id);
        $this->ci->db->where('tipo', 'conteudo');
        $this->ci->db->order_by('ordem');
        $this->ci->db->select('id, nick, titulo, grupo, rel');
        $sql = $this->ci->db->get('cms_conteudo');

        
        $this->this_group_hierarchy = $sql->result_array();  
        
        $this->set_posts_arquitetura();
        
        return $this->this_group_hierarchy;
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
    
    // -------------------------------------------------------------------------
    /**
     * Recebe uma strins nnnn, nnnn, nnnn e retorna a referência da tabela cont.*
     * @param type $string
     * @return type
     */
    private function prep_campos($string){
        $campos = explode(',', $string);
        $return = array();
        foreach($campos as $c){
            
            $return[] = 'cont.'.trim($c);
            
        }
        
        return implode(',', $return);
    }
    
    // -------------------------------------------------------------------------
    /**
     *  Compara dois arrays para ver se existem valores iguais.
     * @param type $array1
     * @param type $array2
     * @return boolean
     */
    private function array_compare($array1, $array2){
        $return = false;
        
        foreach ($array1 as $v){
            if (in_array($v,$array2)){
               $return = true;
            }
        }
        return $return;
    }
    
    // -------------------------------------------------------------------------
    /**
     * Se o admin estiver logado... pesquisa o módulo, separa o controller e 
     * monta a uri da aadministração.
     * 
     * @param array $row
     * @return boolean|string 
     */
    private function get_adminbar($row){
        if($this->user_admin === FALSE){
            return false;
        }
        
        // pesquisa
        $this->ci->db->where('id', $row['modulo_id']);
        $result = $this->ci->db->get('cms_modulos');        
        $modulo = $result->row_array();
        
        // dados do admin
        $user = $this->user_admin;
        
        // se o usuário tem permissão
        if($modulo['tipo'] >= $user['tipo']){
            // separa o controller
            $controller = explode('/', $modulo['uri']);
            $seg = $controller[1];
            
            return $this->ci->cms_conteudo->adminbar_template('cms/'.$seg.'/edita/co:'.$modulo['id'].'/id:'.$row['id']);
        } else {
            return '';
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Recebe a variável. Checa se pode estar na URI. Se for string 
     * pesquisa pelo ID.
     * @param mixed $tags
     * @return int|boolean
     */
    private function get_tag_id($tags){
        
        // se não for passada pela biblioteca, checa a URI por 'tag:'
        if($tags === FALSE){
            $uri = $this->ci->uri->to_array(array('tag'));
            $tags = $uri['tag'];
        }
        
        // não tem nada
        if(strlen($tags) == 0){
            return FALSE;
        } 
        // vefirica se é ID ou NICK
        else if(!is_numeric($tags)){
            // é nick
            // pesquisa ID da tag
            $result = $this->ci->db->where('nick', $tags)
                        ->where('tipo', 'tag')
                        ->limit(1)
                        ->order_by('id')
                        ->select('id')
                        ->get('cms_conteudo');
            if($result->num_rows() > 0){
                $tag_array = $result->row_array();
                $tag_id = $tag_array['id'];
            } else {
                return FALSE;
            }
        } 
        // é número - ID
        else {
            $tag_id = $tags;
        }
        
        return $tag_id;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Salva tags do módulo na memória.
     * @param type $modulo_id
     */
    private function set_modulo_tags($modulo_id){
        
        if(count($this->mod_tags) == 0){
            $result = $this->ci->db->where('modulo_id', $modulo_id)
                        ->where('status', 1)
                        ->where('tipo', 'tag')
                        ->select('id, nick, titulo, img')
                        ->order_by('ordem')
                        ->get('cms_conteudo');
            
            if($result->num_rows() > 0){
                $this->mod_tags = $this->tags_parser($result->result_array());
            }
        }
        
    }


    // -------------------------------------------------------------------------
    /**
     * Retorna as tags do POST e 
     * @param type $post_id
     * @param type $modulo_id
     * @return boolean
     */
    public function get_post_tags($post_id, $modulo_id = FALSE){
        
        // se existe modulo_id, recupera as tags e memoriza
        if($modulo_id){
            $this->set_modulo_tags($modulo_id);
        } 
        
        // recupera ids das tags deste post
        $this->ci->db->from('cms_tag_conteudo');
        $this->ci->db->where('conteudo_id', $post_id);
        $this->ci->db->select('tag_id');
        $result = $this->ci->db->get();
        
        
        
        if($result->num_rows() > 0){
            
            $return = array();
            $post_tags = $result->result_array();
            $post_t_ids = array();
            
            // retorna apenas os IDs, simgle array
            foreach ($post_tags as $row){
                $post_t_ids[] = $row['tag_id'];
            }
            
            // compara os ID
            foreach($this->mod_tags as $row){
                
                if(in_array($row['id'], $post_t_ids)){
                    $return[] = $row;
                }
                
            }
            
            return $return;
        } else {
            return FALSE;
        }
        
    }
    

    
    // -------------------------------------------------------------------------
    /**
     * Parseia dados das tags.
     * Retorna:
     * 'id' =>  '54' 
       'nick' =>  'post-tag-a' 
       'nome' =>  'post tag a' 
       'cor1' =>  '#d3d3d3' 
       'cor2' =>  '#282626' 
     * @param type $tags
     * @return array
     */
    private function tags_parser($tags){
        
        $ret = array();
        
        foreach($tags as $row){
            
            $row['nome'] = $row['titulo'];
            unset($row['titulo']);
            
            // cores da tag
            $cores = (strlen($row['img']) > 6) ? explode('|', $row['img']) : array("#000000", "#cccccc");
            $row['cor1'] = $cores[0];
            $row['cor2'] = $cores[1];
            unset($row['img']);
            
            $ret[] = $row;
            
        }
        return $ret;
    }
    
}