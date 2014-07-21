<?php

class SurveyQuery_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($surveyObj)
    {
        return $this->getQueries($surveyObj->self->modulo_id, $surveyObj->self->id);
    }

    public function getQueries($moduloId, $surveyId, $groupId = null)
    {
        $g = ($groupId) ? $groupId : 0;


        $query = $this->db->where('modulo_id', $moduloId)
                ->where('rel', $surveyId)
                ->where('grupo', $g)
                ->where('tipo', 'survey_query')
                ->order_by('ordem, id')
                ->get('cms_conteudo');

        return $query->result();
    }
    
    public function findAllByStep($stepObj)
    {
        $query = $this->db->where('modulo_id', $stepObj->modulo_id)
                ->where('rel', $stepObj->rel)
                ->where('grupo', $stepObj->id)
                ->where('tipo', 'survey_query')
                ->order_by('ordem, id')
                ->get('cms_conteudo');

        return $query->result();
    }

}