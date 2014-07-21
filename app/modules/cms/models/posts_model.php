<?php

/**
 * Posts_model
 *
 * @package
 * @author Bruno
 * @copyright Copyright (c) 2010
 * @version $Id$
 * @access public
 * */


class Posts_model extends MY_Model{

    /**
     * Meta dados registrados
     * @var type 
     */
    protected $_metas = array(
        array('meta_key' => 'priority', 'meta_type' => '', 'meta_value' => ''),
        array('meta_key' => 'my-test-graph', 'meta_type' => '', 'meta_value' => ''),
    );
    
    function __construct() {
        parent::__construct();
    }

    /**
     * Lista os conteudos dos grupos e conteudos
     *
     * @param mixed $v array com dados via URI
     * @param string $tipo grupo ou conteudo
     * @param array $modulo array dados do módulo
     * @return
     */
    function lista_conteudos($v, $tipo = 'conteudo', $modulo = array()) {
        // -- trata as variaveis --//
        $pps = $this->config->item('pagination_limits');       
        $pp = ($v['pp'] == '') ? $pps[0] : $v['pp']; // por página
                
        $pag = $this->uri->to_array('pag');
        if($pag['pag'] == ''){
            $offset = 0;
        } else {
            $offset = ($pag['pag']-1) * $pp;
        }
        

        // opções de filtro
        $uri_filters = $this->set_posts_filters();

        $this->db->limit($pp, $offset);
        
        // ordenação
        if (isset($modulo['ordenavel']) && $modulo['ordenavel']) {
            $this->db->order_by('ordem');
        } else {
            $this->db->order_by('dt_ini desc, titulo');
        }

        $this->db->where('modulo_id', $v['co']);

        if ($tipo == 'grupo') {

            $this->db->where('grupo', 0); // busca grupos
            $this->db->where('tipo', 'conteudo');
        } else if ($tipo == 'tag') {

            $this->db->where('grupo', 0); // busca grupos
            $this->db->where('tipo', 'tag');
        } else {

            $this->db->where('grupo >', 0);
            $this->db->where('tipo', 'conteudo');
        }

        $this->db->where('lang', get_lang());
         $this->db->select('SQL_CALC_FOUND_ROWS *', false);
        $sql = $this->db->get('cms_conteudo');

//        mybug($this->db->last_query());
        
        // -- pega o Total de registros -- //
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $ttl_rows = $query->row()->Count;
        
        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/posts/index/co:' . $v['co'].$uri_filters);
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 11; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
     
        
        $saida = array('ttl_rows' => $ttl_rows,
            'rows' => $this->parse_lista_conteudos($sql->result_array(), $modulo));

        return $saida;
    }
    
    // ----------------------------------------------------------------------
   
    /**
     * Parseia $_POST e URI para saber se existem filtros ativos
     * Retorna URI para paginação.
     * 
     * @return string
     */
    private function set_posts_filters(){
        
        // define os campos que serão usados no filtro
        $campos_usados[] = array('campo' => 'titulo', 'type' => 'like');
        $campos_usados[] = array('campo' => 'grupo', 'type' => 'text');
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
     * Faz busca pelos grupos recursivamente
     * Não tem paginação
     */
    function lista_grupos($uriVars) {
        
        $this->db->where('grupo', 0);
        $this->db->where('rel', 0); // primeiro nível
        $this->db->where('tipo', 'conteudo');
        $this->db->where('lang', get_lang());
        $this->db->where('modulo_id', $uriVars['co']);
        $this->db->order_by('ordem');

        $sql = $this->db->get('cms_conteudo');

        $saida = array();
        foreach ($sql->result_array() as $row) {

            $row['level'] = 0;
            $sub = $this->_get_recursive_grupos($row['id'], 0);
            $row['sub'] = $sub; // false ou array            

            $saida[] = $this->_parse_grupo($row);
        }

        return $saida;
    }

    /**
     * Pesquisa recursivamente pelos grupos do módulo
     * @param <type> $grupo_id
     */
    function _get_recursive_grupos($grupo_id, $level = 0) {

        $this->db->where('rel', $grupo_id); // sub nível
        $this->db->where('grupo', 0);
        $this->db->order_by('ordem');
        $sql = $this->db->get('cms_conteudo');

        if ($sql->num_rows() == 0) {
            return false;
        }

        $saida = array();
        $level++;
        foreach ($sql->result_array() as $row) {

            $row['level'] = $level;
            $sub = $this->_get_recursive_grupos($row['id'], $level);
            $row['sub'] = $sub; // false ou array
            $saida[] = $this->_parse_grupo($row);
        }

        return $saida;
    }

    /**
     * Monta a hierarquia dos grupos e monta um combobox
     */
    function getGrupoComboHierarchy($uriVars) {

        $hierarchy = $this->lista_grupos($uriVars);

        $gruposFlated = $this->flatMultidimensionalArray($hierarchy, 'sub');

        // prepara dados para combobox
        $option = array();

        $combo = '<select name="grupos" class="input-combo " id="grupos">';

        // só apresenta opção se for um Grupo, se for conteúdo não
        if (substr($this->uri->segment(3), 0, 5) == 'grupo') {
            $combo .= '<option value="0">Nenhum</option>';
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

        $hierarchy = $this->lista_grupos(array('co' => $modulo_id));

        $gruposFlated = $this->flatMultidimensionalArray($hierarchy, 'sub');

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
            
            if($index == 'semana'){
                $saida['semana_html'] = $this->calendario_model->semanaHtml($val);
            }

            $saida[$index] = $val;
        }

        return $saida;
    }
    
    /**
     * Faz busca pelos grupos recursivamente
     * Não tem paginação
     */
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
//            $sub = $this->_get_recursive_grupos($row['id'], 0);
//            $row['sub'] = $sub; // false ou array            

            $saida[] = $this->_parse_grupo($row);
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

                )$row['status'] = 'ativo';
            else if ($row['status'] == 0

                )$row['status'] = 'inativo';
            else if ($row['status'] == 2

                )$row['status'] = 'editando';

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
            $oque = "Novo Grupo: <a href=\"" . cms_url('cms/posts/grupoEdita/co:' . $var['co'] . '/id:' . $this->db->insert_id()) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {

            $nick = $this->input->post('nick_edita');
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Grupo: <a href=\"" . cms_url('cms/posts/grupoEdita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }

        return $sql;
    }

    function conteudo_salva($var) {
        
//        dd($this->input->post());
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
        $scripts = $this->input->post('scripts');
        $prioridade = $this->input->post('prioridade');
//        $show = $this->input->post('show'); # não implementado

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
        
        
        

//        mybug($dados['rel']);
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            
            $dados['tipo'] = 'conteudo';
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['hr_ini'] = date("H:i:s");
            $dados['lang'] = get_lang();
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => '', 'nick' =>$dados['nick']), $grupo, $var['co']);
            $dados['dt_ini'] = date("Y-m-d");

