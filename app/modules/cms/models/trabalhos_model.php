<?php

use Gestalt\Congresso5;

class Trabalhos_model extends MY_Model
{

    /**
     * keep a copy of last model
     * @var null
     */
    protected $thisModel = null;

    /**
     * @var Gestalt\Congresso5
     */
    protected $congresso;

    /**
     * Meta dados registrados
     * @var type
     */
    protected $_metas = array(
        array('meta_key' => 'subtitulo', 'meta_type' => '', 'meta_value' => ''),
        array('meta_key' => 'authors', 'meta_type' => '', 'meta_value' => ''),
        array('meta_key' => 'eixo_tematico', 'meta_type' => '', 'meta_value' => ''),
        array('meta_key' => 'modalidade', 'meta_type' => '', 'meta_value' => ''),
    );

    function __construct()
    {
        parent::__construct();
        $this->congresso = new Congresso5();
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

        $query    = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $ttl_rows = $query->row()->Count;

        // paginação
        $this->load->library('pagination');
        $config['base_url']      = cms_url('cms/trabalhos/index/co:' . $v['co'] . $uri_filters);
        $config['total_rows']    = $ttl_rows;
        $config['per_page']      = $pp;
        $config['uri_segment']   = 11; // segmentos + 1
        $config['num_links']     = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open']  = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open']  = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);

        $saida = array('ttl_rows' => $ttl_rows,
                       'rows'     => $this->parse_lista_conteudos($sql->result_array(), $modulo));

        return $saida;
    }


    /**
     * Parseia $_POST e URI para saber se existem filtros ativos
     * Retorna URI para paginação.
     *
     * @return string
     */
    private function set_posts_filters()
    {

        // define os campos que serão usados no filtro
        $campos_usados[]  = array('campo' => 'titulo', 'type' => 'like');
        $campos_usados[]  = array('campo' => 'grupo', 'type' => 'text');
        $campos_usados[]  = array('campo' => 'destaque', 'type' => 'int');
        $campos_usados[]  = array('campo' => 'dt_ini', 'type' => 'date');
        $campos_usados[]  = array('campo' => 'dt_fim', 'type' => 'date');
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
    function parse_lista_conteudos($array, $modulo = array())
    {
        if (count($array) == 0)
        {
            return false;
        }
        // percorre array
        $saida = array();
        foreach ($array as $row)
        {

            $row['titulo_ori'] = $row['titulo'];
            $row['titulo']     = strip_tags($row['titulo']);

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
                $row['grupo'] = 'Grupo';
            }
            else
            {

                // pega grupo com seus parentes, se houver
                $grupoParents        = $this->paginas_model->getGrupoParents($row['grupo'], $modulo['id']);
                $row['grupoParents'] = $grupoParents;
            }

            $saida[] = $row;
        }

        return $saida;
    }


    public function save_new($var = array())
    {

        // - salva os dados do menu principal Raiz
        $grupo = $this->input->post('grupos');
        //        $rel      = $this->input->post('rel');
        $titulo = trim($this->input->post('titulo'));
        $nick   = trim($this->input->post('nick'));

        $dados['titulo']     = $titulo;
        $dados['dt_ini']     = date("Y-m-d");
        $dados['dt_fim']     = date("Y-m-d");
        $dados['hr_ini']     = date("H:i:s");
        $dados['hr_fim']     = date("H:i:s");
        $dados['grupo']      = $grupo;
        $dados['modulo_id']  = $var['co'];
        $dados['status']     = 2;
        $dados['atualizado'] = date("Y-m-d H:i:s");

        $dados['tipo']     = 'conteudo';
        $dados['autor']    = $this->phpsess->get('admin_id', 'cms');
        $dados['lang']     = get_lang();
        $dados['nick']     = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
        $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => '', 'nick' => $dados['nick']), $grupo, $var['co']);

        $sql    = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();

        // -- >> LOG << -- //
        $oque = "Novo trabalho: <a href=\"" . cms_url('cms/trabalhos/edita/co:' . $var['co'] . '/id:' . $esteid) .
            "\">" . $titulo . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return $esteid;
    }


    /**
     * Insere/atualiza dados no módulo calendário.
     *
     * @param array $var
     * @return int
     */
    function save($var)
    {
        // - salva os dados do menu principal Raiz
        $grupo  = $this->input->post('grupos');
        $rel    = $this->input->post('rel');
        $titulo = trim($this->input->post('titulo'));
        $nick   = trim($this->input->post('nick'));
        $data1  = trim($this->input->post('dt1'));
        //        $data2 = trim($this->input->post('dt2'));
        //        $hora1 = trim($this->input->post('hora1'));
        //        $hora2 = trim($this->input->post('hora2'));
        $status   = $this->input->post('status');
        $resumo   = trim($this->input->post('resumo'));
        $tags     = trim($this->input->post('tags'));
        $txt      = trim($this->input->post('txt'));
        $txtmulti = trim($this->input->post('txtmulti'));
        $mytags   = $this->input->post('mytags');
        $scripts  = $this->input->post('scripts');

        $dados['titulo'] = campo_texto_utf8(clean_html_to_db($titulo));
        $dados['resumo'] = clean_html_to_db($resumo);
        $dados['dt_ini'] = formaSQL($data1);

        //        $dados['dt_fim'] = formaSQL($data2);
        //        $dados['hr_ini'] = $hora1;
        //        $dados['hr_fim'] = $hora2;
        //        $dados['semana'] = $semana;
        $dados['grupo']      = $grupo;
        $dados['modulo_id']  = $var['co'];
        $dados['tags']       = $tags;
        $dados['status']     = $status;
        $dados['txt']        = campo_texto_utf8(clean_html_to_db($txt));
        $dados['rel']        = prep_rel_to_sql($rel);
        $dados['atualizado'] = date("Y-m-d H:i:s");

        // antes de salvar, salva última versão
        $this->cms_libs->save_revision($var['id']);

        $nick = $this->input->post('nick_edita');

        $dados['nick']     = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
        $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => $var['id'], 'nick' => $dados['nick']), $grupo, $var['co']);
        $dados['txtmulti'] = $this->paginas_model->concatenateMultiContents();
        $dados['scripts']  = $scripts;

        $this->db->where('id', $var['id']);
        $sql = $this->db->update('cms_conteudo', $dados);

        // -- >> LOG << -- //
        $oque = "Atualizou Trabalho: <a href=\"" . cms_url('cms/trabalhos/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        $esteid = $var['id'];

        // faz atualização das tags. precisa do ID, por isso está aqui
        $this->paginas_model->set_tag_conteudo($mytags, $var);

        // salva conteúdo meta
        $this->saveMetas($var['id']);

        return $esteid;
    }


	/**
	 * get the job
	 * decorate and return
	 *
	 * @param mixed $var
	 * @param bool $newQuery
	 * @return array|bool
	 */
    function find($var, $newQuery = true)
    {
        if ($this->thisModel !== null && $newQuery)
        {
            return $this->thisModel;
        }

        $this->load->library('cms/cms_libs');
        $this->load->model('cms/paginas_model');
        $this->load->model('cms/calendario_model');

        $dd = $this->cms_libs->conteudo_dados($var);
        if (!$dd)
        {
            return $this->thisModel = false;
        }

        $this->load->library('cms_metadados');

        // percorre array
        $model = array();
        foreach ($dd as $chv => $vlr)
        {
            if ($chv == 'titulo')
            {
                $model['titulo_txt'] = clean_html_field($vlr);
                //                $vlr = clean_html_field($vlr);
            }

            // data
            if ($chv == 'dt_ini')
            {
                $model['dt1'] = formaPadrao($vlr);
            }
            if ($chv == 'dt_fim')
            {
                $model['dt2'] = formaPadrao($vlr);
            }
            // quantidade de imagens na galeria
            if ($chv == 'galeria')
            {
                if (strlen($vlr) == 0)
                {
                    $model['quantGal'] = 0;
                }
                else
                {
                    $array             = explode('|', $vlr);
                    $model['quantGal'] = count($array);
                }
            }
            // trata horas
            if ($chv == 'hr_ini')
            {
                $model['hora1'] = substr($vlr, 0, 5);
            }
            if ($chv == 'hr_fim')
            {
                $model['hora2'] = substr($vlr, 0, 5);
            }
            // checkboxes dias da semana
            if ($chv == 'semana')
            {
                $model['cbSemana'] = $this->calendario_model->semanaCheckBox($vlr);
            }
            if ($chv == 'id')
            {
                // pega grupo com seus parentes, se houver
                $grupoParents          = $this->paginas_model->getGrupoParents($dd['grupo'], $dd['modulo_id']);
                $model['grupoParents'] = $grupoParents;

                $metas = $this->cms_metadados->getAllByContent($vlr);

                //                dd($metas);

                // subtítulo
                $model['subtitulo'] = get_meta($metas, 'subtitulo', null, true);

                // eixo temático
                $tema                   = $this->congresso->getTema(get_meta($metas, 'eixo_tematico', null, true));
                $model['eixo_tematico'] = $tema['title'];

                // modalidade
                $moda                = $this->congresso->getModalidade(get_meta($metas, 'modalidade', null, true));
                $model['modalidade'] = $moda['title'];
            }

            // coloca no array
            $model[$chv] = $vlr;
        }

        // save
        return $this->thisModel = $model;
    }


    /**
     * save authors metadata
     *
     * @param $contentId
     * @param $data
     * @return bool
     */
    public function saveAutores($contentId, $data)
    {
        $authorsData = serialize($data);

        $this->load->library('cms_metadados');

        $ret = $this->cms_metadados->saveByContent($contentId, array(
            'meta_key'   => 'authors',
            'meta_type'  => '',
            'meta_value' => $authorsData,
        ));

        return (bool)$ret;
    }


    /**
     * return the authors metadata as array
     *
     * @param $contentId
     * @return mixed|string
     */
    public function getAutoresFromTrabalhoId($contentId)
    {
        $this->load->library('cms_metadados');
        $ret = $this->cms_metadados->getByContent($contentId, 'authors', null, true);

        $aAuthors = unserialize($ret);
        if (!is_array($aAuthors))
        {
            return '';
        }

        return $aAuthors;
    }


    /**
     * establishes relationship between users and job
     * @param array $userIds
     * @param null $contentId Optional
     * @throws Exception
     */
    public function setAvaliadores($userIds = array(), $contentId = null)
    {
        if(! is_array($userIds))
        {
            throw new Exception("Os IDs dos avaliadores devem ser um array");
        }

        if (is_numeric($contentId))
        {
            $job = $this->find($contentId);
        }
        else if ($contentId === null && $this->thisModel !== null)
        {
            $job = $this->thisModel;
        }
        else
        {
            throw new Exception("Trabalho não encontrado com o ID {$contentId}");
        }


    }


    /**
     * return the appraisers of this job
     * @param null $contentId Optional
     * @throws Exception
     */
    public function getAvaliadores($contentId = null)
    {
        if (is_numeric($contentId))
        {
            $job = $this->find($contentId);
        }
        else if ($contentId === null && $this->thisModel !== null)
        {
            $job = $this->thisModel;
        }
        else
        {
            throw new Exception("Trabalho não encontrado com o ID {$contentId}");
        }



    }

}