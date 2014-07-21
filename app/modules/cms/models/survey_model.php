<?php

class Survey_model extends CI_Model
{

    /**
     * Configurações em cms/config/surveyConfig.php
     * @var array
     */
    public $configs = array();
    // todo questionário deve ter
    /**
     * ID of survey
     * @var int
     */
    public $surveyId;

    /**
     * Memória de todos os registros da survey (step, group, query)
     * @var array
     */
    public $structure;

    public function __construct()
    {
        parent::__construct();
        $this->config->load('cms/surveyConfig');
        $this->configs = $this->config->item('survey');
    }

    /* ----------------------------------------------------------
     * GETTERS SETTERS
     * ---------------------------------------------------------
     */

    /**
     * Retorna itens de configuração de 'surveys'
     * Por padrão retorna todos, ou pode usar a notação 'str.str' para
     * selecionar itens específicos no array de configurações.
     *
     * <code>
     * $config = $this->survey->getConfig('answer.binary');
     * </code>
     *
     * @param string $item
     * @return mixed
     */
    public function getConfig($item = null)
    {
        $c = $this->configs;

        if ($item !== null)
        {
            $a = explode('.', $item);
            for ($x = 0; $x < count($a); $x++)
            {
                // se não existir o índice, termina
                if (!isset($c[$a[$x]]))
                {
                    break;

                    return null;
                }
                $c = $c[$a[$x]];
                // no final
                if ($x === count($a) - 1)
                {
                    return $c;
                }
            }
        }
        else
        {
            return $c;
        }
    }

    /**
     * Retorna array de objetos com os passos de uma survey
     * @param int $surverId
     * @return boolean|array
     */
    public function getStepsFrom($surverId)
    {
        $query = $this->db->where('rel', $surverId)
            ->where('grupo', 0)
            ->where('tipo', 'survey_step')
            ->order_by('ordem, id')
            ->get('cms_conteudo');

        if ($query->num_rows() == 0)
        {
            return false;
        }

        return $query->result();
    }

    /**
     * Retorna array com todos os grupos de uma survey
     * @param int $surveyId
     */
    public function getGroupsFromSurvey($surveyId)
    {
        return $this->findGroups($surveyId, null);
    }

    /**
     * Retorna array com todos os grupos de um passo
     * @param int $stepId
     */
    public function getGroupsFromStep($stepId)
    {
        return $this->findGroups(null, $stepId);
    }

    /**
     * Retorna array com todas as perguntas de uma survey
     * @param int $surverId
     */
    public function getQueriesFromSurvey($surverId)
    {

    }

    /**
     * Retorna array com todas as perguntas de uma passo (step)
     * @param int $stepId
     */
    public function getQueriesFromStep($stepId)
    {

    }

    /**
     * Retorna array com todas as perguntas de um grupo
     * @param int $groupId
     */
    public function getQueriesFromGroup($groupId)
    {

    }

    /* ----------------------------------------------------------
     * CRUD para surveys
     * --------------------------------------------------------
     */

    /**
     * Retorna a listagem de questionários paginados
     * @return array
     */
    public function all($v, $modulo = array())
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

        // grupo e rel == 0 são características do quastionário
        $this->db->where('grupo', 0);
        $this->db->where('rel', 0);
        $this->db->where('tipo', 'survey');

        $this->db->where('lang', get_lang());
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
        $sql = $this->db->get('cms_conteudo');

        //        mybug($this->db->last_query());
        // -- pega o Total de registros -- //

        $query    = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $ttl_rows = $query->row()->Count;

        // paginação
        $this->load->library('pagination');
        $config['base_url']      = cms_url('cms/survey/index/co:' . $v['co'] . $uri_filters);
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
     * Cria espaço do questionário e retorna o ID.
     *
     * @param int $moduloId
     * @param array $data Normalmente o POST
     */
    public function create($moduloId, $data)
    {

        $dados['tipo']       = 'survey';
        $dados['grupo']      = 0; // "0, irrelevante para este obj"
        $dados['rel']        = 0; // "0, pois é o obj mais alto na hierarquia"
        $dados['titulo']     = $data['titulo'];
        $dados['dt_ini']     = formaSQL($data['dt1']);
        $dados['modulo_id']  = $moduloId;
        $dados['status']     = $data['status'];
        $dados['atualizado'] = date("Y-m-d H:i:s");
        $dados['autor']      = ($this->phpsess->get('admin_id', 'cms') === null) ? 1 : $this->phpsess->get('admin_id', 'cms');
        $dados['hr_ini']     = date("H:i:s");
        $dados['lang']       = get_lang();
        $dados['nick']       = $this->cms_libs->confirma_apelido('', $data['nick'], $dados);

        //        dd($dados);

        $sql    = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();
        // -- >> LOG << -- //
        $oque = "Novo Questionário: <a href=\"" . cms_url('cms/survey/edita/co:' . $moduloId . '/id:' . $esteid) . "\">" . $dados['titulo'] . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return $esteid;
    }

