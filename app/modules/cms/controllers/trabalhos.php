<?php

class Trabalhos extends Cms_Controller
{

    protected $namespace = '/trabalhos/';


    public function __construct()
    {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model(array('cms/trabalhos_model', 'cms/admin_model', 'cms/paginas_model',
            'cms/calendario_model'));

        /*
         * Permite separar os arquivos de VIEW para personalizalos
         */
        if ($this->modulo['id'] == 999)
        {
            $this->setNamespace('nome-da-pasta');
        }
    }


	public function test()
	{
		$this->load->model('cms/avaliacao_model', 'ava');
		$a = $this->ava->allByContent(238);
//		$a = $this->ava->find(2);
		dd($a);
	}



    /**
     * list of rows
     */
    public function index()
    {
        /*
         * VARIÁVEIS
         */
        $modulo        = $this->modulo; // infos do módulo
        $this->title   = 'Lista de Trabalhos ';
        $this->tabela  = 'cms_conteudo';
        $this->tit_css = 'lista';
        $this->var     = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));

        /*
         * ASSETS
         */
        $this->jquery = array('jquery.tablednd', 'ui.autocomplete.182');
        $this->cmsJS  = array('listas');
        $this->css    = array();
        $this->setNewPlugin(array('nyromodal', 'datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes         = array('check' => '', 'apagar' => '#',
                                      'novo'  => 'cms/trabalhos/novo/co:' . $this->var['co']);
        $navegaOff            = array();
        $dados['listOptions'] = array('sortable'  => $modulo['ordenavel'],
                                      'destaque'  => $modulo['destaques'],
                                      'inscricao' => $modulo['inscricao']);
        /*
         * PROCESSA
         */
        $saida         = $this->trabalhos_model->lista_conteudos($this->var, 'conteudo', $modulo); // lista do conteúdo
        $dados['rows'] = $saida['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/trabalhos/edita/co:' . $this->var['co'];
        $dados['linkFilter'] = 'cms/trabalhos/index/co:' . $this->var['co'];

        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms' . $this->getNamespace() . 'lista', $dados, true);
        $this->templateRender();
    }


    function novo($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->var     = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var    = $_var;
        $this->tabela  = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo        = $this->modulo; // infos do módulo
        $this->title   = $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit');
        $this->cmsJS  = array('tabs-forms', 'conteudo');
        //        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'maskedinput'));

        /*
         * OPÇÕES
         */
        $this->botoes               = array(
            'voltar'    => 'cms/trabalhos/index/co:' . $this->var['co'],
            'continuar' => 'cms/trabalhos/salva/co:' . $this->var['co']);
        $navegaOff                  = array();
        $this->dados['listOptions'] = array('sortable' => 0);
        $this->getDadosConteudo();
        $this->dados['swfUplForm'] = $this->getSwfUplForm();
        /*
         * PROCESSA
         */
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c']      = $this->c;

        $this->tabs['tab_title'][] = 'Novo Trabalho';
        $this->tabs['tab_contt'][] = $this->load->view('cms' . $this->getNamespace() . 'novo', $this->dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }


    function salva()
    {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op', 'co', 'tab'));
        $id  = $var['id'];
        $co  = $var['co'];
        $tab = $var['tab'];

        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required');
        if ($id == '')
        {
            $this->form_validation->set_rules('nick', 'Endereço amigável', 'trim|required');
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
                $this->edita('id:' . $id . '/tip:faltaCampos');
            } // editando
        }
        // OK VÁLIDO !!!
        else
        {

            if ($id == '')
            {
                $ret = $this->trabalhos_model->save_new($var);
            }
            else
            {
                $ret = $this->trabalhos_model->save($var);
                $this->cms_libs->extrasSalva($var['co'], $var['id']);
                /*
                 * Se existe arquivo insere
                 */
                $this->cms_libs->salvaArquivo($ret, $this->modulo['pasta_arq']);
            }

            if ($ret)
            {
                redirect('cms/trabalhos/edita/id:' . $ret . '/co:' . $co . '/tip:ok/tab:' . $tab);
            }
            else
            {
                redirect('cms/trabalhos/edita/id:' . $ret . '/co:' . $co . '/tip:erro/tab:' . $tab);
            }
        }
    }


    function edita($_var = '')
    {

        $this->load->helper('string');
        /*
         * VARIÁVEIS
         */
        $modulo        = $this->modulo; // infos do módulo
        $this->title   = 'Editando ' . $modulo['label'];
        $this->tabela  = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->var     = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var    = $_var;
        $conteudo      = $this->trabalhos_model->find($this->var);
//dd($conteudo);
        /*
         * ASSETS
         */
        $this->json_vars('trabalho', $conteudo);
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS  = array('tabs-forms', 'conteudo', 'galeria_init', 'inscritos');
        //        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'tinymce', 'nyromodal', 'chosen', 'angularjs', 'maskedinput'));
        $this->setNewScript('angular/ng-sortable');
        $this->setNewScript('angular/textAngular/textAngular-sanitize.min');
        $this->setNewScript('angular/textAngular/textAngular.min');
        $this->setNewScript('trabalhos/trabalho.controllers');
        $this->setNewScript('trabalhos/trabalho.services');
        $this->setNewEstyle(array('font-awesome.min'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id']
        );

        $navegaOff                  = array();
        $this->dados['linkReload']  = 'cms/trabalhos/edita/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = array('sortable' => 0);
        $this->dados['swfUplForm']  = $this->setArqvs() . $this->getSwfUplForm();
        /*
         * PROCESSA
         */
        //        dd($conteudo);
        $this->dados['row']    = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c']      = $this->c;
        $this->dados['metas']  = $this->trabalhos_model->getPostMetas($this->var);

        $this->tabs['tab_title'][] = 'Dados do trabalho';
        $this->tabs['tab_contt'][] = $this->load->view('cms' . $this->getNamespace() . 'edita', $this->dados, true);
        $this->tabs['tab_title'][] = 'Autores';
        $this->tabs['tab_contt'][] = $this->load->view('cms' . $this->getNamespace() . 'autores', $this->dados, true);

        $this->setMultiContent();
        $this->setCamposExtra();
        //        $this->setGaleria();
        //        $this->setInscricoes();
        //        $this->setPreco();
        $this->setMetadados();

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }


	/**
	 * tela para geração de pdfs
	 */
	public function pdf()
	{
		$modulo        = $this->modulo;
		$this->var     = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));


		try{
			$pdf = new \Cms\Exporters\AllJobsExport();
			$pdf->make();
		}catch (Exception $e)
		{
			d($e->getMessage());
		}


	}



	public function avaliacao_answers($id)
	{
		/*
		 * VARIÁVEIS
		 */
		$this->var = $this->uri->to_array(array('insc', 'i', 'erro', 'tip', 'imgs', 'arqs'));
		$modulo = $this->modulo; // infos do módulo
		$this->title = 'Avaliação';
		$this->tabela = 'cms_conteudo';

		/*
		 * ASSETS
		 */
		$this->jquery = array();
		$this->cmsJS = array();
		$this->css = array();

		/*
		 * OPÇÕES
		 */
		$this->botoes = array(
			//'url' => 'Label'
		);
		/*
		 * PROCESSA
		 */
		$this->load->model('cms/avaliacao_model', 'avaliacao');
		$ret = $this->avaliacao->find($id, array('job' => 1));
		$answer = new \Gestalt\Trabalho\EvaluationForm();
		$answer->setData($ret['form_answers']);

		$dados['html'] = $answer->getAnswers('html');

		// -- chama as views -- //
		$this->corpo = $this->load->view('cms/trabalhos/avaliacao_trabalho_form', $dados, true);
		$this->modalRender();
	}


    /**
     * Recebe requisição AJAX para atualizar lista de autores
     *
     * @param int $contentId
     */
    public function updateautores($contentId)
    {

        $method = $this->input->server('REQUEST_METHOD');

        if (!is_numeric($contentId))
        {
            echo json_encode(array('error' => true, 'msg' => 'ID do trabalho não existe.'));
            exit;
        }

        $this->load->model('trabalhos_model', 'trabalhos');

        if ($method == 'GET')
        {
//            echo json_encode(array('error' => false, 'data' => array(
//                array('id'        => rand(5, 99),
//                      'ordem'     => 0,
//                      'nome'      => "nome",
//                      'curriculo' => "meu texto",
//                      'status'    => 1),
//                array('id'        => rand(55, 99),
//                      'ordem'     => 0,
//                      'nome'      => "nome",
//                      'curriculo' => "meu texto",
//                      'status'    => 1),
//            )));
//
//            exit;



            $return = $this->trabalhos->getAutoresFromTrabalhoId($contentId);
            if (!$return)
            {
                echo json_encode(array('error' => false, 'data' => array()));
                exit;
            }
            echo json_encode(array('error' => false, 'data' => $return));
        }
        else if ($method == 'PUT')
        {

            $data  = file_get_contents('php://input');
            $array = json_decode($data, true);

            if (!is_array($array))
            {
                echo json_encode(array('error' => true, 'msg' => 'Lista de autores não está no formato correto.'));
                exit;
            }

            $this->trabalhos->saveAutores($contentId, $array);

            echo json_encode(array('error' => false, 'msg' => 'Lista de autores salva.'));
        }
    }

}