<?php

/**
 * Sistema genérico para criar conteúdo. Passando o ID do conteúdo todo restante será modificado.
 * A variavel 'co' deverá sempre setar presente.
 *
 * @version 3
 * @copyright 2010
 */
class Calendario extends Cms_Controller {



    protected  $namespace = '/calendario/';

    public function __construct()
    {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model(array('cms/calendario_model', 'cms/admin_model', 'cms/paginas_model'));
        $this->load->model('cms/subscriptions_options_model', 'subsoptions');

        /*
         * Permite separar os arquivos de VIEW para personalizalos
         */
        if ($this->modulo['id'] == 999)
        {
            $this->setNamespace('nome-da-pasta');
        }
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
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Lista de Eventos ';
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));

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
        $this->botoes = array('check' => '', 'apagar' => '#',
            'calendar' => 'cms/calendario/mensal/co:' . $this->var['co'],
            'novo' => 'cms/calendario/novo/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => $modulo['ordenavel'], 'destaque' => $modulo['destaques'], 'inscricao' => $modulo['inscricao']);
        $dados['listOptions'] = $listOptions;
        /*
         * PROCESSA
         */
        $saida = $this->calendario_model->lista_conteudos($this->var, 'conteudo', $modulo); // lista do conteúdo
        $dados['rows'] = $saida['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/calendario/edita/co:' . $this->var['co'];
        $dados['linkFilter'] = 'cms/calendario/index/co:' . $this->var['co'];

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
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Nova ' . $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit');
        $this->cmsJS = array('tabs-forms', 'conteudo');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'maskedinput'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'cms/calendario/index/co:' . $this->var['co'],
            'continuar' => 'cms/calendario/salva/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['listOptions'] = $listOptions;
        $this->getDadosConteudo();
        $this->dados['swfUplForm'] = $this->getSwfUplForm();
        /*
         * PROCESSA
         */
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;

        $this->tabs['tab_title'][] = 'Novo Evento';
        $this->tabs['tab_contt'][] = $this->load->view('cms' . $this->getNamespace() . 'novo', $this->dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    function edita($_var = '')
    {

        $this->load->helper('string');
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Editando ' . $modulo['label'];
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
        $conteudo = $this->calendario_model->conteudo_dados($this->var); // dados deste conteúdo reprocessados


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'galeria_init', 'inscritos');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'tinymce', 'nyromodal', 'chosen', 'maskedinput'));

        /*
         * OPÇÕES
         */

        $this->botoes = array(
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id']
        );
        $this->options_menu->addDuplicate($this->var['co'], $this->var['id'], 'calendario');
        $this->options_menu->addBannerize($this->var['co'], $this->var['id']);

        $opts = $this->subsoptions->checkHasOptions($this->var['id'], $this->var['co']);
        if ($opts)
        {
            $this->options_menu->add(array(
                'order' => 11,
                'label' => 'Gerenciar opções de inscrição',
                'url' => "calendario/subscriptions_options_edit/co:{$opts['modulo_id']}/id:{$opts['id']}",
                'icon' => '',
                'co' => $this->var['co']
            ));
        }
        else
        {
            $this->options_menu->add(array(
                'order' => 11,
                'label' => 'Criar opções de inscrição',
                'url' => "calendario/subscriptions_options_addnew/co:{$this->var['co']}",
                'icon' => '',
                'co' => $this->var['co']
            ));
        }

        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/calendario/edita/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = $listOptions;
        $this->dados['swfUplForm'] = $this->setArqvs() . $this->getSwfUplForm();
        /*
         * PROCESSA
         */
