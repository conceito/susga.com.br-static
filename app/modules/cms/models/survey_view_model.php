<?php

class Survey_view_model extends CI_Model
{

    /**
     * ID da survey para consultas
     * @var int
     */
    public $surveyId;
    /**
     * Memória de todos os registros da survey (step, group, query)
     * @var array
     */
    public $structure;
    /**
     * save the last total count query
     * @var int
     */
    protected $totalRows = 0;

    /**
     * items per page
     * @var int
     */
    protected $perPage = 10;
    /**
     * days behind
     * @var int
     */
    protected $chartTimeBefore = 180;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getChartTimeBefore()
    {
        return $this->chartTimeBefore;
    }

    /**
     * @param int $chartTimeBefore
     */
    public function setChartTimeBefore($chartTimeBefore)
    {
        $this->chartTimeBefore = $chartTimeBefore;
    }

    /**
     * @return int
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * @param int $totalRows
     */
    public function setTotalRows($totalRows)
    {
        $this->totalRows = $totalRows;
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
     * @return $this
     */
    public function getStructure($id)
    {
        $this->surveyId = $id;

        $query = $this->db->where('rel', $id)
            ->order_by('ordem, id')
            ->select('id, ordem, titulo, resumo, txtmulti, tags, grupo, rel, modulo_id, tipo')
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
                $steps[] = $obj;
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
                $groups[] = $obj;
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
                $queries[] = $obj;
            }
        }

        return $queries;
    }

    /**
     * return the answers about a specific query
     *
     * @param $queryId
     * @return bool
     */
    public function getAnswersById($queryId)
    {
        //        SELECT insc.user_id, res.valor, res.data FROM `cms_conteudo_rel` as res
        //inner join cms_inscritos as insc ON insc.id = res.parent_id
        //WHERE insc.status = 1 AND res.conteudo_id = 215

        $v = $this->uri->to_array('pag');
        if ($v['pag'] == '')
        {
            $offset = 0;
        }
        else
        {
            $offset = ($v['pag'] - 1) * $this->getPerPage();
        }

        //        d($offset);

        $this->db->from('cms_conteudo_rel as res');
        $this->db->join('cms_inscritos as insc', 'insc.id = res.parent_id', 'inner');
        $this->db->where('insc.status', 1);
        $this->db->where('res.conteudo_id', $queryId);
        $this->db->select('SQL_CALC_FOUND_ROWS insc.user_id, res.valor, res.data', false);
        $this->db->order_by('res.data DESC');
        $this->db->limit($this->getPerPage(), $offset);
        $query = $this->db->get();

        if ($query->num_rows() == 0)
        {
            return false;
        }

        $pagQuery = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $this->setTotalRows($pagQuery->row()->Count);

        return $query->result_array();
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @param $surveyId
     * @return mixed
     */
    public function getAnswersByTime($surveyId)
    {
        $dtInit = voltaData(date("d/m/Y"), $this->getChartTimeBefore());

        $sql = "SELECT data, count(*) as por_dia
        FROM cms_inscritos as insc
        WHERE conteudo_id = {$surveyId} AND status = 1 AND data > '{$dtInit}'
        GROUP BY data";

//        dd($sql);

        $queryComplete = $this->db->query($sql);

        $sql = "SELECT data, count(*) as por_dia
        FROM cms_inscritos as insc
        WHERE conteudo_id = {$surveyId} AND status = 2 AND data > '{$dtInit}'
        GROUP BY data";

        $queryUncomplete = $this->db->query($sql);


        return array(
            'complete' => $queryComplete->result_array(),
            'uncomplete' => $queryUncomplete->result_array()
        );
    }

}