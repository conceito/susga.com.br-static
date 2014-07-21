<?php

class Test_cms_survey_model extends CodeIgniterUnitTestCase
{

    public function __construct()
    {
        parent::__construct('cms/survey_model');
//        session_start();
        $this->load->library('cms/cms_libs');
        $this->load->model('cms/survey_model', 'survey');
    }
    
    public function setUp()
    {        
//        Mock::generate('Phpsess', 'MockPhpsess');
//        $this->phpsess = new MockPhpsess();
//        $this->phpsess->returns('get', 1);
    }

    public function test_getAllConfigs()
    {
        $c = $this->survey->getConfig();

        $this->assertIsA($c, 'array');
    }

    public function test_getConfigAnswers()
    {
        $c = $this->survey->getConfig('answer');

        $this->assertEqual($c['binary'][0], 'Não');
        $this->assertEqual($c['5levels'][5], 'Excelente');
        $this->assertEqual($c['5and1'][2], 'Ruim');
        $this->assertEqual(count($c['range10']), 11);
        $this->assertIsA($c['singleOptions'], 'array');
        $this->assertIsA($c['multiOptions'], 'array');
    }

    public function test_getConfigEspecificItem()
    {
        $c = $this->survey->getConfig('answer.binary');
        $this->assertIsA($c, 'array');
        $this->assertEqual($c[1], 'Sim');
    }

    public function test_getConfigNonExistin()
    {
        $c = $this->survey->getConfig('answer.blowblow');
        $this->assertIsA($c, 'null');
    }

    /**
     * CRUD
     */
//    public function test_createSurvey()
//    {
//        $rand = rand(000, 999);
//        $moduloId = 62; // default
//        $data = array(
//            'conteudo_id' => '',
//            'titulo' => 'minha questão ' . $rand,
//            'nick' => 'minha-questao-'.$rand,
//            'dt1' => date("d/m/Y"),
//            'status' => '2');
//        
//        $ret = $this->survey->create($moduloId, $data);
//        
//        $this->assertIsA($ret, 'number');
//    }

}