//        mybug($conteudo);
        $this->dados['row'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;

        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms' . $this->getNamespace() . 'edita', $this->dados, true);

        $this->setMultiContent();
        $this->setCamposExtra();
        $this->setGaleria();
        $this->setInscricoes();
        $this->setPreco();
        $this->setMetadados();

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
        $id = $var['id'];
        $co = $var['co'];
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
                $this->novo('tip:faltaCampos');
            else
                $this->edita('id:' . $id . '/tip:faltaCampos'); // editando
        }
        // OK VÁLIDO !!!
        else
        {

            $ret = $this->calendario_model->conteudo_salva($var);
            $this->cms_libs->extrasSalva($var['co'], $var['id']);
            /*
             * Se existe arquivo insere
             */
            $this->cms_libs->salvaArquivo($ret, $this->modulo['pasta_arq']);

            if ($ret)
            {
                redirect('cms/calendario/edita/id:' . $ret . '/co:' . $co . '/tip:ok/tab:' . $tab);
            }
            else
            {
                redirect('cms/calendario/edita/id:' . $ret . '/co:' . $co . '/tip:erro/tab:' . $tab);
            }
        }
    }

    // -------------------------------------------------------------------------
    /**
     * Salva e atualiza dados do conteúdo
     */
    function salva_copia()
    {

        // loading...
        $this->load->library(array('form_validation'));

        // uri vars
        $var = $this->uri->to_array(array('id', 'op', 'co'));
        $id = $var['id'];
        $co = $var['co'];

        // obrigatório
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('nick', 'Endereço amigável', 'trim|required|min_length[3]');

        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');

        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {

            redirect('cms/calendario/duplicar/co:' . $co . '/id:' . $id . '/tip:erro');
        }
        // OK VÁLIDO
        else
        {

            $ret = $this->calendario_model->conteudo_salva_copia($var);

            if ($ret)
            {
                redirect('cms/calendario/edita/id:' . $ret . '/co:' . $co . '/tip:ok');
            }
            else
            {
                redirect('cms/calendario/duplicar/tip:erro/co:' . $co . '/id:' . $id);
            }
        }
    }

    // -------------------------------------------------------------------------

    public function duplicar($_var = '')
    {

        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->paginas_model->conteudo_dados($this->var); // dados deste conteúdo reprocessados
        $this->title = 'Cópia de ' . $conteudo['titulo'];

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
            'continuar' => 'salva_copia/co:' . $this->var['co'] . '/id:' . $this->var['id']
        );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);

        /*
         * PROCESSA
         */
