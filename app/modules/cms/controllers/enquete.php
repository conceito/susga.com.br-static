<?php

/**
 * Emquete CMS
 * A variavel 'co' deverá sempre setar presente.
 *
 * @version 3
 * @copyright 2010
 */
class Enquete extends Cms_Controller {

    function __construct() {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model(array('cms/enquete_model', 'cms/admin_model'));
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
        $this->title = 'Lista de Enquetes';
        $this->tabela = 'cms_enquete_per';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->var['g'] = 0;

        /*
         * ASSETS
         */
        $this->jquery = array('jquery.tablednd');
        $this->cmsJS = array('listas');
//        $this->css = array();
        $this->setNewPlugin(array('nyromodal', 'datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#',
            'novo' => 'novo/co:' . $this->var['co']);
        $navegaOff = array('grupos');
        $listOptions = array('sortable' => 0, 'destaque' => 0);
        $dados['listOptions'] = $listOptions;
        /*
         * PROCESSA
         */
        $saida = $this->enquete_model->lista_conteudos($this->var);
        $dados['rows'] = $saida['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/enquete/edita/co:' . $this->var['co'];

        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/enquete/lista', $dados, true);
        $this->templateRender();
    }

    function novo($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Nova Enquete';
        $this->tabela = 'cms_enquete_per';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms', 'enquete');
//        $this->css = array('');
        $this->setNewPlugin(array('nyromodal'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'novo/co:' . $this->var['co'],
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'],
            'continuar' => 'salva/co:' . $this->var['co'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        /*
         * PROCESSA
         */
        // combobox grupos
        $dados['grupos'] = $this->cms_libs->combo_grupos($this->var['co'], '', false);
        // combo de conteudos relacionados
        $dados['rel'] = $this->cms_libs->combo_relacionados($modulo);
        $dados['listOptions'] = $listOptions;
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/enquete/novo', $dados, true);
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
        $this->title = 'Editando Enquete';
        $this->tabela = 'cms_enquete_per';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->var['g'] = 0;

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'enquete');
//        $this->css = array('');
        $this->setNewPlugin(array('nyromodal', 'datepicker'));

        /*
         * OPÇÕES
         */

        $this->botoes = array('limpar' => 'edita/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'continuar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/op:continua');
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        /*
         * PROCESSA
         */
        // pega os dados deste item
        $saida = $this->enquete_model->conteudo_dados($this->var);
        $dados['row'] = $saida; // dados de conteúdo
        // combo de conteudos relacionados
        $dados['rel'] = false;

        // combobox grupos
        $dados['listOptions'] = $listOptions;
        // dados para galeria
        $dados['linkReload'] = $this->botoes['limpar'] . '/tab:2';

        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Opções';
        $this->tabs['tab_contt'][] = $this->load->view('cms/enquete/edita_opcoes', $dados, true);
        $this->tabs['tab_title'][] = $saida['titulo'];
        $this->tabs['tab_contt'][] = $this->load->view('cms/enquete/edita', $dados, true);
        $this->tabs['tab_title'][] = 'Relatório';
        $this->tabs['tab_contt'][] = 'quem e quando';

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
        if ($id != '')
            $this->form_validation->set_rules('dt1', 'Data', 'trim|required|min_length[10]');
        $this->form_validation->set_rules('titulo', 'Pergunta', 'trim|required|min_length[3]');
        if ($id == '') {
            $this->form_validation->set_rules('opc_1', 'Opção #1', 'trim|required|min_length[3]');
            $this->form_validation->set_rules('opc_2', 'Opção #2', 'trim|required|min_length[3]');
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
            $ret = $this->enquete_model->conteudo_salva($var);
            if ($id == '') {

                if ($ret)
                    redirect('cms/enquete/index/co:' . $co . '/tip:ok');
                else
                    redirect('cms/enquete/novo/tip:erroGravacao/co:' . $co);
            } else {
                if ($ret) {
                    if ($var['op'] == 'continua')
                        redirect('cms/enquete/edita/id:' . $ret . '/co:' . $co . '/tip:ok');
                    else
                        redirect('cms/enquete/index/co:' . $co . '/tip:ok');
                } else {
                    redirect('cms/enquete/index/tip:erro/co:' . $co);
                }
            }
        }
    }

}

?>