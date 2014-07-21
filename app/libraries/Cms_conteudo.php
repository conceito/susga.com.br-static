<?php

/**
 * @dependencies: $this->load->library('site_utils').
 * 
 * Biblioteca para manipular páginas/conteúdo do CMS.
 * NÃO é adequado para tratar listagem de posts. Para isso usar Cms_posts. 
 * 
 * Para inserir páginas antes ou depois, use o seguinte padrão:
 * $config['append'] = array(
            array(
                'titulo' => 'Fale conosco', 'uri' => 'contato/faleconosco', 'class' => 'classe'
            ),
            array(
                'titulo' => 'Contato', 'uri' => 'contato/envia'
            )
        );
 * 
 * Para gerar o menu:
 * $menu = $this->cms_conteudo->generate_menu($config);
 * 
 * Como retornar dados das páginas:
 * 1º) Por padrão do método $this->set_page() deve estar no Front_controller
 * 
 * # Retorna dados do módulo da página na URI: 
 *   $this->cms_conteudo->set_get_modulo();
 * 
 * # Retorna dados básicos da página pela URI: 
 *   $this->cms_conteudo->get_page();
 * 
 * # Retorna dados básicos da página por variável e muda a referência da 
 *   página da URI: 
 *   $this->cms_conteudo->get_page(ID);
 * 
 * # Retorna dados básicos da página por variável SEM alterar a referência 
 *   da página que está na URI: 
 *   $this->cms_conteudo->get_page(ID, true);
 * 
 * # Retorna as páginas filhas. Pode retornar todas, ou de primeiro nível
 *   $this->cms_conteudo->get_children(true|false);
 * 
 * # Retorna a hierarquia de páginas/grupos de um conteúdo
 *   $hierarchy = $this->cms_conteudo->get_hierarchy();
 *   Para criar breadcrumb: $this->breadcrumb->add($hierarchy);
 * 
 * # Retorna array com todas as imagens da galeria da página da URI, 
 *   se for passado ID da página retorna a galeria sem armazenar na memória:
 *   $this->cms_conteudo->get_page_gallery([ID]); 
 * 
 * # Retorna array com todos os conteúdos relacionados no campo 'rel' da página, 
 *   se for passado ID da página retorna a galeria sem armazenar na memória:
 *   $this->cms_conteudo->get_page_related([ID]);
 * 
 * # Retorna array com todos os anexos da página da URI, se for passado ID 
 *   da página retorna os anexos sem armazenar na memória: 
 *   $this->cms_conteudo->get_page_attachments([ID]);
 * 
 * # Retorna array com dados de preço da página na URI, se passar o ID retorna
 *   do conteúdo sem armazená-lo.
 *   $this->cms_conteudo->get_precos([ID]);
 * 
 * # Retorna array com dados de cupons da página na URI, se passar o ID retorna
 *   do conteúdo sem armazená-lo.
 *   $this->cms_conteudo->get_cupons([ID]);
 * 
 * # Retorna os dados do cupom passado como argumento
 *   $this->cms_conteudo->get_cupom($cupom);
 * 
 * # Retorna preços e cupons separados num array multi
 *   $this->cms_conteudo->get_precos_cupons([ID]);
 * 
 * # Retorna o valor final do produto baseado na data. 
 * # Se passar um cupom faz o desconto.
 * # Se o array de preços e descontos for passado no segundo argumento
 *   não faz busca no BD
 *   $this->cms_conteudo->preco_final([cupom], [preços array]);
 * 
 * @todo: verificar a página ativa e marcar no menu
 */
class Cms_conteudo{
    
    // Configurações para montar menu
    public $config = array(
        // retorna menu com tags HTML
        'html' => true,        
        // retorna as páginas filhas, ou apenas de primeiro nível
        'child_pages' => true, 
        // inclui o item Página inicial
        'home' => array(
                        'titulo' => 'Início',
                        'uri' => 'inicio',
                        'class' => 'home-link'
                    ),     
        // inclui páginas no final
        'append' => false, 
        // inclui páginas no início, após a HOME
        'prepend' => false, 
        // campos da tabela que serão retornados
        'fields' => 'id, nick, titulo', 
        // ID do módulo
        'modulo_id' => 6,    
        'lang' => 'pt',
        // controller que gerencia páginas
        'main_controller' => '', 
        // classe do menu
        'ul_class' => 'main-menu',
        // ID do menu
        'ul_id' => 'menu', 
        // IDs das páginas que deve ser excluídas
        'exclude' => false,  
        // IDs das páginas que deve ser incluídas
        'include' => false, 
        // No caso do menu ser gerado várias vezes, esta opção sobrepõe as 
        // configurações anteriores
        'overload' => false,                                  
        // quando se está parseando children o level inicial é maior que zero
        // por isso não se deve contar com o level hierarquico da pagina
        'reset_level' => false,
        // classe para li ativo
        'active_class' => 'active'
    );
    
    /**************************************************
     * Até aqui os dados se referem a criação do menu 
     * Abaixo, variáveis sobre o conteúdo aberto.
     */
    public  $page_module = array(6);// módulos sobre 'paginas'
    public  $post_module = array(7);// módulos sobre 'posts'
    public  $cale_module = array(21);// módulos sobre 'calendario'
    private $ci = NULL;
    public  $user_admin = FALSE;
    public  $all_pages = false;  // armazena as páginas
    public  $all_groups = false;  // armazena os grupos do módulo    
    public  $this_page = false;     // armazena dados basicos da página (cms_conteudo)
    public  $this_modulo = false;   // armazena dados do módulo (cms_modulos)
    public  $this_galeria = false;  // armazena array da galeria, se houver
    public  $this_attachments = false; // armazena array dos anexos, se houver
    public  $this_precos = false; // armazena array dados de preço e descontos, se houver
    public  $this_related = false;// armazena conteúdos relacionados 
    public  $this_tags = false;// armazena as tags do conteúdo 
    public  $shortcodes = array(); 