    /**
     * Retorna dados de um survey como array
     * @param int $id
     * @return array
     */
    public function retrieve($id)
    {
        $query  = $this->db->where('id', $id)->get('cms_conteudo');
        $survey = $query->row_array();

        $return = array();
        foreach ($survey as $chv => $vlr)
        {
            // date
            if ($chv == 'dt_ini')
            {
                $return['dt1'] = formaPadrao($vlr);
            }
            if ($chv == 'dt_fim')
            {
                $return['dt2'] = formaPadrao($vlr);
            }

            $return[$chv] = $vlr;
        }

        return $return;
    }

    /**
     * Atualiza dados da survey
     *
     * @param int $moduloId
     * @param int $id
     * @param array $data
     */
    public function update($moduloId, $id, $data)
    {
        //        dd($data);

        $dados['titulo']     = $data['titulo'];
        $dados['resumo']     = $data['resumo'];
        $dados['tags']       = $data['tags'];
        $dados['status']     = $data['status'];
        $dados['txt']        = $data['txt'];
        $dados['atualizado'] = date("Y-m-d H:i:s");
        //        $dados['prioridade'] = $data['prioridade'];
        // antes de salvar, salva última versão
        $this->cms_libs->save_revision($id);

        $nick = $this->input->post('nick_edita');

        $dados['nick']     = $this->cms_libs->confirma_apelido($id, $nick, null);
        $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => $id, 'nick' => $nick), null, $moduloId);
        $dados['txtmulti'] = $this->paginas_model->concatenateMultiContents();
        $dados['scripts']  = $scripts;

        $this->db->where('id', $id);
        $sql = $this->db->update('cms_conteudo', $dados);
        // -- >> LOG << -- //
        $oque = "Atualizou Questionário: <a href=\"" . cms_url('cms/survey/edita/co:' . $moduloId . '/id:' . $id) . "\">" . $dados['titulo'] . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        // faz atualização dos metadados
        $this->cms_libs->set_metadados($id);

        return ($sql) ? $id : false;
    }

    public function delete($id)
    {

    }

    /**
     * Retorna a quantidade de respostas válidas da survey
     * @param int $surveyId
     * @return int
     */
    public function isAnswered($surveyId)
    {
        $q = $this->db->where('conteudo_id', $surveyId)
            ->where('status', 1)
            ->get('cms_inscritos');

        return $q->num_rows();
    }

    /**
     * Retorna um objeto da survey com os objetos dependentes
     *
     * <code>
     * //
     * $struc = $this->survey->structure($is);
     * // passos
     * $steps = $struc->steps();
     * // questões de um passo
     * $querys = $steps[0]->querys();
     * // grupos de um passo
     * $groups = $steps[0]->groups();
     * // questões de um grupo
     * $groupsQ = $groups[0]->querys();
     * </code>
     *
     * @param type $id
     */
    public function getStructure($moduleId, $id)
    {
        $query = $this->db->where('modulo_id', $moduleId)
            ->where('rel', $id)
            //                ->where('status', 1)
            ->order_by('ordem, id')
            ->get('cms_conteudo');

        $this->structure = $query->result();

        return $this;
    }

    /**
     * Itera pela estrutura e retorna os passos
     * @return array
     */
    public function steps()
    {
        $steps = array();

        foreach ($this->structure as $c => $obj)
        {
            if ($obj->tipo == 'survey_step')
            {
                $obj->editUri = "cms/survey/editStep/co:{$obj->modulo_id}/id:{$obj->rel}/step:{$obj->id}";
                $steps[]      = $obj;
            }
        }

        return $steps;
    }

    /**
     * Itera pela estrutura e retorna os grupos relacionados ao ID
     * passado como argumento
     * @param int $stepId
     * @return array
     */
    public function groups($stepId)
    {
        $groups = array();

        foreach ($this->structure as $c => $obj)
        {
            if ($obj->tipo == 'survey_group' && $obj->grupo == $stepId)
            {
                $obj->editUri = "cms/survey/editGroup/co:{$obj->modulo_id}/id:{$obj->rel}/group:{$obj->id}";
                $groups[]     = $obj;
            }
        }

        return $groups;
    }

    /**
     * Itera pela estrutura e retorna as questões do grupo/passo com
     * o ID passado
     * @param int $parentId ID do step ou group
     * @return array
     */
    public function queries($parentId)
    {
        $queries = array();

        foreach ($this->structure as $c => $obj)
        {
            if ($obj->tipo == 'survey_query' && $obj->grupo == $parentId)
            {
                $obj->editUri = "cms/survey/editQuery/co:{$obj->modulo_id}/id:{$obj->rel}/query:{$obj->id}";
                $queries[]    = $obj;
            }
        }

        return $queries;
    }

    /**
     * Cria um passo dentro de um questionário
     * @param int $moduloId
     * @param int $surveyId
     * @param array $data
     */
    public function createStep($moduloId, $surveyId, $data)
    {

        $dados['tipo']       = 'survey_step';
        $dados['grupo']      = 0; // "hierarquia de perguntas/grupos. 0, pois é o nível mais alto"
        $dados['rel']        = $surveyId; // "ID do questionário survey_id"
        $dados['titulo']     = $data['titulo'];
        $dados['resumo']     = $data['resumo'];
        $dados['ordem']      = $data['ordem'];
        $dados['dt_ini']     = date("Y-m-d");
        $dados['modulo_id']  = $moduloId;
        $dados['status']     = 1;
        $dados['atualizado'] = date("Y-m-d H:i:s");
        $dados['autor']      = ($this->phpsess->get('admin_id', 'cms') === null) ? 1 : $this->phpsess->get('admin_id', 'cms');
        $dados['hr_ini']     = date("H:i:s");
        $dados['lang']       = get_lang();

        //        dd($dados);

        $sql    = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();
        // -- >> LOG << -- //
        $oque = "Novo Passo de Questionário: <a href=\"" . cms_url('cms/survey/editStep/co:' . $moduloId . '/id:' . $surveyId) . '/step:' . $esteid . "\">" . $dados['titulo'] . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return $esteid;
    }

    public function retrieveStep($id)
    {

    }

    /**
     * Atualiza dados de um passo
     *
     * @param int $id
     * @param array $data
     */
    public function updateStep($id, $data)
    {
        $step     = $this->retrieve($id);
        $surveyId = $step['rel'];
        $moduloId = $step['modulo_id'];

        $dados['titulo'] = $data['titulo'];
        $dados['resumo'] = $data['resumo'];
        $dados['ordem']  = $data['ordem'];

        //        dd($data);
        //        dd($step);

        $this->db->where('id', $id);
        $updated = $this->db->update('cms_conteudo', $dados);

        // -- >> LOG << -- //
        $oque = "Atualizou Passo de Questionário: <a href=\"" . cms_url('cms/survey/editStep/co:' . $moduloId . '/id:' . $surveyId . '/step:' . $id) . "\">" . $dados['titulo'] . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return ($updated) ? $id : false;
    }

    public function deleteStep($id)
    {

    }

    /**
     * Salva dados de um grupo, que está relacionado com um passo
     * @param type $moduloId
     * @param type $surveyId
     * @param type $stepId
     * @param type $data
     */
    public function createGroup($moduloId, $surveyId, $stepId = null, $data = array())
    {
        // se o $stepId não for passado, deve vir via POST
        if ($stepId === null)
        {
            $stepId = $this->input->post('survey_steps');
        }

        $dados['tipo']       = 'survey_group';
        $dados['grupo']      = $stepId; // "hierarquia de perguntas/grupos. ID do passo correspondente"
        $dados['rel']        = $surveyId; // "ID do questionário survey_id"
        $dados['modulo_id']  = $moduloId;
        $dados['titulo']     = $data['titulo'];
        $dados['resumo']     = $data['resumo'];
        $dados['ordem']      = $data['ordem'];
        $dados['dt_ini']     = date("Y-m-d");
        $dados['status']     = 1;
        $dados['atualizado'] = date("Y-m-d H:i:s");
        $dados['autor']      = ($this->phpsess->get('admin_id', 'cms') === null) ? 1 : $this->phpsess->get('admin_id', 'cms');
        $dados['hr_ini']     = date("H:i:s");
        $dados['lang']       = get_lang();

        //        dd($dados);

        $sql    = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();
        // -- >> LOG << -- //
        $oque = "Novo Questionário: <a href=\"" . cms_url('cms/survey/editGroup/co:' . $moduloId . '/id:' . $surveyId) . '/group:' . $esteid . "\">" . $dados['titulo'] . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return $esteid;
    }

    public function retrieveGroup($id)
    {

    }

    /**
     * Atualiza dados de um grupo de questão
     *
     * @param int $id
     * @param array $data
     * @return int
     */
    public function updateGroup($id, $data)
    {
        $group    = $this->retrieve($id);
        $surveyId = $group['rel'];
        $moduloId = $group['modulo_id'];

        $dados['grupo']  = $data['survey_steps'];
        $dados['titulo'] = $data['titulo'];
        $dados['resumo'] = $data['resumo'];
        $dados['ordem']  = $data['ordem'];

        //        dd($dados);
        //        dd($group);

        $this->db->where('id', $id);
        $updated = $this->db->update('cms_conteudo', $dados);

        // -- >> LOG << -- //
        $oque = "Atualizou Grupo de Questionário: <a href=\"" . cms_url('cms/survey/editGroup/co:' . $moduloId . '/id:' . $surveyId . '/group:' . $id) . "\">" . $dados['titulo'] . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return ($updated) ? $id : false;
    }

    public function deleteGroup($id)
    {

    }


    /**
     * Recebe dados e cria nova questão no banco
     * @param int $moduloId ID do módulo
     * @param int $surveyId ID da survey
     * @param array $data Outros dados importantes
     * @return int
     */
    public function createQuery($moduloId, $surveyId, $data)
    {
        // se o grupo for vazio, a questão pertence ao step
        $hyerarqui = (is_numeric($data['survey_groups'])) ? $data['survey_groups'] : $data['survey_steps'];

        $dados['tipo']       = 'survey_query';
        $dados['grupo']      = $hyerarqui; // "hierarquia de perguntas/grupos. ID do grupo/passo correspondente"
        $dados['rel']        = $surveyId; // "ID do questionário survey_id"
        $dados['modulo_id']  = $moduloId;
        $dados['tags']       = $data['survey_types'];
        $dados['titulo']     = $data['titulo'];
        $dados['resumo']     = $data['resumo'];
        $dados['ordem']      = $data['ordem'];
        $dados['dt_ini']     = date("Y-m-d");
        $dados['status']     = 1;
        $dados['atualizado'] = date("Y-m-d H:i:s");
        $dados['autor']      = ($this->phpsess->get('admin_id', 'cms') === null) ? 1 : $this->phpsess->get('admin_id', 'cms');
        $dados['hr_ini']     = date("H:i:s");
        $dados['lang']       = get_lang();

        //        dd($dados);

        $sql    = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();
        // -- >> LOG << -- //
        $oque = "Nova Questão: <a href=\"" . cms_url('cms/survey/editQuery/co:' . $moduloId . '/id:' . $surveyId) . '/query:' . $esteid . "\">" . $dados['titulo'] . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return $esteid;
    }


    /**
     * Retorna dados da questão combinado com o passo e grupo a que pertence
     * @param int $id
     * @return array
     */
    public function retrieveQuery($id)
    {
        $query     = $this->retrieve($id);
        $hierarchy = $this->getQueryStepGroup($query['grupo']);
        $opt       = array();

        if (substr($query['tags'], -7) == 'Options')
        {
            $opt['answer_options'] = $this->unserializeQueryOptions($query['txtmulti']);
        }

        return array_merge($query, $hierarchy, $opt);
    }

    /**
     * Atualiza dados da questão
     * @param int $id
     * @param array $data
     * @return int|bool
     */
    public function updateQuery($id, $data)
    {
        // se o grupo for vazio, a questão pertence ao step
        $hyerarqui = (is_numeric($data['survey_groups'])) ? $data['survey_groups'] : $data['survey_steps'];

        $query    = $this->retrieve($id);
        $surveyId = $query['rel'];
        $moduloId = $query['modulo_id'];

        $dados['grupo']    = $hyerarqui; // "hierarquia de perguntas/grupos. ID do grupo/passo correspondente"
        $dados['titulo']   = $data['titulo'];
        $dados['resumo']   = $data['resumo'];
        $dados['ordem']    = $data['ordem'];
        $dados['txtmulti'] = $this->serializeQueryOptions($this->parseQueryOptions($data, $query));

        //        dd($dados);
        //        dd($query);

        $this->db->where('id', $id);
        $updated = $this->db->update('cms_conteudo', $dados);

        // -- >> LOG << -- //
        $oque = "Atualizou Grupo de Questionário: <a href=\"" . cms_url('cms/survey/editQuery/co:' . $moduloId . '/id:' . $surveyId . '/query:' . $id) . "\">" . $dados['titulo'] . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return ($updated) ? $id : false;
    }

    public function deleteQuery($id)
    {

    }

    /**
     * retorna um array com o passo e grupo a que pertence.
     * Se o grupo não existir, retorna NULL
     *
     * <code>
     * $queryHierarchy = $this->survey->getQueryStepGroup($query['grupo']);
     *
     * // return
     * array(
     *  'step' => obj|null,
     *  'group' => obj|null
     * );
     * </code>
     * @param type $param
     * @return type
     */
    public function getQueryStepGroup($queryGroup)
    {
        $obj = $this->retrieve($queryGroup);

        if ($obj['tipo'] == 'survey_step')
        {
            $a['step']  = $obj;
            $a['group'] = null;
        }
        else if ($obj['tipo'] == 'survey_group')
        {
            $step       = $this->retrieve($obj['grupo']);
            $a['step']  = $step;
            $a['group'] = $obj;

        }

        return $a;
    }

    /* -------------------------------------------------------------
     * métodos 
     * ------------------------------------------------------------
     */

    /**
     * Recebe opções de resposta como string JSON e retorna um array
     * de índice numérico
     *
     * @param string $serialized
     * @return array|null
     */
    public function unserializeQueryOptions($serialized)
    {
        return json_decode($serialized);
    }

    /**
     * Recebe o array de opções e transforma em string JSON
     *
     * @param array $array
     * @return string
     */
    public function serializeQueryOptions($array)
    {
        return json_encode($array);
    }


    /**
     * Parseia dados vindo do form e combina com as opções já existentes.
     * @param array $data Geralmente dados via POST
     * @param array $queryObj Array da questão
     * @return array
     */
    public function parseQueryOptions($data, $queryObj)
    {

        $options = array();

        // percorre dados
        foreach ($data as $c => $v)
        {
            if (substr($c, 0, 6) == 'multi_' && strlen($v) > 1)
            {
                $options[] = $v;
            }
        }

        $oldVals = $this->unserializeQueryOptions($queryObj['txtmulti']);

        $n = array_merge(
            (!is_array($oldVals) ? array() : $oldVals),
            $options);

        return $n;
    }

    /**
     * Retorna um array de objetos com os grupos de uma survey ou step
     * @param int $surveyId ID da survey
     * @param int $stepId ID do step
     * @return boolean|array
     */
    public function findGroups($surveyId = null, $stepId = null)
    {
        if ($surveyId !== null)
        {
            $this->db->where('rel', $surveyId);
        }
        if ($stepId !== null)
        {
            $this->db->where('grupo', $stepId);
        }

        $this->db->where('tipo', 'survey_group');
        $this->db->order_by('grupo, ordem, id');
        $query = $this->db->get('cms_conteudo');

        if ($query->num_rows() == 0)
        {
            return false;
        }

        return $query->result();
    }

    /**
     * Gera combobox com as opções de tipo de pergunta
     * @param string $selected
     * @param string $attrbs
     * @param bool|array $firstOption
     * @return string
     */
    public function formQueryTypesCombo($selected = '', $attrbs = '', $firstOption = false)
    {
        $answers = $this->getConfig('answer');
        $opts    = array_keys($answers);
        $options = array();

        foreach ($opts as $o)
        {
            $options[$o] = $o;
        }

        return form_dropdown('survey_types', $options, $selected, $attrbs);
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
        //        $campos_usados[] = array('campo' => 'grupo', 'type' => 'text');
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

            // se existe comentários pesquisa a quantidade
            if (isset($modulo['comments']) && $modulo['comments'] == 1)
            {
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 1);
                $sqlA            = $this->db->get('cms_comentarios');
                $row['comm_ttl'] = $sqlA->num_rows();
                // novos
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 2);
                $sqlN            = $this->db->get('cms_comentarios');
                $row['comm_new'] = $sqlN->num_rows();
            }

            // se existe inscrição pesquisa a quantidade

            $this->db->where('conteudo_id', $row['id']);
            $this->db->where('status', 1);
            $this->db->select('id');
            $sqlA            = $this->db->get('cms_inscritos');
            $row['insc_ttl'] = $sqlA->num_rows();

            $saida[] = $row;
        }

        return $saida;
    }

}
