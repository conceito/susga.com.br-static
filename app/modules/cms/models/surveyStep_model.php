<?php

class SurveyStep_model extends CI_Model
{
    /**
     * memória dos passos retornados
     * @var type 
     */
    protected $steps;

    public function __construct()
    {
        parent::__construct();
    }

    public function get($surveyObj)
    {
        $this->steps = $this->getSteps($surveyObj->self->modulo_id, $surveyObj->self->id);
        return $this->steps;
    }

    public function getSteps($moduloId, $surveyId)
    {
        $query = $this->db->where('modulo_id', $moduloId)
                ->where('rel', $surveyId)
                ->where('grupo', 0)
                ->where('tipo', 'survey_step')
                ->order_by('ordem, id')
                ->get('cms_conteudo');

        return $query->result();
    }

    public function queries()
    {
        if(empty($this->steps))
        {
            return NULL;
        }
        
        $q = array();
        // percorre os passos
        foreach ($this->steps as $step)
        {
            $q[] = $this->surveyQuery->findAllByStep($step);
        }
        return $q;
    }
    
    public function groups()
    {
        
    }

}