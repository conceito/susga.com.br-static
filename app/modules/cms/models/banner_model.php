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
class Banner_model extends CI_Model {

    public $banner_array = array();


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
    function lista_banners($v, $modulo = array()) {
        // -- trata as variaveis --//        
        $pps = $this->config->item('pagination_limits');
        
        
        $pp = ($v['pp'] == '') ? $pps[0] : $v['pp']; // por página
                
        $pag = $this->uri->to_array('pag');
        if($pag['pag'] == ''){
            $offset = 0;
        } else {
            $offset = ($pag['pag']-1) * $pp;
        }
        
        $b = $v['b'];
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
            $stt = $v['stt'];
        }
        // se foi feita seleção com grupos
        if (strlen(trim($this->input->post('grupos'))) > 0) {
            $g = $this->input->post('grupos');
        } else {
            $g = ($v['g'] == '') ? 0 : $v['g'];
        }
        // pelas datas
        if (strlen(trim($this->input->post('dt1'))) > 0) {
            $dt1 = formaSQL($this->input->post('dt1'));
        } else {
            $dt1 = $v['dt1'];
        }
        if (strlen(trim($this->input->post('dt2'))) > 0) {
            $dt2 = formaSQL($this->input->post('dt2'));
        } else {
            $dt2 = $v['dt2'];
        }

        //------------------------------------------------
        // -- SQL básica com paginação -- //
        
        $this->db->from('cms_conteudo as conteudo');
        $this->db->select('conteudo.*, bn.views, bn.clicks, bn.limit, bn.target');
        $this->db->join('cms_banner as bn', 'conteudo.id = bn.conteudo_id');
        
        if ($dt1 != '' && $dt2 == '') {
            $this->db->where('conteudo.dt_ini', $dt1);
        } else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('conteudo.dt_ini >=', $dt1);
            $this->db->where('conteudo.dt_ini <=', $dt2);
        }
        if ($stt != '') {
            $this->db->where('conteudo.status', $stt);
        }

        if ($b != '') {
            $this->db->like('conteudo.titulo', $b);
            $this->db->or_like('conteudo.resumo', $b);
        }
        $this->db->limit($pp, $offset);

        // ordenação    ----------------

        if (isset($modulo['ordenavel']) && $modulo['ordenavel']) {
            $this->db->order_by('conteudo.grupo, conteudo.ordem');
        } else {
            $this->db->order_by('conteudo.dt_ini desc, conteudo.titulo');
        }

        $this->db->where('conteudo.modulo_id', $v['co']);

        

        if ($g == 0) {
            $this->db->where('conteudo.grupo !=', 0); // todos conteudos
        } else {
            $this->db->where('conteudo.grupo', $g); // conteudos do grupo
        }
        $this->db->where('conteudo.tipo', 'banner');
        

        $this->db->where('conteudo.lang', get_lang());

        $sql = $this->db->get();

        // -- pega o Total de registros --------------------------------------------------- //
        // -- pega o Total de registros -- //
        if ($dt1 != '' && $dt2 == '') {
            $this->db->where('dt_ini', $dt1);
        } else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('dt_ini >=', $dt1);
            $this->db->where('dt_ini <=', $dt2);
        }
        if ($stt != '') {
            $this->db->where('status', $stt);
        }
        if ($b != '') {
            $this->db->like('titulo', $b);
            $this->db->or_like('resumo', $b);
        }
        $this->db->where('modulo_id', $v['co']);
        
        if ($g == 0) {
            $this->db->where('grupo !=', 0); // todos conteudos
        } else {
            $this->db->where('grupo', $g); // conteudos do grupo
        }
        $this->db->where('tipo', 'banner');
        
        $this->db->where('lang', get_lang());
        $sql_ttl = $this->db->get('cms_conteudo');
        $ttl_rows = $sql_ttl->num_rows();
        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/banner/index/co:' . $v['co'] . '/pp:' . $pp . '/g:' . $g . '/dt1:' . $v['dt1'] . '/dt2:' . $v['dt2'] . '/b:' . $b . '/stt:' . $stt . '/');
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

            if ($index == 'tags') {
                $w = explode('x', $val);
                $saida['banner_width'] = $w[0];
                $saida['banner_height'] = $w[1];
            }
            
            if($index == 'txt'){
                
                if($val == 'rand') $saida['ordem'] = 'Aleatória';
                if($val == 'ordem') $saida['ordem'] = 'Ordenada';
                
            }

            $saida[$index] = $val;
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
                $cores = $this->get_grupo_cores($row['tags']);
                $row['grupoCor1'] = $cores['cor1'];
                $row['grupoCor2'] = $cores['cor2'];
            }
            // se for conteúdo
            else {

                // pega grupo com seus parentes, se houver
                $grupoParents = $this->getGrupoParents($row['grupo'], $modulo['id']);                               
                $row['grupoParents'] = $grupoParents;
                
                // valor do limite de exibição
                if($row['limit'] == 0) $row['limit'] = '&#8734;';
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
        $titulo = $this->input->post('titulo');
        $nick = url_title($titulo);
        $resumo = $this->input->post('resumo');
        $banner_width = $this->input->post('banner_width');
        $banner_height = $this->input->post('banner_height');
        $banners_type = $this->input->post('banners_type');// not necessary
        $ordem = $this->input->post('ordem');
        
        

        $dados['titulo'] = $titulo;
        $dados['resumo'] = $resumo;
        $dados['tags'] = $banner_width . 'x' . $banner_height;
        $dados['tipo'] = 'conteudo';
        $dados['rel'] = 0;
        $dados['txt'] = $ordem;// a ordenação fica no campo txt

        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['dt_ini'] = date("Y-m-d");
            $dados['hr_ini'] = date("H:i:s");
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, date("Y-m-d"));
            $dados['grupo'] = 0;
            $dados['lang'] = get_lang();
            $dados['status'] = 1;
            $dados['modulo_id'] = $var['co'];
            
