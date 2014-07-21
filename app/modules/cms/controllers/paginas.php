
<?php

/**
 * Sistema genérico para criar conteúdo. Passando o ID do conteúdo todo restante será modificado.
 * A variavel 'co' deverá sempre estar presente.
 *
 * @version 3
 * @copyright 2010
 */
class Paginas extends Cms_Controller {

    protected $namespace;
//    public $botoes = array();

    function __construct() {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model(array('cms/paginas_model', 'cms/admin_model'));

        /*
         * Permite separar os arquivos de VIEW para personalizalos
         */
        $this->namespace = ""; // ao preencher usar "/" após a string
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
        $this->botoes = array('check' => '', 'apagar' => '#',
            'novo' => 'novo/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => $modulo['ordenavel'], 'destaque' => $modulo['destaques'],
            'comments' => $modulo['comments'], 'inscricao' => $modulo['inscricao'], 'email' => 0);
        $dados['listOptions'] = $listOptions;

        /*
         * PROCESSA INFORMAÇÕES
         */
//        $saida = $this->paginas_model->lista_conteudos($this->var, 'conteudo', $modulo); // lista do conteúdo
//        $saida = $this->paginas_model->lista_paginas($this->var, $modulo); // lista do conteúdo
        $this->paginas_model->set_modulo($modulo);
        $this->paginas_model->set_vars($this->var);
        $saida = $this->paginas_model->get_arquitetura(false); // lista do conteúdo
//        mybug($saida['rows'], true);
        $dados['rows'] = $saida['rows']; // dados de conteúdo
        $dados['linkEditar'] = 'cms/paginas/edita/co:' . $this->var['co'];
        $dados['linkFilter'] = 'cms/paginas/index/co:' . $this->var['co'];

        /*
         * TEMPLATE
         */
        $this->namespace = 'paginas/';
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/'.$this->namespace.'conteudo_lista', $dados, true);
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
        $this->dados['c'] = $this->c;


        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Nova Página';
        $this->tabs['tab_contt'][] = $this->load->view('cms/'.$this->namespace.'conteudo_novo', $this->dados, true);

       
        /*
         *  TEMPLATE
         */

        $this->templateRender();
    }

    /**
     * Abre form para edição do conteúdo
     *
     * @param string $_var Opcional
     */
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
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'tinymce', 'nyromodal', 'chosen'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            //'apagar' => 'apaga/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'duplicar' => 'duplicar/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/paginas/edita/co:'.$this->var['co'].'/id:'.$this->var['id'].'/tab:2';
        $this->dados['listOptions'] = $listOptions;
        $this->dados['swfUplForm'] = $this->setArqvs().$this->getSwfUplForm();
        /*
         * PROCESSA INFORMAÇÕES
         */
        // pega os dados deste item
        $this->dados['row'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
//        mybug($this->dados['rel']);
        /**
         * TABS
         */
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/'.$this->namespace.'conteudo_edita', $this->dados, true);

        $this->setMultiContent();
        $this->setCamposExtra();
        $this->setGaleria();
        $this->setComentarios();
        $this->setInscricoes();
        $this->setMetadados();
        

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    // -------------------------------------------------------------------------
    /**
     * Salva e atualiza dados do conteúdo
     */
    function salva() {
        
        // loading...
        $this->load->library(array('form_validation'));

        // uri vars
        $var = $this->uri->to_array(array('id', 'op', 'co', 'tab'));
        $id = $var['id'];
        $co = $var['co'];
        $tab = $var['tab'];
        
        // obrigatório
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
        if ($id == ''){
            $this->form_validation->set_rules('nick', 'Endereço amigável', 'trim|required|min_length[3]');
        }


        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            if ($id == ''){
                $this->novo('tip:faltaCampos');
            }
            else{
                $this->edita('id:' . $id . '/tip:faltaCampos'); // editando
            }
        }
        // OK VÁLIDO
        else {
            $ret = $this->paginas_model->conteudo_salva($var);
            $this->cms_libs->extrasSalva($var['co'], $var['id']);
            /*
             * Se existe arquivo insere
             */
            $this->cms_libs->salvaArquivo($ret, $this->modulo['pasta_arq']);

            if ($ret) {                
               redirect('cms/paginas/edita/id:' . $ret . '/co:' . $co . '/tip:ok/tab:' . $tab, 'meta');
            } else {
                redirect('cms/paginas/index/tip:erro/co:' . $co .'/tab:' . $tab, 'meta');
            }
        }
    }
    // -------------------------------------------------------------------------
    /**
     * Salva e atualiza dados do conteúdo
     */
    function salva_copia() {
        
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
        if ($this->form_validation->run() == false) {
            
            redirect('cms/paginas/duplicar/co:'.$co.'/id:'.$id.'/tip:erro');
            
        }
        // OK VÁLIDO
        else {
            
            $ret = $this->paginas_model->conteudo_salva_copia($var);            

            if ($ret) {                
                redirect('cms/paginas/edita/id:' . $ret . '/co:' . $co . '/tip:ok', 'meta');
            } else {
                redirect('cms/paginas/duplicar/tip:erro/co:'.$co.'/id:'.$id, 'meta');
            }
        }
    }
    
