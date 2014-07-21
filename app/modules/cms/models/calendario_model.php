<?php

/**
 *
 * @version $Id$
 * @copyright 2010
 */
class Calendario_model extends MY_Model {


    /**
     * Meta dados registrados
     * @var type
     */
    protected $_metas = array(
        array('meta_key' => 'priority', 'meta_type' => '', 'meta_value' => ''),
    );

    function __construct()
    {
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
    function lista_conteudos($v, $tipo = 'conteudo', $modulo = array())
    {
        // -- trata as variaveis --//
        $pps = $this->config->item('pagination_limits');
        $pp = ($v['pp'] == '') ? $pps[0] : $v['pp']; // por página

        $pag = $this->uri->to_array('pag');
        if ($pag['pag'] == '')
        {
            $offset = 0;
        }
        else
        {
            $offset = ($pag['pag'] - 1) * $pp;
        }


        // opções de filtro
        $uri_filters = $this->set_posts_filters();

        $this->db->limit($pp, $offset);
        // ordenação
        if (isset($modulo['ordenavel']) && $modulo['ordenavel'])
        {
            $this->db->order_by('ordem');
        }
        else
        {
            $this->db->order_by('dt_ini desc, titulo');
        }

        $this->db->where('modulo_id', $v['co']);

        if ($tipo == 'grupo')
        {

            $this->db->where('grupo', 0); // busca grupos
            $this->db->where('tipo', 'conteudo');
        }
        else if ($tipo == 'tag')
        {

            $this->db->where('grupo', 0); // busca grupos
            $this->db->where('tipo', 'tag');
        }
        else
        {

            $this->db->where('grupo >', 0);
            $this->db->where('tipo', 'conteudo');
        }

        $this->db->where('grupo >', 0);
        $this->db->where('lang', get_lang());
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
        $sql = $this->db->get('cms_conteudo');

        // -- pega o Total de registros -- //

        $query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $ttl_rows = $query->row()->Count;

        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/calendario/index/co:' . $v['co'] . $uri_filters);
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
    private function set_posts_filters()
    {

        // define os campos que serão usados no filtro
        $campos_usados[] = array('campo' => 'titulo', 'type' => 'like');
        $campos_usados[] = array('campo' => 'grupo', 'type' => 'text');
        $campos_usados[] = array('campo' => 'destaque', 'type' => 'int');
        $campos_usados[] = array('campo' => 'dt_ini', 'type' => 'date');
        $campos_usados[] = array('campo' => 'dt_fim', 'type' => 'date');
        $campos_usados[] = array('campo' => 'status', 'type' => 'int');
        $campos_valorados = array();

//        mybug($this->input->post());
        // uri de filtros para paginação
        $return = '';

        // verifica se veio pelo POST ou URI
        foreach ($campos_usados as $row)
        {

            $campo = $row['campo'];
            $type = $row['type'];
            $uri = $this->uri->to_array('filter_' . $campo);

            // tem post?
            if (isset($_POST['filter_' . $campo]))
            {
                $valor = $_POST['filter_' . $campo];
            }
            // tem na URI
            else if ($uri['filter_' . $campo] != '')
            {
                $valor = $uri['filter_' . $campo];
            }
            else
            {
                $valor = '';
            }

            // acrescenta o valor
            $row['valor'] = $valor;
            $campos_valorados[] = $row;
        }

        // faz pesquisa
        foreach ($campos_valorados as $row)
        {

            if ($row['valor'] != '')
            {

                $campo = $row['campo'];
                $type = $row['type'];
                $valor = $row['valor'];

                // se for data
                if ($type == 'date' && strlen($valor) == 10)
                {
                    $valor = formaSQL($valor);
                }

                if ($type == 'like')
                {
                    $this->db->like('' . $campo, $valor);
                }
                else
                {
                    $this->db->where('' . $campo, $valor);
                }

                // incrementa uri
                $return .= '/filter_' . $campo . ':' . $valor;
            }
        }


//        mybug($return);
        return $return;
    }

    /**
     * Prepara pesquisa de item de menu
     *
     * @param mixed $array
     * @return
     */
    function parse_lista_conteudos($array, $modulo = array())
    {
        if (count($array) == 0)
            return false;
        // percorre array
        $saida = array();
        foreach ($array as $row)
        {
            if ($row['status'] == 1)
                $row['status'] = 'ativo';
            else if ($row['status'] == 0)
                $row['status'] = 'inativo';
            else if ($row['status'] == 2)
                $row['status'] = 'editando';

            if ($row['grupo'] == 0)
            {
                $row['grupo'] = 'Grupo';
            }
            else
            {

                // pega grupo com seus parentes, se houver
                $grupoParents = $this->paginas_model->getGrupoParents($row['grupo'], $modulo['id']);
                $row['grupoParents'] = $grupoParents;
            }
            // se existe inscrição pesquisa a quantidade
            if (isset($modulo['inscricao']) && $modulo['inscricao'] == 1)
            {
                $this->load->library('inscricao/inscricao');
                $row['insc_ttl'] = $this->inscricao->countByStatus($row['id'], 1);

                // novos
                $row['insc_new'] = $this->inscricao->countByStatus($row['id'], 2);
            }

            $saida[] = $row;
        }

        return $saida;
    }

    /**
     * Insere/atualiza dados no módulo calendário.
     * 
     * @param array $var
     * @return int 
     */
    function conteudo_salva($var)
    {
        // - salva os dados do menu principal Raiz
        $grupo = $this->input->post('grupos');
        $rel = $this->input->post('rel');
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $data1 = trim($this->input->post('dt1'));
        $data2 = trim($this->input->post('dt2'));
        $hora1 = trim($this->input->post('hora1'));
        $hora2 = trim($this->input->post('hora2'));
        $status = $this->input->post('status');
        $resumo = trim($this->input->post('resumo'));
        $tags = trim($this->input->post('tags'));
        $txt = trim($this->input->post('txt'));
        $txtmulti = trim($this->input->post('txtmulti'));
        $mytags = $this->input->post('mytags');
        $scripts = $this->input->post('scripts');
        $semana = $this->montaStringSemana($this->input->post('seg'), $this->input->post('ter'), $this->input->post('qua'), $this->input->post('qui'), $this->input->post('sex'), $this->input->post('sab'), $this->input->post('dom'));

        $dados['titulo'] = $titulo;
        $dados['resumo'] = $resumo;
        $dados['dt_ini'] = formaSQL($data1);
        $dados['dt_fim'] = formaSQL($data2);
        $dados['hr_ini'] = $hora1;
        $dados['hr_fim'] = $hora2;
        $dados['semana'] = $semana;
        $dados['grupo'] = $grupo;
        $dados['modulo_id'] = $var['co'];
        $dados['tags'] = $tags;
        $dados['status'] = $status;
        $dados['txt'] = campo_texto_utf8($txt);
        $dados['rel'] = prep_rel_to_sql($rel);
        $dados['atualizado'] = date("Y-m-d H:i:s");



        // --  NOVO ITEM  -- //
        if ($var['id'] == '')
        {

            $dados['tipo'] = 'conteudo';
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['lang'] = get_lang();
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => '', 'nick' => $dados['nick']), $grupo, $var['co']);
            $dados['txt'] = $txt;
            $dados['txtmulti'] = $txtmulti;

            $sql = $this->db->insert('cms_conteudo', $dados);
            $esteid = $this->db->insert_id();
            // -- >> LOG << -- //
            $oque = "Novo Evento: <a href=\"" . cms_url('cms/calendario/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else
        {

            // antes de salvar, salva última versão
            $this->cms_libs->save_revision($var['id']);

            $nick = $this->input->post('nick_edita');

            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => $var['id'], 'nick' => $dados['nick']), $grupo, $var['co']);
            $dados['txtmulti'] = $this->paginas_model->concatenateMultiContents();
            $dados['scripts'] = $scripts;

            // salva dados Se existir sobre o preço e descontos
            $this->save_preco_desconto($var);

            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Evento: <a href=\"" . cms_url('cms/calendario/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }

        // faz atualização das tags. precisa do ID, por isso está aqui
        $this->paginas_model->set_tag_conteudo($mytags, $var);

        // salva conteúdo meta
        $this->saveMetas($var['id']);


        return $esteid;
    }

    // -------------------------------------------------------------------------

    public function conteudo_salva_copia($var)
    {

        $grupo = $this->input->post('grupos');
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $data = trim($this->input->post('dt1'));
        $status = $this->input->post('status');

        $conteudo = $this->conteudo_dados($var);



        $dados['titulo'] = $titulo;
        $dados['ordem'] = $conteudo['ordem'];
        $dados['resumo'] = $conteudo['resumo'];
        $dados['dt_ini'] = formaSQL($data);
        $dados['tags'] = $conteudo['tags'];
        $dados['grupo'] = $grupo;
        $dados['modulo_id'] = $conteudo['modulo_id'];
        $dados['tipo'] = $conteudo['tipo'];
        $dados['status'] = $status;
        $dados['txt'] = $conteudo['txt'];
        $dados['rel'] = $conteudo['rel'];
        $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
        $dados['hr_ini'] = date("H:i:s");
        $dados['lang'] = $conteudo['lang'];
        $dados['txtmulti'] = $conteudo['txtmulti'];
        $dados['show'] = $conteudo['show'];
        $dados['galeria'] = $conteudo['galeria'];
        $dados['destaque'] = $conteudo['destaque'];
        $dados['img'] = $conteudo['img'];
        $dados['semana'] = $conteudo['semana'];
        $dados['extra'] = $conteudo['extra'];
        $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);

        $sql = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();

        // copia preços e descontos
        $this->copy_preco_desconto($conteudo['id'], $esteid);

        // -- >> LOG << -- //
        $oque = "Cópia de Evento: <a href=\"" . cms_url('cms/calendario/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        $new_tags = array();
        $t = $this->paginas_model->get_conteudo_tags($var['id']);


        if (count($t) > 0)
        {
            foreach ($t as $tag)
            {
                $new_tags[] = $tag['id'];
            }

            // faz atualização das tags. precisa do ID, por isso está aqui
            $this->paginas_model->set_tag_conteudo($new_tags, $esteid);
        }

        return $esteid;
    }

    /**
     * Faz todo tratamento do $_POST para atualizar os preços e descontos do conteúdo.
     * 
     * @param type $vars 
     */
    public function save_preco_desconto($vars)
    {
        $co = $vars['co']; // módulo ID
        $id = $vars['id']; // conteudo ID
        // arrays finais para atualização
        $precos_update = array();
        $precos_insert = array();
        $precos_remove = explode(',', trim($this->input->post('precos_remove'), ','));
        $cupons_remove = explode(',', trim($this->input->post('cupons_remove'), ','));
        $precos_remove = array_merge($precos_remove, $cupons_remove);
        $precos_remove = array_unique($precos_remove);


        // percorre POST - faz tratamentos
        foreach ($_POST as $c => $v)
        {

            // salva os preços ----------------------------------------------
            if (substr($c, 0, 10) == 'preco_opt_')
            {
                $preco_id = $v[0];
                $preco_valor = moneyFormat($v[1]);
                $preco_regra = $v[2];
                $preco_condicao = formaSQL($v[3]);

                // validação
                if (strlen($v[1]) < 2 || strlen($v[3]) < 10)
                {
                    continue;
                }

                // para atualizar
                if ($preco_id == '')
                {
                    $precos_insert[] = array(
                        'id' => $preco_id,
                        'conteudo_id' => $id,
                        'valor' => $preco_valor,
                        'regra' => $preco_regra,
                        'data' => $preco_condicao,
                        'tipo' => 'preco'
                    );
                }
                // para inserir
                else
                {
                    $precos_update[] = array(
                        'id' => $preco_id,
                        'conteudo_id' => $id,
                        'valor' => $preco_valor,
                        'regra' => $preco_regra,
                        'data' => $preco_condicao,
                        'tipo' => 'preco'
                    );
                }
            }

            // salva os cupons ---------------------------------------------
            if (substr($c, 0, 10) == 'cupom_opt_')
            {
                $cupom_id = $v[0];
                // o valor pode ser dinheiro, ou percentual
                $cupom_valor = ($v[2] == '%') ? $v[1] : moneyFormat($v[1]);
                $cupom_regra = $v[2];
                $cupom_condicao = strtoupper(url_title($v[3]));

                // validação
                if (strlen($v[1]) == 0 || strlen($cupom_condicao) < 2)
                {
                    continue;
                }

                // para atualizar
                if ($cupom_id == '')
                {
                    $precos_insert[] = array(
                        'id' => $cupom_id,
                        'conteudo_id' => $id,
                        'valor' => $cupom_valor,
                        'regra' => $cupom_regra,
                        'verificador' => $cupom_condicao,
                        'tipo' => 'cupom'
                    );
                }
                // para inserir
                else
                {
                    $precos_update[] = array(
                        'id' => $cupom_id,
                        'conteudo_id' => $id,
                        'valor' => $cupom_valor,
                        'regra' => $cupom_regra,
                        'verificador' => $cupom_condicao,
                        'tipo' => 'cupom'
                    );
                }
            }
        }


        // faz inserções
        if (count($precos_insert) > 0)
        {
            foreach ($precos_insert as $row)
            {
                $this->db->insert('cms_precos', $row);
            }
        }
        // faz updates
        if (count($precos_update) > 0)
        {
            foreach ($precos_update as $row)
            {

                $this_id = $row['id'];
                unset($row['id']);

                $this->db->where('id', $this_id);
                $this->db->update('cms_precos', $row);
            }
        }

        // faz deletes
        foreach ($precos_remove as $row)
        {
            if ($row != '')
            {
                $this->db->delete('cms_precos', array('id' => $row));
            }
        }
    }

    // -------------------------------------------------------------------------
    /**
     * Recupera dados do conteúdo original e salva com ID de destino.
     * 
     * @param       int     $origin_id
     * @param       int     $destiny_id
     * @return      boolean
     */
    public function copy_preco_desconto($origin_id, $destiny_id)
    {
        // carrega dados
        $this->db->where('conteudo_id', $origin_id);
        $this->db->order_by('id');
        $result = $this->db->get('cms_precos');

        if ($result->num_rows() == 0)
        {
            return false;
        }

        foreach ($result->result_array() as $row)
        {
            unset($row['id']);
            $row['conteudo_id'] = $destiny_id;
            $this->db->insert('cms_precos', $row);
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Retorna um array multidimensional com os:
     * precos => array(),
     * cupons => array()
     * 
     * @param type $conteudo_id
     * @return boolean 
     */
    public function get_preco_desconto($conteudo_id)
    {

        // carrega dados
        $this->db->where('conteudo_id', $conteudo_id);
        $this->db->order_by('id');
        $result = $this->db->get('cms_precos');

        $precos = array();
        $cupons = array();
        $descontos = array(); // futuramente

        if ($result->num_rows() > 0)
        {

            foreach ($result->result_array() as $row)
            {

                if ($row['tipo'] == 'preco')
                {
                    $precos[] = array(
                        'id' => $row['id'],
                        'valor' => $row['valor'],
                        'regra' => $row['regra'],
                        'data' => formaPadrao($row['data'])
                    );
                }
                else if ($row['tipo'] == 'cupom')
                {
                    $cupons[] = array(
                        'id' => $row['id'],
                        'valor' => $row['valor'],
                        'regra' => $row['regra'],
                        'verificador' => $row['verificador']
                    );
                }
            }
        }

        // insere os últimos em branco
        $precos[] = array(
            'id' => '',
            'valor' => '',
            'regra' => '',
            'data' => ''
        );
        $cupons[] = array(
            'id' => '',
            'valor' => '',
            'regra' => '',
            'verificador' => ''
        );

        return array(
            'precos' => $precos,
            'cupons' => $cupons
        );
    }

    /**
     * Concatena as variáveis para string que representa os dias da semana.
     * 
     * @param type $seg
     * @param type $ter
     * @param type $qua
     * @param type $qui
     * @param type $sex
     * @param type $sab
     * @param type $dom
     * @return type 
     */
    function montaStringSemana($seg, $ter, $qua, $qui, $sex, $sab, $dom)
    {
        $saida = '';
        $saida .= ($seg == '1') ? '1' : '0';
        $saida .= ($ter == '1') ? '1' : '0';
        $saida .= ($qua == '1') ? '1' : '0';
        $saida .= ($qui == '1') ? '1' : '0';
        $saida .= ($sex == '1') ? '1' : '0';
        $saida .= ($sab == '1') ? '1' : '0';
        $saida .= ($dom == '1') ? '1' : '0';

        return $saida;
    }

    // -------------------------------------------------------------------------

    /**
     * Monta checkbox dos dias da semana.
     *
     * @param string $semana
     * @return
     */
    function semanaCheckBox($semana = '0000000')
    {
        $semana = (strlen($semana) < 2) ? '0000000' : $semana;
        $saida = '';
        $sem = array('seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom');
        for ($x = 0; $x < 7; $x++)
        {
            $valor = $semana[$x];
            $saida .= '<label class="" for="' . $sem[$x] . '">' . ucfirst($sem[$x]) . '</label>' . form_checkbox(
                            array(
                                'name' => $sem[$x],
                                'id' => $sem[$x],
                                'value' => '1',
                                'checked' => $valor,
                                'style' => ''
            ));
        }
        return $saida;
    }

    // -------------------------------------------------------------------------

    /**
     * Monta estrutura HTML para exibir dias da semana.
     * @param string $semana
     * @param string $open
     * @param string $close
     * @return string
     */
    function semanaHtml($semana = '0000000', $open = '<span class="week-day">', $close = '</span>')
    {
        if (strlen($semana) != 7)
        {
            return FALSE;
        }
        $saida = '<div class="weeks">';
        $sem = array('seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom');
        for ($x = 0; $x < 7; $x++)
        {
            $valor = $semana[$x];

            if ($valor == 1)
            {
                $saida .= $open . ' ' . ucfirst($sem[$x]) . ' ' . $close;
            }
        }
        $saida .= '</div>';
        return $saida;
    }

    /**
     * Pega os dados na Library e parseia os dados
     *
     * @param mixed $var
     * @return array|bool
     */
    function conteudo_dados($var)
    {
        $dd = $this->cms_libs->conteudo_dados($var);
        if (!$dd)
            return false;

        // percorre array
        $saida = array();
        foreach ($dd as $chv => $vlr)
        {
            // data
            if ($chv == 'dt_ini')
                $saida['dt1'] = formaPadrao($vlr);
            if ($chv == 'dt_fim')
                $saida['dt2'] = formaPadrao($vlr);
            // quantidade de imagens na galeria
            if ($chv == 'galeria')
            {
                if (strlen($vlr) == 0)
                {
                    $saida['quantGal'] = 0;
                }
                else
                {
                    $array = explode('|', $vlr);
                    $saida['quantGal'] = count($array);
                }
            }
            // trata horas
            if ($chv == 'hr_ini')
                $saida['hora1'] = substr($vlr, 0, 5);
            if ($chv == 'hr_fim')
                $saida['hora2'] = substr($vlr, 0, 5);
            // checkboxes dias da semana
            if ($chv == 'semana')
            {
                $saida['cbSemana'] = $this->semanaCheckBox($vlr);
            }
            if ($chv == 'id')
            {
                // pega grupo com seus parentes, se houver
                $grupoParents = $this->paginas_model->getGrupoParents($dd['grupo'], $dd['modulo_id']);
                $saida['grupoParents'] = $grupoParents;
            }
            // coloca no array
            $saida[$chv] = $vlr;
        }
//        mybug($saida);

        return $saida;
    }

    /**
     * MOnta o array de eventos do Mes e Ano
     *
     * @param integer $y
     * @param integer $m
     * @return
     */
    function eventos_calendario($y = 0, $m = 0, $uri = 'cms/calendario/mensal/data:')
    {
        // se não existe referencia pega o me atual
        if ($y == 0)
            $y = date("Y");
        if ($m == 0)
            $m = date("m");
        // busca eventos
        $dt_inicial = $y . '-' . $m . '-01';
        $dt_final = $y . '-' . $m . '-31';
        $this->db->where('dt_ini >=', $dt_inicial);
        $this->db->where('dt_ini <=', $dt_final);
        $this->db->where('modulo_id', 21);
        $this->db->where('grupo !=', 0);
        $this->db->where('status', 1);
        $sql = $this->db->get('cms_conteudo');
        $saida = array();
        foreach ($sql->result_array() as $eve)
        {
            $dt_ini = $eve['dt_ini'];
            $titulo = $eve['titulo'];
            $id = $eve['id'];
            // o DIA
            $dia = substr($dt_ini, -2);
            $dia = (substr($dia, 0, 1) == 0) ? substr($dia, -1) : $dia; // retira o ZERO
            $link = cms_url($uri . $dt_ini);

            $saida[$dia] = $link;
        }
        // echo '<pre>';
        // var_dump($saida);
        // exit;
        return $saida;
    }

    function eventos_do_dia($data)
    {
        $this->uri = $this->uri->to_array(array('co'));
        $modulo['id'] = $this->uri['co'];

        $this->db->where('dt_ini', $data);
        $this->db->where('status', 1);
        $this->db->where('modulo_id', 21);
        $this->db->where('grupo !=', 0);
        $sql = $this->db->get('cms_conteudo');
        // parseia eventos
        $saida = array();
        $saida = $this->parse_lista_conteudos($sql->result_array(), $modulo);
//        echo '<pre>';
//         var_dump($saida);
//         exit;


        return $saida;
    }

    // -------------------------------------------------------------------------
    /**
     * Todos os inscritos do conteúdo
     * @param int $id_cont
     * @param int $status
     * @return boolean|array
     */
    function inscritos_dados($id_cont, $status = '')
    {
        $this->db->where('conteudo_id', $id_cont);
        $this->db->order_by('data desc, hora desc, rel');
//        $this->db->order_by('hora desc');
        if (strlen($status) > 0)
            $this->db->where('status', $status);
        $sql = $this->db->get('cms_inscritos');


        if ($sql->num_rows())
        {
            return $this->parse_inscritos_dados($sql->result_array());
        }
        else
        {
            return false;
        }
    }

    // -------------------------------------------------------------------------
    /**
     * Parseia array para exibição no CMS.
     * 
     * @param array $array
     * @return array
     */
    function parse_inscritos_dados($array = array())
    {
        if (count($array) == 0)
            return false;

        $this->load->model('cms/subscriptions_options_model', 'subs');
        
        // percorre array
        $saida = array();
        $last_status = false;
        foreach ($array as $row)
        {

            // os dependentes não exibem status
            if ($row['rel'] == 0)
            {
                if ($row['status'] == 1)
                {
                    $row['status'] = 'ativo';
                }
                else if ($row['status'] == 0)
                {
                    $row['status'] = 'inativo';
                }
                else if ($row['status'] == 2)
                {
                    $row['status'] = 'editando';
                }
                $last_status = $row['status'];
            }
            else
            {
                $row['status'] = false;
            }
            // dados dos usuários
            if ($row['user_id'])
            {
                //$row['user'] = $this->usuarios_model->usuario_dados($row['user_id']);
                $row['user'] = $this->cms_libs->conteudo_dados($row['user_id'], 'cms_usuarios');
            }

            $options = $this->subs->getAnswersFromUserSubscription($row['id']);
            if($options)
            {
                $row['subscription_options'] = $options;
            }

            // resgata o comprovante
            $uri = $this->uri->to_array(array('co'));
            $row['comprovante_url'] = 'cms/calendario/extrato/co:' . $uri['co'] . '/i:' . $row['id'];

            $saida[] = $row;
        }



//        mybug($saida);

        return $saida;
    }

    // -------------------------------------------------------------------------
    /**
     * Recebe as variáveis:
     * $vars = array(
     *      'co' => int,
     *      'i' => int
     * )
     * e gera três arrays para extrato.
     */
    public function get_full_extrato($vars)
    {

        $this->load->library('cms_extrato');
        $this->load->library('cms_conteudo');

        $modulo_id = $vars['co'];
        $inscricao_id = $vars['i'];

        // pedido detalhes
        $inscricao = $this->get_inscricao_dados($inscricao_id);
        $quantidade = ($inscricao['dependentes']) ? count($inscricao['dependentes']) + 1 : 1;

        // dados do conteúdo
//        $conteudo = $this->conteudo_dados($inscricao['conteudo_id']);
        $this->cms_conteudo->set_page($inscricao['conteudo_id']);
        $conteudo = $this->cms_conteudo->get_page();
//        $conteudo['preco_final'] = $this->cms_conteudo->preco_final();
        // extrato

        $all_extrts = $this->get_all_extrats_from_insc($inscricao_id);
        if (!$all_extrts)
        {
            return $all_extrts;
        }
        // faz looping pelos extratos
        $pedido = array();
        foreach ($all_extrts as $row)
        {

            $id = $row['id'];

            // dados do conteúdo
            $row['titulo'] = $conteudo['titulo'];

            // total de usuários
            $row['quantidade'] = $quantidade;

            // incrementa com o histórico
            $row['historico'] = $this->get_extrato_historico($id);

            // comprovante anexo
            $row['comprovante'] = $this->get_comprovante($row['comprovante']);

            $pedido[$id] = $row;
        }
//        $extrt = $this->cms_extrato->get($);
//             mybug($conteudo);

        return $pedido;
    }

    // -------------------------------------------------------------------------
    /**
     * Retorna os dados de cms_extrato de uma inscrição.
     * @param int $inscricao_id
     * @return array
     */
    private function get_all_extrats_from_insc($inscricao_id)
    {

        $result = $this->db->where('inscricao_id', $inscricao_id)
                ->order_by('id')
                ->get('cms_extratos');

        if ($result->num_rows() == 0)
        {
            return FALSE;
        }
        else
        {
            return $result->result_array();
        }
    }

    // -----------------------------------------------------------------------
    /**
     * Retorna os dados do arquivo anexo.
     * 
     * @param       int         $arquivo_id
     * @return      array
     */
    public function get_comprovante($arquivo_id)
    {

        $result = $this->db->where('id', $arquivo_id)
                ->get('cms_arquivos');

        if ($result->num_rows() == 0)
        {
            return FALSE;
        }
        else
        {
            return $result->row_array();
        }
    }

    // -------------------------------------------------------------------------
    /**
     * Retorna o histórico do extrato.
     * @param type $id
     * @return boolean
     */
    public function get_extrato_historico($id)
    {
        $result = $this->db->where('extrato_id', $id)
                ->order_by('id')
                ->get('cms_extrat_hist');

        if ($result->num_rows() == 0)
        {
            return FALSE;
        }
        else
        {
            return $result->result_array();
        }
    }

    // -------------------------------------------------------------------------
    public function get_inscricao_dados($id)
    {
//        
//        $this->db->from('cms_inscritos as principal');
//        $this->db->join('cms_inscritos as dependente', 'dependente.rel = principal.id');
//        $this->db->select('principal.*, dependente.user_id as ');

        $result = $this->db->where('id', $id)
                ->get('cms_inscritos');

        // parse para adicionar dependentes
        $dependentes = $this->db->where('rel', $id)
                ->select('user_id')
                ->get('cms_inscritos');

        if ($result->num_rows() == 0)
        {
            return FALSE;
        }
        else
        {

            $insc = $result->row_array();
            if ($dependentes->num_rows() == 0)
            {
                $insc['dependentes'] = false;
            }
            else
            {
                $insc['dependentes'] = $dependentes->result_array();
            }


            return $insc;
        }
    }

}

?>