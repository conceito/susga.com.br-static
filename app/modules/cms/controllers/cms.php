<?php
/* 
 * Página inicial do CMS
 */
class Cms extends Cms_Controller{

    function  __construct() {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model(array('cms/painel_model', 'cms/admin_model'));
//        $this->output->enable_profiler(true);
    }


    /**
     * Carrega página inicial do CMS -> resumo.
     *
     * @return view
     */
    function index($_var = ''){
  
        /*
         * recebe variaveis
         */
        $this->title = 'Painel';
        $this->tit_css = 'painel';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;

        $this->jquery = array('jquery.ajaxify', 'jquery.charlimit', 'ui.interactions.182');
        $this->cmsJS = array('painel');
//        $this->css = array();
        $this->setNewPlugin(array('nyromodal'));
       
        // -
        // -- processa informações -- //

        $dados['paineis'] = $this->painel_model->getPanels();
//        $dados['blocos'][] = $this->painel_model->painel_mensagens();
//        $dados['blocos'][] = $this->painel_model->painel_oquefazer();
//        $dados['blocos'][] = $this->painel_model->painel_suporte();

        $this->corpo = $this->load->view('cms/painel/painel_grid', $dados, true);
        $this->templateRender();
    }

    /**
     * recebe dados via AJAX e salva ordem para este admin
     */
    function salvaPainelOrdem(){

        $col1 = $this->input->post('column1');
        $col2 = $this->input->post('column2');

        $ret = $this->painel_model->salvaOrdemPaineis();

        echo $ret;

    }

    function mensagens($_var = '')
    {

/*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Quadro de Mensagens';
        $this->tabela = 'cms_sis_mens';
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'erro', 'imgs', 'arqs', 't'));
        $this->_var = $_var;
        /*
         * ASSETS
         */
        $this->jquery = array();
        $this->cmsJS = array();
        $this->css = array();

        /*
         * OPÇÕES
         */
        $this->botoes = array('Não lidas' => 'cms/cms/mensagens/t:0',
            'Lidas' => 'cms/cms/mensagens/t:1',
            'Todas' => 'cms/cms/mensagens');



        // -
        // -- processa informações -- //
        $mens = $this->painel_model->mensagens_internas_lista(100);
        
        // -
        // -- chama as views complementares -- //
        $dados['mens'] = $mens; // ID do conteudo
        $dados['tipo'] = $this->var['t'];

  
        // -
        // -- chama as views -- //
        $this->corpo = $this->load->view('cms/painel/mensagens_todas', $dados, true);

        
        $this->modalRender();
    }

}
?>
