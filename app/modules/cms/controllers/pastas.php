<?php

/**
 * Gerencia as pastas de Imagens, Arquivos e Álbuns com seus conteúdos.
 * A variável 't' definirá sé é Imagen == 0, álbum == 1, arquivos == 2
 *
 * @version 1
 * @copyright 2010
 */
class Pastas extends Cms_Controller {

//    var $modulo;

    function __construct() {

        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model(array('cms/pastas_model', 'cms/admin_model'));

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
        $this->title = $this->pastas_model->labels($this->var['co'], 'title');
        $this->tabela = 'cms_pastas';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));

        /*
         * ASSETS
         */
        $this->jquery = array('jquery.tablednd');
        $this->cmsJS = array('listas');
//        $this->css = array('');
        $this->setNewPlugin(array('nyromodal', 'datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#',
            'novo' => 'cms/pastas/novo/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => $this->modulo['ordenavel'], 'destaque' => $this->modulo['destaques']);
        $dados['listOptions'] = $listOptions;
        /*
         * PROCESSA
         */
        // pega os itens do menu RAIZ
        $saida = $this->pastas_model->lista_conteudos($this->var, 'conteudo', $this->modulo);

//        mybug($saida['rows']);
        
        $dados['rows'] = $saida['rows']; // dados de conteúdo
        $dados['linkEditar'] = 'cms/pastas/edita/co:' . $this->var['co'];

        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff, 'cms_pastas');

        $this->corpo .= $this->load->view('cms/pastas_lista', $dados, true);
        $this->templateRender();
    }

    function novo($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = $this->pastas_model->labels($this->var['co'], 'novo');
        $this->tabela = 'cms_pastas';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'jquery.charlimit', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
        $this->css = array();
        $this->setNewPlugin(array('datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'cms/pastas/novo/co:' . $this->var['co'],
            'voltar' => 'cms/pastas/index/co:' . $this->var['co'],
            'salvar' => 'cms/pastas/salva/co:' . $this->var['co'],
            'continuar' => 'cms/pastas/salva/co:' . $this->var['co'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $dados['listOptions'] = $listOptions;
        /*
         *  PROCESSA
         */
        // combobox grupos
        $dados['tipo'] = $this->pastas_model->labels($this->var['co'], 'tipo');
        $dados['grupos'] = $this->cms_libs->combo_grupos($this->var['co'], '', false, '', 'cms_pastas');

       
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/pastas_novo', $dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    function edita($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = $this->pastas_model->labels($this->var['co'], 'edita');
        $this->tabela = 'cms_pastas';
        $this->tit_css = 'edita';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;

//        mybug($modulo);

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'jquery.charlimit', 'ui.sortable.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'galeria_init');
//        $this->css = array('');
        $this->setNewPlugin(array('datepicker', 'nyromodal'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(            
            'voltar' => 'cms/pastas/index/co:' . $this->var['co'],
            'salvar' => 'cms/pastas/salva/co:' . $this->var['co'] . '/id:' . $this->var['id']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        /*
         * PROCESSA
         */
        // pega os dados deste item
        $saida = $this->pastas_model->conteudo_dados($this->var);
        $this->dados['row'] = $saida; // dados de conteúdo
        $quantgal = $saida['quantGal'];
        $this->dados['tipo'] = $this->pastas_model->labels($this->var['co'], 'tipo');
        // combobox grupos
        $this->dados['grupos'] = $this->cms_libs->combo_grupos($this->var['co'], $saida['grupo'], false, '', 'cms_pastas');

        $this->dados['listOptions'] = $listOptions;
        
        // tipo de arquivo
//        $contoller = ($this->var['co'] == 2) ? 'arquivo' : 'multImg';

        // dados para galeria
        $this->dados['labelAddImage'] = 'Adicionar arquivos na pasta';
        $this->dados['linkAddImage'] = 'cms/upload/multImg/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $this->var['id'] . '/onde:pasta';

        $this->dados['linkReload'] = 'cms/pastas/edita/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['galery'] = $this->pastas_model->arquivos_dados($this->var); //
        $this->dados['addImgFromFolder'] = 'cms/imagem/explorer/co:0/id:' . $this->var['id'];


        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = $this->pastas_model->labels($this->var['co'], 'abaArqs') . ' (' . $quantgal . ')';
        if ($this->var['co'] == 2){
            $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_arquivos', $this->dados, true); // arquivos
        } else{
            $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_galeria', $this->dados, true); // imagens
        }
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/pastas_edita', $this->dados, true);

        
        /*
         *  template
         */
//        $this->corpo = $this->load->view('cms/template_tabs', $dados, true);
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
        $this->form_validation->set_rules('dt1', 'Data', 'trim|required|min_length[10]');
        $this->form_validation->set_rules('titulo', 'Nome', 'trim|required|min_length[3]');
        if ($id == '')
            $this->form_validation->set_rules('nick', 'Apelido', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('txt', 'Descrição', 'trim|required|max_length[400]');
        // se for imagem este são requeridos
        if ($this->input->post('tipo') != 2) {
            $this->form_validation->set_rules('mini_w', 'Mini W', 'trim|required|min_length[2]');
            $this->form_validation->set_rules('mini_h', 'Mini H', 'trim|required|min_length[2]');
            $this->form_validation->set_rules('max_w', 'Max W', 'trim|required|min_length[2]');
            $this->form_validation->set_rules('max_h', 'Max H', 'trim|required|min_length[2]');
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
            $ret = $this->pastas_model->conteudo_salva($var);

            if ($ret) {                
                redirect('cms/pastas/edita/id:' . $ret . '/co:' . $co . '/tip:ok');                
            } else {
                redirect('cms/pastas/edita/id:' . $ret . '/co:' . $co . '/tip:erro');
            }
        }
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
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Lista de Grupos de Pastas';
        $this->tabela = 'cms_pastas';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->var['g'] = 0;

        /*
         * ASSETS
         */
        $this->jquery = array('ui.datepicker.182', 'jquery.tablednd');
        $this->cmsJS = array('listas', 'datapicker_init');
        $this->css = array();

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '',
            'apagar' => '#',
            'novoGrupo' => 'grupoNovo/co:' . $this->var['co']);
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'pastas';

        // pega os itens do menu RAIZ
        $saida = $this->pastas_model->lista_conteudos($this->var, 'grupo');
        $dados['rows'] = $saida['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/pastas/grupoEdita/co:' . $this->var['co'];
      
        /*
         * TEMPLATE
         */
        $this->corpo = $this->load->view('cms/grupo_lista', $dados, true);
        $this->templateRender();
    }

    function grupoNovo($_var = '') {


        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Novo Grupo de Pastas';
        $this->tabela = 'cms_pastas';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->var['g'] = 0;


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
//        $this->css = array('');
        $this->setNewPlugin(array('colorpicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'cms/pastas/grupoNovo/co:' . $this->var['co'],
            'voltar' => 'cms/pastas/grupos/co:' . $this->var['co'],
            'salvar' => 'cms/pastas/grupoSalva/co:' . $this->var['co']);

        $navegaOff = array();
        $listOptions = array('sortable' => 1);
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'pastas';
        $dados['rel'] = false;



        $this->tabs['tab_title'][] = 'Novo Grupo';
        $this->tabs['tab_contt'][] = $this->load->view('cms/grupo_novo', $dados, true);

        $this->templateRender();

      
    }

    function grupoEdita($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Editando Grupo de Pastas';
        $this->tabela = 'cms_pastas';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id'));


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
//        $this->css = array('');
        $this->setNewPlugin(array('datepicker', 'colorpicker'));

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
        // -
        // -- processa informações -- //
        // combobox grupos
        $saida = $this->pastas_model->conteudo_dados($this->var);
        $dados['row'] = $saida; // dados de conteúdo
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'pastas';
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = $saida['titulo'];
        $this->tabs['tab_contt'][] = $this->load->view('cms/grupo_edita', $dados, true);
        // -
        // -- chama as views -- //

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
            $ret = $this->pastas_model->grupo_salva($var);
            if ($id == '') {
                if ($ret)
                    redirect('cms/pastas/grupos/co:' . $var['co'] . '/tip:ok');
                else
                    redirect('cms/pastas/grupoNovo/tip:erroGravacao/co:' . $var['co'] . '/tip:erro');
            } else {
                if ($ret) {
                    if ($var['op'] == 'continua')
                        redirect('cms/pastas/grupoEdita/id:' . $id . '/co:' . $var['co'] . '/tip:ok');
                    else
                        redirect('cms/pastas/grupos/co:' . $var['co'] . '/tip:ok');
                } else {
                    redirect('cms/pastas/grupos/tip:erro/co:' . $var['co'] . '/tip:erro');
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
     * Abre player em janela modal
     * */
    function player() {
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(3, array('v'));
        $video = $var['v'];

        $this->load->view('cms/player_video', $var);
    }

}