            $sql = $this->db->insert('cms_conteudo', $dados);
            $esteid = $this->db->insert_id();
            
            
            
            // -- >> LOG << -- //
            $oque = "Novo Post: <a href=\"" . cms_url('cms/posts/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {

            // antes de salvar, salva última versão
            $this->cms_libs->save_revision($var['id']);
            
            $nick = $this->input->post('nick_edita');

            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => $var['id'], 'nick' =>$dados['nick']), $grupo, $var['co']);
            $dados['txtmulti'] = $this->paginas_model->concatenateMultiContents();
            $dados['scripts'] = $scripts;
            $dados['prioridade'] = $prioridade;
            
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Post: <a href=\"" . cms_url('cms/posts/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }

        // faz atualização das tags. precisa do ID, por isso está aqui
        $this->set_tag_conteudo($mytags, $var);
        
        // salva conteúdo meta
        $this->saveMetas($var['id']);
        
        // faz atualização dos metadados
        $this->cms_libs->set_metadados($var['id']);

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
        $oque = "Cópia de Post: <a href=\"" . cms_url('cms/posts/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
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

        // 1º) remove as tags anteriores relacionadas a este conteúdo
        $this->db->delete('cms_tag_conteudo', array('conteudo_id' => $id));

        if (count($listaTagIds) == 0 || $listaTagIds === false) {
            return false;
        }

        // remove itens duplicados
        $listaTagIds = array_unique($listaTagIds);


        // senão percorre a lista atualizando na tabela de relacionamentos
        foreach ($listaTagIds as $tagId) {

            $dados['conteudo_id'] = $id;
            $dados['tag_id'] = $tagId;

            // insere tag por tag
            $this->db->insert('cms_tag_conteudo', $dados);
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
            $oque = "Nova Tag: <a href=\"" . cms_url('cms/posts/tagEdita/co:' . $var['co'] . '/id:' . $this->db->insert_id()) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Tag: <a href=\"" . cms_url('cms/posts/tagEdita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }

        return $sql;
    }

    /**
     * Pega os dados na Library 'cms/cms_libs' e parseia os dados
     *
     * @param mixed $var
     * @return
     */
    function conteudo_dados($var) {
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
    function get_conteudo_tags($conteudoID) {


        // pesquisa as tags deste conteúdo e combina com a tabela cms_conteudo
        // para retornar os dados completos das tags
        $this->db->select('*');
//        $this->db->where('cms_tag_conteudo.status', 1);
        $this->db->where('conteudo_id', $conteudoID);
        $this->db->order_by('ordem');
        $this->db->from('cms_tag_conteudo');
        $this->db->join('cms_conteudo', 'cms_conteudo.id = cms_tag_conteudo.tag_id');
        $this->db->where('cms_conteudo.status', 1);

        $sql = $this->db->get();

        $tags = $sql->result_array();

        if ($sql->num_rows() == 0) {
            return false;
        }

        // Parseia dados para retornar as cores
        $saida = array();

        foreach ($tags as $tag) {


            $cores = $this->get_grupo_cores($tag['tags']);
            $tag['cor1'] = $cores['cor1'];
            $tag['cor2'] = $cores['cor2'];


            $saida[] = $tag;
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

        // se não existem tags, retorna vazio
        if (!$dados['modulo_tags']) {
            return '';
        }

        // monta a view
        $view = $this->load->view('cms/tags', $dados, true);

        return $view;
    }



}