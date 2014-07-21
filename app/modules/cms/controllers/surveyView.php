<?php

class SurveyView extends Cms_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('cms/survey_model', 'survey');
        $this->load->model('cms/survey_view_model', 'surveyView');
        $this->load->library(array('cms/surveygraph', 'cms_survey'));

        $this->surveygraph->setController($this);

        /*
         * Permite separar os arquivos de VIEW para personalizalos
         */
        $this->viewFolder = "survey_view/"; // ao preencher usar "/" após a string
    }

    /**
     * Exibição inicial.
     * - grafico de respostas
     * - botões de filtros personalizados
     * @param type $surveyId
     */
    public function index($surveyId = null)
    {
        $this->load->library('cms/highcharts');

        $survey = $this->survey->retrieve($surveyId);
        $stats  = $this->surveyView->getAnswersByTime($surveyId);


        $this->highcharts->setKeys(array('data', 'por_dia'));
        $this->highcharts->setCategory('data');
        $chart = $this->highcharts->make($stats);
//        dd($chart);


        $this->json_vars('chart', array(
            'series'   => $chart['series'],
            'categories' => $chart['categories'],
            'days' => $this->surveyView->getChartTimeBefore()
        ));


        //                dd($data);
        //        dd($surveyStructure);

        $this->surveygraph->setSurvey($survey);
        $this->surveygraph->setData($stats);

        $this->surveygraph->title = "Relatório";

        $this->json_vars(null, array('c' => 'v'));

        //        dd($this->json_vars);

        $this->surveygraph->renderIndexPanel();
    }

    /**
     *
     * @param type $surveyId
     */
    public function sheet($surveyId = null)
    {
        $survey = $this->survey->retrieve($surveyId);

        $surveyStructure = $this->surveyView->getStructure($surveyId);
        //        $surveyStructure = $this->surveyView->getOrderedStructure($surveyId);

        //        $st = $surveyStructure->steps();
        //                dd($survey);
        //        dd($surveyStructure->queries(162));
        //        dd($answers);

        $this->surveygraph->setSurvey($survey);
        $this->surveygraph->setStructure($surveyStructure);

        $this->surveygraph->title = "Relatório";

        $this->surveygraph->renderSheetPanel();
    }

    /**
     * show panel with a list of text answers
     *
     * @param $surveyId
     * @param $queryId
     */
    public function query($surveyId, $queryId)
    {

        $survey = $this->survey->retrieve($surveyId);

        //        $surveyStructure = $this->surveyView->getStructure($surveyId);
        //        $surveyStructure = $this->surveyView->getOrderedStructure($surveyId);
        $query = $this->survey->retrieveQuery($queryId);

        $answers = $this->surveyView->getAnswersById($queryId);

        //        $st = $surveyStructure->steps();
        //        dd($survey);
        //        dd($surveyStructure->queries(162));
        //                dd($this->surveyView->getTotalRows());

        $this->surveygraph->setSurvey($survey);
        $this->surveygraph->setQuery($query);
        $this->surveygraph->setData($answers);
        $this->surveygraph->setPagination("cms/surveyView/query/{$surveyId}/{$queryId}",
            $this->surveyView->getTotalRows(), $this->surveyView->getPerPage());

        $this->surveygraph->title = "Relatório";

        $this->surveygraph->renderQueryPanel();
    }
}