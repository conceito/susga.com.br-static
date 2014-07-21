<?php

class SurveyContainer_model extends CI_Model {
    
     public $id;
     public $self;


     public function __construct()
     {
         parent::__construct();
         $this->load->model('cms/surveyStep_model', 'surveyStep');
         $this->load->model('cms/surveyQuery_model', 'surveyQuery');
     }
     
//     public function __get($key)
//     {
//         parent::__get($key);
//         return (isset($this->self->$key)) ? $this->self->$key : null;
//     }

//    public function __get($key)
//    {
//        parent::__get($key);
//    }

     public function set($surveyId)
     {
         $q = $this->db->where('id', $surveyId)->get('cms_conteudo');
         $this->self = $q->row();
     }


     public function get($surveyId)
     {
         
         $this->id = $surveyId;
         $this->set($surveyId);
         return $this;
     }
     
     public function steps()
     {
         return $this->surveyStep->get($this);
     }
     
     
}