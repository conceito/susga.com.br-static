<?php

class Test_contato_model extends CodeIgniterUnitTestCase
{

    public function __construct()
    {
        parent::__construct('Model Contato');
        $this->load->model('contato_model', 'contato');
    }

    public function test_debugModeTrue()
    {
        $ret = $this->contato->debugMode('[debug] Nome');

        $this->assertEqual($ret, true);
    }

    public function test_debugModeFalse()
    {
        $ret = $this->contato->debugMode('Nome');

        $this->assertEqual($ret, false);
    }

}