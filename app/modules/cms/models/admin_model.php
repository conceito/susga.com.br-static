<?php

/**
 *
 * @version $Id$
 * @copyright 2009
 */
class Admin_model extends CI_Model {

    
    /**
     * Campos de configurações preferenciais do site
     * @var array
     */
    public $prefs = array(
            'header_txt',
            'footer_txt'
        );
    
    
    function __construct() {
        parent::__construct();
    }

    function menus_raiz($v) {
        // -- trata as variaveis --//
        $pps = $this->config->item('pagination_limits');
        $offset = ($v['offset'] == '') ? 0 : $v['offset'];
        $pp = ($v['pp'] == '') ? $pps[0] : $v['pp']; // por página
        $b = $v['b'];
        // se foi feita uma busca
        if (strlen(trim($this->input->post('q'))) > 0) {
            $b = $this->cms_libs->limpa_caracteres(trim($this->input->post('q')));
            $b = ($b == 'busca') ? '' : $b; // prevenir contra falsa busca
            $offset = 0;
        }
        // se foi feita bsca avançada
        if (strlen(trim($this->input->post('ativo'))) > 0) {
            $stt = $this->input->post('ativo');
            $offset = 0;
        } else {
            $stt = $v['stt'];
        }
        // echo '<pre>';
        // var_dump($b);
        // exit;
        if ($stt != '')
            $this->db->where('status', $stt);
        if ($b != '')
            $this->db->like('label', $b);
        $this->db->limit($pp, $offset);
        $this->db->order_by('ordem', 'label');
        $this->db->where('grupo', 0);
        $sql = $this->db->get('cms_modulos');
        // pega o Total de registros
        if ($stt != '')
            $this->db->where('status', $stt);
        if ($b != '')
            $this->db->like('label', $b);
        $this->db->where('grupo', 0);
        $sql_ttl = $this->db->get('cms_modulos');
        $ttl_rows = $sql_ttl->num_rows();
        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/administracao/menu/pp:' . $pp . '/g:' . $v['g'] . '/dt1:' . $v['dt1'] . '/dt2:' . $v['dt2'] . '/b:' . $b . '/stt:' . $stt . '/');
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 10; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
        // echo '<pre>';
        // var_dump($this->parse_menus_raiz($sql->result_array()));
        // exit;
        $saida = array('ttl_rows' => $ttl_rows,
            'rows' => $this->parse_menus_raiz($sql->result_array()));

        return $saida;
    }

    /**
     * Prepara pesquisa de item de menu
     *
     * @param mixed $array
     * @return
     */
    function parse_menus_raiz($array) {
        if (count($array) == 0)
            return false;
        // percorre array
        $saida = array();
        foreach ($array as $row) {
            if ($row['status'] == 1)
                $att = 'ativo';
            else if ($row['status'] == 0)
                $att = 'inativo';

            if ($row['tipo'] == 0)
                $quem = 'God';
            else
                $quem = 'Admins';
            // coloca no array
            $saida[] = array('id' => $row['id'],
                'label' => $row['label'],
                'quem' => $quem,
                'status' => $att);
        }
        return $saida;
    }

    /**
     * Salva dados de um Novo item de menu nivel 0 Raiz
     *
     * @return
     */
    function menu_salva_novo() {
        $label = trim($this->input->post('label'));
        $uri = trim($this->input->post('uri'));
        $front_uri = trim($this->input->post('front_uri'));
        $tipo = $this->input->post('modulo_id');
        $tabela = trim($this->input->post('tabela'));
        $status = $this->input->post('status');

        $ordenavel = $this->input->post('ordenavel');
        $comments = $this->input->post('comments');
        $destaques = $this->input->post('destaques');
        $inscricao = $this->input->post('inscricao');
        $pastas_img = $this->input->post('pastas_0');
        $pastas_arq = $this->input->post('pastas_2');
        $pastaAjuda = $this->input->post('pastaAjuda');
        $relacionamento1 = $this->input->post('modulos');
        $grupos1 = trim($this->input->post('grupos'));
        // --
        $grupos1 = (strlen($grupos1) == 0) ? 0 : $grupos1;


        $dados = array('label' => $label,
            'tipo' => $tipo,
            'tabela' => $tabela,
            'status' => $status,
            'ordem' => 0,
            'uri' => $uri,
            'front_uri' => $front_uri,
            'grupo' => 0,
            'acao' => '',
            'ordenavel' => $ordenavel,
            'comments' => $comments,
            'destaques' => $destaques,
            'inscricao' => $inscricao,
            'pasta_img' => $pastas_img,
            'pasta_arq' => $pastas_arq,
            'pasta_ajuda' => $pastaAjuda,
            'rel' => (is_array($relacionamento1)) ? implode('|', $relacionamento1) : $relacionamento1
        );

        $ret = $this->db->insert('cms_modulos', $dados);
        // -- >> LOG << -- //
        $oque = "Inseriu novo Módulo: <a href=\"" . cms_url('cms/administracao/menuEdita/id:' . $this->db->insert_id()) . "\">" . $label . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return $ret;
    }

