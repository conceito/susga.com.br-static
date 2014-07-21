<?php

/**
 *
 * @version $Id$
 * @copyright 2010
 */
class Usuarios_model extends CI_Model
{

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
	 * @return array
	 */
    function lista_conteudos($v, $tipo = 'conteudo', $modulo = array())
    {
        // -- trata as variaveis --//
        $pps = $this->config->item('pagination_limits');
        $pp  = ($v['pp'] == '') ? $pps[0] : $v['pp']; // por página

        $pag = $this->uri->to_array('pag');
        if ($pag['pag'] == '')
        {
            $offset = 0;
        }
        else
        {
            $offset = ($pag['pag'] - 1) * $pp;
        }

        //        $b = $v['b'];
        //        // se foi feita uma busca
        //        if (strlen(trim($this->input->post('q'))) > 0) {
        //            $b = $this->cms_libs->limpa_caracteres(trim($this->input->post('q')));
        //            $b = ($b == 'busca') ? '' : $b; // prevenir contra falsa busca
        //            $offset = 0;
        //        }
        //        // se foi feita bsca avançada ------------------------
        //        if (strlen(trim($this->input->post('ativo'))) > 0) {
        //            $stt = $this->input->post('ativo');
        //            $offset = 0;
        //        } else {
        //            $stt = $v['stt'];
        //        }
        //        // se foi feita seleção com grupos
        //        if (strlen(trim($this->input->post('grupos'))) > 0) {
        //            $g = $this->input->post('grupos');
        //        } else {
        //            $g = ($v['g'] == '') ? 0 : $v['g'];
        //        }
        //        // pelas datas
        //        if (strlen(trim($this->input->post('dt1'))) > 0
        //
        //            )$dt1 = formaSQL($this->input->post('dt1'));
        //        else
        //            $dt1 = $v['dt1'];
        //        if (strlen(trim($this->input->post('dt2'))) > 0
        //
        //            )$dt2 = formaSQL($this->input->post('dt2'));
        //        else
        //            $dt2 = $v['dt2'];
        //        // echo '<pre>';
        //        // var_dump($v['co']);
        //        // exit;
        //        // -- SQL básica com paginação -- //
        //        if ($dt1 != '' && $dt2 == '') {
        //            $this->db->where('dt_ini', $dt1);
        //        } else if ($dt1 != '' && $dt2 != '') {
        //            $this->db->where('dt_ini >=', $dt1);
        //            $this->db->where('dt_ini <=', $dt2);
        //        }
        //        if ($stt != '') {
        //            $this->db->where('status', $stt);
        //        }
        //        if ($b != '') {
        //            $this->db->like('nome', $b);
        //            $this->db->or_like('email', $b);
        //        }

        // opções de filtro
        $uri_filters = $this->set_users_filters();

        $this->db->limit($pp, $offset);
        // ordenação
        if (isset($modulo['ordenavel']) && $modulo['ordenavel'])
        {
            $this->db->order_by('ordem');
        }
        else
        {
            $this->db->order_by('nome');
        }
        // $this->db->where('tipo', $v['co']);// deprecated
        if ($tipo == 'grupo')
        {
            $this->db->where('grupo', 0); // busca grupos
        }
        else
        {
            //            if ($g == 0) $this->db->where('grupo !=', 0); // todos conteudos
            //            else
            //                $this->db->where('grupo', $g); // conteudos do grupo
            $this->db->where('grupo >', 0);
        }

        $this->db->where('lang', get_lang());
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
        $sql = $this->db->get('cms_usuarios');

        // -- pega o Total de registros -- //
        // -- pega o Total de registros -- //
        //        if ($dt1 != '' && $dt2 == '') {
        //            $this->db->where('dt_ini', $dt1);
        //        } else if ($dt1 != '' && $dt2 != '') {
        //            $this->db->where('dt_ini >=', $dt1);
        //            $this->db->where('dt_ini <=', $dt2);
        //        }
        //        if ($stt != '') $this->db->where('status', $stt);
        //        if ($b != '') {
        //            $this->db->like('nome', $b);
        //            $this->db->or_like('email', $b);
        //        }
        //        // $this->db->where('tipo', $v['co']);// deprecated
        //        if ($tipo == 'grupo') {
        //            $this->db->where('grupo', 0); // busca grupos
        //        } else {
        //            if ($g == 0)
        //                $this->db->where('grupo !=', 0); // todos conteudos
        //            else
        //                $this->db->where('grupo', $g); // conteudos do grupo
        //
        //
        //
        //
        //
        //        }
        //        $this->db->where('lang', get_lang());
        //        $sql_ttl = $this->db->get('cms_usuarios');
        //        $ttl_rows = $sql_ttl->num_rows();

        $query    = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $ttl_rows = $query->row()->Count;

        // paginação
        $this->load->library('pagination');
        $config['base_url']      = cms_url('cms/usuarios/index/co:' . $v['co'] . $uri_filters);
        $config['total_rows']    = $ttl_rows;
        $config['per_page']      = $pp;
        $config['uri_segment']   = 11; // segmentos + 1
        $config['num_links']     = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open']  = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open']  = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
        // echo '<pre>';
        // var_dump($sql->result_array());
        // exit;
        $saida = array('ttl_rows' => $ttl_rows,
                       'rows'     => $this->parse_lista_conteudos($sql->result_array()));