//        // combo de conteudos relacionados
//        $dados['rel'] = $this->cms_libs->combo_relacionados($this->modulo);
        $dados['listOptions'] = $listOptions;
        $this->getDadosConteudo();

        $this->dados['row'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;

        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Novo Evento';
        $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_duplicar', $this->dados, true);


        /*
         *  TEMPLATE
         */

        $this->templateRender();
    }

    /**
     * Página principal com as recentes
     */
    function mensal($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Calendário mensal';
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('y', 'm', 'offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'jquery.nyroModal');
        $this->cmsJS = array('tabs-forms', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array('voltar' => 'cms/calendario/index/co:' . $this->var['co'],
            'novo' => 'cms/calendario/novo/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['listOptions'] = $listOptions;

        /*
         * CALENDARIO
         */
        $y = $this->var['y'];
        $m = $this->var['m'];
        $prefs = array('start_day' => 'monday',
            'month_type' => 'long',
            'day_type' => 'short',
            'show_next_prev' => true,
            'next_prev_url' => cms_url('cms/calendario/mensal/co:' . $this->var['co'])
        );
        $prefs['template'] = '
                {table_open}<table border="0" cellpadding="4" cellspacing="0" class="table table-bordered calendar">{/table_open}
		{heading_previous_cell}<th class="nav"><a href="{previous_url}" class="btn">&lt;&lt;</a></th>{/heading_previous_cell}
		{heading_next_cell}<th class="nav"><a href="{next_url}" class="btn">&gt;&gt;</a></th>{/heading_next_cell}
		{cal_cell_start_today}<td class="hj">{/cal_cell_start_today}
		{cal_cell_content}<a href="{content}" class="nyroModal" target="_blank">{day}</a>{/cal_cell_content}
		{cal_cell_content_today}<div class="highlight"><a href="{content}" class="nyroModal" target="_blank">{day}</a></div>{/cal_cell_content_today}';

        $this->load->library('calendar', $prefs);

        $data = $this->calendario_model->eventos_calendario($y, $m, 'cms/calendario/data/data:');

        $this->dados['calendar'] = $this->calendar->generate($y, $m, $data);
        $y = ($y == '') ? date('Y') : $y;
        $this->dados['ano'] = $y;
        // link voltar para hoje
        if ($y != date("Y") || $m != date("m"))
        {
            $this->dados['link_hoje'] = '<div class="hoje"><a href="' . cms_url('cms/calendario/mensal/co:' . $this->var['co'] . '/y:' . date("Y") . '/m:' . date("m")) . '">hoje</a></div>';
        }

        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Calendário';
        $this->tabs['tab_contt'][] = $this->load->view('cms/calendario/calendario', $this->dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    // -------------------------------------------------------------------------
    /**
     * Abre floater com os eventos do dia.
     * @param type $_var
     */
    function data($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'imgs', 'arqs', 'onde', 'data'));
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Eventos do dia: ' . formaPadrao($this->var['data']);
        $this->tabela = 'cms_conteudo';

        $this->_var = $_var;
        /*
         * ASSETS
         */
        $this->jquery = array();
        $this->cmsJS = array('modal');
        $this->css = array();

        /*
         * OPÇÕES
         */
        $this->botoes = array();
        /*
         * PROCESSA
         */
        $eventos = $this->calendario_model->eventos_do_dia($this->var['data']);
        $dados['eventos'] = $eventos;

        // -- chama as views -- //
        $this->corpo = $this->load->view('cms/calendario/cal_eventos', $dados, true);
        $this->modalRender();
    }

    // -------------------------------------------------------------------------

    public function extrato()
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('co', 'i', 'erro', 'tip', 'imgs', 'arqs'));
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Extrato do pedido';
        $this->tabela = 'cms_conteudo';

        /*
         * ASSETS
         */
        $this->jquery = array('ui.accordion.182');
        $this->cmsJS = array('extrato');
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
//        $eventos = $this->calendario_model->eventos_do_dia($this->var['data']);
        $dados['extrt'] = $this->calendario_model->get_full_extrato($this->var);

        // -- chama as views -- //
        $this->corpo = $this->load->view('cms/calendario/extrato', $dados, true);
        $this->modalRender();
    }

    // -------------------------------------------------------------------------

    function inscritosPlanilha()
    {

        $this->load->library(array('table', 'site_utils'));
        $this->load->model('contato_m');
        $this->load->helper('text');

        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('co', 'id', 't'));
        $stt = ($var['t'] == 'todos') ? '' : 1;
        // pega os dados deste item
        $saida = $this->calendario_model->conteudo_dados($var);
        $curso = $saida['nick'];
        $dt = str_replace('/', '_', $saida['dt1']);

        $inscritos = $this->calendario_model->inscritos_dados($var['id'], $stt);

        // cabeças
        $data = array(
            array('Nome', 'E-mail', 'Telefone 1', 'Nascimento', 'CPF', 'CEP', utf8_decode('Endereço'), 'Cidade', 'UF', 'Status', 'Comentario')
        );
        $data[] = array($saida['titulo'], $saida['dt_ini'], '', '', '');

        if (!$inscritos)
        {
            
        }
        else
        {
            // corpo
            foreach ($inscritos as $row)
            {

                $cidade = $this->site_utils->cidade_dados($row['user']['cidade']);

                $data[] = array(utf8_decode($row['user']['nome']),
                    $row['user']['email'],
                    $row['user']['tel1'],
                    formaPadrao($row['user']['nasc']),
                    $row['user']['cpf'],
                    $row['user']['cep'],
                    utf8_decode($row['user']['logradouro'] . ', ' . $row['user']['num'] . ', ' . $row['user']['compl']),
                    utf8_decode($cidade['nome']),
                    $row['user']['uf'],
                    $row['status'],
                    utf8_decode($row['comentario'])
                );
            }
        }


        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=" . substr($curso, 0, 150) . '_' . $dt . ".xls");
        echo $this->table->generate($data);
    }

    /**
     * Listagem das opções de inscrição para cada conteúdo
     */
    public function subscriptions_options()
    {

        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Opções de inscrição ';
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));

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
            'check' => '', 'apagar' => '#',
            'novo' => 'cms/calendario/subscriptions_options_addnew/co:' . $this->var['co']
        );
        $navegaOff = array('export' => '');
        /*
         * PROCESSA
         */
//        $saida = $this->calendario_model->lista_conteudos($this->var, 'conteudo', $modulo); // lista do conteúdo
        $saida = $this->subsoptions->getAll($this->var);
        $dados['rows'] = $saida['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/calendario/subscriptions_options_edit/co:' . $this->var['co'];
        $dados['linkFilter'] = 'cms/calendario/subscriptions_options/co:' . $this->var['co'];

        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view("cms/calendario/subs_options_lista", $dados, true);
        $this->templateRender();
    }

    public function subscriptions_options_addnew()
    {

        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Nova ' . $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'chosen', 'maskedinput', 'angularjs'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'cms/calendario/subscriptions_options/co:' . $this->var['co'],
            'continuar' => 'cms/calendario/subscriptions_options_save/co:' . $this->var['co']);

        $this->dados['listOptions'] = array('sortable' => 0);
        $this->getDadosConteudo();