    /**
     * Retorna os dados do menu parseados.
     * 
     * @param type $var
     * @return type 
     */
    function dados_menu($var) {
        $result = $this->cms_libs->dados_menus_raiz($var);

        $saida = array();

        foreach ($result as $chv => $vlr) {

            if ($chv == 'multicontent') {

                $itens = explode(',', $vlr);
                $multi = array();
                foreach ($itens as $r) {
                    if ($r != '') {
                        $multi[] = $r;
                    }
                }
                $multi[] = ''; // o último é vazio [+]

                $vlr = $multi;
            }

            $saida[$chv] = $vlr;
        }

        return $saida;
    }

    function dados_submenus($vars) {
        $this->db->where('grupo', $vars['id']);
        $this->db->order_by('ordem');
        $sql = $this->db->get('cms_modulos');
        if ($sql->num_rows() == 0)
            return false;
        else
            return $sql->result_array();
    }

    function menu_atualiza($var) {
        // - salva os dados do menu principal Raiz
        $label1 = trim($this->input->post('label'));
        $uri1 = trim($this->input->post('uri'));
        $front_uri1 = trim($this->input->post('front_uri'));
        $tipo1 = $this->input->post('tipo');
        $tabela1 = trim($this->input->post('tabela'));
        $status1 = $this->input->post('status');

        $ordenavel1 = $this->input->post('ordenavel');
        $destaques1 = $this->input->post('destaques');
        $comments1 = $this->input->post('comments');
        $inscricao1 = $this->input->post('inscricao');
        $relacionamento1 = $this->input->post('modulos');
//        $grupos1 = trim($this->input->post('grupos'));
        $pastas_0 = $this->input->post('pastas_0');
        $pastas_2 = $this->input->post('pastas_2');
        $pastaAjuda = $this->input->post('pastaAjuda');
        // --
//        $grupos1 = (strlen($grupos1) == 0) ? 0 : $grupos1;

        $dados1['label'] = $label1;
        $dados1['uri'] = $uri1;
        $dados1['front_uri'] = $front_uri1;
        $dados1['tipo'] = $tipo1;
        $dados1['tabela'] = $tabela1;
        $dados1['status'] = $status1;
        $dados1['ordenavel'] = $ordenavel1;
        $dados1['destaques'] = $destaques1;
        $dados1['comments'] = $comments1;
        $dados1['inscricao'] = $inscricao1;
        $dados1['pasta_img'] = $pastas_0;
        $dados1['pasta_arq'] = $pastas_2;
        $dados1['pasta_ajuda'] = $pastaAjuda;
        $dados1['rel'] = (is_array($relacionamento1)) ? implode('|', $relacionamento1) : $relacionamento1;

//        mybug($dados1);
        // valida dados
        if (strlen($label1) > 0) {
            $this->db->where('id', $var['id']);
            $this->db->update('cms_modulos', $dados1);
            // -- >> LOG << -- //
            $oque = "Atualizou Módulo: <a href=\"" . cms_url('cms/administracao/menuEdita/id:' . $var['id']) . "\">" . $label1 . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // - salva os dados de um NOVO subitem
        $label_n = trim($this->input->post('novo_label'));
        $uri_n = trim($this->input->post('novo_uri'));
        $acao_n = $this->input->post('novo_acao');
        $tipo_n = $this->input->post('novo_tipo');
        $tabela_n = trim($this->input->post('novo_tabela'));
        $status_n = $this->input->post('novo_status');
        $dados_n['label'] = $label_n;
        $dados_n['uri'] = $uri_n;
        $dados_n['acao'] = $acao_n;
        $dados_n['tipo'] = $tipo_n;
        $dados_n['tabela'] = $tabela_n;
        $dados_n['status'] = $status_n;
        $dados_n['grupo'] = $var['id'];
        // valida dados
        if (strlen($label_n) > 0 and strlen($uri_n) > 0) {
            $this->db->insert('cms_modulos', $dados_n);
        }
        // - salva os dados dos submenus antigos
        foreach ($_POST as $chv => $vlr) {
            $separa = explode("_", $chv);
            $etiqueta = $separa[0]; // label
            if (count($separa) > 1) {
                $identificador = $separa[1]; // number
            } else {
                $identificador = ''; // não deve salvar
            }
            // atualiza cada lista
            if ($etiqueta == 'label' and is_numeric($identificador)) { // so grava neata variavel
                $sLabel = $this->input->post('label_' . $identificador);
                $sUri = $this->input->post('uri_' . $identificador);
                $sAcao = $this->input->post('acao_' . $identificador);
                $sTipo = $this->input->post('tipo_' . $identificador);
                $sTabela = $this->input->post('tabela_' . $identificador);
                $sStatus = $this->input->post('status_' . $identificador);
                $sOrdem = $this->input->post('ordem_' . $identificador);
                // -
                $dados_x['label'] = $sLabel;
                $dados_x['uri'] = $sUri;
                $dados_x['acao'] = $sAcao;
                $dados_x['tipo'] = $sTipo;
                $dados_x['tabela'] = $sTabela;
                $dados_x['status'] = $sStatus;
                $dados_x['ordem'] = $sOrdem;
                // / faz  update
                $this->db->where('id', $identificador);
                $this->db->update('cms_modulos', $dados_x);
            }
        }

        // salva os multi conteúdos
        $this->multiContentSave($var);
        
        // escreve as configurações sobre os módulos
        $this->cms_libs->write_modules_config();

        return true;
    }

    /**
     * Concatena e salva na tabela de módulos.
     * 
     * @param type $var 
     */
    function multiContentSave($var) {

        $saida = array();

        foreach ($_POST as $chv => $vlr) {
            if (substr($chv, 0, 6) == 'multi_' && $vlr != '') {

                $v = str_replace(',', '', $vlr);
                $saida[] = $v; // , é a cola do array
            }
        }

        $multicontent = implode(',', $saida);

//        mybug($multicontent);
        // / faz  update
        $this->db->where('id', $var['id']);
        $this->db->update('cms_modulos', array('multicontent' => $multicontent));
    }

    function extras_atualiza($var) {
        // - salva os dados dos submenus antigos
        $saida = '';
        foreach ($_POST as $chv => $vlr) {
            if (substr($chv, 0, 5) == "extra") {

                $separa = explode("_", $chv);
                $campo = $separa[0]; // label
                $campoId = $separa[1]; // string
                //
	        if ($campo == 'extraCampo' && strlen($vlr) > 1) {
                    $saida .= trim($vlr) . ':';
                    $saida .= $this->input->post('extraId_' . $campoId) . ':';
                    $tipoCampo = $this->input->post('extraType_' . $campoId);
                    if ($tipoCampo == 'input' || $tipoCampo == 'text' || $tipoCampo == 'arq' || $tipoCampo == 'img') {
                        $saida .= $this->input->post('extraType_' . $campoId) . '{};';
                    } else {
                        $saida .= $this->input->post('extraType_' . $campoId) . '{';

                        $opc = '';
                        for ($x = 0; $x < 20; $x++) {

                            if ($this->input->post('extraOp_' . $campoId . '_' . $x)) {
                                $opc .= $this->input->post('extraOp_' . $campoId . '_' . $x) . '|';
                                $opc .= $this->input->post('extraClass_' . $campoId . '_' . $x) . ',';
                            }
                        }

                        $saida .= trim($opc, ',');

                        $saida .= '};';
                    }
                }
            }
        }

//echo '<pre>';
//echo $saida;
//exit;
        // / faz  update
        $this->db->where('id', $var['id']);
        $this->db->update('cms_modulos', array('extra' => $saida));
    }

    function lista_administradores($v) {
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
        // se foi feita bsca avançada
        if (strlen(trim($this->input->post('ativo'))) > 0) {
            $stt = $this->input->post('ativo');
            $offset = 0;
        } else {
            $stt = $v['stt'];
        }
        // echo '<pre>';
        // var_dump($b);
        // exit;
        if ($stt != '')
            $this->db->where('status', $stt);
        if ($b != '')
            $this->db->like('nome', $b);
        $this->db->limit($pp, $offset);
        $this->db->order_by('nome');
        $this->db->where('tipo >=', $this->phpsess->get('admin_tipo', 'cms'));

        $sql = $this->db->get('cms_admin');
        // pega o Total de registros
        if ($stt != '')
            $this->db->where('status', $stt);
        if ($b != '')
            $this->db->like('nome', $b);
        $this->db->where('tipo >=', $this->phpsess->get('admin_tipo', 'cms'));
        $sql_ttl = $this->db->get('cms_admin');
        $ttl_rows = $sql_ttl->num_rows();
        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/administracao/admins/pp:' . $pp . '/g:' . $v['g'] . '/dt1:' . $v['dt1'] . '/dt2:' . $v['dt2'] . '/b:' . $b . '/stt:' . $stt . '/');
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 10; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
        // echo '<pre>';
        // var_dump($this->parse_menus_raiz($sql->result_array()));
        // exit;
        $saida = array('ttl_rows' => $ttl_rows,
            'rows' => $this->parse_lista_administradores($sql->result_array()));

        return $saida;
    }

    function parse_lista_administradores($array) {
        if (count($array) == 0)
            return false;
        // percorre array
        $saida = array();
        foreach ($array as $row) {
            if ($row['status'] == 1)
                $att = 'ativo';
            else if ($row['status'] == 0)
                $att = 'inativo';

            if ($row['tipo'] == 0)
                $quem = 'God';
            else if ($row['tipo'] == 1)
                $quem = 'Super admin';
            else if ($row['tipo'] == 2)
                $quem = 'Segmentado';
            else
                $quem = '-';
            // coloca no array
            $saida[] = array('id' => $row['id'],
                'nome' => $row['nome'],
                'tipo' => $quem,
                'apelido' => $row['nick'],
                'status' => $att,
                'ultima' => formaPadrao($row['ultima_dt']),
                'visitas' => $row['visitas']);
        }
        return $saida;
    }

    function dados_administrador($vars) {
        $this->db->where('id', $vars['id']);
        $sql = $this->db->get('cms_admin');
        if ($sql->num_rows() == 0)
            return false;
        else
            return $this->_parse_dados_administrador($sql->row_array());
    }

    function _parse_dados_administrador($array) {
        if (count($array) == 0)
            return false;
        // percorre array
        $saida = array();
        foreach ($array as $chv => $vlr) {
            // ações
            if ($chv == 'acoes') {
                $list = explode('|', $vlr);
                $saida['apagar'] = (array_search('a', $list) === false) ? false : true;
                $saida['criar'] = (array_search('c', $list) === false) ? false : true;
                $saida['relatorio'] = (array_search('r', $list) === false) ? false : true;
            }
            // coloca no array
            $saida[$chv] = $vlr;
        }
        // echo '<pre>';
        // var_dump($saida);
        // exit;
        return $saida;
    }

    function administrador_salva($var) {
        // - salva os dados do menu principal Raiz
        $nome = trim($this->input->post('nome'));
        $email = $this->input->post('email');
        $nick = trim($this->input->post('nick'));
        $status = $this->input->post('status');
        $login = $this->input->post('login');
        $tipo = $this->input->post('tipo');
        $modulos = $this->input->post('modulos');
        $acoes_a = $this->input->post('acoes_a');
        $acoes_c = $this->input->post('acoes_c');
        $acoes_r = $this->input->post('acoes_r');
        // se existe senha altera
        if (strlen(trim($this->input->post('senha'))) > 4) {
            $dados['senha'] = md5($this->input->post('senha'));
        }
        // ações
        $acoes = (strlen($acoes_a) > 0) ? 'a|' : '';
        $acoes .= (strlen($acoes_c) > 0) ? 'c|' : '';
        $acoes .= (strlen($acoes_r) > 0) ? 'r|' : '';
        // modulos se for segmentado
        if ($tipo == 2) {
            if (!$modulos) {
                $dados['mod'] = '';
            } else {
                $mods = trim(implode('|', $modulos), '|'); // n|n|n|n
                $dados['mod'] = $mods;
            }
        }

        $dados['nome'] = $nome;
        $dados['email'] = $email;
        $dados['login'] = $login;
        $dados['nick'] = $nick;

        // se não puder alterar suas permissões não altera estes campos
        if ($status !== FALSE) {
            $dados['status'] = $status;
            $dados['tipo'] = $tipo;
            $dados['acoes'] = trim($acoes, '|');
        }

        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            // mensagem co dados ao criar admin
            $this->notifica_por_email($dados, $this->input->post('senha'));
            $dados['criador'] = $this->phpsess->get('admin_id', 'cms');
            $sql = $this->db->insert('cms_admin', $dados);
            // -- >> LOG << -- //
            $oque = "Novo Administrador: <a href=\"" . cms_url('cms/administracao/adminEdita/id:' . $this->db->insert_id()) . "\">" . $nome . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_admin', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Administrador: <a href=\"" . cms_url('cms/administracao/adminEdita/id:' . $var['id']) . "\">" . $nome . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }

        return $sql;
    }

    function notifica_por_email($dados, $senha) {
        $this->load->library('e_mail');
        $emailRem = $this->config->item('email1');
        $title = $this->config->item('title');
        $assunto = '[Novo admin] ' . $title;
        $emailDes = $dados['email'];
        $nomeDes = $dados['nome'];
        $apelido = $dados['nick'];
        $login = $dados['login'];
        $urladmin = cms_url('cms/login');

        $corpo = "$title <br/>
		------------------------------------------------------<br/>
		Sistema de Gerenciamento de Conteúdo<br/>
		<br/>
		<br/>
		Olá $nomeDes,<br/>
		você foi adicionado como um administrador. Para começar a tabalhar você deve fazer seu login no sistema.
		<br/><br/>
		Atenção! Guarde este e-mail, pois ele contém as informações necessárias para isso.
		<br/><br/>
		1) Visite o link: $urladmin <br/>
		2) Entre com seu login e senha<br/>
		3) Caso tenha perdido seus dados use o link \"lembrar senha\"<br/>
		<br/>
		::: Seus dados :::<br/>
		Nome: $nomeDes <br/>
		E-mail: $emailDes <br/>
		Apelido: $apelido <br/>
		Login: $login <br/>
		Senha: $senha <br/>
		<br/>
		* Ao se logar você poderá alterar suas informações pessoais. Para isso vá em \"Administração > Administradores\".
		";

        $menHTML = $corpo;
        // notifica admin
        $ret = $this->e_mail->envia($emailDes, $nomeDes, $assunto, $menHTML, $menHTML, $emailRem, $title);

        return $ret;
    }

    /**
     * Retorna o combo box de todos os módulos
     * @param <type> $ids
     * @param <type> $multi
     * @param <type> $extra2
     * @return string
     */
    function combo_modulos($ids = '', $multi = true, $extra2 = array()) {
        if (!is_array($ids)) { // se não for array, processa para validar no combobox
            $ids = explode('|', $ids);
        }
        $menus = $this->parse_carrega_menu($this->layout_cms->_carrega_menu(true));

        $cb = $this->cms_libs->cb($ids, $menus, 'modulos', $multi, '', $extra2);
        return $cb;
    }

    function combo_modulos_conteudo($ids = '', $multi = true, $extra2 = array()) {


        $saida = array();
        // -- pegar os menus principais RAIZ
        $this->db->where('grupo', 0);
        $this->db->where('status', 1);
        $this->db->where('tabela', 'cms_conteudo');
        $this->db->select('id, label');
        if ($this->phpsess->get('admin_tipo', 'cms') == 1) {
            $this->db->where('tipo', 1);
        } // para superadmin vê tudos menos tipo ==0
        else if ($this->phpsess->get('admin_tipo', 'cms') == 2) { // para Segmentados escolhe os módulos
            $this->db->where('id', 0); // hack do AR
            $listMods = explode('|', $this->phpsess->get('admin_mod', 'cms'));
            foreach ($listMods as $idmod) {
                $this->db->or_where('id', $idmod);
            }
        }
        $this->db->order_by('ordem');
        $sql = $this->db->get('cms_modulos');

        $saida = array();
        foreach ($sql->result_array() as $r) {

            $saida[$r['id']] = $r['label'];
        }


//mybug($saida);


        $cb = $this->cms_libs->cb($ids, $saida, 'modulos', $multi, '', $extra2);
        return $cb;
    }

    function parse_carrega_menu($menus) {
//        mybug($menus);
        $saida = array();
        foreach ($menus as $m) {
            $r = $m['raiz'];

            // só permite estes módulos, pois não poserá haver relacionamento 
            // entre módulos que não estejam nestas tabelas
            if ($r['tabela'] == 'cms_conteudo' || $r['tabela'] == 'cms_usuarios') {
                $saida[$r['id']] = $r['label'];
            }
        }
        return $saida;
    }

    function variaveis_raiz($v) {
        // -- trata as variaveis --//
        $pps = $this->config->item('pagination_limits');
        $offset = ($v['offset'] == '') ? 0 : $v['offset'];
        $pp = ($v['pp'] == '') ? $pps[0] : $v['pp']; // por página
        $b = $v['b'];
        // se foi feita uma busca
        if (strlen(trim($this->input->post('q'))) > 0) {
            $b = $this->cms_libs->limpa_caracteres(trim($this->input->post('q')));
            $b = ($b == 'busca') ? '' : $b; // prevenir contra falsa busca
            $offset = 0;
        }
        // se foi feita bsca avançada
        if (strlen(trim($this->input->post('ativo'))) > 0) {
            $stt = $this->input->post('ativo');
            $offset = 0;
        } else {
            $stt = $v['stt'];
        }
        // echo '<pre>';
        // var_dump($b);
        // exit;
        $this->db->where('tipo >=', $this->phpsess->get('admin_tipo', 'cms'));
        if ($stt != '')
            $this->db->where('status', $stt);
        if ($b != '')
            $this->db->like('titulo', $b);
        $this->db->limit($pp, $offset);
        $this->db->order_by('ordem', 'label');
        $this->db->where('grupo', 0);
        $sql = $this->db->get('cms_combobox');
        // pega o Total de registros
        $this->db->where('tipo >=', $this->phpsess->get('admin_tipo', 'cms'));
        if ($stt != '')
            $this->db->where('status', $stt);
        if ($b != '')
            $this->db->like('titulo', $b);
        $this->db->where('grupo', 0);
        $sql_ttl = $this->db->get('cms_combobox');
        $ttl_rows = $sql_ttl->num_rows();
        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/administracao/variaveis/pp:' . $pp . '/g:' . $v['g'] . '/dt1:' . $v['dt1'] . '/dt2:' . $v['dt2'] . '/b:' . $b . '/stt:' . $stt . '/');
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 10; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
        // echo '<pre>';
        // var_dump($this->parse_menus_raiz($sql->result_array()));
        // exit;
        $saida = array('ttl_rows' => $ttl_rows,
            'rows' => $this->parse_variaveis_raiz($sql->result_array()));

        return $saida;
    }

    /**
     * Prepara pesquisa de item de menu
     *
     * @param mixed $array
     * @return
     */
    function parse_variaveis_raiz($array) {
        if (count($array) == 0)
            return false;
        // percorre array
        $saida = array();
        foreach ($array as $row) {
            if ($row['status'] == 1)
                $att = 'ativo';
            else if ($row['status'] == 0)
                $att = 'inativo';
            // pega a quantidade de itens
            $this->db->where('grupo', $row['id']);
            $this->db->where('status', 1);
            $sql = $this->db->get('cms_combobox');
            $ttlitem = $sql->num_rows();
            // coloca no array
            $saida[] = array('id' => $row['id'],
                'titulo' => $row['titulo'],
                'ttlitem' => $ttlitem,
                'resumo' => $row['descricao'],
                'status' => $att);
        }
        return $saida;
    }

    /**
     * Salva dados de um Novo item de menu nivel 0 Raiz
     *
     * @return
     */
    function variaveis_salva_novo() {
        $label = trim($this->input->post('titulo'));
        $desc = trim($this->input->post('descricao'));
        $status = $this->input->post('status');
        $tipo = $this->input->post('tipo');

        $dados = array('titulo' => $label,
            'status' => $status,
            'descricao' => $desc,
            'ordem' => 0,
            'grupo' => 0,
            'tipo' => $tipo
        );

        $ret = $this->db->insert('cms_combobox', $dados);

        return $ret;
    }

    function dados_variavel_raiz($vars) {
        $this->db->where('id', $vars['id']);
        $this->db->order_by('ordem');
        $sql = $this->db->get('cms_combobox');
        if ($sql->num_rows() == 0)
            return false;
        else
            return $sql->row_array();
    }

    function dados_subitens($vars) {
        $this->db->where('grupo', $vars['id']);
        $this->db->order_by('ordem');
        $sql = $this->db->get('cms_combobox');
        if ($sql->num_rows() == 0)
            return false;
        else
            return $sql->result_array();
    }

    function variaveis_atualiza($var) {
        // - salva os dados do menu principal Raiz
        $label1 = trim($this->input->post('titulo'));
        $desc1 = trim($this->input->post('descricao'));
        $status1 = $this->input->post('status');
        $tipo1 = $this->input->post('tipo');

        $dados1['titulo'] = $label1;
        $dados1['descricao'] = $desc1;
        $dados1['status'] = $status1;
        $dados1['tipo'] = $tipo1;
        // valida dados
        if (strlen($label1) > 0) {
            $this->db->where('id', $var['id']);
            $this->db->update('cms_combobox', $dados1);
        }
        // - salva os dados de um NOVO subitem
        $label_n = trim($this->input->post('novo_titulo'));
        $valor_n = trim($this->input->post('novo_valor'));

        $dados_n['titulo'] = $label_n;
        $dados_n['valor'] = $valor_n;
        $dados_n['status'] = 1;
        $dados_n['grupo'] = $var['id'];
        // valida dados
        if (strlen($label_n) > 0) {
            $this->db->insert('cms_combobox', $dados_n);
        }
        // - salva os dados dos submenus antigos
        foreach ($_POST as $chv => $vlr) {
            $separa = explode("_", $chv);
            $etiqueta = $separa[0]; // label
            if (count($separa) > 1) {
                $identificador = $separa[1]; // number
            } else {
                $identificador = ''; // não deve salvar
            }
            // atualiza cada lista
            if ($etiqueta == 'titulo' and is_numeric($identificador)) { // so grava neata variavel
                // echo '<pre>';
                // print_r($separa[0]);
                // exit;
                $sLabel = $this->input->post('titulo_' . $identificador);
                $sValor = $this->input->post('valor_' . $identificador);
                $sStatus = $this->input->post('status_' . $identificador);
                $sOrdem = $this->input->post('ordem_' . $identificador);
                // -
                $dados_x['titulo'] = $sLabel;
                $dados_x['valor'] = $sValor;
                $dados_x['status'] = $sStatus;
                $dados_x['ordem'] = $sOrdem;
                // / faz  update
                $this->db->where('id', $identificador);
                $this->db->update('cms_combobox', $dados_x);
            }
        }

        return true;
        // echo '<pre>';
        // var_dump($_POST);
        // exit;
    }

    function config_dados() {
        $this->db->order_by('id');
        $sql = $this->db->get('cms_config');
        $saida = array();
        $x = 1;
        foreach ($sql->result_array() as $conf) {
            $saida[$x] = $conf;
            $x++;
        }

        return $saida;
    }

    function config_salva() {
        // percorre os campos
        $sql = $this->db->get('cms_config');
        $configs = $sql->result_array();
        
        
        foreach ($configs as $con) {
            $valor = '';
            // trata as exceções
            if ($con['campo'] == 'redirecionamento') {
                $chave = $this->input->post('chave'); // se está ligao ou não [0 ou 1]
                $redirecionamento = $this->input->post($con['campo']); // tipo [0 ou 1]
                $redirecionar = trim($this->input->post('redirecionar')); // onde redirecionar
                // -- trata os dados --  //
                if ($chave == 1) { // site no ar
                    $valor = $redirecionar;
                } else if ($redirecionamento == 0 && $chave == 0) { // manutenção
                    $valor = 0;
                } else if ($chave == 0 && $redirecionamento == 1 && strlen($redirecionar) > 6) { // url específica
                    $valor = prep_url($redirecionar);
                } else { // manutenção
                    $valor = 0;
                }
            } 
            else if($con['campo'] == 'logotipo'){
                $valor = $this->input->post('hidFileID');
                
                if($valor){
                    $this->db->where('id', $con['id']);
                    $this->db->update('cms_config', array('valor' => $valor));
                    $pastaArq = fisic_path() . $this->config->item('upl_arqs');
                    $this->resizeImage($pastaArq.'/'.$valor, 200);
                }
                
            }
            else {
                if ($con['campo'] != '') {
                    $valor = $this->input->post($con['campo']);
                }
            }
            if ($valor != '') {
                // salva o dado
                $this->db->where('id', $con['id']);
                $this->db->update('cms_config', array('valor' => $valor));
            }
            
            
        }


        return true;
    }
    
    /**
     * Redimensiona a imagem para tamanho aceitável.
     * 
     * @param type $image
     * @param type $width 
     */
    function resizeImage($image, $width = 200){
//        $config['image_library'] = 'gd2';
        $config['source_image']	= $image;
//        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']	 = $width;
        $config['height']	 = $width;

        $this->load->library('image_lib'); 
        
        $this->image_lib->initialize($config);

        $this->image_lib->resize();
         $this->image_lib->clear();
    }

    function config_padroes() {
        $lista = array(1, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16); // ID dos configs que devem voltar
        foreach ($lista as $id) {
            $this->db->where('id', $id);
            $sql = $this->db->get('cms_config');
            $con = $sql->row_array();
            // - update
            $this->db->where('id', $id);
            $this->db->update('cms_config', array('valor' => $con['padrao']));
        }
        return true;
    }

    /**
     * É acionado pelo controller Admins::restaura_bd() e faz o restaure do DB passado va var '$sqlfile'.
     *
     * @param string $sqlfile
     * @return bool
     */
    function restaura_bd($sqlfile) {
        $this->error = '';
        $sql = "";

        if (!is_file($sqlfile)) {
            $this->error = "Error : Not a valid file.";
            return false;
        }
        // elimina as tabelas existentes
        $tables = $this->db->list_tables();
        foreach ($tables as $table) {
            mysql_query("DROP TABLE $table");
        }

        $lines = @file($sqlfile);

        if (!is_array($lines))
            $uploadMsg = "Sql File is empty.";
        else {
            foreach ($lines as $line) {
                $sql .= trim($line);
                if (empty($sql)) {
                    $sql = "";
                    continue;
                } elseif (preg_match("/^[#-].*+\r?\n?/i", trim($line))) {
                    $sql = "";
                    continue;
                } elseif (!preg_match("/;[\r\n]+/", $line))
                    continue;

                @mysql_query($sql);
                if (mysql_error() != "") {
                    $this->error .= "<br>" . mysql_error();
                }

                $sql = "";
            }
            if (!empty($this->error))
                return false;

            return true;
            // debug
            // echo '<pre>';
            // var_dump($tables);
            // exit;
        }
    }

    function extraCamposDados($estruturaModulo) {
        $estruturaModulo = trim($estruturaModulo);
        if (strlen($estruturaModulo) == 0)
            return false;


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

                $data = '';
            } else {

                // transforma em um array
                $campos = explode(',', $p1[1]);
                $data = array();
                // percorre cada item
                for ($j = 0; $j < count($campos); $j++) {

                    $data[] = $campos[$j];
                }
            }

            $saida[] = array(
                'name' => $name,
                'id' => $id,
                'type' => $type,
                'data' => $data
            );
        }

        return $saida;
    }
    
    // -----------------------------------------------------------------------
    /**
     * Retorna dados de configuração.
     * 
     * @return type
     */
    function get_prefs(){
        
        $saida = array();
        
        foreach($this->prefs as $row){
            
            $return = $this->db->where('campo', $row)->get('cms_config');
            $saida[$row] = '';
            
            if($return->num_rows())
            {
                $config = $return->row_array();
                $saida[$row] = $config['valor'];
            }
            
        }
        
        return $saida;
        
    }
    
    // ------------------------------------------------------------------------
    
    /**
     * Verifica se existem os campos de configuração,
     * se não, cria registro
     */
    function set_prefs(){

        foreach($this->prefs as $row){
            
            // verifica se existe
            $return = $this->db->where('campo', $row)->get('cms_config');
            
            // se não existir cria campo vazio
            if($return->num_rows() == 0){
                $this->db->insert('cms_config', array(
                    'titulo' => 'Configuração do site',
                    'campo' => $row
                ));
            }
            
        }
        
        return $this->prefs;
        
    }
    
    // ---------------------------------------------------------------------
    
    function prefs_salva($vars){        
        
        $this->set_prefs();
        $saida = array();
        
        foreach($this->prefs as $row){
            
            $this->db->where('campo', $row);
            $this->db->update('cms_config', array(
                'valor' => $this->input->post($row)
            ));            
            
        }
        
    }

}

?>