        return $saida;
    }

    // ----------------------------------------------------------------------

    /**
     * Parseia $_POST e URI para saber se existem filtros ativos
     * Retorna URI para paginação.
     *
     * @return string
     */
    private function set_users_filters()
    {

        // define os campos que serão usados no filtro
        $campos_usados[]  = array('campo' => 'nome', 'type' => 'like');
        $campos_usados[]  = array('campo' => 'grupo', 'type' => 'text');
        $campos_usados[]  = array('campo' => 'destaque', 'type' => 'int');
        $campos_usados[]  = array('campo' => 'dt_ini', 'type' => 'date');
        $campos_usados[]  = array('campo' => 'status', 'type' => 'int');
        $campos_valorados = array();

        //        mybug($this->input->post());

        // uri de filtros para paginação
        $return = '';

        // verifica se veio pelo POST ou URI
        foreach ($campos_usados as $row)
        {

            $campo = $row['campo'];
            $type  = $row['type'];
            $uri   = $this->uri->to_array('filter_' . $campo);

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
            $row['valor']       = $valor;
            $campos_valorados[] = $row;

        }
        //        mybug($campos_valorados);
        // faz pesquisa
        foreach ($campos_valorados as $row)
        {

            if ($row['valor'] != '')
            {

                $campo = $row['campo'];
                $type  = $row['type'];
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
    function parse_lista_conteudos($array)
    {

        if (count($array) == 0)
        {
            return false;
        }
        // percorre array
        $saida = array();
        foreach ($array as $row)
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

            if ($row['grupo'] == 0)
            {
                $row['grupo']  = 'Grupo';
                $row['resumo'] = $row['obs'];
                $row['titulo'] = $row['nome'];

                $cores            = $this->paginas_model->get_grupo_cores($row['filtro']);
                $row['grupoCor1'] = $cores['cor1'];
                $row['grupoCor2'] = $cores['cor2'];

            }
            else
            {
                $this->db->where('id', $row['grupo']);
                $this->db->select('nome, filtro');
                $sql = $this->db->get('cms_usuarios');
                if ($sql->num_rows() > 0)
                {
                    $item             = $sql->row_array();
                    $row['grupo']     = $item['nome'];
                    $cores            = $this->paginas_model->get_grupo_cores($item['filtro']);
                    $row['grupoCor1'] = $cores['cor1'];
                    $row['grupoCor2'] = $cores['cor2'];
                }
                else
                {
                    $row['grupo']     = 'desconhecido';
                    $row['grupoCor1'] = '';
                    $row['grupoCor2'] = '';
                }
            }
            $row['titulo'] = $row['nome'];
            $row['resumo'] = $row['email'] . '<br/>' . $row['obs'];
            // coloca no array
            // $saida[] = array('id' => $row['id'],
            // 'titulo' => $row['titulo'],
            // 'tipo' => $tipo,
            // 'status' => $att);
            $saida[] = $row;
        }
        // echo '<pre>';
        // var_dump($saida);
        // exit;
        return $saida;
    }

    /**
     * Pega os dados na Library e parseia os dados
     *
     * @param mixed $var
     * @return
     */
    function usuario_dados($var)
    {

        $dd = $this->cms_libs->conteudo_dados($var, 'cms_usuarios');
        if (!$dd)
        {
            return false;
        }

        $this->load->library('cms_metadados');

        // percorre array
        $saida = array();

        foreach ($dd as $chv => $vlr)
        {
            if($chv == 'id')
            {
                // with metas
                $metas = $this->cms_metadados->getAllByUser($vlr);
                $saida['metas'] = $metas;
            }

            // data
            if ($chv == 'dt_ini')
            {
                $vlr = formaPadrao($vlr);
            }
            if ($chv == 'dt_fim')
            {
                $vlr = formaPadrao($vlr);
            }
            if ($chv == 'nasc')
            {
                $vlr = formaPadrao($vlr);
            }
            // quantidade de imagens na galeria
            if ($chv == 'galeria')
            {
                if (strlen($vlr) == 0)
                {
                    $saida['quantGal'] = 0;
                }
                else
                {
                    $array             = explode('|', $vlr);
                    $saida['quantGal'] = count($array);
                }
            }
            // acerta os telefones
            if ($chv == 'tel1' || $chv == 'tel2')
            {
                $vlr = tel_input($vlr);
            }
            if ($chv == 'uf')
            {
                $saida['combo_uf'] = $this->cms_libs->combo_estados($vlr);
            }
            if ($chv == 'cidade')
            {
                $cbC                   = $this->cms_libs->combo_cidades($dd['uf'], $vlr);
                $saida['combo_cidade'] = ($cbC == '') ? '<br/><br/>' : $cbC;
            }
            if ($chv == 'filtro')
            {
                $ids                    = $this->cms_libs->str_to_array($vlr);
                $cbF                    = $this->cms_libs->combo_sist_vars('filtros', 20, $ids, true);
                $saida['combo_filtros'] = $cbF;
            }

            // coloca no array
            $saida[$chv] = $vlr;
        }

        return $saida;
    }

    function usuario_salva($var)
    {

        $this->load->helper('checkfix');

        // - salva os dados do menu principal Raiz
        $grupo     = $this->input->post('grupos');
        $fantasia  = trim($this->input->post('fantasia'));
        $razao     = trim($this->input->post('razao'));
        $profissao = trim($this->input->post('profissao'));
        $atividade = trim($this->input->post('atividade'));
        $cargo     = trim($this->input->post('cargo'));
        $nome      = trim($this->input->post('nome'));
        $email     = trim($this->input->post('email'));
        $email2    = trim($this->input->post('email2'));
        $sexo      = trim($this->input->post('sexo'));
        $nasc      = trim($this->input->post('nasc'));
        $tel1      = $this->input->post('tel1');
        $tel2      = $this->input->post('tel2');
        $obs       = trim($this->input->post('obs'));
        $status    = $this->input->post('status');
        //-
        $logra          = trim($this->input->post('logradouro'));
        $num            = trim($this->input->post('num'));
        $compl          = trim($this->input->post('compl'));
        $cep            = trim($this->input->post('cep'));
        $uf             = $this->input->post('uf');
        $cidade         = $this->input->post('cidade');
        $bairro         = trim($this->input->post('bairro'));
        $cpf            = trim($this->input->post('cpf'));
        $rg             = trim($this->input->post('rg'));
        $cnpj           = trim($this->input->post('cnpj'));
        $insc_estadual  = trim($this->input->post('insc_estadual'));
        $insc_municipal = trim($this->input->post('insc_municipal'));
        //-
        $news    = $this->input->post('news');
        $filtros = $this->input->post('filtros'); // array

        $dados['nome']  = $nome;
        $dados['grupo'] = $grupo;
        $dados['email'] = $email;
        $dados['obs']   = $obs;

        // --  NOVO ITEM  -- //
        if ($var['id'] == '')
        {
            $dados['lang']   = get_lang();
            $dados['dt_ini'] = date("Y-m-d");

            // cria senha            
            $dados['senha'] = cf_password();

            $sql    = $this->db->insert('cms_usuarios', $dados);
            $esteid = $this->db->insert_id();
            // -- >> LOG << -- //
            $oque = "Novo Usuário: <a href=\"" . cms_url('cms/usuarios/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $nome . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else
        {

            $dados['fantasia']  = $fantasia;
            $dados['razao']     = $razao;
            $dados['profissao'] = $profissao;
            $dados['atividade'] = $atividade;
            $dados['cargo']     = $cargo;
            $dados['email2']    = $email2;
            $dados['sexo']      = $sexo;
            $dados['nasc']      = formaSQL($nasc);
            $dados['tel1']      = tel_to_sql($tel1);
            $dados['tel2']      = tel_to_sql($tel2);
            $dados['status']    = $status;
            //-
            $dados['logradouro']     = $logra;
            $dados['num']            = $num;
            $dados['compl']          = $compl;
            $dados['cep']            = str_replace(array('-', '.', ' '), '', $cep);
            $dados['uf']             = strtoupper($uf);
            $dados['cidade']         = $cidade;
            $dados['bairro']         = $bairro;
            $dados['cpf']            = cf_cpf($cpf);
            $dados['rg']             = cf_rg($rg);
            $dados['cnpj']           = $cnpj;
            $dados['insc_estadual']  = $insc_estadual;
            $dados['insc_municipal'] = $insc_municipal;
            $dados['foto']           = cf_foto($email);
            //-
            $dados['news']   = $news;
            $dados['filtro'] = ($filtros) ? '|' . implode('|', $filtros) . '|' : '';

            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_usuarios', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Usuário: <a href=\"" . cms_url('cms/usuarios/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $nome . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }

        return $esteid;
    }

    function grupo_salva($var)
    {
        // - salva os dados do menu principal Raiz
        $titulo = trim($this->input->post('titulo'));
        $nick   = trim($this->input->post('nick'));
        $txt    = trim($this->input->post('txt'));
        $cor1   = trim($this->input->post('cor1'));
        $cor2   = trim($this->input->post('cor2'));

        $dados['nome']   = $titulo;
        $dados['obs']    = $txt;
        $dados['filtro'] = $cor1 . '|' . $cor2;
        // echo '<pre>';
        // var_dump($dados);
        // exit;
        // --  NOVO ITEM  -- //
        if ($var['id'] == '')
        {
            $dados['dt_ini'] = date("Y-m-d");
            $dados['grupo']  = 0;
            $dados['lang']   = get_lang();
            $dados['status'] = 1;

            $sql = $this->db->insert('cms_usuarios', $dados);
            // -- >> LOG << -- //
            $oque = "Novo Grupo: <a href=\"" . cms_url('cms/usuarios/grupoEdita/co:' . $var['co'] . '/id:' . $this->db->insert_id()) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else
        {
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_usuarios', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Grupo: <a href=\"" . cms_url('cms/usuarios/grupoEdita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }

        return $sql;
    }

    function mensagem_envia()
    {
        $this->load->library('e_mail');
        $emailRem = $this->config->item('email1');
        $nomeRem  = $this->config->item('title');
        $assunto  = $this->input->post('assunto');
        $emailDes = $this->input->post('email');
        $nomeDes  = $this->input->post('nome');
        $mensagem = $this->input->post('mensagem');
        $urlsite  = base_url();

        $dados['siteNome']  = $this->config->item('title');
        $dados['corpo']     = auto_link(nl2br($mensagem));
        $dados['emailSite'] = $emailRem;
        $dados['urlSite']   = $urlsite;

	    /**
	     * so, use the front-end template
	     */
	    if(view_exist('template/email'))
	    {
		    $v['body'] = "<h1>{$assunto}</h1>";
		    $v['body'] .= $dados['corpo'];
		    $menHTML = $this->load->view('template/email', $v, true);
	    }
	    /**
	     * use the default
	     */
	    else
	    {
		    $menHTML = $this->load->view('cms/email_usuarios', $dados, true);
	    }

        $menTXT = $mensagem . PHP_EOL . "
		--------------------------------------------------------------------------------------------------------
		" . PHP_EOL . "
		$emailRem | $urlsite";

        // notifica admin
        $ret = $this->e_mail->envia($emailDes, $nomeDes, $assunto, $menHTML, $menTXT, $emailRem, $nomeRem);

        return $ret;
    }

    /**
     * Valida o CSV se foi enviado. Se não existe retorna false.
     *
     * @param mixed $file
     * @return
     * */
    function valida_csv($file)
    {
        $nome = $file['name'];
        $tipo = $file['type'];
        $tmp  = $file['tmp_name'];
        $erro = $file['error'];
        $size = $file['size'];

        // veriica se existe e é válido
        if (strlen($nome) > 4 && $erro == 0 && $size > 0)
        {
            $ext = substr($nome, -3);
            if (strtolower($ext) != 'csv')
            {
                $saida = false;
            }
            else
            {
                $saida = true;
            }
        }
        else
        {
            // não existe
            $saida = false;
        }

        return $saida;
    }

    /**
     * Extrai CSV para ARRAY
     *
     * @param mixed $file
     * @return
     * */
    function csv_dados($file)
    {
        $this->load->library('csvreader');
        $data  = $this->csvreader->parse_file($file['tmp_name']);
        $saida = array();
        $this->load->helper('email');
        foreach ($data as $item)
        {

            $pars = array();
            foreach ($item as $c => $v)
            {
                $pars[$c] = utf8_encode($v);
            }
            $saida[] = $pars;
        }

        return $saida;
    }

    function txt_dados($nomes, $emails)
    {
        $anomes  = preg_split("/\r{0,1}\n/", $nomes); //explode("\n", $nomes);
        $aemails = preg_split("/\r{0,1}\n/", $emails); //explode("\n", $emails);
        if (count($anomes) != count($aemails))
        {
            $saida = '!=';
        }
        else
        {
            $lista = array();
            $this->load->helper('email');
            for ($i = 0; $i < count($aemails); $i++)
            {
                $n = $anomes[$i];
                $e = $aemails[$i];
                //$lista[] = array('nome' => $n, 'email' => $e);
                // é válido
                if (valid_email($e))
                {
                    $lista[] = array('nome' => $n, 'email' => $e);
                }
            }

            if (count($lista) == 0

            )
            {
                $saida = false;
            }
            else
            {
                $saida = $lista;
            }
        }

        return $saida;
    }

    /**
     * Grava no BD.
     * array = ['nome' = blablabla,
     *            'email' = blablabla
     * ]
     *
     * @param mixed $importacao
     * @return
     * */
    function salva_importacao($import, $grupo)
    {
        //echo '<pre>';
        //var_dump($import);
        foreach ($import as $user)
        {
            $user['grupo']  = $grupo;
            $user['status'] = 1;
            //var_dump($user);
            $salva = $this->db->insert('cms_usuarios', $user);
        }

        return $salva;
    }

    function usuario_descadastra()
    {
        $email = $this->input->post('seuemail');
        $this->db->where('email', $email);
        $sql  = $this->db->get('cms_usuarios');
        $user = $sql->row_array();

        // apenas se estiver ativo adiciona stats
        if ($user['news'] == 1)
        {
            // adiciona estatística
            $dados['mens_id'] = $id_mens;
            $dados['user_id'] = $id_user;
            $dados['data']    = date("Y-m-d");
            $dados['hora']    = date("H:i:s");
            $dados['acao']    = 5; // remover
            $this->db->insert('cms_news_stats', $dados);
        }

        if ($sql->num_rows() == 0)
        {
            return false;
        }
        else
        {
            // remove
            $this->db->where('email', $email);
            $ret = $this->db->update('cms_usuarios', array('news' => 0));

            return $ret;
        }
    }

    // -----------------------------------------------------------------------
    /**
     * Pesquisa usuários com o termo passado e retorna um array para autocomplete
     * @param       string $term
     * @param       type $config
     * @return      array
     */
    public function search_for_autocomplete($term, $config = array())
    {

        $termo = trim($term);

        if (strlen($termo) == 0)
        {
            return '';
        }

        $this->db->where("(nome LIKE '%$termo%' || email LIKE '%$termo%' || razao LIKE '%$termo%' || fantasia LIKE '%$termo%')");

        $this->db->where('status', 1);
        $this->db->where('lang', get_lang());

        if (isset($config['limit']))
        {
            $this->db->limit($config['limit']);
        }
        else
        {
            $this->db->limit(10);
        }

        if (isset($config['grupos']) && $config['grupos'] === true)
        {
            $this->db->where('grupo', 0);
        }
        else
        {
            $this->db->where('grupo !=', 0);
        }

        $this->db->order_by('nome');
        $this->db->select('id, nome');
        $return = $this->db->get('cms_usuarios');

        //        return $this->db->last_query();

        if ($return->num_rows() == 0)
        {
            return '';
        }

        $retorno = array();

        foreach ($return->result_array() as $row)
        {

            $retorno[] = array('id' => $row['id'], 'label' => $row['nome'], 'value' => $row['nome']);

        }

        return $retorno;

    }

}

?>