<?php

/**
 *
 * @version $Id$
 * @copyright 2010
 */
class Usuarios extends Cms_Controller
{

	function __construct()
	{
		parent::__construct();

		/*
		 * FAZ VERIFICAÇÃO DE USUÁRIO
		 */
		$this->logado = $this->sessao_model->controle_de_sessao();

		$this->load->model(array('cms/usuarios_model', 'cms/admin_model'));
	}

	/**
	 * Lista o conteúdo
	 *
	 * @return
	 */
	function index($_var = '')
	{
		/*
		 * VARIÁVEIS
		 */
		$modulo        = $this->modulo; // infos do módulo
		$this->title   = 'Lista de ' . $modulo['label'];
		$this->tabela  = 'cms_usuarios';
		$this->tit_css = 'lista';
		$this->var     = $this->uri->to_array("offset, pp, g, dt1, dt2, b, stt, tip, co");

		/*
		 * ASSETS
		 */
		$this->jquery = array('jquery.tablednd', 'ui.autocomplete.182');
		$this->cmsJS  = array('listas');
		$this->css    = array();
		$this->setNewPlugin(array('datepicker', 'nyromodal'));

		/*
		 * OPÇÕES
		 */
		$this->botoes         = array('check'    => '', 'apagar' => '#',
		                              'novo'     => 'novo/co:' . $this->var['co'],
		                              'importar' => 'cms/usuarios/import/co:' . $this->var['co']);
		$navegaOff            = array();
		$listOptions          = array('sortable' => $modulo['ordenavel'], 'destaque' => $modulo['destaques'], 'email' => 'cms/usuarios/mensagemForm', 'inscricao' => $modulo['inscricao']);
		$dados['listOptions'] = $listOptions;
		/*
		 * PROCESSA
		 */
		$saida               = $this->usuarios_model->lista_conteudos($this->var, 'conteudo', $modulo); // lista do conteúdo
		$dados['rows']       = $saida['rows']; // dados de conteúdo
		$dados['linkEditar'] = 'cms/usuarios/edita/co:' . $this->var['co'];
		$dados['linkFilter'] = 'cms/usuarios/index/co:' . $this->var['co'];
		/*
		 *  TEMPLATE
		 */
		$this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff, 'cms_usuarios');
		$this->corpo .= $this->load->view('cms/usuarios/lista', $dados, true);
		$this->templateRender();
	}

	function novo($_var = '')
	{
		/*
		 * VARIÁVEIS
		 */
		$modulo        = $this->modulo; // infos do módulo
		$this->title   = 'Novo ' . $modulo['label'];
		$this->tabela  = 'cms_usuarios';
		$this->tit_css = 'novo';
		$this->var     = $this->uri->to_array("offset, pp, g, dt1, dt2, b, stt, tip, co, id, tab");

		/*
		 * ASSETS
		 */
		$this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'ui.sortable.182', 'ui.button.182', 'jquery.nyroModal', 'jquery.maskedinput', 'jquery.ajaxify');
		$this->cmsJS  = array('tabs-forms', 'usuarios', 'galeria_init', 'nyroModal_init');
		$this->css    = array('nyroModal');

		/*
		 * OPÇÕES
		 */
		$this->botoes = array(
			'voltar'    => 'index/co:' . $this->var['co'],
			'continuar' => 'salva/co:' . $this->var['co']);
		$navegaOff    = array();
		$listOptions  = array('sortable' => 0);

		/*
		 * PROCESSA
		 */
		// pega os dados deste item
		// combobox grupos
		$dados['grupos']      = $this->cms_libs->combo_grupos($this->var['co'], false, false, '', 'cms_usuarios');
		$dados['listOptions'] = $listOptions;

		$dados['botoes'] = $this->botoes;
		$dados['modulo'] = $modulo;
		$dados['c']      = $this->c;

		// -
		// -- chama as views complementares -- //
		$this->tabs['tab_title'][] = 'Dados básicos';
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/novo_pessoais', $dados, true);

		/*
		 * TEMPLATE
		 */
		$this->templateRender();
	}

	function edita($_var = '')
	{
		//        mybug($this->conteudo);

		/*
		 * VARIÁVEIS
		 */
		$modulo        = $this->modulo; // infos do módulo
		$this->title   = 'Editando: ' . $this->conteudo['nome'];
		$this->tabela  = 'cms_usuarios';
		$this->tit_css = 'lista';
		$this->var     = $this->uri->to_array("offset, pp, g, dt1, dt2, b, stt, tip, co, id, tab");

		/*
		 * ASSETS
		 */
		$this->jquery = array('ui.tabs.182', 'ui.sortable.182', 'ui.button.182', 'jquery.maskedinput', 'jquery.ajaxify');
		$this->cmsJS  = array('tabs-forms', 'usuarios');
		$this->css    = array();
		$this->setNewPlugin(array('datepicker', 'nyromodal'));

		/*
		 * OPÇÕES
		 */
		$this->botoes = array(
			'voltar' => 'index/co:' . $this->var['co'],
			'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id']
		);
		$navegaOff    = array();
		$listOptions  = array('sortable' => 0);
		/*
		 * PROCESSA
		 */

		// pega os dados deste item
		$saida = $this->usuarios_model->usuario_dados($this->var);

		$this->dados['row'] = $saida; // dados de conteúdo

		// combobox grupos
		$this->dados['grupos']      = $this->cms_libs->combo_grupos($this->var['co'], $saida['grupo'], false, '', 'cms_usuarios');
		$this->dados['listOptions'] = $listOptions;
		// dados para galeria
		//        $this->dados['labelAddImage'] = 'Adicionar novas imagens';
		//        $this->dados['linkAddImage'] = 'cms/upload/img/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $modulo['pasta_img'] . '/onde:conteudo';
		//        $this->dados['linkAddArq'] = 'cms/upload/arquivo/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $modulo['pasta_arq'] . '/onde:pasta';
		//        $this->dados['addImgFromFolder'] = 'cms/imagem/explorer/co:0/id:' . $this->var['id'];
		$this->dados['linkReload'] = 'cms/usuarios/edita/co:' . $this->var['co'] . '/id:' . $this->var['id'];

		// -
		// -- chama as views complementares -- //
		$this->tabs['tab_title'][] = 'Dados pessoais';
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/edita_pessoais', $this->dados, true);
		$this->tabs['tab_title'][] = 'Empresa/Profissional';
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/edita_profissional', $this->dados, true);
		$this->setCamposExtra();
		$this->tabs['tab_title'][] = 'Endereço';
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/edita_endereco', $this->dados, true);

		// Aba de anexos
		$this->setAttachments(array(
			'tab' => 4
		));
		// modificadores para ordenação dos anexos
		$this->json_vars('attachments', array(
			'tabela' => 'cms_arquivos'
		));

		$newsletter = $this->cms_libs->dados_menus_raiz(29);
		if ($newsletter['status'] == 1)
		{
			$this->tabs['tab_title'][] = 'Newsletter';
			$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/edita_newsletter', $this->dados, true);
		}

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
		$var = $this->uri->to_array(array('id', 'op', 'co'));
		$id  = $var['id'];
		$co  = $var['co'];

		// -
		// -- processa informações -- //
		$this->form_validation->set_rules('grupos', 'Grupo', 'trim|required');
		$this->form_validation->set_rules('nome', 'Nome', 'trim|required|min_length[3]');
		$this->form_validation->set_rules('email', 'E-mail principal', 'trim|required|email_valid');

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
		// OK VÁLIDO !!!
		else
		{
			$ret = $this->usuarios_model->usuario_salva($var);
			$this->cms_libs->extrasSalva($var['co'], $var['id'], 'cms_usuarios');

			if ($ret)
			{
				redirect('cms/usuarios/edita/id:' . $ret . '/co:' . $co . '/tip:ok');
			}
			else
			{
				redirect('cms/usuarios/edita/id:' . $ret . '/co:' . $co . '/tip:erro');
			}
		}

	}

	/**
	 * Lista o conteúdo dos grupos
	 *
	 * @return
	 */
	function grupos($_var = '')
	{
		/*
		 * VARIÁVEIS
		 */
		$modulo         = $this->modulo; // infos do módulo
		$this->title    = 'Lista de Grupos de Usuarios';
		$this->tabela   = 'cms_usuarios';
		$this->tit_css  = 'lista';
		$this->var      = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
		$this->var['g'] = 0;

		/*
		 * ASSETS
		 */
		$this->jquery = array('ui.datepicker.182', 'jquery.tablednd');
		$this->cmsJS  = array('listas', 'datapicker_init');
		$this->css    = array();

		/*
		 * OPÇÕES
		 */
		$this->botoes         = array('check' => '', 'apagar' => '#', 'novoGrupo' => 'cms/usuarios/grupoNovo/co:' . $this->var['co']);
		$navegaOff            = array('dt1', 'dt2', 'grupos');
		$listOptions          = array('sortable' => 1);
		$dados['listOptions'] = $listOptions;
		$dados['tipoGrupo']   = 'conteudo';

		/*
		 * PROCESSA
		 */
		$saida = $this->usuarios_model->lista_conteudos($this->var, 'grupo');

		$dados['rows'] = $saida['rows']; // dados de conteúdo

		//        mybug($dados['rows']);

		$dados['linkEditar'] = 'cms/usuarios/grupoEdita/co:' . $this->var['co'];
		/*
		 *  TEMPLATE
		 */
		$this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
		$this->corpo .= $this->load->view('cms/usuarios/grupo_lista', $dados, true);
		$this->templateRender();
	}

	function grupoNovo($_var = '')
	{

		// -
		// -- chama as views complementares -- //
		//        $dados['tab_title'][] = 'Novo Grupo';
		//        $dados['tab_contt'][] = $this->load->view('cms/grupo_novo', $dados, true);
		// -
		// -- chama as views -- //
		//        $tmp['tabela'] = 'cms_usuarios';
		//        $tmp['title'] = $title;
		//        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
		//        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
		//        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
		//        $tmp['head'] = $this->layout_cms->head($_var);
		//        $tmp['menu'] = $this->layout_cms->menu($var);
		//        $tmp['corpo'] = $this->layout_cms->titulo($title, 'novo');
		//        $tmp['corpo'] .= $this->layout_cms->barra_botoes($botoes);
		//        $tmp['corpo'] .= $this->load->view('cms/template_tabs', $dados, true);
		// -
		// -- descarrega no template -- //
		//        $this->load->view('cms/template', $tmp);

		/*
		 * VARIÁVEIS
		 */
		$modulo         = $this->modulo; // infos do módulo
		$this->title    = 'Grupo Novo';
		$this->tabela   = 'cms_usuarios';
		$this->tit_css  = 'novo';
		$this->var      = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id'));
		$this->var['g'] = 0;

		/*
		 * ASSETS
		 */
		$this->jquery = array('ui.tabs.182');
		$this->cmsJS  = array('tabs-forms');
		//        $this->css = array();
		$this->setNewPlugin(array('colorpicker'));

		/*
		 * OPÇÕES
		 */

		$this->botoes = array(
			'voltar'    => 'grupos/co:' . $this->var['co'],
			'novo'      => 'grupoNovo/co:' . $this->var['co'],
			'salvar'    => 'grupoSalva/co:' . $this->var['co'] . '/id:' . $this->var['id'],
			'continuar' => 'grupoSalva/id:' . $this->var['id'] . '/co:' . $this->var['co'] . '/op:continua'
		);
		$navegaOff    = array();
		$listOptions  = array('sortable' => 0);
		/*
		 *  PROCESSA
		 */

		$dados['listOptions'] = $listOptions;
		$dados['tipoGrupo']   = 'usuarios';
		// -
		// -- chama as views complementares -- //
		$this->tabs['tab_title'][] = 'Novo grupo';
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/grupo_novo', $dados, true);
		/*
		 *  TEMPLATE
		 */
		$this->templateRender();
	}

	function grupoEdita($_var = '')
	{
		/*
		 * VARIÁVEIS
		 */
		$modulo         = $this->modulo; // infos do módulo
		$this->title    = 'Editando Grupo';
		$this->tabela   = 'cms_usuarios';
		$this->tit_css  = 'lista';
		$this->var      = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id'));
		$this->var['g'] = 0;

		/*
		 * ASSETS
		 */
		$this->jquery = array('ui.tabs.182');
		$this->cmsJS  = array('tabs-forms');
		//        $this->css = array();
		$this->setNewPlugin(array('colorpicker'));

		/*
		 * OPÇÕES
		 */

		$this->botoes = array('apagar'    => 'grupoApaga/co:' . $this->var['co'] . '/id:' . $this->var['id'],
		                      'voltar'    => 'grupos/co:' . $this->var['co'],
		                      'novo'      => 'grupoNovo/co:' . $this->var['co'],
		                      'salvar'    => 'grupoSalva/co:' . $this->var['co'] . '/id:' . $this->var['id'],
		                      'continuar' => 'grupoSalva/id:' . $this->var['id'] . '/co:' . $this->var['co'] . '/op:continua');
		$navegaOff    = array();
		$listOptions  = array('sortable' => 0);
		/*
		 *  PROCESSA
		 */
		$saida                = $this->cms_libs->conteudo_dados($this->var, 'cms_usuarios', 'grupo');
		$dados['row']         = $saida; // dados de conteúdo
		$dados['listOptions'] = $listOptions;
		$dados['tipoGrupo']   = 'usuarios';
		// -
		// -- chama as views complementares -- //
		$this->tabs['tab_title'][] = $saida['nome'];
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/grupo_edita', $dados, true);
		/*
		 *  TEMPLATE
		 */
		$this->templateRender();
	}

	function grupoSalva()
	{
		// -- carrega classes -- //
		$this->load->library(array('form_validation'));
		// -
		// -- recebe variaveis -- //
		$var = $this->uri->to_array(array('id', 'op', 'co'));
		$id  = $var['id'];
		// echo '<pre>';
		// var_dump($_POST);
		// exit;
		// -
		// -- processa informações -- //
		$this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
		if ($id == '')
		{
			$this->form_validation->set_rules('nick', 'Apelido', 'trim|required|min_length[3]');
		}
		$this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
		// NÃO VALIDOU !!!
		if ($this->form_validation->run() == false)
		{
			if ($id == '')
			{
				$this->grupoNovo('tip:faltaCampos');
			}
			else
			{
				$this->grupoEdita('id:' . $id);
			} // editando

		}
		// OK VÁLIDO !!!
		else
		{
			$ret = $this->usuarios_model->grupo_salva($var);
			if ($id == '')
			{
				if ($ret)
				{
					redirect('cms/usuarios/grupos/co:' . $var['co']);
				}
				else
				{
					redirect('cms/usuarios/grupoNovo/tip:erroGravacao/co:' . $var['co']);
				}
			}
			else
			{
				if ($ret)
				{
					if ($var['op'] == 'continua')
					{
						redirect('cms/usuarios/grupoEdita/id:' . $id . '/co:' . $var['co']);
					}
					else
					{
						redirect('cms/usuarios/grupos/co:' . $var['co']);
					}
				}
				else
				{
					redirect('cms/usuarios/grupos/tip:erro/co:' . $var['co']);
				}
			}
		}
	}

	/**
	 * open modal form to composer a message and send e-mail
	 *
	 * @param string $_var
	 * @return void
	 */
	function mensagemForm($_var = '')
	{
		/*
		 * VARIÁVEIS
		 */
		$modulo       = $this->modulo; // infos do módulo
		$this->title  = 'Enviando mensagem';
		$this->tabela = 'cms_usuarios';
		$this->var    = $this->uri->to_array(array('tip', 'id', 'user', 'imgs', 'arqs', 'tipo'));
		$this->_var   = $_var;

		/*
		 * ASSETS
		 */
		$this->jquery = array();
		$this->cmsJS  = array();
		$this->css    = array();

		/*
		 * OPÇÕES
		 */
		$this->botoes = array();
		/*
		 * PROCESSA
		 */
		$user         = $this->usuarios_model->usuario_dados($this->var['id']);
		$dados['row'] = $user;
		//        echo '<pre>';
		//        var_dump($user);
		//        exit;

		// -
		// -- chama as views -- //
		$this->corpo = $this->load->view('cms/usuarios/form_mensagem', $dados, true);
		$this->modalRender();
	}

	function mensagemEnvia()
	{
		// -- carrega classes -- //
		$this->load->library(array('form_validation'));
		// -
		// -- recebe variaveis -- //
		$var = $this->uri->to_array(array('tip', 'id', 'imgs', 'arqs'));
		// echo '<pre>';
		// var_dump($_POST);
		// exit;
		// -
		// -- processa informações -- //
		$this->form_validation->set_rules('user_id', 'Identificação do usuário', 'trim|required');
		$this->form_validation->set_rules('nome', 'Nome do usuário', 'trim|required|min_length[3]');
		$this->form_validation->set_rules('email', 'E-mail do usuário', 'trim|required|email_valid');
		$this->form_validation->set_rules('assunto', 'Assunto', 'trim|required|min_length[3]');
		$this->form_validation->set_rules('mensagem', 'Mensagem', 'trim|required|min_length[3]');
		$this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
		// NÃO VALIDOU !!!
		if ($this->form_validation->run() == false)
		{
			$this->mensagemForm('tip:faltaCampos/id:' . $var['id']);
			//redirect('cms/usuarios/mensagemForm/tip:faltaCampos/id:'.$var['id']);
		}
		// OK VÁLIDO !!!
		else
		{
			$ret = $this->usuarios_model->mensagem_envia();
			if ($ret)
			{
				redirect('cms/usuarios/mensagemForm/tip:envioOk/id:' . $var['id']);
			}
			else
			{
				redirect('cms/usuarios/mensagemForm/tip:erro/id:' . $var['id']);
			}
		}
		// -
		// -- chama as views complementares -- //
		// -
		// -- chama as views -- //
		// -
		// -- descarrega no template -- //
	}

	/**
	 * Importa arquivos CSV para array
	 * */
	function import($_var = '')
	{
		/*
		 * VARIÁVEIS
		 */
		$modulo        = $this->modulo; // infos do módulo
		$this->title   = 'Importação de usuários';
		$this->tabela  = 'cms_usuarios';
		$this->tit_css = 'novo';
		$this->var     = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'tab'));

		/*
		 * ASSETS
		 */
		$this->jquery = array('ui.tabs.182', 'jquery.nyroModal');
		$this->cmsJS  = array('tabs-forms', 'nyroModal_init');
		$this->css    = array('nyroModal');

		/*
		 * OPÇÕES
		 */
		$this->botoes = array('limpar'   => 'import/co:' . $this->var['co'],
		                      'voltar'   => 'index/co:' . $this->var['co'],
		                      'importar' => 'fazimportacao/co:' . $this->var['co']);
		$navegaOff    = array();
		$listOptions  = array('sortable' => 1);
		/*
		 *  PROCESSA
		 */
		$dados['grupos']      = $this->cms_libs->combo_grupos($this->var['co'], '', false, '', 'cms_usuarios');
		$dados['listOptions'] = $listOptions;
		$dados['tipoGrupo']   = 'conteudo';
		// -
		// -- chama as views complementares -- //
		$this->tabs['tab_title'][] = 'Opções de importação';
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/import_opt', $dados, true);
		$this->tabs['tab_title'][] = 'De arquivo CSV';
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/import_csv', $dados, true);
		$this->tabs['tab_title'][] = 'Lista de TXT';
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/import_txt', $dados, true);
		$this->tabs['tab_title'][] = 'Extrair E-mails';
		$this->tabs['tab_contt'][] = $this->load->view('cms/usuarios/extrai_emails', $dados, true);
		/*
		 * TEMPLATE
		 */
		$this->templateRender();
	}

	function fazimportacao()
	{
		//echo '<pre>';
		//var_dump($_FILES['csv']);
		$nomes  = trim($_POST['nomes']);
		$emails = trim($_POST['emails']);
		$grupo  = $_POST['grupos'];

		// verifica item obrigatórios
		if (!is_numeric($grupo))
		{
			//echo 'não tem grupo';
			redirect('cms/usuarios/import/tip:erroImport/erro:Escolha_o_grupo');
		}
		if ($this->usuarios_model->valida_csv($_FILES['csv']))
		{

			$import = $this->usuarios_model->csv_dados($_FILES['csv']);
			$grava  = $this->usuarios_model->salva_importacao($import, $grupo);

		}
		else if (strlen($nomes) > 5 && strlen($emails) > 6)
		{

			$import = $this->usuarios_model->txt_dados($nomes, $emails);

			if ($import == '!=')
			{
				//echo 'Colunas desiguais!';
				redirect('cms/usuarios/import/tip:erroImport/erro:Nomes_nao_batem_com_emails');
			}
			else if (!$import)
			{
				redirect('cms/usuarios/import/tip:erroImport/erro:TXT_sem_email_valido');
			}
			else
			{
				$grava = $this->usuarios_model->salva_importacao($import, $grupo);
			}
		}
		else
		{
			//echo 'Importação impossível!';
			redirect('cms/usuarios/import/tip:erroImport/erro:Sem_dados_de_entrada');
		}

		// resultado da gravação
		if ($grava)
		{
			redirect('cms/usuarios/import/tip:ok');
		}
		else
		{
			redirect('cms/usuarios/import/tip:erroImport/erro:Erro_ao_gravar');
		}
	}

	/**
	 * Janela modal com dados do usuário
	 *
	 * @return
	 */
	function dadosUser($_var = '')
	{
		/*
		 * VARIÁVEIS
		 */
		$modulo       = $this->modulo; // infos do módulo
		$this->title  = 'Dados do usuário';
		$this->tabela = 'cms_usuarios';
		$this->var    = $this->uri->to_array(array('tip', 'id', 'imgs', 'arqs'));
		$this->_var   = $_var;
		/*
		 * ASSETS
		 */
		$this->jquery = array();
		$this->cmsJS  = array();
		$this->css    = array('groundwork');

		/*
		 * OPÇÕES
		 */
		$this->botoes = array();

		// -
		// -- processa informações -- //
		$user = $this->usuarios_model->usuario_dados($this->var['id']);

		// nome cidade
		$this->db->select('nome');
		$this->db->where('id', $user['cidade']);
		$sql   = $this->db->get('opt_cidades');
		$citie = $sql->row_array();
		if ($sql->num_rows() > 0)
		{
			$cidade = $citie['nome'];
		}
		else
		{
			$cidade = '';
		}

		// -
		// -- chama as views complementares -- //
		$dados           = array();
		$dados['user'][] = array(
			'label' => 'Nome',
			'valor' => $user['nome']
		);
		$dados['user'][] = array(
			'label' => 'E-mail',
			'valor' => $user['email']
		);
		$dados['user'][] = array(
			'label' => 'Endereço',
			'valor' => $user['logradouro'] . ', ' . $user['num'] . ' / ' . $user['compl'] . ' - ' . $user['bairro'] . ' - ' . $cidade . ' - ' . $user['uf']
		);
		$dados['user'][] = array(
			'label' => 'CEP',
			'valor' => $user['cep']
		);
		$dados['user'][] = array(
			'label' => 'Telefone #1',
			'valor' => $user['tel1']
		);
		$dados['user'][] = array(
			'label' => 'Telefone #2',
			'valor' => $user['tel2']
		);
		$dados['user'][] = array(
			'label' => 'Observações',
			'valor' => $user['obs']
		);

		/*
		 * TEMPLATE
		 */
		$this->corpo = $this->load->view('cms/usuarios/dados_resumo', $dados, true);
		$this->modalRender();

	}

	// -----------------------------------------------------------------------
	/**
	 * Recebe requisição AJAX para retornar array de usuários
	 */
	public function get_users_by_json()
	{

		$term = $this->input->get('term');
		//        $limit = $this->input->get('pagesize');

		$value = $this->usuarios_model->search_for_autocomplete($term);

		//        $value = array(
		//            array('id' => 1, 'label' => $term, 'value' => $term),
		//            array('id' => 2, 'label' => 'Beltrano', 'value' => 'Beltrano')
		//        );

		echo json_encode($value);

	}

	// -----------------------------------------------------------------------
	/**
	 * Recebe requisição AJAX para retornar array de usuários
	 */
	public function get_usergroups_by_json()
	{

		$term = $this->input->get('term');
		//        $limit = $this->input->get('pagesize');

		$value = $this->usuarios_model->search_for_autocomplete($term, array(
			'grupos' => true
		));

		//        $value = array(
		//            array('id' => 1, 'label' => $term, 'value' => $term),
		//            array('id' => 2, 'label' => 'Beltrano', 'value' => 'Beltrano')
		//        );

		echo json_encode($value);

	}

}

?>