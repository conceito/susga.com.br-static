<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Cms_utils: Funções comuns do CMS. Algumas são requisitadas por AJAX, outras podem ser acessadas diretamente.
 *
 * @package Library
 * @author Bruno Barros
 * @copyright Copyright (c) 2009
 * @version 2.0
 * @access public --------------------->>>> atenção a linha de depreciação
 */
class Cms_libs {

    /**
     * Instancia o CI.
     */
    function Cms_libs() {
        $this->ci = &get_instance();
    }
    
    // --------------------------------------------------------------------------
    /**
     * Monta barra de opções da revisão.
     * 
     * @param       array       $post
     * @return      string
     */
    public function output_revision_options_bar($post){
        
        if(strstr($post['tipo'], 'revision') === false){
            return '';
        }
        
        // pega ID do conteúdo original
        $rev_ori_id = revision_id($post['tipo']);
        $rev_num    = revision_num($post['tipo']);
        
        $vars = $this->ci->uri->to_array(array('id'));
        
        $uri = trim($this->ci->uri->uri_string(), '/');
        $uri_array = explode('/', $uri);
        $uri = '';
        
        for($x = 0; $x < count($uri_array); $x++){
            
            if(substr($uri_array[$x], 0, 3) != 'id:'){
                $uri .= $uri_array[$x] . '/';
            }           
            
        }
        
        // prepara uri para ser enviada como valor
        $suri = trim(str_replace('/', '_', $uri), '_');
        $suri = str_replace(':', '=', $suri);
        
        $view['rev_set'] = 'cms/cmsutils/revisionToPublic/back:'. $suri.'/rev:'.$vars['id'].'/ori:'.$rev_ori_id;
        $view['rev_original'] = $uri.'id:'.$rev_ori_id;
        
        return $this->ci->load->view('cms/conteudo_revisions_bar', $view, true);
        
    }

    // -------------------------------------------------------------------------
    /**
     * Exibe caixa de revisões no conteúdo.
     * 
     * @return string
     */
    public function output_revisions($post){
        
        if(strstr($post['tipo'], 'revision') !== false){
            return '';
        }
        
        $vars = $this->ci->uri->to_array(array('id'));
        $revs = $this->get_revisions($vars['id']);
        
        $uri = trim($this->ci->uri->uri_string(), '/');
        $uri_array = explode('/', $uri);
        $uri = '';
//        mybug($uri_array);
        for($x = 0; $x < count($uri_array); $x++){
            
            if(substr($uri_array[$x], 0, 2) != 'id' && 
                    substr($uri_array[$x], 0, 3) != 'tab' && 
                    substr($uri_array[$x], 0, 3) != 'tip' ){
                
               $uri .= $uri_array[$x] . '/';
                
                
            }             
            
        }
        
        $view['total'] = (empty($revs)) ? 0 : count($revs);
        $view['uri'] = $uri;
        $view['revs'] = $revs;
        return $this->ci->load->view('cms/conteudo_revisions', $view, true);
        
    }