//            mybug($dados);

            $sql = $this->db->insert('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Novo Grupo: <a href=\"" . cms_url('cms/banner/grupoEdita/co:' . $var['co'] . '/id:' . $this->db->insert_id()) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {

            $nick = $this->input->post('nick_edita');
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, date("Y-m-d"));

            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Grupo: <a href=\"" . cms_url('cms/banner/grupoEdita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }

        return $sql;
    }

    
    /**
     * Salva dados do banner
     * 
     * via $_POST
     *  'conteudo_id' => string '' (length=0)
        'grupos' => string '41' (length=2)
        'titulo' => string 'descrição' (length=11)
        'flag' => string 'via-link' (length=8)
        'txt' => string 'www.linkdo banner.com' (length=21)
        'rel' => string '' (length=0)
        'target' => string '_blank' (length=6)
        'hidFileID' => string 'alteracao_imagem2.jpg' (length=21)
        'arquivos' => string '...' (length=3)
        'dt1' => string '11/07/2012' (length=10)
        'dt2' => string '12/07/2012' (length=10)
        'limit' => string '?' (length=1)
     * 
     * @param type $var
     * @return type 
     */
    function conteudo_salva($var) {
        // - salva os dados do menu principal Raiz
        $grupo = $this->input->post('grupos');
        $rel = $this->input->post('rel');// no caso de conteúdo tras o ID
        $titulo = $this->input->post('titulo');// descrição
        $txtmulti = $this->input->post('txtmulti');// texto secundário
        $dt_ini = $this->input->post('dt1');
        $dt_fim = $this->input->post('dt2');
        
        $txt = $this->input->post('txt');// link do banner
        $flag = $this->input->post('flag'); // via-link | via-conteudo
        $arquivoNovo = ($this->input->post('hidFileID') == '') ? false : $this->input->post('hidFileID');
        $arquivoEdit = $this->input->post('arquivo');
        $limit = $this->input->post('limit');
        $target = $this->input->post('target');

       
        
        $dados['titulo'] = $titulo;
        $dados['txtmulti'] = $txtmulti;
        
        $dados['dt_ini'] = ($dt_ini) ? formaSQL($dt_ini) : date("Y-m-d");
        $dados['dt_fim'] = ($dt_fim || $dt_fim == '00/00/0000') ? formaSQL($dt_fim) : "2200-12-30";
        $dados['grupo'] = $grupo;
        $dados['extra'] = $flag;
        $dados['tipo'] = 'banner';
        
        $dados['txt'] = $txt;
        $dados['rel'] = $rel;
        
        $banner['target'] = $target;
        $banner['limit'] = $limit;

//        mybug($this->input->post());
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['hr_ini'] = date("H:i:s");
            $dados['lang'] = get_lang();
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], url_title($titulo), date("Y-m-d"));
            $dados['modulo_id'] = $var['co'];
            $dados['resumo'] = prepend_upload_file($arquivoNovo);
            $dados['status'] = 1;
            $sql = $this->db->insert('cms_conteudo', $dados);
            $esteid = $this->db->insert_id();
            
            // faz atualização da tabela complementar cms_banner
        
            $banner['conteudo_id'] = $esteid;
            $this->db->insert('cms_banner', $banner);
            
            
            // -- >> LOG << -- //
            $oque = "Novo Banner: <a href=\"" . cms_url('cms/banner/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {

            $nick = $this->input->post('nick_edita');
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, date("Y-m-d"));
            $dados['status'] = $this->input->post('status');
            $dados['resumo'] = ($arquivoNovo) ? prepend_upload_file($arquivoNovo) : $arquivoEdit;
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            
            // Se está submetendo um novo banner remove o antigo
            if($arquivoNovo){
                $this->removeOldBannersFromBannerId($var['id']);
            }
            
            // faz atualização da tabela complementar cms_banner        

            $this->db->where('conteudo_id', $var['id']);
            $this->db->update('cms_banner', $banner);
            
            
            // -- >> LOG << -- //
            $oque = "Atualizou Banner: <a href=\"" . cms_url('cms/banner/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }

        
        

        return $esteid;
    }
    
    /**
     * Usa o ID do conteúdo para remover e apagar os arquivos relacionados a ele.
     * Este método é invocado antes da gravação do novo banner .
     * 
     * @param type $banner_id 
     */
    function removeOldBannersFromBannerId($banner_id){
        
        // pega infos dos banners
        $this->db->where('conteudo_id', $banner_id);
        $result = $this->db->get('cms_arquivos');
        
        $banners = $result->result_array();
       
        // loop e deleta
        foreach($banners as $arq_array){
            
           $this->cms_libs->deleta_arquivo($arq_array); 
            
        }        
        
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
        $dados['tags'] = $cor1 . '|' . $cor2;
        $dados['tipo'] = 'tag';

        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['dt_ini'] = date("Y-m-d");
            $dados['hr_ini'] = date("H:i:s");
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, date("Y-m-d"));
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
     * Pega os dados na Library 'cms/cms_libs' e parseia os dados
     *
     * @param mixed $var
     * @return
     */
    function conteudo_dados($var) {
        
        $this->db->from('cms_conteudo as conteudo');
        $this->db->select('conteudo.*, banner.target, banner.views, banner.clicks, banner.limit');
        $this->db->join('cms_banner as banner', 'conteudo.id = banner.conteudo_id');
        $this->db->where('conteudo.id', $var['id']);
        
        $row = $this->db->get();        
        
        if ($row->num_rows() == 0){
            return false;
        }
        
        // percorre array
        $saida = array();
        foreach ($row->row_array() as $chv => $vlr) {
            // data
            if ($chv == 'dt_ini'){
                $saida['dt1'] = formaPadrao($vlr);
            }
            if ($chv == 'dt_fim'){
                $saida['dt2'] = formaPadrao($vlr);
            }
                
            if($chv == "limit"){
                if($vlr == 0) {
                    $vlr = '&#8734;';
                }
            }
            
            
            
            // coloca no array
            $saida[$chv] = $vlr;
        }
        
//        mybug($saida);

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
     * Gera a imagem miniatura, ou ícone do banner em flash
     * @param type $banner_array 
     */    
    function getBannerThumb($banner_array){
        
        $banner = $banner_array['resumo'];
        $ext = explode('.', $banner);
        $ext = strtolower($ext[count($ext)-1]);
        
        // arqs path
        $path = cms_url().$this->config->item('upl_arqs').'/';
        
        if($ext == 'swf'){
            $return = '<a href="'.$path.$banner.'" class="nyroModal" title="Ampliar" ><img src="'.cms_url().'ci_itens/img/swf.png" class="banner-thumb" /></a>';
        } 
        // tem que ser imagem
        else {
            $return = '<a href="'.$path.$banner.'" class="nyroModal" title="Ampliar" ><img src="'.$path.$banner.'"  class="banner-thumb" /></a>';
        }
        
//        mybug($path);
        return $return;
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

?>