<?php

/**
 * Class SurveyGraph
 *
 * receive ajax requests to return the graph of survey queries
 */
class SurveyGraph extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('cms/survey_model', 'survey');
        $this->load->library(array('cms/surveyquery'));
        $this->load->helper(array('cmshelper'));
    }

    public function index()
    {
        $var = $this->uri->to_array(array('queryId', 'type'));

        $query = $this->survey->retrieve($var['queryId']);

        echo $this->surveyquery->render($query);

    }
}