    // -------------------------------------------------------------------------
    /**
     * É colocada na função de salvamento do conteúdo.
     * Salva versão enterior como revisão e elimina a versão que excede o 
     * máximo permitido.
     * 
     * @param type $post_id
     */
    public function save_revision($post_id){
        
        $revisions = $this->get_revisions($post_id);
        // remove 1 pois vai acrescentar nova versão
        $max = $this->ci->config->item('revisions_limit') - 1;
        
        // se a quantidade de revisões exceder o máximo permitido
        // elimina a revisão mais antiga
        // atualiza o 'tipo' das reviões restantes
        if($revisions !== FALSE){
            
            for($x = 0; $x < $max; $x++){
                
                $rev = $revisions[$x];
                unset($revisions[$x]);// remove
                                
                // atualiza
                $id = $rev['id'];
                unset($rev['id']);
                
                // atualiza a revisão para iniciar em '-2'
                $rev['tipo'] = $post_id.'-revision-'.($x+2);
                
                $this->ci->db->where('id', $id);
                $this->ci->db->update('cms_conteudo', $rev);
                
            }
            
        }
//        mybug($revisions);
        // get last post status
        $post = $this->ci->db->where('id', $post_id)->get('cms_conteudo');
        $revisao = $post->row_array();
        unset($revisao['id']);
        $revisao['tipo'] = $post_id.'-revision-1';
        
        // se existirem revisões, pega a primeira para salvar a revisão
        // mais recente e elimina o restante.
        if(is_array($revisions) && count($revisions) > 0){
            
            // reseta chave do array
            $revisions = array_merge($revisions, array());
            
            // pega o ID do primeiro para salvar revisão '-1'
            $rev_temp_id = $revisions[0]['id'];
            
            unset($revisions[0]);            

            $this->ci->db->where('id', $rev_temp_id);
            $this->ci->db->update('cms_conteudo', $revisao);
            
            // se ainda existirem revisões elimina, pois passou do máximo permitido
            if(is_array($revisions) && !empty($revisions)){
                foreach ($revisions as $key => $row){
                    $this->ci->db->where('id', $row['id']);
                    $this->ci->db->delete('cms_conteudo');
                }
            }
            
        }
        else {
            // insere registro com revisão mais recente
            $this->ci->db->insert('cms_conteudo', $revisao);
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna as revisões do conteúdo.
     * 
     * @param type $post_id
     * @return array
     */
    public function get_revisions($post_id){
        
        $this->ci->db->like('tipo', $post_id.'-revision');
        $this->ci->db->order_by('tipo');
        $result = $this->ci->db->get('cms_conteudo');
        
        if($result->num_rows() == 0){
            return FALSE;
        }
        
        return $result->result_array();
        
    }

    // -------------------------------------------------------------------------
    /**
     * Confronta a matriz de metadados com os metadados do conteúdo.
     * Retorna a matriz com os valores do conteúdo.
     */
    public function prep_metadados($md_string){
        
        $md = $this->ci->config->item('metadados');
        
        $content_md = $this->str_to_metadados($md_string);
        
         
        
        $ret = array();
        
        foreach($md as $c => $v){
            
            // verifica se existe
            if(isset($content_md[$c]) && $content_md){
                // se for uma lista de valores
                if(is_array($v)){
                    $i = 0;
                    foreach ($v as $row){
                        
                        if($content_md[$c] == $row){
                            $selected = $i;
                        }
                        $i++;
                    }
                }  
                // se for string atribui o valor
                else {
                    $v = $content_md[$c];
                }
            } else {
                $selected = 0;
            }
            
            $ret[$c] = array(
                'type' => (is_array($v)) ? 'radio' : 'input',
                'values' => $v,
                'selected' => $selected
            );
            
        }
//        mybug($ret);
        return $ret;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Quebra string de metadados em array associativo.
     * @param string $str
     * @return boolean
     */
    public function str_to_metadados($str){
        
        if(strlen($str) == 0){
            return false;
        }
        
        // quebra pelas linhas
        $arr = explode(PHP_EOL, $str);
       
        // percorre cada metadado
        $ret = array();
        foreach($arr as $m){
            
            if(strlen($m) < 3){
                continue;
            }
            
            $mdarr = explode('=', $m);
            
            $ret[$mdarr[0]] = $mdarr[1];
            
        }
        
        return $ret;       
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Recebe o ID do conteúdo e retorna um array associativo com metadados.
     * @access public
     * @param  int $content_id
     * @return array|boolean
     */
    public function get_meta_by_id($content_id){
        
        $ret = $this->ci->db->where('id', $content_id)
                ->select('metadados')
                ->get('cms_conteudo');
        
        if($ret->num_rows() == 0){
            return FALSE;
        }
        
        $post = $ret->row_array();
        
        return $this->str_to_metadados($post['metadados']);
        
    }


    // -------------------------------------------------------------------------
    /**
     * Este método é colocado no final do processo de salvamento do conteúdo
     * para salvar os metadados.
     * Percorre o POST por 'meta_'...
     * @param type $content_id
     * @return boolean
     */
    public function set_metadados($content_id = ''){
        if($content_id == ''){
            return false;
        }
        $ret = '';
        // percorre o $_POST
        foreach ($_POST as $c => $v){
            
            if(substr($c, 0, 5) == 'meta_'){
                $ret .= $c .'='.$v.PHP_EOL;
            }
            
        }
        
        // salva resultado
        $this->ci->db->update('cms_conteudo', array('metadados' => $ret), array('id' => $content_id));

    }

    // -------------------------------------------------------------------------
    /**
     * Após o salvamento das páginas é gerado um arquivo "cache/routes.php" com
     * o mapeamento das páginas modulo_id = 6.
     * É utilizado a library do front_end.
     * @todo: como ficará o roteamento em outras línguas
     */
    public function write_dynamic_routes(){
        
        // retorna todas as páginas, de todos os status, de todos os módulos 
        // do tipo "paginas"
        $query = $this->ci->db->where('tipo', 'conteudo')
                ->where_in('modulo_id', $this->ci->config->item('modulo_paginas'))
                ->select('id, full_uri')
                ->order_by('ordem')
                ->get('cms_conteudo');
        
        
        $data[] = '<?php ';
        
        // for every page in the database, get the route using the recursive function - _get_route()
        foreach( $query->result_array() as $route )
        {
            $data[] = '$route["' . $route['full_uri'] . '"] = "' . "pages/display/{$route['id']}" . '";';
        }

        $output = implode("\n", $data);

        $this->ci->load->helper('file');
        write_file(APPPATH . "cache/config/routes.php", $output);        
        
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Escreve no arquivo cache/config/modules.php os IDs dos módulos pelo tipo
     */
    public function write_modules_config(){
        
        $result = $this->ci->db->where('status', 1)
                ->where('grupo', 0)
                ->get('cms_modulos');
        
        $data[] = '<?php ';
        $mod_paginas = '';
        $mod_posts = '';
        $mod_calendarios = '';
        $mod_pastas = '';
        $mod_usuarios = '';
        $mod_loja = '';
        
        foreach($result->result_array() as $row){
            
            $id = $row['id'];
            $uri = $row['uri'];
            
            if(strpos($uri, 'paginas')){
                $mod_paginas .= $id .',';
            }
            else if(strpos($uri, 'posts')){
                $mod_posts .= $id .',';
            }
            else if(strpos($uri, 'calendario')){
                $mod_calendarios .= $id .',';
            }
            else if(strpos($uri, 'pastas')){
                $mod_pastas .= $id .',';
            }
            else if(strpos($uri, 'usuarios')){
                $mod_usuarios .= $id .',';
            }
            else if(strpos($uri, 'loja')){
                $mod_loja .= $id .',';
            }
            
        }
        
        $data[] = '$config[\'modulo_paginas\'] = array('.trim($mod_paginas, ',').');';
        $data[] = '$config[\'modulo_posts\'] = array('.trim($mod_posts, ',').');';
        $data[] = '$config[\'modulo_calendario\'] = array('.trim($mod_calendarios, ',').');';
        $data[] = '$config[\'modulo_pastas\'] = array('.trim($mod_pastas, ',').');';
        $data[] = '$config[\'modulo_usuarios\'] = array('.trim($mod_usuarios, ',').');';
        $data[] = '$config[\'modulo_loja\'] = array('.trim($mod_loja, ',').');';
        
        $output = implode("\n", $data);

        $this->ci->load->helper('file');
        write_file(APPPATH . "cache/config/modulos.php", $output);
    }

    // -------------------------------------------------------------------------
    /**
     * RETORNA OS DADOS DO MÓDULO   
     * @param array|int $vars
     * @return bool|array
     */
    function dados_menus_raiz($vars) {
        if (is_array($vars)) {
            $id = $vars['id'];
        }else{
            $id = $vars;
        }
        $this->ci->db->where('id', $id);
        $sql = $this->ci->db->get('cms_modulos');
        if ($sql->num_rows() == 0) {
            return false;
        } else{
            return $sql->row_array();
        }
    }

    // -------------------------------------------------------------------------
    function conf($id) {
        $this->ci->db->where('id', $id);
        $sql = $this->ci->db->get('cms_config');
        $conf = $sql->row_array();
        return $conf['valor'];
    }

    // -------------------------------------------------------------------------
    /**
     * Apaga arquivo, se for imagem apaga a miniatura
     *
     * @param mixed $array
     * @return array
     */
    function deleta_arquivo($arq = array()) {
        
//        log_message('error', implode(' = ', $arq));
        
        // identifica o tipo do arquivo
        if ($arq['img'] == 1) { // imagem
            $path = fisic_path() . $this->ci->config->item('upl_imgs');
            // apaga sua miniatura
            @unlink($path . '/' . thumb($arq['nome']));
            // apaga sua media
            @unlink($path . '/' . med($arq['nome']));
            // apaga sua grande
            @unlink($path . '/' . grande($arq['nome']));
            
//            log_message('error', '\$path = '.$path);
            
        } else { // outro tipo
            $path = fisic_path() . $this->ci->config->item('upl_arqs');
        }
        // apaga arquivo principal
        @unlink($path . '/' . $arq['nome']);
        // apaga registro no BD
        $this->ci->db->where('id', $arq['id']);
        $resp = $this->ci->db->delete('cms_arquivos');

        return $resp;
    }

    // -------------------------------------------------------------------------
    /**
     * Quebra uma string separade com separadores e transforma em Array.
     * n,n,n,n,n => array(n,n,n,n)
     * |n|n|n|n| => array(n,n,n,n)
     *
     * @param mixed $str
     * @param string $separador
     * @return
     */
    function str_to_array($str, $separador = '|') {
        if (strlen(trim($str)) == 0

            )return false;
        // quebra a stringe em array
        $array1 = explode($separador, $str);
        // elimina os vazios
        $array2 = array();
        foreach ($array1 as $vlr) {
            if (strlen(trim($vlr)) != 0) {
                $array2[] = $vlr;
            }
        }
        // se não houver valores retorna FALSE
        if (count($array2) == 0)
            return false;
        else
            return $array2;
    }

    // -------------------------------------------------------------------------
    /**
     * Lê a galeria atual, combina com o novo array e salva a alteração
     *
     * @param mixed $str
     * @param string $separador
     * @return
     */
    function atualiza_galeria($id_galeria, $array = array(), $tabela = 'cms_conteudo') {
        // tem que ser array
        if (!is_array($array)) {
            $array = array($array);
        }
        // carrega a galeria
        $this->ci->db->where('id', $id_galeria);
        $this->ci->db->select('galeria');
        $sql = $this->ci->db->get($tabela);
        $galeria = $sql->row_array();
        if (strlen($galeria['galeria']) == 0) {
            $lista1 = array();
        }
        else {
            $lista1 = explode('|', $galeria['galeria']);
        }
        // serializa
        $lista2 = array_merge($lista1, $array);
        $listafinal = implode('|', $lista2);
        $listafinal = trim($listafinal);
        $listafinal = trim($listafinal, '|');
        // salva o novo array
        $this->ci->db->where('id', $id_galeria);
        $this->ci->db->update($tabela, array('galeria' => $listafinal));

        return true;
    }

    // -------------------------------------------------------------------------
    function faz_log_atividade($oque) {
        $dados = array('data' => date("Y-m-d"),
            'hora' => date("H:i:s"),
            'quem' => $this->ci->phpsess->get('admin_id', 'cms'),
            'oque' => $oque
        );

        $this->ci->db->insert('cms_log_atividades', $dados);
    }

    // -------------------------------------------------------------------------
    /**
     * Pesquisa se este apelido é único e reconverte com a função nativa do CI para não haver erro.
     *
     * @param mixed $id : ID do registro que está sendo testado
     * @param mixed $apelido : apelido gerado
     * @param mixed $dadosPost : dados do conteúdo
     * @param mixed $tabela : tabela MySql
     * @return string
     */
    function confirma_apelido($id, $apelido, $dadosPost = array(), $tabela = 'cms_conteudo') {
        
        $apelido = strtolower(url_title($apelido));
        
        if(is_array($dadosPost)){
            
            // se for grupo não altera o apelido
            if($dadosPost['grupo'] == 0 || (isset($dadosPost['tipo']) && $dadosPost['tipo'] == 'tag')){
                $num_rows = 0;
            }
            // comportamento padrão
            else {
                $this->ci->db->where('nick', $apelido);
                $this->ci->db->where('id !=', $id);

                if($tabela === 'cms_conteudo')
                {
                    $this->ci->db->where('modulo_id', $dadosPost['modulo_id']);
                }

                $this->ci->db->where('grupo !=', 0);
                $this->ci->db->where('tipo', 'conteudo');
                $this->ci->db->where('lang', get_lang());
                $this->ci->db->order_by('id desc');
                $this->ci->db->limit(1);
                $sql = $this->ci->db->get($tabela);
                $num_rows = $sql->num_rows();
            }
            
        }        

        if ($num_rows == 1) {  
            $this->ci->load->helper('string');
            $row = $sql->row_array();
            $apelido = $row['nick'];
            $apelido = increment_string($apelido);
        }

        return $apelido;
    }

    // -------------------------------------------------------------------------
    /**
     * Incrementa o campo 'visitas' na tabela especificada.
     *
     * @param mixed $nick : o apelido deste registro, ou ID
     * @param mixed $tabela : tabela que será feito o incremento
     */
    function soma_visita($nick, $tabela) {
        // pega o valor atual
        $this->ci->db->where('nick', $nick);
        $sql = $this->ci->db->get($tabela);
        $row = $sql->row_array();
        $soma = $row['visitas'] + 1;
        // atualiza
        $this->ci->db->update($tabela, array('visitas' => $soma));
    }

    /**
     * Cms_utils::tamanho_arquivo()
     *
     * @param mixed $tamanhoarquivo
     * @return
     */
    function tamanho_arquivo($tamanhoarquivo) {
        $bytes = array('Kb', 'Mb', 'Gb', 'Tb');

        if ($tamanhoarquivo <= 999) {
            $tamanhoarquivo = 1;
        }

        for ($i = 0; $tamanhoarquivo > 999; $i++) {
            $tamanhoarquivo /= 1024;
        }

        if (($i - 1) < 0) {
            $offset = 0;
        } else {
            $offset = $i - 1;
        }

        return round($tamanhoarquivo) . $bytes[$offset];
    }

    /**
     * Limpa uma String de caracteres especiais, espaços e pontuação.
     *
     * @param       string      $texto : string de entrada
     * @return      string
     */
    function limpa_caracteres($texto = '') {      
        $this->ci->load->helper('text');
        return convert_accented_characters($texto);
    }

    /**
     * Framework para gerar comboboxes
     *
     * @param array $ids
     * @param mixed $name
     * @param mixed $multi
     * @param string $extra
     * @return
     */
    function cb($ids = array(), $populaCombo = array(), $name = 'combo', $multi = false, $extra = '', $extra2 = array(), $disabled = false) {
        // -- >> se existe coloca primeiro campo
        if (count($extra2) > 0) {
            foreach ($extra2 as $c => $v) {
                $a_rows[$v] = $c;
            }
        }
        // echo '<pre>';
        // print_r($a_rows);
        // exit;
        // -- >> entra com os campos do combobox << -- //
        foreach ($populaCombo as $c => $v) {
            $a_rows[$c] = $v;
        }
        // -- se for multi seleção  -- //
        
        if($multi) {
            $tag_multi = ' multiple="multiple"';
            $label = $name . '[]';
            
            // se é multi acrescenta classe para aumentar combo
            $user_extra = $extra;
            $extra = "combo-multi " . $user_extra;
            
        } else {
            $label = $name;
            $tag_multi = '';
        }
        
        // os IDs devem ser arrays
        if (!is_array($ids))
            $selected = array($ids);
        else
            $selected = $ids;
        // Popula o array
        $mark = array();
        foreach ($selected as $id) {
            if ($id != '')
                $mark[] = $id;
        }
        $drop = form_dropdown($label, $a_rows, $mark, 'class="input-combo ' . $extra . '" id="' . $name . '"' . $tag_multi);
        return $drop;
    }

    /**
     * Cria combobox dos grupos de conteúdo
     * Retorna o campo <select> comtado para a view
     *
     * @param int $co
     * @param string|array $ids
     * @param bool $multi
     * @param array $habilitado
     * @param string $tabela
     * @return string
     */
    function combo_grupos($co, $ids = '', $multi = true, $habilitado = '', $tabela = 'cms_conteudo') {
        $desabilita = (is_array($habilitado)) ? array_flip($habilitado) : array();

        if (isset($desabilita['grupos'])) {
            return false;
        }

        // pesquisa
        if ($tabela == 'cms_usuarios') {
            $this->ci->db->order_by('ordem', 'nome');
            $this->ci->db->select('id, nome');
        } else {
            $this->ci->db->order_by('ordem', 'titulo');

            // o campo tipo na tabela cms_conteudo tem uma função diferente das outras tabelas
            // a partir da versão 4.4 a tabela cms_conteudo mudou 'tipo' para 'modulo_id'
            if ($tabela == 'cms_conteudo') {
                $this->ci->db->where('modulo_id', $co);
                $this->ci->db->where('tipo', 'conteudo');
            } else {
                $this->ci->db->where('tipo', $co);
            }



            $this->ci->db->select('id, titulo, nick');
        }

        $this->ci->db->where('grupo', 0);
        $this->ci->db->where('lang', get_lang());
        $this->ci->db->where('status', 1);
        $sql = $this->ci->db->get($tabela);
        if ($sql->num_rows() == 0)
            return false;
        // prepara para combo
        // A partir da v4.4 o combo de grupos não tem opção vazia
        # $menus = ($multi) ? array() : array('' => 'Grupos');
        $menus = array();

        foreach ($sql->result_array() as $i) {
            if ($tabela == 'cms_usuarios') {
                $label = $i['nome'];
            } else {
                $label = $i['titulo'];
            }
            $menus[$i['id']] = $label;
        }
        $cb = $this->cb($ids, $menus, 'grupos', $multi);
        return $cb;
    }

    /**
     * Gera combobox de conteúdos relacionados
     * Retorna o campo <select> comtado para a view
     *
     * @param array $dadosModulo
     * @param array $dadosConteudo
     * @param string $tabela
     * @return <type>
     */
    function combo_relacionados($dadosModulo, $dadosConteudo = '', $tabela = 'cms_conteudo') {
       
        
        $uri = $this->ci->uri->to_array(array('id'));
        
        $rels = $dadosModulo['rel'];
        $rels2 = explode('|', $rels);
        $idMod = ($rels2[0] == '') ? 6 : $rels2;
//        $idGrupo = (!isset($rels2[1])) ? 0 : $rels2[1]; # deprecated
        
        
        // Se o relacionamento for com mais de um módulo, estes módulos só podem utilizar a tabela cms_conteudo,
        if($rels != 25){
            $tabela = 'cms_conteudo';
            // pesquisa pelos conteúdos dos módulos do array
            $this->ci->db->select('conteudo.id, conteudo.titulo, conteudo.nick, conteudo.grupo, modulo.label as modulo');
            $this->ci->db->from('cms_conteudo as conteudo');
            $this->ci->db->order_by('conteudo.modulo_id, conteudo.titulo');
            
            $this->ci->db->join('cms_modulos as modulo', 'modulo.id = conteudo.modulo_id', 'left');
            $this->ci->db->join('cms_conteudo as grupo', 'grupo.id = conteudo.grupo', 'left');
            
            
            $this->ci->db->distinct();            
            
            // elimina o próprio conteúdo
            if(isset($uri['id'])){
                $this->ci->db->where('conteudo.id !=', $uri['id']);
            }            
            
//            $this->ci->db->where_in('conteudo.modulo_id', implode(',', $idMod));
            
//            $this->ci->db->where('conteudo.grupo >', 0);
            $this->ci->db->where('conteudo.tipo', 'conteudo');
            $this->ci->db->where('conteudo.lang', get_lang());
            $this->ci->db->where('conteudo.status', 1);
            
            $this->ci->db->where_in('conteudo.modulo_id', $idMod);

            
            
        }
        // não é um array, então pode se relacionar com outras tabelas
        else {
            
            // se for usuários
            $tabela = 'cms_usuarios';
            $this->ci->db->order_by('ordem, nome');
            $this->ci->db->select('id, nome');
            $this->ci->db->from('cms_usuarios');
            $this->ci->db->where('status', 1);
            $this->ci->db->where('lang', get_lang());
            $this->ci->db->where('grupo >', 0);
            
            
        }
       
        
        
        

         // gera os ids já relaionados neste conteúdo
        if (isset($dadosConteudo['rel']) &&  $dadosConteudo['rel'] != ''){
            $ids = explode(',', $dadosConteudo['rel']);
        } else {
            $ids = '';
        }
        
        
        $sql = $this->ci->db->get();
        
        
        
//         mybug($this->ci->db->last_query(), true);
//        mybug($sql->result_array());
        
        if ($sql->num_rows() == 0){
            return false;
        }
        
        // prepara para combo
        $menus = array('' => '');

        foreach ($sql->result_array() as $i) {
            if ($tabela == 'cms_usuarios') {
                $label = $i['nome'];
            } else {
                $label = $i['modulo'] .' > '.$i['titulo'];
            }
            $menus[$i['id']] = $label;
        }
        
//        mybug($ids);

        $cb = $this->cb($ids, $menus, 'rel', true , '" data-placeholder="Pesquisar..." style="width:100%');
        return $cb;
    }


    /**
     * Retorna os dados básicos do conteúdo  que está relacionado com o arquivo.
     * 
     * @param type $arquivo
     * @return type 
     */
    function getComboImgRel($arquivo){

        // pega dados do arquivo e módulo
        $this->ci->db->select('tba.id, tba.titulo, tbb.label, tba.modulo_id');
        $this->ci->db->from('cms_conteudo as tba');
        $this->ci->db->where('tba.lang', get_lang());
        $this->ci->db->where('tba.id', $arquivo['rel']);// <<
        $this->ci->db->join('cms_modulos as tbb', 'tbb.id = tba.modulo_id');
        $sql = $this->ci->db->get();

        if($sql->num_rows()){
            return $sql->row_array();
        } else {
            return array('id' => '', 'titulo' => '', 'label' => '', 'modulo_id' => '');
        }

    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna dados do conteúdo de forma simples. Usado no Cms_controller
     * @param type $var
     * @param type $tb
     * @param type $tipo
     * @return type
     */
    public function conteudo_dados_simples($var, $tb = 'cms_conteudo', $tipo = 'conteudo'){
        if (!is_array($var)) {
            $id = $var;
        } else {
            $id = $var['id'];
        }
        
        $sql = $this->ci->db->from($tb)
                    ->where('id', $id)
                    ->get();
        
        
        // parseia conteudo
        $saida = array();

        $dadosConteudo = $sql->row_array();

        
        foreach ($dadosConteudo as $chv => $vlr) {

            
            // identifica se é o campo 'tags' com valor de cor hexadecimal #ffff
            //if (substr($vlr, 0, 1) == '#') {
                // os valores de cor nos grupos de usuário estão guardados no campo 'filtro'
                if ($tb == 'cms_usuarios' && $chv == 'filtro') {
                    $cores = (strlen($vlr) > 6) ? explode('|', $vlr) : array("", "");
                    $saida['cor1'] = $cores[0];
                    $saida['cor2'] = $cores[1];
                }
                // os valores de cor nos grupos de conteudo estão guardados no campo 'tags'
                else if ($tb != 'cms_usuarios' && $chv == 'img') {
                    $cores = (strlen($vlr) > 6) ? explode('|', $vlr) : array("", "");
                    $saida['cor1'] = $cores[0];
                    $saida['cor2'] = $cores[1];
                }
            //}

                    

            $saida[$chv] = $vlr;
        }


        return $saida;
    }

    /**
     * Retorna as informações de um conteúdo através do ID
     *
     * @param array $var
     * @return
     */
    function conteudo_dados($var, $tb = 'cms_conteudo', $tipo = 'conteudo') {
        if (!is_array($var)) {
            $id = $var;
        } else {
            $id = $var['id'];
        }
        
        $controller = $this->ci->uri->segment(2);
//        $metodo = $this->ci->uri->segment(3);
        
//        mybug($controller);
        // se for pesquisa de conteúdo
        // faz join para grupos. é usado para montar URI de front-end
        if($tipo == 'conteudo' && ($tb == 'cms_conteudo' || $tb == 'cms_usuarios') && $controller != 'paginas'){
            $this->ci->db->from($tb.' as cont');
            $this->ci->db->join($tb.' as grupo', 'grupo.id = cont.grupo');
            $this->ci->db->where('cont.id', $id);

            if($tb == 'cms_conteudo'){
                $this->ci->db->select('cont.*, grupo.nick as grupo_nick, grupo.titulo as grupo_titulo');
            } else {
                $this->ci->db->select('cont.*');
            }
        } 
        // senão, faz pesquisa simples
        else {
            $this->ci->db->from($tb)
                    ->where('id', $id);
        }
      
        
        $sql = $this->ci->db->get();
        
//        mybug( $this->ci->db->last_query());
        
        if ($sql->num_rows() == 0)
            return false;

        // parseia conteudo
        $saida = array();

        $dadosConteudo = $sql->row_array();

        
        foreach ($dadosConteudo as $chv => $vlr) {

            
            // identifica se é o campo 'tags' com valor de cor hexadecimal #ffff
            //if (substr($vlr, 0, 1) == '#') {
                // os valores de cor nos grupos de usuário estão guardados no campo 'filtro'
                if ($tb == 'cms_usuarios' && $chv == 'filtro') {
                    $cores = (strlen($vlr) > 6) ? explode('|', $vlr) : array("", "");
                    $saida['cor1'] = $cores[0];
                    $saida['cor2'] = $cores[1];
                }
                // os valores de cor nos grupos de conteudo estão guardados no campo 'tags'
                else if ($tb != 'cms_usuarios' && $chv == 'img') {
                    $cores = (strlen($vlr) > 6) ? explode('|', $vlr) : array("", "");
                    $saida['cor1'] = $cores[0];
                    $saida['cor2'] = $cores[1];
                }
            //}

                    

            $saida[$chv] = $vlr;
        }





        return $saida;
    }

    /**
     * Gera um array com as chaves correspondentes as letras das açoes possíveis
     * no sistema e os valores sendo 0 ou 1 caso o admin tenha esta permissão.
     * Ex: array('a'=>1, 'c'=>0, 'r'=>0, 'l'=>1)
     *
     * @return
     */
    function gera_array_acoes() {
        // session
        $acts = $this->ci->phpsess->get('admin_act', 'cms');
        $tipo = $this->ci->phpsess->get('admin_tipo', 'cms'); // se for God libera geral
        $list_acts = $this->str_to_array($acts, '|');
        // valores default
        if ($tipo == 0) {
            return array('a' => 1, 'c' => 1, 'r' => 1, 'l' => 1);
        }
        if (strlen(trim($acts)) == 0 OR count($list_acts) == 0) {
            return array('a' => 0, 'c' => 0, 'r' => 0, 'l' => 0);
        }
        // lista de tipos
        $tipos = array('a', 'c', 'r', 'l');

        $saida = array();
        // percorre e identifica todos os tipos
        foreach ($tipos as $t) {
            if (array_search($t, $list_acts) !== false) {
                $c = $t;
                $v = 1;
            } else {
                $c = $t;
                $v = 0;
            }
            $saida[$c] = $v;
        }
        return $saida;
    }

    /**
     * Retorna os dados da pasta de imagens, arquivos e galerias
     *
     * @param string $id
     * @return
     */
    function pasta_dados($id = '') {
        if (!isset($id) || !is_numeric($id)

            )return false;

        $this->ci->db->where('id', $id);
        $sql = $this->ci->db->get('cms_pastas');
        if ($sql->num_rows() == 0)
            return false;
        return $sql->row_array();
    }

    /**
     * Retorna as pastas de imagens, arquivos e galerias
     *
     * @param string $id
     * @return
     */
    function pastas_lista($co = '') {
        if (!isset($co) || !is_numeric($co)

            )return false;

        $this->ci->db->where('tipo', $co);
        $this->ci->db->where('status', 1);
        $this->ci->db->where('grupo !=', 0);
        $this->ci->db->order_by('ordem');
        $sql = $this->ci->db->get('cms_pastas');
        if ($sql->num_rows() == 0)
            return false;
        return $sql->result_array();
    }

    /**
     * Retorna os dados da imagem ou arquivo
     *
     * @param string $id
     * @return
     */
    function arquivo_dados($id) {
        if (!isset($id))
            return false;

        $this->ci->db->where('id', $id);
        $sql = $this->ci->db->get('cms_arquivos');
        if ($sql->num_rows() == 0) {
            return false;
        }


        return $sql->row_array();
    }

    /**
     * Recebe is IDs unidos por |, ou em forma array() e devolve um array multi
     * com todas as infos dos arquivos
     *
     * @param mixed $str
     * @return
     */
    function arquivos_concat_dados($str) {
        if (!isset($str))
            return false;

        if (!is_array($str)) {
            $lista = explode('|', $str);
        } else {
            $lista = $str;
        }
        if (strlen($lista[0]) == 0) return false;

        $saida = array();
        foreach ($lista as $id) {
            $dados = $this->arquivo_dados($id);
            if ($dados){
                $saida[] = $dados;
            } else{
                $saida[] = $id;
            }
        }
//        mybug($saida);
        return $saida;
    }

    /**
     * Salva infos da imagem no banco de dados.
     * [file_name] => paor-do-sol.jpg
     * [file_type] => image/jpeg
     * [file_path] => C:/apache2triad/htdocs/cms3.com.br/upl/imgs/
     * [full_path] => C:/apache2triad/htdocs/cms3.com.br/upl/imgs/paor-do-sol.jpg
     * [raw_name] => paor-do-sol
     * [orig_name] => paor-do-sol.jpg
     * [file_ext] => .jpg
     * [file_size] => 69.52
     * [is_image] => 1
     * [image_width] => 800
     * [image_height] => 600
     * [image_type] => jpeg
     * [image_size_str] => width="800" height="600"
     *
     * @param  $id = ID da seção, galeria ou pasta
     * return boolean
     */
    function salva_img_dados($data = array(), $pasta = '') {
        $ext = strtolower(str_replace('.', '', $data['file_ext']));
        if ($ext == 'jpg' || $ext == 'gif' || $ext == 'png')
            $eh_img = 1;
        else
            $eh_img = 0;
        // - recarrega dados da imagem, pois se foi redimensionada o tamanho foi alterado
        if ($eh_img) {
            $tmM = @getimagesize($data['full_path']);
            $larg = (int) $tmM[0];
            $alt = (int) $tmM[1];
            if ($larg == $alt) {
                $pos = 'q';
            } else if ($larg > $alt) {
                $pos = 'h';
            } else {
                $pos = 'v';
            }
        } else { // eh arquivo
            $pos = '';
            $larg = '';
            $alt = '';
        }
        // - salva dados da imagem
        $dados['dt_ini'] = date("Y-m-d");
        $dados['nome'] = prepend_upload_file($data['file_name']);
        $dados['width'] = $larg;
        $dados['height'] = $alt;
        $dados['pos'] = $pos;
        $dados['ext'] = $ext;
        $dados['img'] = $eh_img;
        $dados['pasta'] = $pasta;
        $dados['peso'] = @filesize($data['full_path']);
        $dados['conteudo_id'] = (isset($data['conteudo_id'])) ? $data['conteudo_id'] : 0;
        // echo '<pre>';
        // print_r($dados);
        // exit;
        $resp = $this->ci->db->insert('cms_arquivos', $dados);
        // - salva a imagem no conteúdo destino
        // retorna o ID da imagem
        return $this->ci->db->insert_id();
    }

    function salva_link_externo($externo, $pasta) {
        $externo = prep_url($externo);
        $link = $this->verica_tipo_link_externo($externo);
        // -
        if ($link['ehyt'] == 1

            )$eh_img = 2;
        else
            $eh_img = 3; // arq externo
            // - recarrega dados da imagem, pois se foi redimensionada o tamanho foi alterado
 if ($link['ext'] == 'jpg' || $link['ext'] == 'gif' || $link['ext'] == 'png') {
            $tmM = @getimagesize($externo);
            $larg = $tmM[0];
            $alt = $tmM[1];
            if ($larg == $alt) {
                $pos = 'q';
            } else if ($larg > $alt) {
                $pos = 'h';
            } else {
                $pos = 'v';
            }
        } else { // eh arquivo
            $pos = '';
            $larg = '';
            $alt = '';
        }
        // - salva dados da imagem
        $dados['dt_ini'] = date("Y-m-d");
        $dados['nome'] = $externo; //$link['file_name'];
        $dados['descricao'] = $link['file_name'];
        $dados['width'] = $larg;
        $dados['height'] = $alt;
        $dados['pos'] = $pos;
        $dados['ext'] = $link['ext'];
        $dados['img'] = $eh_img;
        $dados['pasta'] = $pasta;
        $dados['peso'] = 0;
        // echo '<pre>';
        // print_r($dados);
        // exit;
        $resp = $this->ci->db->insert('cms_arquivos', $dados);
        // - salva a imagem no conteúdo destino
        // retorna o ID da imagem
        return $this->ci->db->insert_id();
    }

    function verica_tipo_link_externo($url) {
        $nospontos = explode('.', $url);
        $nasbarras = explode('/', $url);
        // verifica se tem extenção
        $ehext = $nospontos[count($nospontos) - 1];
        $ehext = (strlen($ehext) <= 4) ? $ehext : false;
        // verifica se é youtube
        $ehyt = (strtolower($nospontos[1]) == 'youtube') ? 1 : 0;
        // pega o nome do arquivo
        if ($ehyt) {
            $ytparts = explode('v=', $url);
            // limpa complementos de url
            $yturi = explode('&', $ytparts[1]);
            $file_name = 'YouTube [' . $yturi[0] . ']';
        } else if ($ehext) {
            $file_name = $nasbarras[count($nasbarras) - 1];
        } else {
            $file_name = 'unamed';
        }
        // retorna
        return array('ext' => $ehext,
            'ehyt' => $ehyt,
            'file_name' => $file_name);
    }

    /**
     * retorna os dados da imagem ou arquivo que foi copiado para o servidor
     * @param string $nomeArq
     * @param string $tipo 'img' ou 'arq'
     * @return array
     */
    function dados_arquivo($nomeArq, $tipo = 'img') {
        if ($tipo == 'img') {
            $pasta = fisic_path() . $this->ci->config->item('upl_imgs');
            $tmM = @getimagesize($pasta . '/' . $nomeArq);
            $larg = $tmM[0];
            $alt = $tmM[1];
        } else {
            $pasta = fisic_path() . $this->ci->config->item('upl_arqs');
            $larg = '';
            $alt = '';
        }
        $ext1 = explode('.', $nomeArq);
        $ttl = count($ext1);
        $ext2 = '.' . $ext1[$ttl - 1];
        $data['full_path'] = $pasta . '/' . $nomeArq;
        $data['file_name'] = $nomeArq;
        if (file_exists($pasta . '/' . $nomeArq)) {
            $data['file_size'] = @filesize($pasta . '/' . $nomeArq);
        } else {
            $data['file_size'] = 0;
        }
        $data['file_ext'] = $ext2;
        $data['image_width'] = $larg;
        $data['image_height'] = $alt;
        return $data;
    }

    /**
     * Redimenciona no tamanho padrão do site.
     *
     * @param string $caminho_completo
     * @param integer $larg
     * @param integer $alt
     * @return bool
     */
    function redimenciona($caminho_completo, $larg = '', $alt = '', $size = 'limite') {
        if ($size == 'thumb') {
            $larg_final = ($larg == '') ? $this->ci->config->item('imagem_mini_w') : $larg;
            $alt_final = ($alt == '') ? $this->ci->config->item('imagem_mini_h') : $alt;
        } else {
            $larg_final = ($larg == '') ? $this->ci->config->item('imagem_limit_w') : $larg;
            $alt_final = ($alt == '') ? $this->ci->config->item('imagem_limit_h') : $alt;
        }

        // separa o nome do arquivo
        $nome1 = explode('/', $caminho_completo);
        $nome = $nome1[count($nome1) - 1];

        $this->ci->load->library('image_lib');
        // gera o thumbnail e redimensiona para o tamanho padrao
        // $config3['image_library'] = 'gd2';
        $config3['source_image'] = $caminho_completo;

//        if ($size == 'thumb'){
//            $config3['create_thumb'] = true;
//        } else{
//            $config3['new_image'] = substr($nome, 0, -4) . '_'.$size . substr($nome, -4);
//        }

        if ($size != 'limite')
            $config3['new_image'] = substr($nome, 0, -4) . '_' . $size . substr($nome, -4);

        $config3['maintain_ratio'] = true;
        $config3['width'] = $larg_final;
        $config3['height'] = $alt_final;

        $this->ci->image_lib->initialize($config3);
        // $this->load->library('image_lib', $config3);
        if ($this->ci->image_lib->resize()) {
            $this->ci->image_lib->clear();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Recebe o CEP e submete a APPs republicavirtual.com.br para retornar uma string.
     * Os valores são separados por '_'.
     * Normalmente é acessada por AJAX.
     *
     * @param string $cep : Só numeros
     * @return string
     */
    function cep_endereco($cep = '') {
        $resultado = @file_get_contents('http://republicavirtual.com.br/web_cep.php?cep=' . urlencode($cep) . '&formato=query_string');
        if (!$resultado) {
            $resultado = "&resultado=0&resultado_txt=erro+ao+buscar+cep";
        }
        parse_str($resultado, $retorno);
        // retornos
        $tipo_resultado = $retorno['resultado']; // 1(completo), 2(cidade, uf), erro
        // retorno completo
        if ($tipo_resultado == '1') {
            $tipo_logradouro = utf8_encode($retorno['tipo_logradouro']);
            $logradouro = utf8_encode($retorno['logradouro']);
            $bairro = utf8_encode($retorno['bairro']);
            $cidade = utf8_encode($retorno['cidade']);
            $uf = utf8_encode($retorno['uf']);

            $saida = $tipo_logradouro . '_' . $logradouro . '_' . $bairro . '_' . $cidade . '_' . $uf;
        } else if ($tipo_resultado == '2') {
            $cidade = utf8_encode($retorno['cidade']);
            $uf = utf8_encode($retorno['uf']);

            $saida = $cidade . '_' . $uf;
        }
        // erro
        else {
            $saida = 'Não foi encontrado!';
        }

        return $saida;
    }

    /**
     * Recupera infos de configuração do sistema
     *
     * @param mixed $id
     * @param mixed $valor
     * @return
     */
    function config($id, $valor = true) {
        $this->ci->db->where('id', $id);
        $sql = $this->ci->db->get('cms_config');
        $row = $sql->row_array();
        if ($valor

            )return $row['valor'];
        else
            return $row;
    }

    /**
     * Gera o ComboBox dos estados. Caso a variável $id exista marca a opção como selected.
     *
     * @param string $id : sigla do Estado
     * @return string
     */
    function combo_estados($id = '') {
        $selected = array($id);

        $this->ci->db->order_by('nome');
        $sql = $this->ci->db->get('opt_estado');
        // if ($id == 'nenhum') $a_rows[0] = 'Nenhum';
        $a_rows[''] = 'Escolha...';
        foreach ($sql->result_array() as $row) {
            $i = $row['uf'];
            $lbl = $row['nome'];
            $a_rows[$i] = $lbl;
        }

        $cb = $this->cb($id, $a_rows, 'uf', false);

        return $cb;
    }

    /**
     * Gera o ComboBox das cidades dentro da restrição da variável $uf.
     * Caso a variável $id_city exista marca a opção como selected.
     *
     * @param string $uf : sigla do Estado
     * @param string $id_city : ID da cidade
     * @return string
     */
    function combo_cidades($uf = '', $id_city = '') {
        if ($uf == '') {
            return '';
        }
        $selected = array($id_city);

        $this->ci->db->order_by('nome');
        $this->ci->db->where('uf', strtoupper($uf));
        $sql = $this->ci->db->get('opt_cidades');

        if($sql->num_rows() == 0)
        {
            return 'não encontrado';
        }

        foreach ($sql->result_array() as $row) {
            $i = $row['id'];
            $lbl = $row['nome'];
            $a_rows[$i] = $lbl;
        }

        $cb = $this->cb($id_city, $a_rows, 'cidade', false);
        return $cb;
    }

    /**
     * Gera o ComboBox das variáveis do sistema.
     *
     * @param string $uf : sigla do Estado
     * @param string $id_city : ID da cidade
     * @return string
     */
    function combo_sist_vars($combo_name = 'variaveis', $grupo_id = '', $ids = '', $multi = false) {
        if ($grupo_id == '')
            return false;

        $this->ci->db->where('grupo', $grupo_id); // lingua
        $this->ci->db->order_by('ordem');
        $this->ci->db->where('status', 1);
        $sql = $this->ci->db->get('cms_combobox');

        foreach ($sql->result_array() as $row) {
            $i = ($row['valor'] == '') ? $row['id'] : $row['valor'];
            $lbl = $row['titulo'];
            $a_rows[$i] = $lbl;
        }

        $cb = $this->cb($ids, $a_rows, $combo_name, $multi);
        return $cb;
    }

    /**
     * Entra com a string extra do módulo ou o ID
     * Retorna um array multi com a estrutura do Módulo
     * array([0] => array(
     * 			name => "Ação",
     * 			id   => "acao",
     * 			type => "combo",
     * 			data => array(
     * 				[0] => array('valor' => "azul",
     *                                              'class' => "classe")
     * 				),
     * 				[1] => array('valor' => "roxo",
     *                                              'class' => "classe")
     * 				)
     * 			)
     * 		)
     * );
     * @param mixed $estruturaModulo
     * @return
     * */
    function extraModuloArray($estruturaModulo) {
        $estruturaModulo = trim($estruturaModulo);
        if (strlen($estruturaModulo) == 0)
            return false;

        // se for número busca os dados
        if (is_numeric($estruturaModulo)) {
            $this->ci->db->where('id', $estruturaModulo);
            $this->ci->db->select('extra');
            $sql = $this->ci->db->get('cms_modulos');
            $row = $sql->row_array();
            $estruturaModulo = trim($row['extra']);
        }


        // quebra a estrutura, eliminar o último vazio
        $estrutura = explode('};', $estruturaModulo);

        // percorre cada campo e gera array de opções e dados
        $saida = array();
        for ($x = 0; $x < count($estrutura) - 1; $x++) {

            // estrutura // "Ação:acao:radio{*azul,roxo,vermelho"
            $p1 = explode('{', $estrutura[$x]);

            // estrutura // separa nome e tipo
            $n1 = explode(':', $p1[0]);

            // estrutura
            $name = $n1[0];
            $id = $n1[1];
            $type = $n1[2];

            // ajusta conteúdo do campo de acordo com o tipo
            if ($type == "input" || $type == "text" || $type == "arq" || $type == "img") {

                // é string
                $data = '';
            } else {

                // transforma em um array
                $campos = explode(',', $p1[1]);

                $data = array();
                // percorre cada item
                for ($j = 0; $j < count($campos); $j++) {

                    $dds = explode('|', $campos[$j]);
                    $ddclass = (isset($dds[1])) ? $dds[1] : ''; // para quando não haver classe
                    $data[] = array('valor' => $dds[0],
                        'class' => $ddclass);
                }
            }

            $saida[] = array(
                'name' => $name,
                'id' => $id,
                'type' => $type,
                'data' => $data
            );
        }

//echo '<pre>';
//print_r($saida);
//exit;

        return $saida;
    }

    /**
     * Entra com a string extra do conteúdo ou o ID
     * Retorna um array multi com a estrutura
     * array([0] => array(
     * 			id   => "acao",
     * 			type => "combo",
     * 			data => array(
     * 				[0] => "0"
     * 				),
     * 				[1] => "1"
     * 				)
     * 			)
     * 		)
     * );
     * @param mixed $estruturaModulo
     * @return
     * */
    function extraConteudoArray($dadosRegistro, $tb = 'cms_conteudo') {
        $dadosRegistro = trim($dadosRegistro);
        if (strlen($dadosRegistro) == 0)
            return false;

        // se for número busca os dados
        if (is_numeric($dadosRegistro)) {
            $this->ci->db->where('id', $dadosRegistro);
            $this->ci->db->select('extra');
            $sql = $this->ci->db->get($tb);
            $row = $sql->row_array();
            $dadosRegistro = trim($row['extra']);
        }


        // quebra a estrutura, eliminar o último vazio
        $estrutura = explode('};', $dadosRegistro);

        // percorre cada campo e gera array de opções e dados
        $saida = array();
        for ($x = 0; $x < count($estrutura) - 1; $x++) {

            // estrutura // "acao:radio{*azul,roxo,vermelho"
            $p1 = explode('{', $estrutura[$x]);

            // estrutura // separa nome e tipo
            $n1 = explode(':', $p1[0]);

            // estrutura
            $id = $n1[0];
            $type = $n1[1];

            // dados
            $dds = $p1[1];

            // ajusta conteúdo do campo de acordo com o tipo
            if ($type == "input" || $type == "text" || $type == "arq" || $type == "img") {

                // é string
                $data = $dds;
            } else {

                // transforma em um array
                $valores = explode(',', $dds);

                $data = array();
                // percorre cada item
                for ($j = 0; $j < count($valores); $j++) {

                    $data[] = $valores[$j];
                }
            }

            $saida[] = array(
                'id' => $id,
                'type' => $type,
                'data' => $data
            );
        }

        return $saida;
    }

    /**
     * Compara a estrutura de campos extra do módulo com os dados do registro
     * Gera um array multi:
     * array([0] => array(
     * 			name => "Cor do box",
     *                  id => "cor",
     * 			type => "combo",
     * 			data => array(
     * 				[0] => array(
     * 					campo => "azul",
     * 					selected => 1
     * 				),
     * 				[1] => array(
     * 					campo => "roxo",
     * 					selected => 0
     * 				)
     * 			)
     * 		)
     * );
     *
     * @param mixed $estruturaModulo
     * @param mixed $dadosRegistro
     * @return
     * */
    function extraMontaArray($estruturaModulo, $dadosRegistro) {

        $estruturaModulo = $this->extraModuloArray($estruturaModulo);

        $dadosRegistro = $this->extraConteudoArray($dadosRegistro);

        if (!$estruturaModulo)
            return false;


        // percorre cada campo e gera array de opções e dados
        $saida = array();
        for ($x = 0; $x < count($estruturaModulo); $x++) {



            // estrutura Módulo
            $nameM = $estruturaModulo[$x]['name'];
            $idM = $estruturaModulo[$x]['id'];
            $typeM = $estruturaModulo[$x]['type'];
            $camposM = $estruturaModulo[$x]['data']; // array
            // chave para garantir que mesmo que não exista registro gere
            // resultado em todos os dados do Módulo
            $campoProcessado = false;

            // percorre os dados do conteúdo até encontrar o campo com ID igual
            for ($j = 0; $j < count($dadosRegistro); $j++) {

                // dados do Registro
                $idR = $dadosRegistro[$j]['id'];
                $typeR = $dadosRegistro[$j]['type'];
                $valoresR = $dadosRegistro[$j]['data'];


                // ok, encontrou os dados deste registro
                if ($idM == $idR && $typeM == $typeR) {

                    $campoProcessado = true;
                    $idF = $idR;
                    $typeF = $typeR;

                    // ajusta conteúdo do campo de acordo com o tipo
                    if ($typeF == "input" || $typeF == "text" || $typeF == "arq" || $typeF == "img") {

                        $dataF = $valoresR;
                    } else {// são valores em array
                        // percorre o array do módulo e conjuga com o do registro
                        $dataF = array();
                        for ($i = 0; $i < count($camposM); $i++) {

                            // separa o valor da class
                            $campo = $camposM[$i]['valor'];
                            $class = $camposM[$i]['class'];

                            // verifica se existe prioridade
                            // remove *
                            if (substr($campo, 0, 1) == '*') {
                                $campo = substr($campo, 1);
                                $priori = true;
                            } else {
                                $priori = false;
                            }



                            $valor = (isset($valoresR[$i])) ? $valoresR[$i] : 0;

                            $dataF[] = array(
                                'campo' => $campo,
                                'selected' => $valor,
                                'class' => $class
                            );
                        }
                    }
                }
            }

            // se não existe dados gera dados vazios
            if (!$campoProcessado) {

                if ($typeM == "input" || $typeM == "text" || $typeM == "arq" || $typeM == "img") {

                    $dataF = '';
                } else {

                    // percorre o array do módulo e adiciona valores
                    $dataF = array();
                    for ($i = 0; $i < count($camposM); $i++) {

                        // separa o valor da class
                        $campo = $camposM[$i]['valor'];
                        $class = $camposM[$i]['class'];

                        // verifica se existe prioridade
                        if (substr($campo, 0, 1) == '*') {
                            $campo = substr($campo, 1);
                            $priori = true;
                        } else {

                            $priori = false;
                        }

                        $dataF[] = array(
                            'campo' => $campo,
                            'selected' => ($priori) ? 1 : 0,
                            'class' => $class
                        );
                    }
                }
            }


            $saida[] = array(
                'name' => $nameM,
                'id' => $idM,
                'type' => $typeM,
                'data' => $dataF
            );
        }

//echo '<pre>';
//print_r($saida);
//exit;



        return $saida;
    }

    /**
     * Entra com os parámetros extras e os dados do registro
     * Sai campo de formulário preenchido
     *
     * @param mixed $name
     * @param mixed $type
     * @param mixed $data
     * @param string $css
     * @return
     * */
    function extraArrayToForm($name, $id, $type, $data, $css = '') {

        // label
        $id = 'extra_' . $id;

        $saida = '<label for="' . $id . '">' . $name . '</label>';

        if ($type == 'input') {
            $saida .= '<input name="' . $id . '" id="' . $id . '" type="text" class="' . $css . '" value="' . form_prep($data) . '" />';
        } else if ($type == 'text') {
            $saida .= '<textarea name="' . $id . '" class="' . $css . '" id="' . $id . '">' . $data . '</textarea>';
        } else if ($type == 'arq' || $type == 'img') {
            $dado = array($data => $data);
            $saida .= $this->cb($data, array($data => $data), $id, false, 'dyn-' . $type);
        } else {

            if ($type == 'radio' || $type == 'check'
                )$saida .= '<div class="form-opcoes">';

            // percorre um array
            $idsCombo = '';
            $idsMulti = array();
            for ($x = 0; $x < count($data); $x++) {


                $campo = $data[$x]['campo'];
                $selected = $data[$x]['selected'];
                $class = $data[$x]['class'];


                if ($type == 'radio') {

                    $saida .= form_radio($id, $x, $selected) . ' ' . $campo . ' | ';
                } else if ($type == 'check') {

                    $nameCheck = $this->limpa_caracteres($campo);
                    $saida .= form_checkbox($nameCheck, 1, $selected) . ' ' . $campo . ' | ';
                } else if ($type == 'combo') {

                    $idsCombo .= ( $selected == 1) ? (string) $x : '';
                    $populaCombo[$x] = $campo;
                    $multi = false;
                } else if ($type == 'multi') {

                    $idsMulti[] = ($selected == 1) ? (string) $x : null;
                    $populaCombo[$x] = $campo;
                    $multi = true;
                }
            }

//echo '<pre>';
//var_dump($idsCombo);
//exit;
            // se for combobox monta depois de ajeitar o array de entrada
            if ($type == 'combo') {
                $saida .= $this->cb($idsCombo, $populaCombo, $id, $multi, '', array('...' => ''));
            }
            if ($type == 'multi') {
                $saida .= $this->cb($idsMulti, $populaCombo, $id, $multi, '', array('...' => ''));
            }


            if ($type == 'radio' || $type == 'check'

                )$saida .= '</div>';
        }

        return $saida;
    }

    /**
     * Compara os dados do form com a matriz do módulo e salva a string no registro correspondente
     * @param $array Se não for capturar o form via POST especifique um array neste parâmetro
     * */
    function extrasSalva($idModulo, $idRegistro, $tb = 'cms_conteudo', $ident = 'extra_', $array = array()) {
        // monta a extrutura de campos extra
        $extras = $this->extraModuloArray($idModulo);

        // - salva os dados dos submenus antigos
        $saida = '';
        for ($x = 0; $x < count($extras); $x++) {

            // percorre o array do módulo buscando os campos e montando a string
            $campo = $extras[$x]['name'];
            $campoId = $extras[$x]['id'];
            $type = $extras[$x]['type'];
            $data = $extras[$x]['data']; // string ou array
            // concatena o ID e TYPE
            $saida .= $campoId . ':';
            $saida .= $type . '{';

            if ($type == 'input' || $type == 'text' || $type == "arq" || $type == "img") {

                // verifica a fonte de dados
                if (count($array) == 0) {
                    $saida .= $this->ci->input->post($ident . $campoId);
                } else {
                    $saida .= $array[$ident . $campoId];
                }
            } else {

                // seleciona o ID selecionado
                if ($type == 'radio' || $type == 'combo' || $type == 'multi') {

                    // verifica a fonte de dados
                    if (count($array) == 0) {
                        $valueId = $this->ci->input->post($ident . $campoId); // uma opção
                    } else {
                        $valueId = $array[$ident . $campoId]; // uma opção
                    }
                }

                $dds = '';
                // percorre as opções
                for ($j = 0; $j < count($data); $j++) {

                    // dados extras do Módulo
                    $dataValor = $data[$j]['valor'];
                    $dataClass = $data[$j]['class'];

                    // percorre os campos selecionados
                    if ($type == 'check') {

                        // verifica a fonte de dados
                        if (count($array) == 0) {
                            $valueId = $this->ci->input->post($this->limpa_caracteres($dataValor)); // uma opção
                        } else {
                            $valueId = (isset($array[$this->limpa_caracteres($data[$j]['valor'])])) ? $array[$this->limpa_caracteres($data[$j]['valor'])] : false; // uma opção
                        }


                        if ($valueId == "1") {
                            $dds .= '1,';
                        } else {
                            $dds .= '0,';
                        }
                    }

                    // verifica a seleção e concatena 0 e 1
                    if ($type == 'radio' || $type == 'combo') {

                        if ($valueId == $j) {
                            $dds .= '1,';
                        } else {
                            $dds .= '0,';
                        }
                    }

                    // se for mulati percorre array de respostas
                    if ($type == 'multi') {

                        if (array_search($j, $valueId) !== false) {
                            $dds .= '1,';
                        } else {
                            $dds .= '0,';
                        }
                    }
                }

                // limpa a virgula e concatena
                $saida .= trim($dds, ',');
            }


            $saida .= '};'; // fim da concatenação neste laço
        }

//echo '<pre>';
//echo $saida;
//exit;
        // / faz  update
        $this->ci->db->where('id', $idRegistro);
        $this->ci->db->update($tb, array('extra' => $saida));
    }

    /**
     * Insere dados do arquivo na pasta cms_arquivos e liga com o Conteúdo
     * @param int $idConteudo
     * @param int $pastaID Vem da var $this->modulo do Cms_Controller
     * @return boolean
     */
    function salvaArquivo($idConteudo, $pastaID) {
        $arq = $this->ci->input->post('hidFileID');
        // se não existir aborta
        if (strlen($arq) < 5) {
            return false;
        }
        $dados = $this->dados_arquivo($arq, 'arq');
        $dados['conteudo_id'] = $idConteudo;
        $salvou = $this->salva_img_dados($dados, $pastaID);
        return $salvou;
    }

    /**
     * Retorna a lista de link dos arquivos deste conteúdo.
     * Próprio para o form do CMS
     * @param <type> $idConteudo
     * @return string
     */
    function getListaArquivosConteudo($idConteudo) {

        $this->ci->db->where('conteudo_id', $idConteudo);
        $sql = $this->ci->db->get('cms_arquivos');
        if ($sql->num_rows()) {

            $pasta = base_url() . $this->ci->config->item('upl_arqs') . '/';

            $saida = '<label for="status">Anexos do conteúdo</label>
            <div class="form-opcoes">';

            foreach ($sql->result_array() as $a) {

                $class = 'icon-'.$a['ext'];

                $saida .= '<a href="' . $pasta . $a['nome'] . '" target="_blank" class="arquiv '.$class.'" rel="' . $a['id'] . '">' . $a['nome'] . '</a>
             &laquo; <a href="#" class="del del-arquiv" rel="' . $a['id'] . '">[remover]</a><br />';
            }

            $saida .= '<br />
            </div>';
        } else {
            $saida = '<label for="">Anexar arquivo</label>';
        }

        return $saida;
    }

    /**
     * Gera links de planilhas personalizadas. É carregada a apartir da view "barra_navegacao"
     * @param int $co
     * @return string
     */
    function linkPlanilha($co) {
        $saida = '';
        // verifica se existe links para este módulo
        $link = $this->ci->config->item('co' . $co);
        if (!$link || count($link) == 0)
            return '';
        for ($x = 0; $x < count($link); $x++) {

            $item = $link[$x];
            // percorre cada item do link e monta a saida
            $concat = '';
            for ($j = 1; $j < count($item); $j++) {

                $concat .= "/" . $item[$j];
            }

            $saida .= '<a href="';
            $saida .= cms_url("cms/cmsutils/exportacao/co:" . $co . $concat);
            $saida .= '" class="nyroModal" target="_blank">' . $item[0] . '</a> | ';
        }
//        echo '<pre>';
//        var_dump($link);
//        exit;


        return trim($saida, ' | ');
    }

    // ////////////////////////////////////////////////////////////////////////////////////////////////
    // ////////////   TUDO DESTA LINHA PARA BAIXO SERÁ DEPRECIADO    /////////////////////////////////
    // ///////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Gera o ComboBox das Pastas de Imagens. Caso a variável $id exista marca a opção como selected.
     *
     * @param string $id : ID da Pasta
     * @return string
     */
    function combo_pasta_imagens($id = '') {
        $selected = array($id);

        $this->CI->db->order_by('nome');
        $sql = $this->CI->db->get('aw_imagens_tipo');

        if ($id == 'nenhum')
            $a_rows[0] = 'Nenhum';

        foreach ($sql->result_array() as $row) {
            $i = $row['id'];
            $lbl = $row['nome'];
            $a_rows[$i] = $lbl;
        }

        $this->CI->load->helper('form');
        $drop = form_dropdown('pasta', $a_rows, $selected, 'class="texto" id="pasta"');
        return $drop;
    }

    /**
     * Gera combobox com os campos da tabela aw_usuarios.
     *
     * @param mixed $ids : array/string com os IDs
     * @param bool $multi : se 'true' permite multiseleção
     * @param string $extra : permite incluir mais atributos
     * @return string
     */
    function combo_export_user($ids = array(), $multi = true, $extra = '') {
        // se for multi seleção
        if ($multi) {
            $tag_multi = ' multiple="multiple"';
            $label = 'campo[]';
        } else {
            $label = 'campo';
            $tag_multi = '';
        }
        // se não é vira
        if (!is_array($ids))
            $selected = array($ids);
        // Popula o array
        $mark = array();
        foreach ($selected as $id) {
            if ($id != '')
                $mark[] = $id;
        }
        // lê as tabelas
        $fields = $this->CI->db->list_fields('aw_usuarios');

        foreach ($fields as $campo) {
            $a_rows[$campo] = $campo;
        }

        $this->CI->load->helper('form');
        $drop = form_dropdown($label, $a_rows, $mark, 'class="texto" id="campo"' . $tag_multi);
        return $drop;
    }

    /**
     * Extrai email s de um texto ou url e retorna um array
     *
     * @param string $texto
     * @param string $tipo
     * @return array
     */
    function extrai_emails_do_texto($texto, $tipo = 'texto') {

        if ($tipo == 'url') {

            $texto = file_get_contents($texto);
        }

        // original

        $filtro = preg_match_all("/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}/i", $texto, $encontrado);

        // mod 1
        // $filtro = preg_match_all("/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{0,3}/i",  $texto, $encontrado);
        // mod 2
        // $filtro = preg_match_all("/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)/i",  $texto, $encontrado);

        

        if ($filtro) {

            return array_unique($encontrado[0]);

        } else {

            return false;
        }
       
    }

}

?>