    public function __construct() {
        $this->ci = &get_instance(); 
        
        if($this->ci->config->item('modulo_paginas')){
            $this->page_module = $this->ci->config->item('modulo_paginas');
        }
        if($this->ci->config->item('modulo_posts')){
            $this->post_module = $this->ci->config->item('modulo_posts');
        }
        if($this->ci->config->item('modulo_calendario')){
            $this->cale_module = $this->ci->config->item('modulo_calendario');
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Verifica se admin está logado e retorna dados do admin.
     * @return type 
     */
    public function admin_verify(){
        if($this->ci->sessao_model->esta_logado() === TRUE){
            // dados do admin
            $this->user_admin = $this->ci->sessao_model->user_infos($this->ci->phpsess->get('admin_id', 'cms'));            
        }
        
        return $this->user_admin;
    }

    // -------------------------------------------------------------------------
    /**
     * Recebe o nick ou ID da página, se não receber busca na URI.
     * Armazena dados da tabela cms_conteudo.
     * @param mixed $nick_or_id
     * @param bool $hidden_mode esta variável é introduzina na chamada do $this->get_page()
     * @return array 
     */    
    public function set_page($nick_or_id = NULL, $hidden_mode = false){
        
        $this->admin_verify();
        
        if($nick_or_id === NULL){            
            // extrai nick da URI
            $nick_or_id = $this->get_nick_by_uri();
        } 
        // se for vazio retorna 404
        
        // se não houver... volta
        if($nick_or_id === FALSE || $nick_or_id == ''){
            return FALSE;
        }
        // enviada manualmente
        if(is_numeric($nick_or_id)){
            $search_by = 'id';
        } else {
            $search_by = 'nick';
        }
        
        // pesquisa. Se for de primeiro nível o grupo == NULL
        $this->ci->db->from('cms_conteudo as cont');
        $campos = 'cont.*';
        
        $this->ci->db->join('cms_conteudo as grupo', 'grupo.id = cont.grupo', 'left');
        $campos .= ', grupo.id as grupo_id, grupo.nick as grupo_nick, grupo.titulo as grupo_titulo';
        
        $this->ci->db->join('cms_produtos as produto', 'produto.conteudo_id = cont.id', 'left');
        $campos .= ', produto.codigo, produto.download, produto.estoque, produto.dimensoes, produto.peso, produto.valor_base';
        
        $this->ci->db->where('cont.'.$search_by, $nick_or_id);
        
        // evita os Grupos/categorias, a não ser que a busca seja feita pelo ID
        // que indica que foi feito pelas rotas automáticas do módulo de Páginas
        $this->ci->db->where("(cont.grupo != 0 OR cont.modulo_id IN (".implode(',', $this->page_module)."))");
        
        $this->ci->db->where('cont.modulo_id !=', 37);// evita módulo Menus
        $this->ci->db->where('cont.tipo', 'conteudo');// evita as revisões
        $this->ci->db->order_by('id desc');
        $this->ci->db->select($campos);
        $result = $this->ci->db->get();       
//        mybug($this->ci->db->last_query());
        if($result->num_rows() == 0){
            return FALSE;
        }
//        mybug($result->row_array());
        // no modo hidden_mode, o conteúdo é retornado e não altera a página armazenada
        if($hidden_mode){
            return $result->row_array();
        } else {
            // armazena página
            $this->this_page = $result->row_array();
            // armazena módulo
            $this->this_modulo = $this->set_get_modulo();
        }
        
        
    }
    
    // -------------------------------------------------------------------------
    /**
     *  Independente da página retornada, este método retorna os dados da página
     * armazenada na variável de página principal... que vem da URI.
     * ** É necessário executar $this->set_page() antes **
     * @return array
     */
    public function get_url_page(){
        if($this->this_page === FALSE){
            return FALSE;
        } else {
            return $this->page_parser($this->this_page);
        }
    }


    // -------------------------------------------------------------------------
    
    /**
     * Retorna dados da página parseados.
     * ** É necessário executar $this->set_page() antes **
     * 
     * So retornar uma página setando o ID|nick no modo hidden_mode = true, 
     * o retorno não será armazenado na variável global da página atualmente.
     * Será um processo separado da página ativa. 
     * 
     * @param type $page_nick_id
     * @param type $hidden_mode
     * @return boolean
     */
    public function get_page($page_nick_id = NULL, $hidden_mode = false){
        
        // sobrescreve a página... não é necessário
        if($page_nick_id !== NULL){
            $actual_page = $this->set_page($page_nick_id, $hidden_mode);
        } else {
            // se não existe... tem que parar!
            if($this->this_page === FALSE) return FALSE;
            $actual_page = $this->this_page;
        } 
        
        // se o modo hidden_mode não estiver ativo, a página ficará na variável
        // $this->this_page, então temos que retornar para o parseamento
        if($hidden_mode === FALSE){
            $actual_page = $this->this_page;
        }
       
        // se não houver página identificada, retorna false
        if($actual_page === FALSE){
            return FALSE;
        } 
        // processa página
        else {
            return $this->page_parser($actual_page);
        }
        
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Parseia dados básidos da página/conteúdo.
     * @param type $page_array
     * @return array
     */
    private function page_parser($page_array){
        
        $return = array();
        $is_unique = true;
        
        // se for página única transforma em multi
        if(isset($page_array[0])){
            $is_unique = false;
        } else {
            $page_array = array($page_array);
        }
        
        // parseia dados
        foreach($page_array as $row){
            
            // datas
            if(isset($row['dt_ini'])){
                $row['dt_ini'] = formaPadrao($row['dt_ini']);
            }
            if(isset($row['dt_fim'])){
                $row['dt_fim'] = formaPadrao($row['dt_fim']);
            }
            // hora
            if(isset($row['hr_ini'])){
                $row['hr_ini'] =  substr($row['hr_ini'], 0, 5);
            }
            if(isset($row['hr_fim'])){
                $row['hr_fim'] =  substr($row['hr_fim'], 0, 5);
            }
            // se existem shortcodes
            if(isset($row['txt'])){
                $row['txt'] = $this->special_txt_parser($row['txt']);
            }
            // multicontent
            if(isset($row['txtmulti'])){
                $v_prov = explode('<!--breakmulti-->', $row['txtmulti']);
                $v = array();// limpa a variável
                $mod_multicontent = $this->this_modulo['multicontent'];
                // combina o título da página com o conteúdo
                for($x = 0; $x < count($mod_multicontent); $x++){
                    $aba = (isset($v_prov[$x])) ? $v_prov[$x] : '';
                    $v[] = array(
                        'titulo' => $mod_multicontent[$x],
                        'txt' => $this->special_txt_parser($aba)
                    );
                }
                $row['txtmulti'] = $v;
            }
            
            // só faz isso uma vez!
            $row['adminbar'] = $this->get_adminbar($row);
          
            
            $return[] = $row;
            
        }
        
        if($is_unique){
            return $return[0];
        } else {
            return $return;
        }
        
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Verifica se existem shortcodes e aplica na sring.
     * @param string $txt
     * @return string
     */
    public function special_txt_parser($txt){
        if(count($this->shortcodes)){
            return $this->ci->shortcode->run($txt);
        } else {
            return $txt;
        }
    }


    // -------------------------------------------------------------------------
    
    /**
     * Retorna as páginas/grupos que antecedem a páginas atual.
     * Atenção: a página atual participa do retorno.
     * @return array
     */
    public function get_hierarchy(){
       
        if(!$this->this_page) return false;
        
        // configurações para 'paginas'
        if(in_array($this->this_page['modulo_id'], $this->page_module)){
            $config['modulo_id'] = $this->this_page['modulo_id'];
            $config['child_pages'] = true;
            $_index = 'id';// indice para comparar
        } 
        // configurações para 'posts' e 'calendario'
        else {
            $config['modulo_id'] = $this->this_page['modulo_id'];
            $config['child_pages'] = false;
//            $config['main_controller'] = $this->ci->uri->segment(1).'/c';
            $config['main_controller'] = $this->ci->uri->segment(1).'';
            $_index = 'grupo';// indice para comparar
        } 
        // força a recarregar todas as páginas
        $config['overload'] = true;
       
        $this->prepare_config($config);
        $architecture = $this->get_arquitetura(false, true);
   
        // inverte ordem
        $reverseOrder = array_reverse($architecture);
        
        $parents = array();
        $onTrail = false;
        $levelGrupo = 0;
        $prevGrupo = 0;
        
        foreach ($reverseOrder as $row) {
            
            // o rastreamento só é iniciado quando a página ativa é encontrada
            if ($onTrail === true) {
                // se for de nível inferior, e menor que zero,
                // senão já passou para outro grau de parentesco
                if ($levelGrupo > $row['level'] && $row['id'] == $prevGrupo) {
                    $parents[] = $row;
                    $prevGrupo = $row['grupo'];

                    if ($row['level'] == 0) {
                        break;
                    }
                }
                // ao encontrar o próximo nível 0, interrompe
                else if($row['level'] == 0){
                    break;
                }
            }
            
            // ao encontrar começa rastrear parentesco
            if ($row['id'] == $this->this_page[$_index]) {
                $parents[] = $row;
                $onTrail = true;
                $levelGrupo = $row['level'];
                $prevGrupo = $row['grupo'];
            }
        }
        
        // reinverte para direção normal
        $normalParents = array_reverse($parents); 
        
        // Se NÃO for módulo tipo 'paginas', adicionar o post ativo no fim
        if(in_array($this->this_page['modulo_id'], $this->page_module) === FALSE){
            $this_page = $this->page_parser($this->this_page);
            $normalParents[] = array(
                'id' => $this_page['id'],
                'nick' => $this_page['nick'],
                'titulo' => $this_page['titulo'],
                'grupo' => $this_page['grupo'],
                'modulo_id' => $this_page['modulo_id'],
                'adminbar' => $this_page['adminbar']
            );
        }
        
        
        return $normalParents;
        
    }
    
    // -------------------------------------------------------------------------

    /**
     * Retorna as páginas filhas da página instanciada.
     * @param boolean $all_levels Pode retornas as páginas do primeiro nível, ou todas
     * @param array $config
     * @return array|boolean
     */
    public function get_children($all_levels = false, $config = false){
        
       
        $config['modulo_id'] = $this->this_page['modulo_id'];
        $config['child_pages'] = true;
        $this->prepare_config($config);
        $architecture = $this->get_arquitetura(false);
        
        $children = array();
        $base_level = 999; // init
        $passed = false;   // passou da página base
        
        // percorre arquitetura pelas páginas filhas
        foreach($architecture as $row){
            
            // quando for para retornar todos os nívels, 
            // armazena nivel da pagina instanciada
            if($row['id'] == $this->this_page['id']){
                $base_level = $row['level'];
            }
            
            if($all_levels){
                
                // ignora a própria página
                if($row['id'] == $this->this_page['id']){
                    $passed = true;
                    continue;
                }
                // se é de nível superior... entra
                else if($row['level'] > $base_level){
                    $children[] = $row;
                }
                // se a página base for level=0, ao encontrar a próxima... para
                else if($base_level == 0 && $row['level'] == 0){
                    break;
                }
                // após ter encontrado páginas filhas, a próxima de level=0... para
                else if($row['level'] == 0 && $passed){
                    break;
                }
                
            } 
            // apenas filhas de um nível
            else {
                if($row['grupo'] == $this->this_page['id']){
                    $children[] = $row;
                }
            }
            
        }
        
        // nenhum menu foi gerado
        if(empty($children)){
            return FALSE;
        }
        
        // se existem opções
        if(isset($config['html']) && $config['html'] === TRUE){
            $this->prepare_config(array(
                'ul_class' => '.inner-menu',
                'reset_level' => true
                ));
            $children = $this->parse_html($children);
        }
        
        return $children;
    }

    // -------------------------------------------------------------------------
    
    /**
     * Retorna a galeria da página.
     * ** É necessário executar $this->set_page() antes **
     * @param type $page_nick_id
     * @return boolean
     */
    public function get_page_gallery($page_nick_id = NULL){
        
        // Se for passada o ID da página, o retorno não é armazenado,
        // mas retornado.
        if($page_nick_id !== NULL){
            $actual_page = $this->set_page($page_nick_id, true);
        }
        // verifica a pré-existência na memória e que não está sendo feita 
        // uma requizição de nova página        
        else if($this->this_galeria && $page_nick_id === NULL){
            return $this->this_galeria;
        }
        // garante que temos uma página/post!
        else if($this->this_page === FALSE || $this->this_page['galeria'] == ''){
            return FALSE;
        } else {
            $actual_page = $this->this_page;
        } 
        
        // quebra IDs
        $array_galeria = explode('|', $actual_page['galeria']);
        // pega dados das imagens
        $return = $this->ci->site_utils->get_arquivos_from_array($array_galeria, 'id');
        $return = $this->parse_gallery($return, $array_galeria);
        
        // registra galeria na memória para posterior reaproveitamento
        if($page_nick_id === NULL){
            $this->this_galeria = $return;
        }

        return $return;        
        
    }
    
    // -------------------------------------------------------------------------
  
    /**
     * Faz parseamento pelos anexos.
     * @param type $attach_array
     * @param type $order_ids
     * @return type
     */
    public function parse_gallery($attach_array, $order_ids){
        
        // se não validar, retorna o array de entrada
        if(empty($attach_array) || empty($order_ids) || (count($order_ids) != count($attach_array)) ) {
            return $attach_array;
        }
        
        $return = array();
        
        $path = base_url() . $this->ci->config->item('upl_imgs').'/';
        
        for($x = 0; $x < count($attach_array); $x++){
            
            // remove imagens que TAG == 2
            if($attach_array[$x]['tag_opt'] == 2){
                continue;
            }
            
            // pega o id da imagem
            $id = $attach_array[$x]['id'];
            $indice = $this->get_index_by_value($order_ids, $id);
            
           // acrescenta imagem com caminho completo
            $attach_array[$x]['full_path'] = $path . $attach_array[$x]['nome'];
           
            
            $return[$indice] = $attach_array[$x];
        }
        
        // ordena pelos índices
        ksort($return);
        
        // reseta índices
        $return = array_merge($return, array());
        
        return $return;
    }
    
    
    // -----------------------------------------------------------------------
    /**
     * Percorre um array até encontrar o valor desejado, então retorna o índice.
     * @param type $array_needed
     * @param type $value
     * @return boolean|int
     */
    private function get_index_by_value($array_needed, $value) {
        
        foreach($array_needed as $c => $v){
            if($v == $value){
                return $c;
            }
        }
        
        return false;
        
    }
    
    // -------------------------------------------------------------------------
    
    /**
     * Retorna os conteúdos relacionados.
     * ** É necessário executar $this->set_page() antes **
     * @param type $page_nick_id
     * @return boolean
     */
    public function get_page_related($page_nick_id = NULL){

        // Se for passada o ID da página, o retorno não é armazenado,
        // mas retornado.
        if($page_nick_id !== NULL){
            $actual_page = $this->set_page($page_nick_id, true);
        }
        // verifica a pré-existência na memória e que não está sendo feita 
        // uma requizição de nova página        
        else if($this->this_related && $page_nick_id === NULL){
            return $this->this_related;
        }
        // garante que temos uma página/post!
        else if($this->this_page === FALSE || $this->this_page['rel'] == ''){
            return FALSE;
        } else {
            $actual_page = $this->this_page;
        } 
        
        // quebra IDs
        $array_ids = explode('|', $actual_page['rel']);
        // percorre e combina
        $related = array();
        foreach($array_ids as $rel){
            $related[] = $this->get_page($rel, true);
        }        
        
        // registra galeria na memória para posterior reaproveitamento
        if($page_nick_id === NULL){
            $this->this_related = $related;
        }

        return $related;        
        
    }
    
    
    // -------------------------------------------------------------------------
    
    /**
     * Retorna as tags da página.
     * ** É necessário executar $this->set_page() antes **
     * @param type $page_nick_id
     * @return boolean
     */
    public function get_page_tags($page_nick_id = NULL){

        // Se for passada o ID da página, o retorno não é armazenado,
        // mas retornado.
        if($page_nick_id !== NULL){
            $actual_page = $this->set_page($page_nick_id, true);
        }
        // verifica a pré-existência na memória e que não está sendo feita 
        // uma requizição de nova página        
        else if($this->this_tags && $page_nick_id === NULL){
            return $this->this_tags;
        }
        // garante que temos uma página/post!
        else if($this->this_page === FALSE || $this->this_page['rel'] == ''){
            return FALSE;
        } else {
            $actual_page = $this->this_page;
        } 
      
             
        $related = $this->ci->cms_posts->get_post_tags($actual_page['id'], $actual_page['modulo_id']);
        
        // registra galeria na memória para posterior reaproveitamento
        if($page_nick_id === NULL){
            $this->this_tags = $related;
        }

        return $related;        
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna array com os arquivos anexos da página.
     * ** É necessário executar $this->set_page() antes **
     * @param type $page_nick_id
     * @return mixed
     */
    public function get_page_attachments($page_nick_id = NULL){
        
        // Se for passada o ID da página, o retorno não é armazenado,
        // mas retornado.
        if($page_nick_id !== NULL){
            $actual_page = $this->set_page($page_nick_id, true);
        }
        // verifica a pré-existência na memória e que não está sendo feita 
        // uma requizição de nova página        
        else if($this->this_attachments && $page_nick_id === NULL){
            return $this->this_attachments;
        }
        else {
            // garante que temos uma página/post!
            if($this->this_page === FALSE) return FALSE;
            $actual_page = $this->this_page;
        } 
        
        // pega dados dos arquivos anexos
        $return = $this->ci->site_utils->get_arquivos_from_array($actual_page['id'], 'conteudo_id');
        $return = $this->parse_attachments($return);
        
        // registra galeria na memória para posterior reaproveitamento
        if($page_nick_id === NULL){
            $this->this_attachments = $return;
        }
        
        return $return;       
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Faz parseamento pelos anexos.
     * @param type $attach_array
     */
    private function parse_attachments($attach_array){
        return $attach_array;
    }

    // -------------------------------------------------------------------------
    /**
     * Verifica se existe na memória, senão faz a busca e armazena.
     * Se for passdo ID não coloca na memória.
     * 
     * @param type $page_nick_id
     * @return array
     */
    private function set_get_precos_descontos($page_nick_id = NULL){
        
        $actual_page = FALSE;
        // Se for passada o ID da página, o retorno não é armazenado,
        // mas retornado.
        if($page_nick_id !== NULL){            
            $actual_page = $this->set_page($page_nick_id, true);            
        } 
        else {
            
            // verifica a pré-existência na memória e que não está sendo feita 
            // uma requizição de nova página        
            if($this->this_precos){
                return $this->this_precos;                
            } 
            else if(!$this->this_page && !$this->this_precos){
                return FALSE;
            }
            else if($this->this_page && !$this->this_precos){
                $actual_page = $this->this_page;            
            }
            
        }        
        
        // Se a página ainda não está na memória
        // recupera dados de cms_precos
        if($actual_page){
            $this->ci->db->where('conteudo_id', $actual_page['id']);
            $this->ci->db->order_by('data');
            $return = $this->ci->db->get('cms_precos');            
        } 
        
        // se foi passado o ID, retorna
        if($page_nick_id !== NULL){
            
            return $return->result_array();
        } 
        else {
            // registra na memória para posterior reaproveitamento
            $this->this_precos = $return->result_array();
        }       
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna array multi com os precos e cupons.
     * @param type $page_nick_id
     * @return type
     */
    public function get_precos_cupons($page_nick_id = NULL){
        
        $this_precos = $this->set_get_precos_descontos($page_nick_id);
        
        if($page_nick_id !== NULL){
            return array(
                'precos' => $this->precos_parser($this_precos),
                'cupons' => $this->cupons_parser($this_precos)
            );
        } else {
            return array(
                'precos' => $this->precos_parser($this->this_precos),
                'cupons' => $this->cupons_parser($this->this_precos)
            );
        }
    }
    
    // -------------------------------------------------------------------------
    /**
     * Busca o array de cms_precos ou no registro passado como argumento e manda
     * para ser parseado.
     * 
     * @param type $page_nick_id
     * @return type
     */
    public function get_precos($page_nick_id = NULL){
        
        $this_precos = $this->set_get_precos_descontos($page_nick_id);
        
        if($page_nick_id !== NULL){
            return $this->precos_parser($this_precos);
        } else {
            return $this->precos_parser($this->this_precos);
        }
        
    }
    
    // -------------------------------------------------------------------------

    /**
     * Separa apenas os preços e prepara saída para controller.
     * 
     * @param array $precos_descontos_array
     * @return array
     */
    public function precos_parser($precos_descontos_array){
        
        if($precos_descontos_array === FALSE || count($precos_descontos_array) == 0){
            return FALSE;
        }
        
        $return = array();
        
        foreach($precos_descontos_array as $row){
            if($row['tipo'] == 'preco'){
                
                // inicia parseamento //
                // remove itens desnecessários
                unset($row['conteudo_id']);
                unset($row['verificador']);
                unset($row['tipo']);
                
                // retorna
                $return[] = $row;
                
            } 
            
        }
        
        return (count($return) == 0) ? false : $return;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Busca o array de cms_precos ou no registro passado como argumento e manda
     * para ser parseado.
     * 
     * @param type $page_nick_id
     * @return type
     */
    public function get_cupons($page_nick_id = NULL){
        
        $this_cupons = $this->set_get_precos_descontos($page_nick_id);
        
        if($page_nick_id !== NULL){
            return $this->cupons_parser($this_cupons);
        } else {
            return $this->cupons_parser($this->this_precos);
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Recebe o CUPOM e retorna os dados do cupom mais o campo 'desconto'.
     * array
        'id' => string '2' 
        'valor' => string '15' 
        'regra' => string '%' 
        'verificador' => string 'GRUPODE5' 
        'desconto' => string '15%' 
     * 
     * @param type $cupom_verificador
     * @return boolean|string
     */
    public function get_cupom($cupom_verificador = NULL){        
        
        // é necessário que o conteúdo tenha sido instanciado
        if($cupom_verificador === NULL || !$this->this_page){ 
            return FALSE;
        }
        // se os preços ainda não estão na memória, coloca
        if(!$this->this_precos){
            $this->get_precos_cupons();
        }
        
        // percorre os cupons e separa
        $all = $this->get_cupons();
        
        $return = array();
        foreach ($all as $row){
            if($row['verificador'] == strtoupper($cupom_verificador)){
                
                if($row['regra'] == '%'){
                    $row['desconto'] = $row['valor'] .'%';
                } else {
                    $row['desconto'] = 'R$ '.$row['valor'] ;
                }
                
                $return = $row;
                break;
            }
        }
        
        if(count($return) == 0){
            return FALSE;
        } else {
            return $return;
        }
        
    }


    // -------------------------------------------------------------------------

    /**
     * Separa apenas os cupons e prepara saída para controller.
     * 
     * @param array $precos_descontos_array
     * @return array
     */
    public function cupons_parser($cupons_descontos_array){
                
        if($cupons_descontos_array === FALSE || count($cupons_descontos_array) == 0){
            return FALSE;
        }
        
        $return = array();
        
        foreach($cupons_descontos_array as $row){
            if($row['tipo'] == 'cupom'){
                
                // inicia parseamento //
                // remove itens desnecessários
                unset($row['conteudo_id']);
                unset($row['data']);
                unset($row['tipo']);
                
                // retorna
                $return[] = $row;
                
            }
        }
        
        return (count($return) == 0) ? false : $return;
        
    }
    
    // -------------------------------------------------------------------------

    /**
     * Retorna o preço final baseado na data de HOJE. 
     * Se inserir cupom faz o desconto.
     * Caso o array de preços não seja passado no segundo argumento, faz 
     * busca pelo conteúdo instanciado.
     * 
     * @param string $cupom
     * @param array $preco_array
     * @return int
     */    
    public function preco_final($cupom = NULL, $preco_array = NULL){
        
        // se NÃO passou o array de preços
        if($preco_array === NULL){
            // instancia os precos        
            $pc = $this->get_precos_cupons();
            $precos = $pc['precos'];
            $cupons = $pc['cupons'];
        }
        // senão, usa o array de preços passado
        else {
            // verifica se passou array multi com preços e cupons
            // é multi
            if(isset($preco_array['precos']) && isset($preco_array['cupons'])){
                $precos = $preco_array['precos'];
                $cupons = $preco_array['cupons'];
            }
            // passou apenas o array de preços
            else {
                $precos = $preco_array;
                $cupons = FALSE;
            }
            
        }
        
        
        // percorre preços para encontrar o dia de hoje, ou a menor data
        $preco_f = 0;
        
        if(is_array($precos)){
            foreach ($precos as $row){
                // se for no dia, retorna e para looping
                if($row['regra'] == 'no-dia' && $row['data'] == date("Y-m-d")){
                    $preco_f = $row['valor'];
                    break;
                } 
                else if($row['regra'] == 'ate-dia'){

                    // atribui o valor para no caso de erro do admin                
                    // se a data é válida, retorna
                    if($row['data'] >= date("Y-m-d")){ 
                        $preco_f = $row['valor'];
                        break;
                    }
                }

            }  
        }
        
//        mybug($this->this_page);
        // se não existirem descontos programados... retorna o valor base
        if($preco_f == 0 && isset($this->this_page['valor_base'])){
            $preco_f = $this->this_page['valor_base'];
        } 
                        
        // se existe cupom
        if($cupom !== NULL && $cupons){
            $cupom_f = false;
            // valida o cupom
            foreach ($cupons as $row){
                if($row['verificador'] == strtoupper($cupom)){
                    $cupom_f = $row;
                }
            }
            
            // se existe cupom modifica o valor final
            if($cupom_f){
                $regra_cupom = $cupom_f['regra'];
                $valor_cupom = $cupom_f['valor'];
                // se for percentual
                if($regra_cupom == '%'){
                    $valor_percentual = round(($preco_f * $valor_cupom) / 100, 2);
                    $preco_f = $preco_f - $valor_percentual;
                } 
                // se for valor em real
                else if($regra_cupom == 'R$'){
                    $preco_f = $preco_f - $valor_cupom;
                }
            }
        }
        
        
        return round($preco_f, 2);
        
    }

    // -------------------------------------------------------------------------
    /**
     * Pesquisa e armazena dados do módulo em $this->this_modulo
     */
    public function set_get_modulo(){
        
        // verifica se já está armazenado
        if($this->this_modulo){
            return $this->this_modulo;
        }        
        
        $modulo_id = $this->this_page['modulo_id'];
        
        if(!is_numeric($modulo_id)){
            return FALSE;
        }
        // pesquisa
        $this->ci->db->where('id', $modulo_id);
        $result = $this->ci->db->get('cms_modulos');
        if($result->num_rows() == 0){
            return FALSE;
        }
        return $this->modulo_parser($result->row_array());
    }
    
    // -------------------------------------------------------------------------
    /**
     * Parseia dados do módulo
     * @param array $mod_array
     * @return array
     */
    private function modulo_parser($mod_array){
        
        $return = array();
        
        foreach ($mod_array as $c => $v){
            
            // títulos multi content
            if($c == 'multicontent'){
                $v = explode(',', $v);
            }
            
            $return[$c] = $v;
        }
        
        return $return;
    }

    // -------------------------------------------------------------------------
    /**
     * Parseia URI e retorna o último segmento... removendo qualquer segmento
     * que contenha ':' para evitar variáveis concatenadas (id:321/p:3).
     * @return mixed 
     */
    private function get_nick_by_uri(){
        // resgata dados da uri
        $uri = $this->ci->uri->segment_array();
        // reseta os indeces
        $uri = array_merge($uri, array());
        $ttl = count($uri);
        // parseia array para remover strings com ':'
        for($x = 0; $x < $ttl; $x++){
            if(strpos($uri[$x], ':') !== FALSE){
                unset($uri[$x]);
            }
        }
        
        // recomeça
        $ttl = count($uri);
        if($ttl == 0){
            return false;
        } else {
            return $uri[$ttl-1];
        }      
        
    }

    // -------------------------------------------------------------------------
    /**
     * Método usado pelo usuário para montar o menu
     * @param array $config
     * @return type 
     */
    public function generate_menu($config){
        
        // inicializa configurações
        $this->prepare_config($config);        
        
        $menu = $this->build_menu();        
        
        if($this->html){// retorna com HTML
            return $this->parse_html($menu);
        } else {
            return $menu;
        }  
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Parseia as configurações iniciais e seta pariáveis
     * @param type $config 
     */
    private function prepare_config($config){
        
        if(! is_array($config)){
            $config = array($config);
        }
        
        foreach($this->config as $chv => $vlr){
            
            if(isset($config[$chv])){
                $this->$chv = $config[$chv];
            } else {
                $this->$chv = $vlr;
            }
            
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Monta o menu como um array dependendo das configurações
     * @return array 
     */
    private function build_menu(){
        
        $return = array();        
        
        // se HOME existe
        if($this->home){
            $return[] = $this->home;
        }
        
        // se está injetando páginas antes
        if($this->prepend){
            $return = array_merge($return, $this->inject_pages($this->prepend));
        }
        
        // monta a hierarquia de páginas
        $return = array_merge($return, $this->get_arquitetura(false));
        
        // se está injetando páginas após
        if($this->append){
            $return = array_merge($return, $this->inject_pages($this->append));
        }
                
        return $return;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Prepara o array de páginas para ser injetada no menu.
     * 
     * @param type $array
     * @return array 
     */
    private function inject_pages($array){
        
        // verifica se é um array simples, ou multiarray
        if(isset($array['titulo'])){
            return array($array);
        } else {
            return $array;
        }
        
    }

    // -------------------------------------------------------------------------
    /**
     * Retorna todas as páginas ordenadas
     */
    private function get_all_pages($getAll = false){
//        mybug($this->modulo_id);
        /**
         * Montagem da SQL 
         */
        $this->ci->db->select('id, nick, titulo,  grupo, modulo_id, rel');
        $this->ci->db->from('cms_conteudo');

        if($getAll === false)
        {
            // status
            $this->ci->db->where('status', 1);         
            // modo de visualização
            $this->ci->db->where('show', 1);         
        }         
        
        $this->ci->db->order_by('ordem');        
        
        // se for apenas de primeiro nível
        if($this->child_pages === FALSE){
            $this->ci->db->where('grupo', 0); 
        }
        
        $this->ci->db->where('tipo', 'conteudo');
        $this->ci->db->where('modulo_id', $this->modulo_id);
        $this->ci->db->where('lang', $this->lang);
        
        // se tiver páginas para excluir
        if($this->exclude){
            if(!is_array($this->exclude)){
                $this->exclude = explode(',', $this->exclude);
            }
            $this->ci->db->where_not_in('id', $this->exclude);            
        }
        
        // este controle DEVE vir por último em função do 'OR'
        // se tiver páginas para incluir
        if($this->include){
            if(!is_array($this->include)){
                $this->include = explode(',', $this->include);
            }
            $this->ci->db->or_where_in('id', $this->include);            
        }
        
        $sql = $this->ci->db->get();        
        
        $resultado = $sql->result_array();
        
        return $resultado;
        
    }
    

    // -------------------------------------------------------------------------
    /**
     * Faz busca pelas páginas recursivamente.
     * Pode retornar um array simples, ou multidimensional
     * 
     * @param type $multidimensional
     * @return type 
     */
    public function get_arquitetura($multidimensional = true, $getAll = false) {        

        // idenfifica o típo de módulo que está sendo usado
        // e seta variaveis globais       
        if(in_array($this->modulo_id, $this->page_module)){
            $this->_modcontrlr = 'paginas';
            $this->_index = 'grupo';// campo de ligação
        }
        // sobre módulo 'calendario'
        else if(in_array($this->modulo_id, $this->cale_module)){
            $this->_modcontrlr = 'calendario';
            $this->_index = 'rel';// campo de ligação
        }
        // sobre módulo 'posts'
        else {
            $this->_modcontrlr = 'posts';
            $this->_index = 'rel';// campo de ligação
        }
        
        // inicializa
        $this->arquitetura = array();
        // se os grupos ainda não estão armazenados
        if($this->all_pages === false || $this->overload){
            $this->all_pages = $this->get_all_pages($getAll);
        }
        
        
        foreach ($this->all_pages as $row) {

            if($row[$this->_index] == 0){// apenas de primeiro nível
                
                $row['level'] = 0;
                // monta uri. se houver um "main_controller"
                $row['uri'] = ($this->main_controller) ? $this->main_controller.'/' : '';
                $row['uri'] .= $row['nick'];
                $row['adminbar'] = $this->adminbar_template('cms/'.$this->_modcontrlr.'/edita/co:'.$row['modulo_id'].'/id:'.$row['id'], 'left');
                
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

        return $this->arquitetura;
    }

    // -------------------------------------------------------------------------
    /**
     * Pesquisa recursivamente pelas páginas do módulo.
     * @param int $grupo_id
     * @param int $level
     * @param array $multidimensional
     * @return mixed 
     */
    private function _get_arquitetura_recursive($grupo_id, $level = 0, $uri = '', $multidimensional = true) {

        
        $saida = array();
        $level++;
        foreach ($this->all_pages as $index => $row) {

            // se o grupo é filho... inclui
            if($row[$this->_index] == $grupo_id){
                
                $row['level'] = $level;
                $row['uri'] = $uri.'/'.$row['nick'];
                $row['adminbar'] = $this->adminbar_template('cms/'.$this->_modcontrlr.'/edita/co:'.$row['modulo_id'].'/id:'.$row['id'], 'left');
                              
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
    
    // -------------------------------------------------------------------------
    /**
     * Paeseia array e monta estrutura HTML.
     * 
     * @param array $builded_menu
     * @return string 
     */
    private function parse_html($builded_menu){
        
        if(empty($builded_menu)){
            return FALSE;
        }
        
        // salva URI para identificar página ativa
        $segs = $this->ci->uri->segment_array();
        $lastSeg = ($segs) ? $segs[count($segs)] : false;
//        mybug($lastSeg);
        // ponteiros
        $level_flag = 0;        
        // tag de abertura do menu
        $this->template_html = array(); // reset/init
        $this->template_html[] = '<ul class="'.$this->ul_class.'" id="'.$this->ul_id.'" >';
        
        // percorre as páginas de primeiro nível
        foreach ($builded_menu as $key => $row){
            /*  [id] => 1
                [nick] => pagina-a
                [titulo] => Página A
                [grupo] => 0
                [level] => 0
                [uri] => pagina/pagina-a
             */
            
            // Resetando os níveis das páginas, usa-se níveis gerados dinamicamente
            // É utilizado na geração de páginas filhas get_children()
            if($this->reset_level){
                
                // registra o nível da primeira página filha
                if($key == 0){
                    $this->level_dif = $row['level'];
                }
                // reduz o nível para zero
                $level = $row['level'] - $this->level_dif;                
                
            } 
            // senão, usa-se os levels das páginas
            else {
                $level = (isset($row['level'])) ? $row['level'] : 0;
            }
            
            
            
            $titulo   = (isset($row['titulo'])) ? $row['titulo'] : '';
            $id       = (isset($row['id'])) ? $row['id'] : 0;
            $grupo    = (isset($row['grupo'])) ? $row['grupo'] : 0;
            $uri      = (isset($row['uri'])) ? $row['uri'] : '';
            $adminbar = (isset($row['adminbar'])) ? $row['adminbar'] : '';
            $liClass  = (isset($row['class'])) ? $row['class'] : '';
            $raw      = (isset($row['raw'])) ? $row['raw'] : false;// dados html
            
            // acrescenta classe 'active
            if(isset($row['nick']) && $lastSeg == $row['nick']){
                $liClass .= ' ' . $this->active_class;
            }
            
            // adiciona no primeiro nível
            if($level == 0){
                
                // se o item é um html puro, injeta no temmplate final
                if($raw){
                    $this->template_html[] = $raw;
                } 
                // senão continua procurando os filhos
                else {
                    
                    $this->template_html[] = '<li class="'.$liClass.'">';
                    $this->template_html[] = '<a href="'.site_url($uri).'" title="'.$titulo.'" '.$adminbar.'>'.$titulo.'</a>';
                    // continua pelos níveis inferiores...
                    $this->template_html[] = $this->parse_html_recursive($builded_menu, $id, $level_flag);
                    $this->template_html[] = '</li>';
                    
                }
            }            
            
        }
        
        $this->template_html[] = '</ul>';
        
        return implode(PHP_EOL, $this->template_html);      
        
    }
    
    // -------------------------------------------------------------------------
    private function parse_html_recursive($builded_menu, $parent_grupo, $level_flag){
        
        // salva URI para identificar página ativa
        $segs = $this->ci->uri->segment_array();
        $lastSeg = ($segs) ? $segs[count($segs)] : false;
        
        $level_flag++;
        $is_sub_menu_flag = false;
        
        $this->template_html[] = '<div class="children children-'.$level_flag.'"><ul class="unstyled">';
        
        foreach ($builded_menu as $row){
            
            // ao resetar os níveis, todos são reduzidos pelo nível da página
            // mais alta
            if($this->reset_level){
                $level = $row['level'] - $this->level_dif;
            } else {
                $level = (isset($row['level'])) ? $row['level'] : 0;
            }
            
            $id = (isset($row['id'])) ? $row['id'] : 0;
            $titulo = (isset($row['titulo'])) ? $row['titulo'] : '';
            $grupo = (isset($row['grupo'])) ? $row['grupo'] : 0;
            $uri = (isset($row['uri'])) ? $row['uri'] : '';
            $adminbar = (isset($row['adminbar'])) ? $row['adminbar'] : '';
            $liClass  = (isset($row['class'])) ? $row['class'] : '';
            
            // acrescenta classe 'active
            if(isset($row['nick']) && $lastSeg == $row['nick']){
                $liClass .= ' ' . $this->active_class;
            }
            
            if($grupo == $parent_grupo && $level == $level_flag){
                /*  [id] => 1
                    [nick] => pagina-a
                    [titulo] => Página A
                    [grupo] => 0
                    [level] => 0
                    [uri] => pagina/pagina-a
                */
                $is_sub_menu_flag = true;                
                
                $this->template_html[] = '<li class="'.$liClass.'">';
                $this->template_html[] = '<a href="'.site_url($uri).'" title="'.$titulo.'" '.$adminbar.'>'.$titulo.'</a>';
                                         $this->parse_html_recursive($builded_menu, $id, $level_flag);
                $this->template_html[] = '</li>';
                
            }
            
        }        
        
        // se NÃO existe subitem remove o último índice
        if($is_sub_menu_flag === false){          
            array_pop($this->template_html);            
        } else {// se existe, fecha o UL
            $this->template_html[] = '</ul></div>';
        }       
        
        
    }

    
    // -------------------------------------------------------------------------
    /**
     * Carega a biblioteca. Inicializa codes. Armazena na memória.
     * @param array $codes
     */
    public function shortcode_reg($codes){
        
        // carrega classes
        $this->ci->load->library('shortcode');
        $this->ci->load->helper('shortcode');
        
        if(!is_array($codes)){
            $codes = explode(',', $codes);
        }
        
        
        for($x = 0; $x < count($codes); $x++){
            
            $sc = trim($codes[$x]);
            $func = 'sc_'.trim($codes[$x]);
                        
//            mybug(function_exists($func));
            
            if(function_exists($func)){
                $this->ci->shortcode->add($sc, $func);
            }           
            
            // memoriza
            $this->shortcodes[] = $sc;
            
        }
        
    }


    // -------------------------------------------------------------------------
    /**
     * Se o admin estiver logado... pesquisa o módulo, separa o controller e 
     * monta a uri da aadministração.
     * 
     * 
     * @param type $adminuri
     * @return type 
     */
    public function get_adminbar($row){  
        
        if($this->user_admin === FALSE){
            return false;
        }
        
        // tem que pesquisar para cada contéudo
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
            
            return $this->adminbar_template('cms/'.$seg.'/edita/co:'.$modulo['id'].'/id:'.$row['id']);
        } else {
            return '';
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Recebe a URI e escreve as tags necessárias.
     * @param type $adminuri
     * @return type 
     */
    public function adminbar_template($adminuri, $class = ''){
        return ' data-admin-url="'.site_url($adminuri).'" data-admin-class="'.$class.'"';
    }
    
    // -------------------------------------------------------------------------
    /**
     * Através do ID do módulo, seta variáveis de configuração global do script
     * @param int $modulo_id
     */
    private function set_module_type($modulo_id){
        
        if(in_array($this->modulo_id, $this->page_module)){
            $this->_modcontrlr = 'paginas';
            $this->_index = 'grupo';// campo de ligação
        }
        // sobre módulo 'calendario'
        else if(in_array($this->modulo_id, $this->cale_module)){
            $this->_modcontrlr = 'calendario';
            $this->_index = 'rel';// campo de ligação
        }
        // sobre módulo 'posts'
        else {
            $this->_modcontrlr = 'posts';
            $this->_index = 'rel';// campo de ligação
        }
        
    }
           
    
}