//        $this->dados['swfUplForm'] = $this->getSwfUplForm();
        /*
         * PROCESSA
         */
        $this->dados['related'] = $this->subsoptions->getRelatedContents($this->var['co'], null, 'combobox');
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;

        $this->tabs['tab_title'][] = 'Novas opções de inscrição';
        $this->tabs['tab_contt'][] = $this->load->view("cms/calendario/subs_options_novo", $this->dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    public function subscriptions_options_edit()
    {

        $this->load->helper('string');
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Editando ' . $modulo['label'];
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));

        $conteudo = $this->subsoptions->find($this->var['id']);

        /*
         * ASSETS
         */
        $this->json_vars('option', $conteudo);
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'galeria_init', 'inscritos');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'angularjs', 'chosen'));
        $this->setNewScript('angular/ng-sortable');
        $this->setNewScript('subscription_options_app');

        /*
         * OPÇÕES
         */

        $this->botoes = array(
            'voltar' => 'subscriptions_options/co:' . $this->var['co'],
            'salvar' => 'subscriptions_options_save/co:' . $this->var['co'] . '/id:' . $this->var['id']
        );

        $this->dados['linkReload'] = 'cms/calendario/subscriptions_options_edit/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = array('sortable' => 0);
        /*
         * PROCESSA
         */
//        mybug($conteudo);
        $this->dados['related'] = $this->subsoptions->getRelatedContents($this->var['co'], $conteudo['rel'], 'combobox');
        $this->dados['row'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;

        $this->tabs['tab_title'][] = 'Lista de opções';
        $this->tabs['tab_contt'][] = $this->load->view("cms/calendario/subs_options_editaopts", $this->dados, true);
        $this->tabs['tab_title'][] = 'Dados básicos';
        $this->tabs['tab_contt'][] = $this->load->view("cms/calendario/subs_options_edita", $this->dados, true);

//        $this->setMultiContent();
//        $this->setCamposExtra();
//        $this->setGaleria();
//        $this->setInscricoes();
//        $this->setPreco();
//        $this->setMetadados();

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    public function subscriptions_options_save()
    {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));

        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op', 'co', 'tab'));
        $id = $var['id'];
        $co = $var['co'];
        $tab = $var['tab'];

        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required');
        $this->form_validation->set_rules('rel', 'Relacionado à', 'trim|required');

        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');

        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {
            if ($id == '')
            {
                $this->subscriptions_options_addnew('tip:faltaCampos');
            }
            else
            {
                $this->subscriptions_options_edit('id:' . $id . '/tip:faltaCampos'); // editando
            }
        }
        // OK VÁLIDO !!!
        else
        {
            if ($id == '')
            {
                $ret = $this->subsoptions->save_new($var);
            }
            else
            {
                $ret = $this->subsoptions->save($var);
            }

            if ($ret)
            {
                redirect('cms/calendario/subscriptions_options_edit/id:' . $ret . '/co:' . $co . '/tip:ok/tab:' . $tab);
            }
            else
            {
                redirect('cms/calendario/subscriptions_options_edit/id:' . $ret . '/co:' . $co . '/tip:erro/tab:' . $tab);
            }
        }
    }

    /**
     * Recebe requisição AJAX para atualizar lista de opções
     * 
     * @param int $optionId
     */
    public function subscriptions_updateoptions($optionId)
    {
        $method = $this->input->server('REQUEST_METHOD');

        if (!is_numeric($optionId))
        {
            echo json_encode(array('error' => true, 'msg' => 'ID da opção não existe.'));
            exit;
        }



        if ($method == 'GET')
        {
            $return = $this->subsoptions->getOptionsListFromOptionId($optionId);
            if (!$return)
            {
                echo json_encode(array('error' => false, 'data' => array()));
                exit;
            }
            echo json_encode(array('error' => false, 'data' => $return));
        }
        else if ($method == 'PUT')
        {

            $data = file_get_contents('php://input');
            $array = json_decode($data, true);

            if (!is_array($array))
            {
                echo json_encode(array('error' => true, 'msg' => 'Lista de opções não está no formato correto.'));
                exit;
            }

            $this->subsoptions->saveOptionsListForOptionId($optionId, $array);

            echo json_encode(array('error' => false, 'msg' => 'Lista de opções salvas.'));
        }
    }
    
    
    public function subscriptions_showoptions()
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('insc', 'i', 'erro', 'tip', 'imgs', 'arqs'));
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Opções do usuário';
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
//        $eventos = $this->calendario_model->eventos_do_dia($this->var['data']);
        $dados['answers'] = $this->subsoptions->getAnswersFromUserSubscription($this->var['insc']);
//        dd($dados['answers']);
        // -- chama as views -- //
        $this->corpo = $this->load->view('cms/calendario/subs_options_answers', $dados, true);
        $this->modalRender();
    }

    /**
     * Pasta alternativa onde encontrar as views do módulo
     * @param string $folder
     */
    private function setView($modulo_id, $folder = '')
    {
        if ($this->modulo['id'] == $modulo_id)
        {
            $this->view = (strlen($folder)) ? $folder : 'calendario';
        }
    }

}