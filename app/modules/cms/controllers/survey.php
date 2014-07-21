<?php

/**
 * Módulo de gerenciamento de questionários
 * A variavel 'co' deverá sempre estar presente.
 */
class Survey extends Cms_Controller
{

    public function __construct()
    {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model('cms/survey_model', 'survey');
        $this->load->model(array('cms/admin_model', 'cms/posts_model'));
        $this->load->library('cms_survey');

        /*
         * Permite separar os arquivos de VIEW para personalizalos
         */
        $this->viewFolder = "survey/"; // ao preencher usar "/" após a string
    }

    /**
     * Listagem dos questionários
     */
    public function index()
    {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Lista de ' . $modulo['label'];
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->load->helper('string');


        /*
         * ASSETS
         */
        $this->jquery = array('jquery.tablednd', 'ui.autocomplete.182');
        $this->cmsJS = array('listas');
        $this->css = array();
        $this->setNewPlugin(array('nyromodal', 'datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'check' => '',
            'apagar' => '#',
            'novo' => 'novo/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array(
            'sortable' => $modulo['ordenavel'],
            'destaque' => $modulo['destaques'],
            'comments' => $modulo['comments'],
            'inscricao' => $modulo['inscricao'],
            'email' => 0);
        $dados['listOptions'] = $listOptions;

        /*
         * PROCESSA INFORMAÇÕES
         */
        $saida = $this->survey->all($this->var, $modulo); // lista do conteúdo
//        dd($saida['rows'], true);
        $dados['rows'] = $saida['rows']; // dados de conteúdo
        $dados['linkEditar'] = 'cms/survey/edita/co:' . $this->var['co'];
        $dados['linkFilter'] = 'cms/survey/index/co:' . $this->var['co'];

        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/' . $this->viewFolder . 'conteudo_lista', $dados, true);
        $this->templateRender();
    }

    /**
     * Abre form para criar novo questionário
     * @param type $_var
     */
    function novo($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Novo ' . $modulo['label'];

//        mybug($this->modulo);


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
        $this->css = array();
        $this->setNewPlugin(array('datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'index/co:' . $this->var['co'],
            'continuar' => 'salva/co:' . $this->var['co'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);

        /*
         * PROCESSA
         */
//        // combo de conteudos relacionados
//        $dados['rel'] = $this->cms_libs->combo_relacionados($this->modulo);
        $dados['listOptions'] = $listOptions;
        $this->getDadosConteudo();

        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c; // uri segments
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Dados básicos';
        $this->tabs['tab_contt'][] = $this->load->view('cms/' . $this->viewFolder . 'conteudo_novo', $this->dados, true);


        /*
         *  TEMPLATE
         */

        $this->templateRender();
    }

    // -------------------------------------------------------------------------
    /**
     * Recebe requisição POST
     * Salva e atualiza dados do conteúdo
     */
    function salva()
    {

        // loading...
        $this->load->library(array('form_validation'));

        // uri vars
        $var = $this->uri->to_array(array('id', 'op', 'co', 'tab'));
        $id = $var['id'];
        $co = $var['co'];
        $tab = $var['tab'];

        // obrigatório
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
        if ($id == '')
        {
            $this->form_validation->set_rules('nick', 'Endereço amigável', 'trim|required|min_length[3]');
        }

        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {
            if ($id == '')
            {
                $this->novo('tip:faltaCampos');
            }
            else
            {
                $this->edita('id:' . $id . '/tip:faltaCampos'); // editando
            }
        }
        // OK VÁLIDO
        else
        {

            if ($id === '')
            {
                $ret = $this->survey->create($co, $this->input->post());
            }
            else
            {
                $ret = $this->survey->update($co, $id, $this->input->post());
            }

//            $this->cms_libs->extrasSalva($var['co'], $var['id']);

            if ($ret)
            {
                redirect('cms/survey/edita/id:' . $ret . '/co:' . $co . '/tip:ok/tab:' . $tab);
            }
            else
            {
                redirect('cms/survey/index/tip:erro/co:' . $co . '/tab:' . $tab);
            }
        }
    }

    /**
     * Abre form para edição do conteúdo
     *
     * @param string $_var Opcional
     */
    function edita($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->survey->retrieve($this->var['id']); // dados deste conteúdo reprocessados
        $this->title = 'Editando ' . $modulo['label'];
//dd($conteudo);
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'tinymce', 'nyromodal', 'chosen'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
//            'duplicar' => 'duplicar/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id']
        );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/posts/edita/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = $listOptions;
        $this->dados['swfUplForm'] = $this->setArqvs() . $this->getSwfUplForm();
        /*
         * PROCESSA INFORMAÇÕES
         */
        // pega os dados deste item
        $this->dados['row'] = $conteudo; // dados de conteúdo
        $this->dados['metas'] = $this->posts_model->getPostMetas($this->var);
//        mybug($this->dados['rel']);

        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;

        $this->dados['addStepLink'] = 'cms/survey/addStep/co:' . $modulo['id'] . '/id:' . $conteudo['id'];
        $this->dados['addGroupLink'] = 'cms/survey/addGroup/co:' . $modulo['id'] . '/id:' . $conteudo['id'];
        $this->dados['addQueryLink'] = 'cms/survey/addQuery/co:' . $modulo['id'] . '/id:' . $conteudo['id'];
        

        $this->dados['structure'] = $this->survey->getStructure($this->var['co'], $this->var['id']);
//        dd( $this->dados['structure'] );
//        dd( $this->dados['structure']->steps() );
//        dd( $s->groups(161) );
//        dd( $s->queries(161) );

        /**
         * TABS
         */
        $this->tabs['tab_title'][] = 'Estrutura';
        $this->tabs['tab_contt'][] = $this->load->view('cms/' . $this->viewFolder . 'estrutura', $this->dados, true);
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/' . $this->viewFolder . 'conteudo_edita', $this->dados, true);

        //$this->setMultiContent();
        //$this->setCamposExtra();
//        $this->setGaleria();
        //$this->setComentarios();
        //$this->setInscricoes();
        $this->setMetadados();


        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    /*
     * ---------------------------------------------------------------------
     * Métodos para passos (steps)
     * ---------------------------------------------------------------------
     */

    /**
     * Abre form para adicionar um passo novo
     */
    public function addStep()
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('id', 'co'));
//        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo

        $survey = $this->survey->retrieve($this->var['id']);

        $this->title = 'Novo passo para: ' . $survey['titulo'];

//        mybug($this->modulo);


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
        $this->css = array();
//        $this->setNewPlugin(array('datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'edita/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'continuar' => 'saveStep/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);

        /*
         * PROCESSA
         */
//        // combo de conteudos relacionados
//        $dados['rel'] = $this->cms_libs->combo_relacionados($this->modulo);
        $this->dados['listOptions'] = $listOptions;
//        $this->getDadosConteudo();

        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c; // uri segments
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Novo passo';
        $this->tabs['tab_contt'][] = $this->load->view('cms/' . $this->viewFolder . 'step_novo', $this->dados, true);


        /*
         *  TEMPLATE
         */

        $this->templateRender();
    }

    /**
     * recebe POST para criar ou atualizar um passo
     */
    public function saveStep()
    {
        // loading...
        $this->load->library(array('form_validation'));

        // uri vars
        $var = $this->uri->to_array(array('id', 'step', 'op', 'co', 'tab'));
        $id = $var['id'];// surveyId
        $co = $var['co'];
        $tab = $var['tab'];
        $step = $var['step'];

        // obrigatório
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
    

        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {
            if ($step == '')
            {
                $this->addStep('tip:faltaCampos');
            }
            else
            {
                $this->editStep('co:'. $co .'/id:'. $id .'/step:'. $step .'/tip:faltaCampos'); // editando
            }
        }
        // OK VÁLIDO
        else
        {

            // novo
            if ($step === '')
            {
                $ret = $this->survey->createStep($co, $id, $this->input->post());

                if ($ret)
                {
                    redirect('cms/survey/edita/id:'. $id .'/co:'. $co .'/tip:ok/tab:' . $tab);
                }
                else // erro
                {
                    redirect('cms/survey/edita/tip:erro/co:'. $co .'/id:'.$id.'/tab:'. $tab);
                }
            }
            else
            {
                $ret = $this->survey->updateStep($step, $this->input->post());
                
                if ($ret)
                {
                    redirect('cms/survey/edita/tip:ok/co:' . $co . '/id:' . $id . '/tab:' . $tab);
                }
                else // erro
                {
                    redirect('cms/survey/edita/tip:erro/co:'. $co .'/id:'.$id.'/tab:'. $tab);
                }
            }
        }
    }

    
    /**
     * Form para editar dados do passo
     */
    public function editStep()
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'step', 'tab'));
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->title = 'Editando passo para: ' . $this->modulo['label'];
               
        $step = $this->survey->retrieve($this->var['step']);


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
        $this->css = array();
        $this->setNewPlugin(array('tinymce', 'nyromodal', 'chosen'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'edita/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'salvar' => 'saveStep/co:' . $this->var['co'] . '/id:' . $this->var['id'] .'/step:'. $this->var['step']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/survey/editStep/co:'.$this->var['co'].'/id:'.$this->var['id'] .'/step:'. $this->var['step'];
        $this->dados['listOptions'] = $listOptions;
        
        /*
         * PROCESSA INFORMAÇÕES
         */
        $this->dados['survey'] = $this->survey->isAnswered($this->var['id']);
        // pega os dados deste item
        $this->dados['row'] = $step; // dados de conteúdo        
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $this->modulo;
        $this->dados['c'] = $this->c;
        
//        dd($step);
        
        /**
         * TABS
         */
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/'.$this->viewFolder.'step_edita', $this->dados, true);

//        $this->setMultiContent();
//        $this->setCamposExtra();
//        $this->setGaleria();
//        $this->setComentarios();
//        $this->setInscricoes();
//        $this->setMetadados();
        

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    /*
     * ---------------------------------------------------------------------
     * Métodos para grupos
     * ---------------------------------------------------------------------
     */

    public function addGroup()
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('id', 'co'));
//        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo

        $survey = $this->survey->retrieve($this->var['id']);

        $this->title = 'Novo grupo de perguntas para: ' . $survey['titulo'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
        $this->css = array();
//        $this->setNewPlugin(array('datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'edita/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'continuar' => 'saveGroup/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);

        /*
         * PROCESSA
         */
        $this->cms_survey->setSurveyId($this->var['id']);
//        dd($this->cms_survey->formStepsCombo());
        
        $this->dados['listOptions'] = $listOptions;
        $this->dados['comboSteps'] = $this->cms_survey->formStepsCombo(null, 'class="input-combo"');
//        $this->getDadosConteudo();

        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c; // uri segments
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Novo grupo de perguntas';
        $this->tabs['tab_contt'][] = $this->load->view('cms/' . $this->viewFolder . 'group_novo', $this->dados, true);


        /*
         *  TEMPLATE
         */

        $this->templateRender();
    }

    public function saveGroup()
    {
        // loading...
        $this->load->library(array('form_validation'));

        // uri vars
        $var = $this->uri->to_array(array('id', 'group', 'op', 'co', 'tab'));
        $id = $var['id'];// surveyId
        $co = $var['co'];
        $tab = $var['tab'];
        $group = $var['group'];

        // obrigatório
        $this->form_validation->set_rules('survey_steps', 'Passo', 'trim|required');
        $this->form_validation->set_rules('titulo', 'Nome do grupo', 'trim|required|min_length[3]');
    

        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {
            if ($group == '')
            {
                $this->addGroup('tip:faltaCampos');
            }
            else
            {
                $this->editGroup('co:'. $co .'/id:'. $id .'/group:'. $group .'/tip:faltaCampos'); // editando
            }
        }
        // OK VÁLIDO
        else
        {

            // novo
            if ($group === '')
            {
                $ret = $this->survey->createGroup($co, $id, null, $this->input->post());

                if ($ret)
                {
                    redirect('cms/survey/edita/id:'. $id .'/co:'. $co .'/tip:ok/tab:' . $tab);
                }
                else // erro
                {
                    redirect('cms/survey/edita/tip:erro/co:'. $co .'/id:'.$id.'/tab:'. $tab);
                }
            }
            else
            {
                $ret = $this->survey->updateGroup($group, $this->input->post());
                
                if ($ret)
                {
                    redirect('cms/survey/edita/tip:ok/id:' . $id . '/co:' . $co . '/tab:' . $tab);
                }
                else // erro
                {
                    redirect('cms/survey/edita/tip:erro/co:'. $co .'/id:'.$id.'/tab:'. $tab);
                }
            }
        }
    }

    /**
     * Form para edição do grupos de questões
     */
    public function editGroup()
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'group', 'tab'));
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->title = 'Editando grupo para: ' . $this->modulo['label'];
               
        $group = $this->survey->retrieve($this->var['group']);
