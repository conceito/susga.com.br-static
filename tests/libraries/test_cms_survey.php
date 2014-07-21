<?php

class Test_cms_survey  extends CodeIgniterUnitTestCase{
    
    public function __construct()
    {
        parent::__construct('libraries/cms_survey.php');
        $this->load->library('cms_survey');
    }
    
    public function test_setSurveyId()
    {
        $c = $this->cms_survey->getSurveyId();
        $this->assertEqual($c, NULL);
        $this->cms_survey->setSurveyId(123);
        $c = $this->cms_survey->getSurveyId();
        $this->assertEqual($c, 123);
    }
    
    public function test_generateStepsComboBox()
    {
        $this->cms_survey->setSurveyId(160);
        $combo = $this->cms_survey->formStepsCombo();
        $this->assertEqual(substr($combo, 0, 28), '<select name="survey_steps">');
    }
    
}