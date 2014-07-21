<?php

class Questionarios extends Frontend_Controller
{

    private $msg_type;
    private $msg;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('cms_survey');
        
        $this->msg_type = $this->phpsess->flashget('msg_type');
        $this->msg = $this->phpsess->flashget('msg');

        Console::log($this->phpsess->get());
    }

    /**
     * Form com login de acesso
     * @param string $surveySlug
     */
    public function index($surveySlug = '')
    {
        $survey = $this->cms_survey->get($surveySlug);
        if (!$survey)
        {
            echo 'Questionário não identificado.';
            exit;
        }

        $view['msg_type'] = $this->msg_type;
        $view['msg'] = $this->msg;
        $view['survey'] = $survey;


        $this->title = $survey->titulo . ' &gt; Questionário';
        $this->corpo = $this->load->view('site_add/survey_login', $view, true);

        $this->templateRender();
    }

    /**
     * Exibe conteúdo 'txt' da survey com botão para iniciar respostas
     * @param string $surveySlug
     */
    public function intro($surveySlug = '')
    {
        if (!$this->cms_survey->isValid())
        {
            $this->phpsess->flashsave('msg_type', 'error');
            $this->phpsess->flashsave('msg', 'Código não é mais válido.');
            redirect("questionarios/index/{$surveySlug}");
        }

        $survey = $this->cms_survey->get($surveySlug);
        if (!$survey)
        {
            echo 'Questionário não identificado.';
            exit;
        }


        $view['survey'] = $survey;


        $this->title = $survey->titulo . ' &gt; Questionário';
        $this->corpo = $this->load->view('site_add/survey_intro', $view, true);

        $this->templateRender();
    }

    /**
     * Exibe cada passo
     * 
     * @param string $surveySlug
     * @param int $step
     */
    public function passo($surveySlug = '', $step = null)
    {
        if (!$this->cms_survey->isValid() || $step === null)
        {
            $this->phpsess->flashsave('msg_type', 'error');
            $this->phpsess->flashsave('msg', 'Código não é mais válido.');
            redirect("questionarios/index/{$surveySlug}");
        }
        if ($this->cms_survey->getValidStep() != $step)
        {
            $ns = $this->cms_survey->getValidStep();
            if ($ns == 'fim')
            {
                $this->phpsess->flashsave('msg_type', 'alert');
                $this->phpsess->flashsave('msg', 'Você já chegou ao fim do questionário.');
                redirect("questionarios/fim/{$surveySlug}");
            }
            else
            {
                $this->phpsess->flashsave('msg_type', 'alert');
                $this->phpsess->flashsave('msg', 'Este passo ainda não foi respondido completamente.');
                redirect("questionarios/passo/{$surveySlug}/{$ns}");
            }
        }

        $survey = $this->cms_survey->get($surveySlug);
        if (!$survey)
        {
            $this->phpsess->flashsave('msg_type', 'error');
            $this->phpsess->flashsave('msg', 'Questionário não identificado.');
            redirect("questionarios/index/{$surveySlug}");
           
        }

        $view['msg_type'] = $this->msg_type;
        $view['msg'] = $this->msg;
        $view['survey'] = $survey;
        $view['progress'] = $this->cms_survey->getProgress();
        $view['step'] = $this->cms_survey->getStep($step);
        // salva objetos e retorna instancia de cms_survey
        $view['stepStructure'] = $this->cms_survey->stepStructure($view['step']->id);


        $this->title = $survey->titulo . ' &gt; Questionário';
        $this->corpo = $this->load->view('site_add/survey_passo', $view, true);

        $this->templateRender();
    }

    /**
     * Exibe tela de agradecimento
     * @param type $surveySlug
     */
    public function fim($surveySlug = '')
    {
        if ($this->cms_survey->getValidStep() != 'fim')
        {
            $ns = $this->cms_survey->getValidStep();
            $this->phpsess->flashsave('msg_type', 'error');
            $this->phpsess->flashsave('msg', 'Este passo ainda não foi respondido completamente.');
            redirect("questionarios/passo/{$surveySlug}/{$ns}");
        }
        
        $this->cms_survey->validateAnswers();


        $survey = $this->cms_survey->get($surveySlug);
        if (!$survey)
        {
            $this->phpsess->flashsave('msg_type', 'error');
            $this->phpsess->flashsave('msg', 'Questionário não identificado.');
            redirect("questionarios/index/{$surveySlug}");
        }

        $view['msg_type'] = $this->msg_type;
        $view['msg'] = $this->msg;
        $view['survey'] = $survey;


        $this->title = $survey->titulo . ' &gt; Questionário';
        $this->corpo = $this->load->view('site_add/survey_fim', $view, true);

        $this->templateRender();
    }

    /**
     * Recebe requisição POST para validar código
     */
    public function postValidateCode()
    {
        $slug = $this->input->post('survey_slug');
        $surveyId = $this->input->post('survey_id');
        $ret = $this->cms_survey->validateCode($this->input->post('code'), $surveyId);
        $error = $ret['error'];
        $msg = $ret['msg'];

        if ($error)
        {
            $this->phpsess->flashsave('msg_type', 'error');
            $this->phpsess->flashsave('msg', $msg);
            redirect("questionarios/index/{$slug}");
        }
        else
        {
            $this->phpsess->flashsave('msg_type', 'success');
            $this->phpsess->flashsave('msg', $msg);
            redirect("questionarios/intro/{$slug}");
        }
    }

    /**
     * Limpa sessão
     */
    public function logout($s = '')
    {
        $this->cms_survey->unsetCode();
        redirect('questionarios/index/' . $s);
    }

    /**
     * Recebe requisição POST com resposta do step
     * 
     * @param int $surveyId
     * @param int $step
     */
    public function postStep($surveyId, $step)
    {

        $slug = $this->input->post('survey_slug');
        $ret = $this->cms_survey->saveStepAnswers($surveyId, $step, $this->input->post());

        $error = $ret['error'];
        $msg = $ret['msg'];
        $nextStep = $ret['nextStep'];

        if ($error)
        {
            $this->phpsess->flashsave('msg_type', 'error');
            $this->phpsess->flashsave('msg', $msg);
            redirect("questionarios/passo/{$slug}/{$step}");
        }
        else
        {

            // salva o passo que usuário pode submeter
            $this->phpsess->save('survey_valid_step', $nextStep);

            if ($nextStep == 'fim')
            {
                redirect("questionarios/fim/{$slug}");
            }
            else
            {
                // redireciona para próximo passo
                redirect("questionarios/passo/{$slug}/{$nextStep}");
            }
        }
    }

}