//        dd($group);


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
        $this->css = array();
        $this->setNewPlugin(array('tinymce', 'nyromodal', 'chosen'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'edita/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'salvar' => 'saveGroup/co:' . $this->var['co'] . '/id:' . $this->var['id'] .'/group:'. $this->var['group']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/survey/editGroup/co:'.$this->var['co'].'/id:'.$this->var['id'] .'/group:'. $this->var['group'];
        $this->dados['listOptions'] = $listOptions;
        
        $this->cms_survey->setSurveyId($group['rel']);
        $this->dados['comboSteps'] = $this->cms_survey->formStepsCombo($group['grupo'], 'class="input-combo"');
        
        /*
         * PROCESSA INFORMAÇÕES
         */
        $this->dados['survey'] = $this->survey->isAnswered($this->var['id']);
        // pega os dados deste item
        $this->dados['row'] = $group; // dados de conteúdo        
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $this->modulo;
        $this->dados['c'] = $this->c;
        
        
        /**
         * TABS
         */
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/'.$this->viewFolder.'group_edita', $this->dados, true);

//        $this->setMultiContent();
//        $this->setCamposExtra();
//        $this->setGaleria();
//        $this->setComentarios();
//        $this->setInscricoes();
//        $this->setMetadados();
        

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    /*
     * ---------------------------------------------------------------------
     * Métodos para questões
     * ---------------------------------------------------------------------
     */

    /**
     * Form para criação de uma pergunta
     */
    public function addQuery()
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('id', 'co'));
//        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo

        $survey = $this->survey->retrieve($this->var['id']);

        $this->title = 'Nova pergunta para: ' . $survey['titulo'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'survey');
        $this->css = array();
//        $this->setNewPlugin(array('datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'edita/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'continuar' => 'saveQuery/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);

        /*
         * PROCESSA
         */
        $this->dados['listOptions'] = $listOptions;
        
        $this->cms_survey->setSurveyId($this->var['id']);        
        $this->dados['comboSteps'] = $this->cms_survey->formStepsCombo(null, 'class="input-combo"');
        $this->dados['comboGroups'] = $this->cms_survey->formGroupsCombo(null, null, 'class="input-combo"', array(''=>'- sem grupo -'));
        $this->dados['comboQueryTypes'] = $this->survey->formQueryTypesCombo(null, 'class="input-combo"');
        
//        dd($this->dados['comboQueryTypes']);
//        dd($this->dados['comboSteps']);
//        dd($this->dados['comboGroups']);
        
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c; // uri segments
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Nova pergunta';
        $this->tabs['tab_contt'][] = $this->load->view('cms/' . $this->viewFolder . 'query_novo', $this->dados, true);


        /*
         *  TEMPLATE
         */

        $this->templateRender();
    }

    /**
     * Recebe requisição POST para criar ou atualizar uma questão
     */
    public function saveQuery()
    {
        // loading...
        $this->load->library(array('form_validation'));

        // uri vars
        $var = $this->uri->to_array(array('id', 'query', 'op', 'co', 'tab'));
        $id = $var['id'];// surveyId
        $co = $var['co'];
        $tab = $var['tab'];
        $query = $var['query'];

        // obrigatório
        $this->form_validation->set_rules('survey_steps', 'Passos', 'trim|required');
        $this->form_validation->set_rules('titulo', 'Enunciado da pergunta', 'trim|required|min_length[3]');    

        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {
            if ($query == '')
            {
                $this->addQuery('tip:faltaCampos');
            }
            else
            {
                $this->editQuery('co:'. $co .'/id:'. $id .'/query:'. $query .'/tip:faltaCampos'); // editando
            }
        }
        // OK VÁLIDO
        else
        {

            // novo
            if ($query === '')
            {
                $ret = $this->survey->createQuery($co, $id, $this->input->post());

                if ($ret)
                {
                    redirect('cms/survey/edita/tip:ok/id:'. $id .'/co:'. $co .'/tab:' . $tab);
                }
                else // erro
                {
                    redirect('cms/survey/edita/tip:erro/co:'. $co .'/id:'.$id.'/tab:'. $tab);
                }
            }
            else
            {
                $ret = $this->survey->updateQuery($query, $this->input->post());
                
                if ($ret)
                {
                    redirect('cms/survey/edita/tip:ok/id:' . $id . '/co:' . $co . '/tab:' . $tab);
                }
                else // erro
                {
                    redirect('cms/survey/edita/tip:erro/co:'. $co .'/id:'.$id.'/tab:'. $tab);
                }
            }
        }
    }

    /**
     * Form de edição de perguntas
     */
    public function editQuery()
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'query', 'tab'));
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->title = 'Editando questão para: ' . $this->modulo['label'];
   
        $query = $this->survey->retrieveQuery($this->var['query']);
