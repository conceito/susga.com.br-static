<?php

/**
 * Controla todas a ações de newsletter
 * Utilizando a tabeça cms_conteudo para as mensagem onde:
 * 	Assunto = titulo
 * 	Mens HTML = txt
 * 	Mens TEXT = resumo
 * 	Editor = tags
 * A variavel 'co' deverá sempre setar presente.
 *
 * @version 3
 * @copyright 2010
 */
class News extends Cms_Controller {

    function __construct() {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();
      
        $this->load->model(array('cms/news_model', 'cms/admin_model'));
        $this->co = 29; // indica artificialmente o ID do módulo
    }

    /**
     * Lista o conteúdo
     *
     * @return
     */
    function index($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Lista de ' . $modulo['label'];
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));

        /*
         * ASSETS
         */
        $this->jquery = array('ui.datepicker.182', 'jquery.tablednd', 'jquery.nyroModal');
        $this->cmsJS = array('listas', 'listas_news', 'datapicker_init', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#',
            'novo' => 'cms/news/novo/co:' . $this->var['co'],
            'agendar' => 'cms/news/agendar/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => $modulo['ordenavel'], 'destaque' => $modulo['destaques'], 'comments' => $modulo['comments'], 'email' => 'cms/news/newsTeste', 'inscricao' => $modulo['inscricao']);
        $dados['listOptions'] = $listOptions;

        /*
         * PROCESSA 
         */
        $saida = $this->news_model->lista_conteudos($this->var, 'conteudo', $modulo); // lista do conteúdo
        $dados['rows'] = $saida['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/news/edita/co:' . $this->var['co'];
        /*
         *  TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/news/news_lista', $dados, true);
        $this->templateRender();

    }

    function novo($_var = '') {
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
        $this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'tiny_mce356'=>'jquery.tinymce', 'jquery.charlimit', 'jquery.nyroModal');
        $this->cmsJS = array('tabs-forms', 'newsletter', 'tinymce_news', 'nyroModal_init');
        $this->css = array('nyroModal');
        

        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'novo/co:' . $this->var['co'],
            'voltar' => 'index/co:' . $this->var['co'],
            'continuar' => 'salva/co:' . $this->var['co'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);

        /*
         * PROCESSA
         */
        // combobox grupos
        $dados['grupos'] = $this->cms_libs->combo_grupos($this->var['co'], '', false);
        $dados['listOptions'] = $listOptions;
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Nova Newsletter';
        $this->tabs['tab_contt'][] = $this->load->view('cms/news/novo', $dados, true);

        /*
         *  TEMPLATE
         */

        $this->templateRender();
    }

    function edita($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Editando ' . $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'newsletter', 'tinymce_news', 'galeria_init', 'comentarios');
        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'tinymce', 'nyromodal'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'edita/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'cms/news/salva/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'continuar' => 'salva/co:' . $this->var['co'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);


        /*
         * PROCESSA
         */
        // pega os dados deste item
        $saida = $this->news_model->conteudo_dados($this->var);
        $links = $this->news_model->links_dados($this->var);
        $stats = $this->news_model->stats_dados($this->var);
        $dados['row'] = $saida; // dados de conteúdo
        $dados['co'] = $modulo['id'];
        $dados['links'] = $links;
        $dados['stats'] = $stats;
        $quantgal = $saida['quantGal'];
        // combobox grupos
        $dados['grupos'] = $this->cms_libs->combo_grupos($this->var['co'], $saida['grupo'], false);
        $dados['listOptions'] = $listOptions;
        // dados para galeria
        $dados['labelAddImage'] = 'Adicionar novas imagens';
        $dados['linkAddImage'] = 'cms/upload/img/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $modulo['pasta_img'] . '/onde:conteudo';
        $dados['linkAddArq'] = 'cms/upload/arquivo/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $modulo['pasta_arq'] . '/onde:pasta';
        $dados['addImgFromFolder'] = 'cms/imagem/explorer/co:0/id:' . $this->var['id'];
        $dados['linkReload'] = 'cms/news/edita/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/tab:2';
        $dados['galery'] = $this->cms_libs->arquivos_concat_dados(explode('|', $saida['galeria']));
        
        // -- Nome da página -- //
        $title = 'Editando ' . $modulo['label'];
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/news/edita', $dados, true);
        $this->tabs['tab_title'][] = 'Galeria (' . $quantgal . ')';
        $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_galeria', $dados, true);
        $this->tabs['tab_title'][] = 'Links (' . $links['ttl_links'] . ')';
        $this->tabs['tab_contt'][] = $this->load->view('cms/news/links', $dados, true);
        $this->tabs['tab_title'][] = 'Estatísticas';
        $this->tabs['tab_contt'][] = $this->load->view('cms/news/stats', $dados, true);
        // se existir comentários cria nova aba
        if ($modulo['comments'] == 1) {
            $dados['comments'] = $this->news_model->comentarios_dados($var['id']);
            $this->tabs['tab_title'][] = 'Comentários';
            $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_comments', $dados, true);
        }
        /*
         *  TEMPLATE
         */

        $this->templateRender();
        
    }

    function salva() {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op', 'co'));
        $id = $var['id'];
        $co = $var['co'];
        // echo '<pre>';
        // var_dump($_POST);
        // exit;
        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('grupos', 'Grupo', 'trim|required');
        // $this->form_validation->set_rules('dt1', 'Data', 'trim|required|min_length[10]');
        $this->form_validation->set_rules('titulo', 'Assunto', 'trim|required|min_length[3]');

        if ($id == '') {
            $this->form_validation->set_rules('nick', 'Apelido', 'trim|required|min_length[3]');
           
        }
        if ($id != ''){
            $this->form_validation->set_rules('txt', 'Mensagem HTML', 'trim|required|min_length[3]');
             $this->form_validation->set_rules('tags', 'Remetente', 'trim|required|min_length[3]');
            $this->form_validation->set_rules('extra', 'E-mail Remetente', 'trim|required|email_valid');
        }
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            if ($id == '')
                $this->novo('tip:faltaCampos');
            else
                $this->edita('id:' . $id . '/tip:faltaCampos'); // editando

        }
        // OK VÁLIDO !!!
        else {
            $ret = $this->news_model->conteudo_salva($var);

            if ($ret) {
                
                if ($var['op'] == 'continua'){
                    redirect('cms/news/edita/id:' . $ret . '/co:' . $co . '/tip:ok');
                }
                else {
                    redirect('cms/news/index/co:' . $co . '/tip:ok');
                }

            } else {
                redirect('cms/news/index/tip:erro/co:' . $co);
            }
        }
    }

    function agendar($_var = '') {

        /*
         * VARIÁVEIS
         */

        $this->var = $this->uri->to_array(array('id', 'tip', 'co'));
        $this->_var = $_var;
        $var2 = $this->uri->dash_to_array($_var);
        $this->var['co'] = $this->co;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Editando ' . $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'jquery.nyroModal');
        $this->cmsJS = array('tabs-forms', 'newsletter', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'cms/news/index/co:' . $this->var['co'],
            'continuar' => 'cms/news/agendarSalva/co:' . $this->var['co'] . '/id:' . $this->var['id']);
        $navegaOff = array();
        $listOptions = array('sortable' => 0);


        /*
         * PROCESSA
         */
        $news = $this->news_model->conteudo_dados($this->var['id']);
        // combobox grupos
        $dados['grupos'] = $this->cms_libs->combo_grupos($news['tipo'], $news['grupo'], true, '', 'cms_usuarios');
        $dados['filtros'] = $cbF = $this->cms_libs->combo_sist_vars('filtros', 20, '', true);
        $dados['news'] = $news;
        //$dados['agenda'] = $this->news_model->agendamentos_dados();
        $dados['lnkDisparar'] = 'cms/news/agendarSalva/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        //$ttl_age = (! $dados['agenda']) ? '0' : count($dados['agenda']);



        $dados['listOptions'] = $listOptions;
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Grupos de envio';
        $this->tabs['tab_contt'][] = $this->load->view('cms/news/grupos_envio', $dados, true);
//        $dados['tab_title'][] = 'Agendamentos em andamento ('.$ttl_age.')';
//        $dados['tab_contt'][] = $this->load->view('cms/news/enviando', $dados, true);

        $this->templateRender();

    }

    /**
     * Executa o disparo em intervalos.
     * @param <type> $_var
     */
    function disparar($_var = '') {


        /*
         * VARIÁVEIS
         */

        $this->var = $this->uri->to_array(array('id', 'tip', 'co', 'age'));
        $this->_var = $_var;
        $var2 = $this->uri->dash_to_array($_var);
        $this->var['co'] = $this->co;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'jquery.nyroModal');
        $this->cmsJS = array('tabs-forms', 'newsletter', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'cms/news/index/co:' . $this->var['co'],
            'continuar' => 'cms/news/agendarSalva/co:' . $this->var['co'] . '/id:' . $this->var['id']);
        $navegaOff = array();
        $listOptions = array('sortable' => 0);


        /*
         * PROCESSA
         */

        // -
        // -- processa informações -- //
        $news = $this->news_model->conteudo_dados($this->var['id']);
        // combobox grupos
        $dados['grupos'] = $this->cms_libs->combo_grupos($news['tipo'], $news['grupo'], true, '', 'cms_usuarios');
        $dados['filtros'] = $cbF = $this->cms_libs->combo_sist_vars('filtros', 20, '', true);
        $dados['news'] = $news;
        $dados['agenda'] = $this->news_model->agendamentos_dados($this->var['age']);
        $dados['lnkDisparar'] = 'cms/news/agendarSalva/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $dados['co'] = $this->co;
        $ttl_age = (!$dados['agenda']) ? '0' : count($dados['agenda']);


        $dados['listOptions'] = $listOptions;
        $this->title = 'Agendando envio de: ' . $news['titulo'];
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Disparo em processo (' . $ttl_age . ')';
        $this->tabs['tab_contt'][] = $this->load->view('cms/news/enviando', $dados, true);
        $this->tabs['tab_title'][] = 'Grupos de envio';
        $this->tabs['tab_contt'][] = $this->load->view('cms/news/grupos_envio', $dados, true);

        $this->templateRender();

    }

    /**
     *********** depreciado ********************
     * @param <type> $_var
     */
    function agendarVer($_var = '') {

/*
         * VARIÁVEIS
         */

        $this->var = $this->uri->to_array(array('id', 'tip', 'co'));
        $this->_var = $_var;
        $var2 = $this->uri->dash_to_array($_var);
        $this->var['co'] = 29; // artificialmente!!!
        $this->var['co'] = $this->co;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'jquery.nyroModal');
        $this->cmsJS = array('tabs-forms', 'newsletter', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'cms/news/index/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => 0);



      
        // -
        // -- processa informações -- //
        $dados['agenda'] = $this->news_model->agendamentos_dados();
        $ttl_age = (!$dados['agenda']) ? '0' : count($dados['agenda']);


        $dados['listOptions'] = $listOptions;
        // -
        // -- chama as views complementares -- //
        $this->title = 'Newsletters agendadas';


        $this->tabs['tab_title'][] = 'Agendamentos em andamento (' . $ttl_age . ')';
        $this->tabs['tab_contt'][] = $this->load->view('cms/news/enviando', $dados, true);

        $this->templateRender();


    }

    function agendarSalva() {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op', 'co'));
        $id = $var['id'];
        $co = $var['co'];
//         echo '<pre>';
//         var_dump($_POST);
//         exit;
        // -
        // -- processa informações -- //

        $this->form_validation->set_rules('titulo', 'Nome do Agendamento', 'trim|required');
        $this->form_validation->set_rules('grupos', 'Grupos', 'required');

        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {

            $this->agendar('id:' . $id . '/co:' . $co . '/tip:faltaCampos'); // editando
        }
        // OK VÁLIDO !!!
        else {
            $idAge = $this->news_model->agendar_envios($var);
            // após salvar os usuários para envio, redireciona para tela com iframe

            if ($idAge) {
                redirect('cms/news/disparar/co:' . $co . '/age:' . $idAge . '/id:' . $id . '/tip:ok');
            } else {
                redirect('cms/news/agendarVer/tip:erro/co:' . $co);
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
     * Lista o conteúdo dos grupos
     *
     * @return
     */
    function grupos($_var = '') {
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $var['g'] = 0;
        // -
        // -- carrega classes -- //
        // -
        // -- processa informações -- //
        // pega os itens do menu RAIZ
        $saida = $this->paginas_model->lista_conteudos($var, 'grupo');
        $dados['rows'] = $saida['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/paginas/grupoEdita/co:' . $var['co'];
        // -- Nome da página -- //
        $title = 'Lista de Grupos de Páginas';
        // -
        // -- recebe variaveis -- //
        $scripts = array('ui.datepicker.182', 'jquery.tablednd');
        $scriptsCms = array('listas', 'datapicker_init');
        $estilos = array();
        $botoes = array('check' => '', 'apagar' => '#', 'novoGrupo' => 'cms/paginas/grupoNovo/co:' . $var['co']);
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        // -
        // -- chama as views -- //
        $tmp['tabela'] = 'cms_conteudo';
        $tmp['title'] = $title;
        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
        $tmp['head'] = $this->layout_cms->head($_var);
        $tmp['menu'] = $this->layout_cms->menu($var);
        $tmp['corpo'] = $this->layout_cms->titulo($title, 'lista');
        $tmp['corpo'] .= $this->layout_cms->barra_botoes($botoes);
        $tmp['corpo'] .= $this->layout_cms->barra_navegacao($var, $saida['ttl_rows'], $navegaOff);
        $tmp['corpo'] .= $this->load->view('cms/grupo_lista', $dados, true);
        // -
        // -- descarrega no template -- //
        $this->load->view('cms/template', $tmp);
    }

    function grupoNovo($_var = '') {
        // -- Nome da página -- //
        $title = 'Novo Grupo';
        // -
        // -- carrega classes -- //
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));

        $scripts = array('ui.tabs.182', 'ui.datepicker.182', 'mlColorPicker');
        $scriptsCms = array('tabs-forms', 'colorpicker_init');
        $estilos = array('mlColorPicker');
        $botoes = array('limpar' => 'cms/paginas/grupoNovo/co:' . $var['co'],
            'voltar' => 'cms/paginas/grupos/co:' . $var['co'],
            'salvar' => 'cms/paginas/grupoSalva/co:' . $var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => 1);
        // -
        // -- processa informações -- //
        // combobox grupos
        $dados['grupos'] = $this->cms_libs->combo_grupos($var['co'], '');
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        // -
        // -- chama as views complementares -- //
        $dados['tab_title'][] = 'Novo Grupo';
        $dados['tab_contt'][] = $this->load->view('cms/grupo_novo', $dados, true);
        // -
        // -- chama as views -- //
        $tmp['tabela'] = 'cms_modulos';
        $tmp['title'] = $title;
        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
        $tmp['head'] = $this->layout_cms->head($_var);
        $tmp['menu'] = $this->layout_cms->menu($var);
        $tmp['corpo'] = $this->layout_cms->titulo($title, 'novo');
        $tmp['corpo'] .= $this->layout_cms->barra_botoes($botoes);
        $tmp['corpo'] .= $this->load->view('cms/template_tabs', $dados, true);
        // -
        // -- descarrega no template -- //
        $this->load->view('cms/template', $tmp);
    }

    function grupoEdita($_var = '') {
        // -- Nome da página -- //
        $title = 'Editando Grupo';
        // -
        // -- carrega classes -- //
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id'));

        $scripts = array('ui.tabs.182', 'ui.datepicker.182', 'mlColorPicker');
        $scriptsCms = array('tabs-forms', 'colorpicker_init');
        $estilos = array('mlColorPicker');
        $botoes = array('apagar' => 'cms/paginas/grupoApaga/co:' . $var['co'] . '/id:' . $var['id'],
            'voltar' => 'cms/paginas/grupos/co:' . $var['co'],
            'novo' => 'cms/paginas/grupoNovo/co:' . $var['co'],
            'salvar' => 'cms/paginas/grupoSalva/co:' . $var['co'] . '/id:' . $var['id'],
            'continuar' => 'cms/paginas/grupoSalva/id:' . $var['id'] . '/co:' . $var['co'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        // -
        // -- processa informações -- //
        // combobox grupos
        $saida = $this->cms_libs->conteudo_dados($var, 'cms_conteudo', 'grupo');
        $dados['row'] = $saida; // dados de conteúdo
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        // -
        // -- chama as views complementares -- //
        $dados['tab_title'][] = $saida['titulo'];
        $dados['tab_contt'][] = $this->load->view('cms/grupo_edita', $dados, true);
        // -
        // -- chama as views -- //
        $tmp['tabela'] = 'cms_modulos';
        $tmp['title'] = $title;
        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
        $tmp['head'] = $this->layout_cms->head($_var, $var);
        $tmp['menu'] = $this->layout_cms->menu($var);
        $tmp['corpo'] = $this->layout_cms->titulo($title, 'novo');
        $tmp['corpo'] .= $this->layout_cms->barra_botoes($botoes);
        $tmp['corpo'] .= $this->load->view('cms/template_tabs', $dados, true);
        // -
        // -- descarrega no template -- //
        $this->load->view('cms/template', $tmp);
    }

    function grupoSalva() {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op', 'co'));
        $id = $var['id'];
        // echo '<pre>';
        // var_dump($_POST);
        // exit;
        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
        if ($id == ''
            )$this->form_validation->set_rules('nick', 'Apelido', 'trim|required|min_length[3]');
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            if ($id == '')
                $this->grupoNovo('tip:faltaCampos');
            else
                $this->grupoEdita('id:' . $id); // editando

        }
        // OK VÁLIDO !!!
        else {
            $ret = $this->paginas_model->grupo_salva($var);
            if ($id == '') {
                if ($ret
                    )redirect('cms/paginas/grupos/co:' . $var['co']);
                else
                    redirect('cms/paginas/grupoNovo/tip:erroGravacao/co:' . $var['co']);
            } else {
                if ($ret) {
                    if ($var['op'] == 'continua'
                        )redirect('cms/paginas/grupoEdita/id:' . $id . '/co:' . $var['co']);
                    else
                        redirect('cms/paginas/grupos/co:' . $var['co']);
                } else {
                    redirect('cms/paginas/grupos/tip:erro/co:' . $var['co']);
                }
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
     * Abre formulário para fazer o upload de imagens
     *
     * @return
     */
    function linkNovo($_var = '') {
        // -- Nome da página -- //
        $title = 'Novo link para newsletter';
        // -
        // -- carrega classes -- //
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('tip', 'id', 'imgs', 'arqs'));
        $scripts = array();
        $scriptsCms = array('modal');
        $estilos = array();
        $botoes = array();
        // -
        // -- processa informações -- //
        // -
        // -- chama as views complementares -- //
        $dados['id'] = $var['id'];
        // echo '<pre>';
        // var_dump($var);
        // exit;
        // -
        // -- chama as views -- //
        $tmp['corpo'] = $this->load->view('cms/news/link_novo', $dados, true);
        $tmp['tabela'] = 'cms_news_links';
        $tmp['title'] = $title;
        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
        $tmp['menu'] = $this->layout_cms->menu_modal($botoes);
        $tmp['resposta'] = $this->layout_cms->modal_resposta($var, $_var);
        // -
        // -- descarrega no template -- //
        $this->load->view('cms/template_modal', $tmp);
    }

    function linkSalva() {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('tip', 'id', 'imgs', 'arqs'));
//         echo '<pre>';
//         var_dump($_POST);
//         exit;
        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('desc1', 'Descrição #1', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('link1', 'Endereço #1', 'trim|required|min_length[11]|max_length[200]');
        $this->form_validation->set_rules('desc2', 'Descrição #2', 'trim|max_length[200]');
        $this->form_validation->set_rules('link2', 'Endereço #2', 'trim|max_length[200]');
        $this->form_validation->set_rules('desc3', 'Descrição #3', 'trim|max_length[200]');
        $this->form_validation->set_rules('link3', 'Endereço #3', 'trim|max_length[200]');
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            $this->linkNovo('tip:faltaCampos/id:' . $var['id']);
            //redirect('cms/usuarios/mensagemForm/tip:faltaCampos/id:'.$var['id']);
        }
        // OK VÁLIDO !!!
        else {
            // aproveito o model dos usuários para enviar
            $ret = $this->news_model->links_salva();
            if ($ret) {
                redirect('cms/news/linkNovo/tip:ok/id:' . $var['id']);
            } else {
                redirect('cms/news/linkNovo/tip:erro/id:' . $var['id']);
            }
        }
        // -
        // -- chama as views complementares -- //
        // -
        // -- chama as views -- //
        // -
        // -- descarrega no template -- //
    }

    function view($_var = '') {
        // -- Nome da página -- //
        $title = 'Preview';
        // -
        // -- carrega classes -- //
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('tip', 'id', 'imgs', 'arqs'));
        $scripts = array();
        $scriptsCms = array('modal');
        $estilos = array();
        $botoes = array();
        // -
        // -- processa informações -- //
        $news = $news = $this->news_model->conteudo_dados($_var);

        // -
        // -- chama as views complementares -- //
        $dados['html'] = $news['txt'];
        // echo '<pre>';
        // var_dump($var);
        // exit;
        // -
        // -- chama as views -- //
        echo $news['txt'];
        exit;
        $tmp['corpo'] = $news['txt']; //$this->load->view('cms/news/view', $dados, true);
        $tmp['tabela'] = 'cms_conteudo';
        $tmp['title'] = $title;
        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
        $tmp['menu'] = $this->layout_cms->menu_modal($botoes);
        $tmp['resposta'] = $this->layout_cms->modal_resposta($var, $_var);
        // -
        // -- descarrega no template -- //
        $this->load->view('cms/template_modal', $tmp);
    }

    /**
     * Abre formulário para fazer o upload de imagens
     *
     * @return
     */
    function newsTeste($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Teste de newsletter' ;
        $this->tabela = 'cms_usuarios';
        $this->var = $this->uri->to_array(array('tip', 'id', 'imgs', 'arqs'));
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
         *  PROCESSA
         */
        $news = $this->news_model->conteudo_dados($this->var['id']);

        $dados['row'] = $news;
        /*
         * TEMPLATE
         */
        $this->corpo = $this->load->view('cms/news/teste_news', $dados, true);
        $this->modalRender();
    }

    function testeEnvia() {
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
        $this->form_validation->set_rules('email', 'E-mail de destino', 'trim|required|email_valid');
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            $this->newsTeste('tip:faltaCampos/id:' . $var['id']);
            //redirect('cms/usuarios/mensagemForm/tip:faltaCampos/id:'.$var['id']);
        }
        // OK VÁLIDO !!!
        else {
            // aproveito o model dos usuários para enviar
            $ret = $this->news_model->mensagem_envia($var);
            if ($ret) {
                redirect('cms/news/newsTeste/tip:envioOk/id:' . $var['id']);
            } else {
                redirect('cms/news/newsTeste/tip:erro/id:' . $var['id']);
            }
        }
    }

}

?>