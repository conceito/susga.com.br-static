<?php

/**
 * Sistema genérico para criar conteúdo. Passando o ID do conteúdo todo restante será modificado.
 * A variavel 'co' deverá sempre setar presente.
 *
 * @version 3
 * @copyright 2010
 */
class Depoimentos extends Cms_Controller {

    function __construct() {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model(array('cms/paginas_model', 'cms/admin_model'));
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
        $this->cmsJS = array('listas', 'datapicker_init', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#',
            'novo' => 'novo/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => $modulo['ordenavel'], 'destaque' => $modulo['destaques'],
            'comments' => $modulo['comments'], 'inscricao' => $modulo['inscricao'], 'email' => 0);
        $dados['listOptions'] = $listOptions;

        /*
         * PROCESSA INFORMAÇÕES
         */
        $saida = $this->paginas_model->lista_conteudos($this->var, 'conteudo', $modulo); // lista do conteúdo
        $dados['rows'] = $saida['rows']; // dados de conteúdo
        $dados['linkEditar'] = 'cms/depoimentos/edita/co:' . $this->var['co'];

        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/conteudo_lista', $dados, true);
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
        $this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'tiny_mce35b'=>'jquery.tinymce', 'jquery.charlimit');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'tinymce');
        $this->css = array();

        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'novo/co:' . $this->var['co'],
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'],
            'continuar' => 'salva/co:' . $this->var['co'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        // -
        // -- processa informações -- //
//        $modulo = $this->admin_model->dados_menus_raiz($this->var['co']);




        // combobox grupos
//        $dados['grupos'] = $this->cms_libs->combo_grupos($this->var['co'], '', false);
//
//
//
//        // combo de conteudos relacionados
//        $dados['rel'] = $this->cms_libs->combo_relacionados($this->modulo);
        $dados['listOptions'] = $listOptions;
        $this->getDadosConteudo();


//        echo '<pre>';
//        var_dump($dados);
//        exit;

        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Nova Página';
        $this->tabs['tab_contt'][] = $this->load->view('cms/depoimentos/depo_novo', $this->dados, true);
        $this->tabs['tab_title'][] = 'Galeria';
        $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_sem_galeria', $this->dados, true);
       
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
        $conteudo = $this->paginas_model->conteudo_dados($this->var); // dados deste conteúdo reprocessados
        $this->title = 'Editando ' . $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'tiny_mce35b' => 'jquery.tinymce', 'jquery.charlimit', 'ui.sortable.182', 'jquery.nyroModal');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'tinymce', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'edita/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'continuar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/depoimentos/edita/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = $listOptions;
        /*
         * PROCESSA INFORMAÇÕES
         */
        // pega os dados deste item
        $this->dados['row'] = $conteudo; // dados de conteúdo

        /**
         * TABS
         */
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/depoimentos/depo_edita', $this->dados, true);

        $this->setCamposExtra();
        $this->setGaleria();
        $this->setComentarios();
        $this->setInscricoes();

        /*
         * TEMPLATE
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
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
        if ($id == '')
            $this->form_validation->set_rules('nick', 'Apelido', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('resumo', 'Resumo', 'trim|required|max_length[400]');
        $this->form_validation->set_rules('txt', 'Conteúdo', 'trim|required|min_length[3]');
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
            $ret = $this->paginas_model->conteudo_salva($var);
            $this->cms_libs->extrasSalva($var['co'], $var['id']);

            if ($ret) {
                if ($var['op'] == 'continua')
                    redirect('cms/paginas/edita/id:' . $ret . '/co:' . $co . '/tip:ok');
                else
                    redirect('cms/paginas/index/co:' . $co . '/tip:ok');
            } else {
                redirect('cms/paginas/index/tip:erro/co:' . $co);
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
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->paginas_model->lista_conteudos($this->var, 'grupo'); // dados deste conteúdo reprocessados
        $this->title = 'Grupos de: '.$modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.datepicker.182', 'jquery.tablednd');
        $this->cmsJS = array('listas', 'datapicker_init');
        $this->css = array();

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#', 'novoGrupo' => 'grupoNovo/co:' . $this->var['co']);
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);


        /**
         * PROCESSA INFORMAÇÕES
         */
        $dados['rows'] = $conteudo['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/paginas/grupoEdita/co:' . $this->var['co'];

        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $conteudo['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/grupo_lista', $dados, true);
        $this->templateRender();

    }

    function grupoNovo($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->cms_libs->combo_grupos($this->var['co'], ''); // dados deste conteúdo reprocessados
        $this->title = 'Novo Grupo de: '.$modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'mlColorPicker');
        $this->cmsJS = array('tabs-forms', 'datapicker_init', 'colorpicker_init');
        $this->css = array('mlColorPicker');

        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'grupoNovo/co:' . $this->var['co'],
            'voltar' => 'grupos/co:' . $this->var['co'],
            'salvar' => 'grupoSalva/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => 1);
        /*
         *  PROCESSA INFORMAÇÕES
         */
        $dados['grupos'] = $conteudo;
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TABS
         */
        $dados['tab_title'][] = 'Novo Grupo';
        $dados['tab_contt'][] = $this->load->view('cms/grupo_novo', $dados, true);

        
        /*
         * TEMPLATE
         */
        $this->corpo = $this->load->view('cms/template_tabs', $dados, true);
        $this->templateRender();
    }

    function grupoEdita($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->cms_libs->conteudo_dados($this->var, 'cms_conteudo', 'grupo'); // dados deste conteúdo reprocessados
        $this->title = 'Editando Grupo ';

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.datepicker.182', 'mlColorPicker');
        $this->cmsJS = array('tabs-forms', 'colorpicker_init');
        $this->css = array('mlColorPicker');

        /*
         * OPÇÕES
         */
        $this->botoes = array('apagar' => 'grupoApaga/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'grupos/co:' . $this->var['co'],
            'novo' => 'grupoNovo/co:' . $this->var['co'],
            'salvar' => 'grupoSalva/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'continuar' => 'grupoSalva/id:' . $this->var['id'] . '/co:' . $this->var['co'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        /*
         * PROCESSA INFORMAÇÕES
         */
        $dados['row'] = $conteudo; // dados de conteúdo
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TABS
         */
        $dados['tab_title'][] = $conteudo['titulo'];
        $dados['tab_contt'][] = $this->load->view('cms/grupo_edita', $dados, true);
        /*
         * TEMPLATE
         */
        $this->corpo = $this->load->view('cms/template_tabs', $dados, true);
        $this->templateRender();
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
        if ($id == '')
            $this->form_validation->set_rules('nick', 'Apelido', 'trim|required|min_length[3]');
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
                if ($ret)
                    redirect('cms/paginas/grupos/co:' . $var['co'].'/tip:ok');
                else
                    redirect('cms/paginas/grupoNovo/tip:erroGravacao/co:' . $var['co']);
            } else {
                if ($ret) {
                    if ($var['op'] == 'continua')
                        redirect('cms/paginas/grupoEdita/id:' . $id . '/co:' . $var['co'].'/tip:ok');
                    else
                        redirect('cms/paginas/grupos/co:' . $var['co'].'/tip:ok');
                } else {
                    redirect('cms/paginas/grupos/tip:erro/co:' . $var['co']);
                }
            }
        }
    }

    /**
     * Abre formulário para fazer o upload de imagens
     *
     * @return
     */
    function mensagemForm($_var = '') {
        // -- Nome da página -- //
        $title = 'Enviando mensagem';
        // -
        // -- carrega classes -- //
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(3, array('tip', 'id', 'imgs', 'arqs'));
        $scripts = array();
        $scriptsCms = array('modal');
        $estilos = array();
        $botoes = array();
        // -
        // -- processa informações -- //
        $user = $this->paginas_model->comentario_dados($var['id']);
        // -
        // -- chama as views complementares -- //
        $dados['row'] = $user;
        // echo '<pre>';
        // var_dump($var);
        // exit;
        // -
        // -- chama as views -- //
        $tmp['corpo'] = $this->load->view('cms/comment_mensagem', $dados, true);
        $tmp['tabela'] = 'cms_usuarios';
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

    function mensagemEnvia() {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        $this->load->model(array('cms/usuarios_model'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(3, array('tip', 'id', 'imgs', 'arqs'));
        // echo '<pre>';
        // var_dump($_POST);
        // exit;
        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('comment_id', 'Identificação do comentário', 'trim|required');
        $this->form_validation->set_rules('nome', 'Nome do usuário', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('email', 'E-mail do usuário', 'trim|required|email_valid');
        $this->form_validation->set_rules('assunto', 'Assunto', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('mensagem', 'Mensagem', 'trim|required|min_length[3]');
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            $this->mensagemForm('tip:faltaCampos/id:' . $var['id']);
            //redirect('cms/usuarios/mensagemForm/tip:faltaCampos/id:'.$var['id']);
        }
        // OK VÁLIDO !!!
        else {
            // aproveito o model dos usuários para enviar
            $ret = $this->usuarios_model->mensagem_envia();
            if ($ret) {
                redirect('cms/paginas/mensagemForm/tip:envioOk/id:' . $var['id']);
            } else {
                redirect('cms/paginas/mensagemForm/tip:erro/id:' . $var['id']);
            }
        }
        // -
        // -- chama as views complementares -- //
        // -
        // -- chama as views -- //
        // -
        // -- descarrega no template -- //
    }

}

?>