//        dd($query);


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'survey');
        $this->css = array();
        $this->setNewPlugin(array('tinymce', 'nyromodal', 'chosen'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'edita/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'salvar' => 'saveQuery/co:' . $this->var['co'] . '/id:' . $this->var['id'] .'/query:'. $this->var['query']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/survey/editGroup/co:'.$this->var['co'].'/id:'.$this->var['id'] .'/query:'. $this->var['query'];
        $this->dados['listOptions'] = $listOptions;
        
        $this->cms_survey->setSurveyId($query['rel']);
        $queryHierarchy = $this->survey->getQueryStepGroup($query['grupo']);
        
        $this->dados['comboSteps'] = $this->cms_survey->formStepsCombo($query['step']['id'], 'class="input-combo"');
        $this->dados['comboGroups'] = $this->cms_survey->formGroupsCombo(null, 
                ($query['group'] == null) ? null : $queryHierarchy['group']['id']
                , 'class="input-combo"', array(''=>'- sem grupo -'));
        $this->dados['comboQueryTypes'] = $this->survey->formQueryTypesCombo($query['tags'], 'class="input-combo"');
        
        /*
         * PROCESSA INFORMAÇÕES
         */
        $this->dados['survey'] = $this->survey->isAnswered($this->var['id']);
        // pega os dados deste item
        $this->dados['row'] = $query; // dados de conteúdo        
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $this->modulo;
        $this->dados['c'] = $this->c;
        
        
        /**
         * TABS
         */
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/'.$this->viewFolder.'query_edita', $this->dados, true);

//        $this->setMultiContent();
//        $this->setCamposExtra();
//        $this->setGaleria();
//        $this->setComentarios();
//        $this->setInscricoes();
//        $this->setMetadados();
        

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    /**
     * Tela com os controles para exibição dos gráficos
     * @param type $surveiId
     */
    public function graph($surveiId)
    {
        echo 'exibe relatório do ID: ' . $surveiId;
    }

}