    // -------------------------------------------------------------------------
    /**
     * Abre informações básicas para personalizar o novo conteúdo e 
     * gera novo registro baseado nele.
     * @param type $_var
     */
    public function duplicar($_var = ''){
        
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->paginas_model->conteudo_dados($this->var); // dados deste conteúdo reprocessados
        $this->title = 'Cópia de '.$conteudo['titulo'];

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
            'continuar' => 'salva_copia/co:' . $this->var['co'].'/id:'.$this->var['id'] . '/op:continua');
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
        $this->tabs['tab_title'][] = 'Nova Página';
        $this->tabs['tab_contt'][] = $this->load->view('cms/'.$this->namespace.'conteudo_duplicar', $this->dados, true);

       
        /*
         *  TEMPLATE
         */

        $this->templateRender();
        
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
        $this->var = $this->uri->to_array(array('tip', 'co'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $modulo = $this->modulo; // infos do módulo
        $modulo['ordenavel'] = 1; // grupo é sempre ordenável
        $conteudo = $this->paginas_model->lista_grupos($this->var); // dados deste conteúdo reprocessados
        $conteudo = $this->paginas_model->flatMultidimensionalArray($conteudo, 'sub');// flat hierarchy
//        mybug($conteudo, true);

        $this->title = 'Grupos de: '.$modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('jquery.tablednd');
        $this->cmsJS = array('listas');
        $this->css = array();
        $this->setNewPlugin(array('datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#', 'novoGrupo' => 'grupoNovo/co:' . $this->var['co']);
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);


        /**
         * PROCESSA INFORMAÇÕES
         */
        $dados['rows'] = $conteudo; // dados de conteúdo

        $dados['linkEditar'] = 'cms/paginas/grupoEdita/co:' . $this->var['co'];

        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TEMPLATE
         */
//        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $conteudo['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/grupo_lista', $dados, true);
        $this->templateRender();

    }

    /**
     * Função genérica para inserir um novo Grupo para o módulo passado como referência
     * @param string $_var
     */
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
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
        $this->setNewPlugin(array('colorpicker'));
        /*
         * OPÇÕES
         */
        $this->botoes = array(
            //'limpar' => 'grupoNovo/co:' . $this->var['co'],
            'voltar' => 'grupos/co:' . $this->var['co'],
            'salvar' => 'grupoSalva/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => 1);
        /*
         *  PROCESSA INFORMAÇÕES
         */
        $this->dados['grupos'] = $conteudo;
        $this->dados['listOptions'] = $listOptions;
        $this->dados['tipoGrupo'] = 'conteudo';
        $this->dados['rel'] = $this->paginas_model->getGrupoComboHierarchy($this->var);
        /*
         * TABS
         */
        $this->tabs['tab_title'][] = 'Novo Grupo';
        $this->tabs['tab_contt'][] = $this->load->view('cms/grupo_novo', $this->dados, true);

        
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    /**
     * Aber form para editar informações dos Grupos
     * @param string $_var
     */
    function grupoEdita($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('tip', 'co', 'id'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $this->conteudo = $this->cms_libs->conteudo_dados($this->var, 'cms_conteudo', 'grupo'); // dados deste conteúdo reprocessados
        $this->title = 'Editando Grupo ';

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
        $this->setNewPlugin(array('colorpicker'));
        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'apagar' => 'grupoApaga/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'grupos/co:' . $this->var['co'],
            'novo' => 'grupoNovo/co:' . $this->var['co'],
            'salvar' => 'grupoSalva/co:' . $this->var['co'] . '/id:' . $this->var['id']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        /*
         * PROCESSA INFORMAÇÕES
         */
        $this->var['relacionamento'] = $this->conteudo['rel'];// para combobox de relacionamento
        $this->dados['row'] = $this->conteudo; // dados de conteúdo
        $this->dados['listOptions'] = $listOptions;
        $this->dados['tipoGrupo'] = 'conteudo';
        $this->dados['rel'] = $this->paginas_model->getGrupoComboHierarchy($this->var);

//        mybug($this->dados['rel'], true);

        /*
         * TABS
         */
        $this->dados['tab_title'][] = $this->conteudo['titulo'];
        $this->dados['tab_contt'][] = $this->load->view('cms/grupo_edita', $this->dados, true);
        /*
         * TEMPLATE
         */
        $this->corpo = $this->load->view('cms/template_tabs', $this->dados, true);
        $this->templateRender();
    }
    
    /**
     * Salvar dados dos Grupos
     * Redireciona de acordo com as variáveis de entrada
     */
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
            if ($id == ''){
                $this->grupoNovo('tip:faltaCampos');
            }
            else{
                $this->grupoEdita('id:' . $id); // editando
            }

        }
        // OK VÁLIDO !!!
        else {
            $ret = $this->paginas_model->grupo_salva($var);
            if ($id == '') {
                if ($ret)
                    redirect('cms/paginas/grupos/co:' . $var['co'].'/tip:ok', 'meta');
                else
                    redirect('cms/paginas/grupoNovo/tip:erroGravacao/co:' . $var['co'], 'meta');
            } else {
                if ($ret) {                    
                    redirect('cms/paginas/grupoEdita/id:' . $id . '/co:' . $var['co'].'/tip:ok', 'meta');
                } else {
                    redirect('cms/paginas/grupoEdita/id:' . $id . '/co:' . $var['co'].'/tip:erro', 'meta');
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

    /**
     * Lista as tags disponíveis para este módulo
     * Controller de TAGS para todos os módulos, assim como o controller 'grupos'
     * Diferente de 'grupos' as tags tem um campo 'tipo' = "tag" e uma
     * tabela para relacionar com o conteúdo (cms_tag_conteudo)
     *
     */
    function tags($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $modulo = $this->modulo; // infos do módulo
        $modulo['ordenavel'] = 1;
//        $conteudo = $this->paginas_model->lista_conteudos($this->var, 'tag', $modulo); // dados deste conteúdo reprocessados
        $conteudo = $this->paginas_model->lista_tags($this->var); // dados deste conteúdo reprocessados
        $this->title = 'Tags de: '.$modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.datepicker.182', 'jquery.tablednd');
        $this->cmsJS = array('listas', 'datapicker_init');
        $this->css = array();

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#', 'novaTag' => 'tagNovo/co:' . $this->var['co']);
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);


        /**
         * PROCESSA INFORMAÇÕES
         */
        $dados['rows'] = $conteudo; // dados de conteúdo        
        

        $dados['linkEditar'] = 'cms/paginas/tagEdita/co:' . $this->var['co'];

        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TEMPLATE
         */
//        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $conteudo['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/grupo_lista', $dados, true);
        $this->templateRender();

    }

    /**
     * Função genérica para inserir uma nova Tag para o módulo passado como referência
     * @param string $_var
     */
    function tagNovo($_var = '') {
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
        $this->title = 'Nova Tag de: '.$modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
        $this->css = array();
        $this->setNewPlugin(array('colorpicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'tags/co:' . $this->var['co'],
            'salvar' => 'tagSalva/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => 1);
        /*
         *  PROCESSA INFORMAÇÕES
         */
        $dados['grupos'] = $conteudo;
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        $dados['rel'] = false;
        /*
         * TABS
         */
        $this->tabs['tab_title'][] = 'Nova Tag';
        $this->tabs['tab_contt'][] = $this->load->view('cms/grupo_novo', $dados, true);


        /*
         * TEMPLATE
         */
        $this->templateRender();
    }
    
    /**
     * Salvar dados dos Grupos
     * Redireciona de acordo com as variáveis de entrada
     */
    function tagSalva() {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op', 'co'));
        $id = $var['id'];

        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
        if ($id == ''){
            $this->form_validation->set_rules('nick', 'Apelido', 'trim|required|min_length[3]');
        }
            
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            if ($id == '')
                $this->tagNovo('tip:faltaCampos');
            else
                $this->tagEdita('id:' . $id); // editando


        }
        // OK VÁLIDO !!!
        else {
            $ret = $this->paginas_model->tag_salva($var);
            if ($id == '') {
                if ($ret)
                    redirect('cms/paginas/tags/co:' . $var['co'].'/tip:ok');
                else
                    redirect('cms/paginas/tagNovo/tip:erroGravacao/co:' . $var['co']);
            } else {
                if ($ret) {                    
                     redirect('cms/paginas/tagEdita/id:' . $id . '/co:' . $var['co'].'/tip:ok');                    
                } else {
                    redirect('cms/paginas/tagEdita/id:' . $id . '/co:' . $var['co'].'/tip:erro');
                }
            }
        }
    }

    /**
     * Aber form para editar informações das tags
     * @param string $_var
     */
    function tagEdita($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
//        $conteudo = $this->cms_libs->conteudo_dados($this->var, 'cms_conteudo', 'tag'); // dados deste conteúdo reprocessados
        $this->title = 'Editando Tag ';

//        mybug($conteudoOfi);

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
        $this->css = array();
        $this->setNewPlugin(array('colorpicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'apagar' => 'grupoApaga/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'tags/co:' . $this->var['co'],
            'novo' => 'tagNovo/co:' . $this->var['co'],
            'salvar' => 'tagSalva/co:' . $this->var['co'] . '/id:' . $this->var['id']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        /*
         * PROCESSA INFORMAÇÕES
         */
        $dados['row'] = $this->conteudo; // dados de conteúdo
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TABS
         */
        $this->tabs['tab_title'][] = $this->conteudo['titulo'];
        $this->tabs['tab_contt'][] = $this->load->view('cms/grupo_edita', $dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

}

?>