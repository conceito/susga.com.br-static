<?php
/**
 * Sistema genérico para controler produtos e pedidos da loja. 
 * Passando o ID do conteúdo todo restante será modificado.
 * A variavel 'co' deverá sempre setar presente.
 *
 * @version 0.1
 * @copyright 2012
 */
class Loja extends Cms_Controller{
    
    function __construct() {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();
        $this->load->model('cms/loja_model', 'loja');
        $this->load->model(array('cms/calendario_model', 'cms/admin_model', 'cms/paginas_model'));
        $this->load->config('loja');
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
        $this->title = 'Lista de Produtos ';
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));

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
            'pedidos' => 'cms/loja/vendas/co:' . $this->var['co'],
            'novo' => 'cms/loja/novo/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => $modulo['ordenavel'], 'destaque' => $modulo['destaques'], 'inscricao' => $modulo['inscricao']);
        $dados['listOptions'] = $listOptions;
        /*
         * PROCESSA
         */
        $saida = $this->loja->lista_conteudos($this->var, 'conteudo', $modulo); // lista do conteúdo
        $dados['rows'] = $saida['rows']; // dados de conteúdo
//        mybug($saida);

        $dados['linkEditar'] = 'cms/loja/edita/co:' . $this->var['co'];
        $dados['linkFilter'] = 'cms/loja/index/co:' . $this->var['co'];

        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/loja/lista', $dados, true);
        $this->templateRender();
    }
    
    // ------------------------------------------------------------------------
    /**
     * Abre form para criar produto
     * @param type $_var
     */
    function novo($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Adicionar produto';

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(            
            'voltar' => 'cms/loja/index/co:' . $this->var['co'],            
            'continuar' => 'cms/loja/salva/co:' . $this->var['co']);
        
        // instancia opções de categorias
        $this->getDadosConteudo();
        
//        $navegaOff = array();
//        $listOptions = array('sortable' => 0);
//        $this->dados['listOptions'] = $listOptions;
        
//        $this->dados['swfUplForm'] = $this->getSwfUplForm();
        /*
         * PROCESSA
         */
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
        
        $this->tabs['tab_title'][] = 'Novo produto';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/novo', $this->dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }
    
    // -------------------------------------------------------------------------
    
    public function duplicar($_var = ''){
        
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->loja->conteudo_dados($this->var); // dados deste conteúdo reprocessados
        $this->title = 'Cópia de '.$conteudo['titulo'];

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
            'continuar' => 'salva_copia/co:' . $this->var['co'].'/id:'.$this->var['id']
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
        $this->tabs['tab_title'][] = 'Novo produto';
        $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_duplicar', $this->dados, true);

       
        /*
         *  TEMPLATE
         */

        $this->templateRender();
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Edição de produto
     * @param type $_var
     */
    function edita($_var = '') {
        
        $this->load->helper('string');
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Editando ' . $modulo['label'];
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
        $conteudo = $this->loja->conteudo_dados($this->var); // dados deste conteúdo reprocessados

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182', 'ui.autocomplete.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'galeria_init','handlebars', 'prod_edicao');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'tinymce', 'nyromodal', 'chosen', 'maskedinput'));

        /*
         * OPÇÕES
         */

        $this->botoes = array(
            'duplicar' => 'duplicar/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/loja/edita/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = $listOptions;
        $this->dados['swfUplForm'] = $this->setArqvs().$this->getSwfUplForm();
        /*
         * PROCESSA
         */
        $this->dados['row'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
        
        $this->getOpcoes();
        
//        mybug($this->dados['options_estoque'], true);
 
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/edita', $this->dados, true);
        
        $this->setMultiContent();
        //$this->setCamposExtra();
        $this->setGaleria();
        //$this->setInscricoes();
        $this->setPreco();
        $this->setOpcoesTab();

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }
    
    
    // -------------------------------------------------------------------------
    /**
     * Salva produto novo, e atualiza
     */
    function salva() {
        
        $this->load->library(array('form_validation'));
        
        $var = $this->uri->to_array(array('id', 'op', 'co', 'tab'));
        $id = $var['id'];
        $co = $var['co'];
        $tab = $var['tab'];

        // obrigatórios
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required');
        if ($id == ''){
            $this->form_validation->set_rules('nick', 'Endereço amigável', 'trim|required');
        }
        // não obrigatórios
        $this->form_validation->set_rules('grupos', 'grupos', 'trim');
//        $this->form_validation->set_rules('rel', 'rel', 'trim');
        $this->form_validation->set_rules('dt1', 'dt1', 'trim');
        $this->form_validation->set_rules('dt2', 'dt2', 'trim');
        $this->form_validation->set_rules('hora1', 'hora1', 'trim');
        $this->form_validation->set_rules('hora2', 'hora2', 'trim');
        $this->form_validation->set_rules('resumo', 'resumo', 'trim');
        $this->form_validation->set_rules('tags', 'tags', 'trim');
        $this->form_validation->set_rules('txt', 'txt', 'trim');    
            
        $this->form_validation->set_rules('download', 'download', 'trim');
        $this->form_validation->set_rules('download_limit', 'download_limit', 'trim');
        $this->form_validation->set_rules('estoque', 'estoque', 'trim');
        $this->form_validation->set_rules('dimensoes', 'dimensoes', 'trim');
        $this->form_validation->set_rules('peso', 'peso', 'trim');
        
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            if ($id == '') {
                $this->novo('tip:faltaCampos');
            }
            else {
                $this->edita('id:' . $id . '/tip:faltaCampos'); // editando
            }
        }
        // OK VÁLIDO !!!
        else {            
            
            $ret = $this->loja->conteudo_salva($var);
            
            // @todo: descontinuar opções extra
//            $this->cms_libs->extrasSalva($var['co'], $var['id']);
            /*
             * Se existe arquivo insere
             */
            $this->cms_libs->salvaArquivo($ret, $this->modulo['pasta_arq']);

            if ($ret) {
                redirect('cms/loja/edita/id:' . $ret . '/co:' . $co . '/tip:ok/tab:' . $tab);               
            } else {
                redirect('cms/loja/edita/id:' . $ret . '/co:' . $co . '/tip:erro/tab:' . $tab);
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
            
            redirect('cms/loja/duplicar/co:'.$co.'/id:'.$id.'/tip:erro');
            
        }
        // OK VÁLIDO
        else {
            
            $ret = $this->loja->conteudo_salva_copia($var);            

            if ($ret) {                
                redirect('cms/loja/edita/id:' . $ret . '/co:' . $co . '/tip:ok');                
            } else {
                redirect('cms/loja/duplicar/tip:erro/co:'.$co.'/id:'.$id);
            }
        }
    }
    
    // -----------------------------------------------------------------------
    /**
     * Listagem das regiões de entrega.
     * @param type $_var
     */
    public function entregas($_var = ''){
       
        
        /*
         * VARIÁVEIS
         */
        $this->title = 'Lista de Regiões de entrega ';
        $this->var = $this->uri->to_array(array('tip', 'co'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
//        $this->tit_css = 'lista';
        $modulo = $this->modulo; // infos do módulo
        $modulo['ordenavel'] = 1; // grupo é sempre ordenável
        $conteudo = $this->loja->lista_regioes($this->var);
//        mybug($conteudo, true);


        /*
         * ASSETS
         */
        $this->jquery = array('ui.datepicker.182', 'jquery.tablednd');
        $this->cmsJS = array('listas', 'datapicker_init');
        $this->css = array();

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#', 'novo' => 'entregaNovo/co:' . $this->var['co']);
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);


        /**
         * PROCESSA INFORMAÇÕES
         */
        $dados['rows'] = $conteudo; // dados de conteúdo

        $dados['linkEditar'] = 'cms/loja/entregaEdita/co:' . $this->var['co'];

        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TEMPLATE
         */
//        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $conteudo['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/loja/entrega_lista', $dados, true);
        $this->templateRender();
        
    }
    
    // -------------------------------------------------------------------------
    
    /**
     * Form para criar uma nova região.
     * 
     * @param string $_var
     */
    public function entregaNovo($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
//        $this->tit_css = 'lista';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->cms_libs->combo_grupos($this->var['co'], ''); // dados deste conteúdo reprocessados
        $this->title = 'Nova Região';

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms');
        $this->css = array();
        $this->setNewPlugin(array('colorpicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'entregaNovo/co:' . $this->var['co'],
            'voltar' => 'entregas/co:' . $this->var['co'],            
            'continuar' => 'entregaSalva/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => 1);
        /*
         *  PROCESSA INFORMAÇÕES
         */
        $this->dados['grupos'] = $conteudo;
        $this->dados['listOptions'] = $listOptions;
        $this->dados['tipoGrupo'] = 'conteudo';
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
//        $this->dados['rel'] = $this->posts_model->getGrupoComboHierarchy($this->var);
        /*
         * TABS
         */
        $this->tabs['tab_title'][] = 'Nova Região';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/entrega_novo', $this->dados, true);

        
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }
    
    // ---------------------------------------------------------------------
    
    /**
     * Salvar dados da região de entrega
     * Redireciona de acordo com as variáveis de entrada
     */
    public function entregaSalva() {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op', 'co', 'tab'));
        $id = $var['id'];
        $tab = $var['tab'];
     
        
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
                $this->entregaNovo('tip:faltaCampos');
            else
                $this->entregaEdita('id:' . $id); // editando


        }
        // OK VÁLIDO !!!
        else {
            
            $ret = $this->loja->entrega_salva($var);            
         
            if ($ret) {                    
                redirect('cms/loja/entregaEdita/id:' . $ret . '/co:' . $var['co'].'/tip:ok/tab:' . $tab);
            } else {
                redirect('cms/loja/entregaEdita/id:' . $ret . '/co:' . $var['co'].'/tip:erro/tab:' . $tab);
            }
            
        }
    }
    
    // ---------------------------------------------------------------------
    
    // -------------------------------------------------------------------------
    /**
     * Edição de produto
     * @param type $_var
     */
    function entregaEdita($_var = '') {
        
        $this->load->helper('string');
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Editando ' . $modulo['label'];
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
        $conteudo = $this->loja->entrega_dados($this->var); // dados deste conteúdo reprocessados
//        mybug($conteudo);
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'galeria_init','entregas');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'tinymce', 'nyromodal', 'chosen', 'maskedinput'));

        /*
         * OPÇÕES
         */

        $this->botoes = array(
            'voltar' => 'entregas/co:' . $this->var['co'],
            'salvar' => 'entregaSalva/co:' . $this->var['co'] . '/id:' . $this->var['id']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/loja/entregaEdita/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = $listOptions;
//        $this->dados['swfUplForm'] = $this->setArqvs().$this->getSwfUplForm();
        /*
         * PROCESSA
         */
        $this->dados['row'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
        
//        $this->getOpcoes();
        
//        mybug($this->dados['options_estoque'], true);
 
        $this->tabs['tab_title'][] = 'Região de entrega';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/entrega_edita', $this->dados, true);
        $this->tabs['tab_title'][] = 'Datas de exceção';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/entrega_excecao', $this->dados, true);
        
        //$this->setMultiContent();
        //$this->setCamposExtra();
        //$this->setGaleria();
        //$this->setInscricoes();
        //$this->setPreco();
        //$this->setOpcoesTab();

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }
    
    // ------------------------------------------------------------------------
    
    /**
     * Lista das vendas realizadas
     *
     * @return
     */
    function vendas($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo;
        $this->title = 'Vendas ';
        $this->tabela = 'cms_extratos';
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));

        /*
         * ASSETS
         */
        $this->jquery = array('jquery.tablednd', 'ui.autocomplete.182');
        $this->cmsJS = array('listas', 'vendas');
        $this->css = array();
        $this->setNewPlugin(array('nyromodal', 'datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'check' => '', 'apagar' => '#',            
            'imprimir' => 'cms/loja/imprimir_fatura/co:' . $this->var['co']
        );
        $navegaOff = array();
        $dados['listOptions'] = array('sortable' => 0, 'destaque' => 0, 'inscricao' => 0);
        /*
         * PROCESSA
         */
        $saida = $this->loja->get_vendas(); // lista do conteúdo
        $dados['rows'] = $saida['rows']; // dados de conteúdo
//        mybug($saida);

        $dados['linkEditar'] = 'cms/loja/vendaEdita/co:' . $this->var['co'];
        $dados['linkFilter'] = 'cms/loja/vendas/co:' . $this->var['co'];

        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        //$this->corpo = '';
        $this->corpo .= $this->load->view('cms/loja/vendas', $dados, true);
        $this->templateRender();
    }
    
    // ------------------------------------------------------------------------
    
    public function vendaEdita($_var = ''){
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Venda ' . $modulo['label'];
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
                
        $conteudo = $this->loja->get_full_extrato($this->var); // dados deste conteúdo reprocessados

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'vendas');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'tinymce', 'nyromodal'));

        /*
         * OPÇÕES
         */

        $this->botoes = array(
            'apagar' => 'duplicar/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'vendas/co:' . $this->var['co']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/loja/vendaEdita/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = $listOptions;
        /*
         * PROCESSA
         */
        $this->dados['extr'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
        
        
//        mybug($this->dados['extr'], true);
 
        $this->tabs['tab_title'][] = 'Detalhes';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/venda_infos', $this->dados, true);
        $this->tabs['tab_title'][] = 'Produtos';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/venda_carrinho', $this->dados, true);
        $this->tabs['tab_title'][] = 'Cliente';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/venda_cliente', $this->dados, true);
        $this->tabs['tab_title'][] = 'Histórico';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/venda_historico', $this->dados, true);
        

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }
    
    // -----------------------------------------------------------------------
    /**
     * Recebe ID do extrato via AJAX e cria fatura do pedido.
     * 
     * @param type $fatura_id
     */
    public function gerar_fatura($fatura_id){
        
        // pega padrão
        $pre = $this->config->item('fatura_preffix');
        $fatura = $pre.$fatura_id;
        
        $this->db->update('cms_extratos', array('fatura' => $fatura), array('id'=>$fatura_id));
        
       echo '<a href="'.cms_url('cms/loja/imprimir_fatura/extrato:'.$fatura_id).'" target="_blank">' . $fatura . '</a>';
        
    }
    
    // ------------------------------------------------------------------------
    
    /**
     * Exibe fatura para impressão
     * 'extrato' pode chegar como int, ou ID-ID-ID...
     */
    public function imprimir_fatura(){
        
        $vars = $this->uri->to_array(array('extrato'));
        $ids = explode('-', $vars['extrato']);
        
        $html = '';
        
        $subview['loja'] = $this->loja->get_configuracoes();

        for ($x = 0; $x < count($ids); $x++)
        {
            $id = $ids[$x];
            $subview['pageBreakAfter'] = ($x == count($ids)-1) ? 'auto' : 'always';
            $subview['f'] = $this->loja->get_full_extrato($id);
            $html .= $this->load->view('cms/loja/fatura', $subview, true);            
        }
        
        $view['faturas'] = $html;
        $this->load->view('cms/loja/fatura_layout', $view);
            
        
    }


    // -------------------------------------------------------------------------
    /**
     * Salva alterações do status do pedido.
     * Envia mensagem para o usuário.
     * Retorna HTML para view.
     */
    public function extrato_update(){
        
        $extrato_id  = $this->input->post('extrato_id');
        $situacao    = $this->input->post('situacao');
	$informar    = $this->input->post('informar');
        $comentarios = $this->input->post('comentarios');
        
        // opções de situação
        $sit_options = $this->config->item('status_transacao');
        $anotacao = $sit_options[$situacao];
        
              
        $upd = array(
            'anotacao' => $anotacao,
            'status' => $situacao
        );
        
        $this->db->update('cms_extratos', $upd, array('id' => $extrato_id));
        
        
        $dados['data']       = date("Y-m-d");
        $dados['hora']       = date("H:i:s");
        $dados['extrato_id'] = $extrato_id;
        $dados['anotacao']   = $anotacao;
        $dados['status']     = $situacao;
        $dados['obs']        = $comentarios;
        $dados['notificado'] = ($informar == 'false') ? 0 : 1;
        
        $ret = $this->db->insert('cms_extrat_hist', $dados);
        
        // emite notificação para usuário
        if($dados['notificado'] == 1){
//            $this->load->model('cms/usuarios_model');
            $this->loja->send_status_notification($dados);
        }
        
        
        $html = '<table class="table table-striped">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '  <th style="width:125px">Adicionado em</th>';
        $html .= '  <th style="width:150px">Situação</th>';
        $html .= '  <th>Comentários</th>';
        $html .= '  <th>Cliente notificado</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '  <td>'. formaPadrao($dados['data']) .' '. $dados['hora'] .'</td>';
        $html .= '  <td>'. $anotacao . '</td>';
        $html .= '  <td>' . $comentarios . '</td>';
        $html .= '  <td>'. (($dados['notificado'] == '1') ? 'Sim' : 'Não') .'</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        
        
        echo $html;
        
        
    }
    
    // ----------------------------------------------------------------------
    
    public function descontos($_var = ''){
        
        /*
         * VARIÁVEIS
         */
        $this->title = 'Descontos e cupons ';
        $this->var = $this->uri->to_array(array('tip', 'co'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_precos';
//        $this->tit_css = 'lista';
        $modulo = $this->modulo; // infos do módulo
        $modulo['ordenavel'] = 1; // grupo é sempre ordenável
        $conteudo = $this->loja->descontos_get();
//        mybug($conteudo, true);


        /*
         * ASSETS
         */
        $this->jquery = array('jquery.tablednd', 'ui.autocomplete.182');        
        $this->cmsJS = array('listas', 'descontos');
        $this->css = array();
        $this->setNewPlugin(array('nyromodal', 'datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#', 'novo' => 'descontosNovo/co:' . $this->var['co']);
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);


        /**
         * PROCESSA INFORMAÇÕES
         */
        $dados['rows'] = $conteudo['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/loja/descontosEdita/co:' . $this->var['co'];
        $dados['linkFilter'] = 'cms/loja/descontos/co:' . $this->var['co'];
        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TEMPLATE
         */
//        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $conteudo['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/loja/descontos_lista', $dados, true);
        $this->templateRender();
        
    }
    
    // ------------------------------------------------------------------------
    /**
     * Abre form para criar promoção ou cupon
     * 
     * @param type $_var
     */
    function descontosNovo($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Novo desconto';

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(            
            'voltar' => 'cms/loja/descontos/co:' . $this->var['co'],            
            'continuar' => 'cms/loja/descontosSalva/co:' . $this->var['co']);
        
        // instancia opções de categorias
        $this->getDadosConteudo();
        
//        $navegaOff = array();
//        $listOptions = array('sortable' => 0);
//        $this->dados['listOptions'] = $listOptions;
        
//        $this->dados['swfUplForm'] = $this->getSwfUplForm();
        /*
         * PROCESSA
         */
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
        
        $this->tabs['tab_title'][] = 'Desconto';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/descontos_novo', $this->dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }
    
    
    // -------------------------------------------------------------------------
    /**
     * Salva produto novo, e atualiza
     */
    function descontosSalva() {
        
        $this->load->library(array('form_validation'));
        
        $var = $this->uri->to_array(array('id', 'op', 'co', 'tab'));
        $id = $var['id'];
        $co = $var['co'];
        $tab = $var['tab'];

        // obrigatórios
        $this->form_validation->set_rules('titulo', 'Nome para identificar o desconto', 'trim|required');
        
        // não obrigatórios
        $this->form_validation->set_rules('regra', 'Tipo de desconto', 'trim');
        $this->form_validation->set_rules('data', 'Data', 'trim');
        $this->form_validation->set_rules('termino', 'Término', 'trim');
        $this->form_validation->set_rules('valor', 'Valor', 'trim');
        $this->form_validation->set_rules('verificador', 'verificador', 'trim');
        $this->form_validation->set_rules('grupo', 'Grupo de usuário', 'trim');
        
        
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            if ($id == '') {
                $this->descontosNovo('tip:faltaCampos');
            }
            else {
                $this->descontosEdita('id:' . $id . '/tip:faltaCampos'); // editando
            }
        }
        // OK VÁLIDO !!!
        else {            
            
            $ret = $this->loja->desconto_salva($var);
            
            // @todo: descontinuar opções extra
//            $this->cms_libs->extrasSalva($var['co'], $var['id']);
            /*
             * Se existe arquivo insere
             */
            $this->cms_libs->salvaArquivo($ret, $this->modulo['pasta_arq']);

            if ($ret) {
                redirect('cms/loja/descontosEdita/id:' . $ret . '/co:' . $co . '/tip:ok/tab:' . $tab);               
            } else {
                redirect('cms/loja/descontosEdita/id:' . $ret . '/co:' . $co . '/tip:erro/tab:' . $tab);
            }
        }
    }
    
    
    // -------------------------------------------------------------------------
    /**
     * Edição de produto
     * @param type $_var
     */
    function descontosEdita($_var = '') {
        
        $this->load->helper('string');
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Editando desconto';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
        $conteudo = $this->loja->desconto_dados($this->var); // dados deste conteúdo reprocessados
//        mybug($conteudo);
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'handlebars', 'descontos');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'nyromodal', 'chosen', 'maskedinput'));

        /*
         * OPÇÕES
         */

        $this->botoes = array(
            'voltar' => 'descontos/co:' . $this->var['co'],
            'salvar' => 'descontosSalva/co:' . $this->var['co'] . '/id:' . $this->var['id']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/loja/descontosEdita/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = $listOptions;
        /*
         * PROCESSA
         */
        $this->dados['des'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
        
        //$this->getOpcoes();
        
//        mybug($this->dados['options_estoque'], true);
 
        $this->tabs['tab_title'][] = 'Desconto';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/descontos_edita', $this->dados, true);
        
        
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }
    
    
    // -------------------------------------------------------------------------
    /**
     * 
     * @param type $_var
     */
    function configuracoes($_var = '') {
        
        $this->load->helper('string');
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Configurações da ' . $modulo['label'];
        $this->tabela = 'cms_config';
        $this->var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
        $conteudo = $this->loja->get_configuracoes(); 

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.button.182', 'jquery.charlimit', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'galeria_init','handlebars', 'prod_edicao');
//        $this->css = array();
        $this->setNewPlugin(array('datepicker', 'tinymce', 'nyromodal', 'chosen', 'maskedinput'));

        /*
         * OPÇÕES
         */

        $this->botoes = array(
            'voltar' => 'configuracoes/co:' . $this->var['co'],
            'salvar' => 'configuracoesSalva/co:' . $this->var['co'] . '/id:' . $this->var['id']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/loja/configuracoes/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = $listOptions;
//        $this->dados['swfUplForm'] = $this->setArqvs().$this->getSwfUplForm();
        /*
         * PROCESSA
         */
        $this->dados['con'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
        
        
//        mybug($conteudo);
 
        $this->tabs['tab_title'][] = 'Configurações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/configuracoes', $this->dados, true);
        
      
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }
    
    // -------------------------------------------------------------------------
    /**
     * Salva configurações
     */
    function configuracoesSalva() {
        
        $this->load->library(array('form_validation'));
        
        $var = $this->uri->to_array(array('co', 'tab'));       
        $co = $var['co'];
        $tab = $var['tab'];

        $this->loja->configuracoes_salva($var);
        redirect('cms/loja/configuracoes/co:' . $co . '/tip:ok/tab:' . $tab);               
         
        
    }
    
    // -----------------------------------------------------------------------
    /**
     * Recebe requisição AJAX para retornar array de produtos
     */
    public function get_products_by_json(){
        
        $term  = $this->input->get('term');
//        $limit = $this->input->get('pagesize');
        
        $value = $this->loja->search_for_autocomplete($term);
        
//        $value = array(
//            array('id' => 1, 'label' => $term, 'value' => $term),
//            array('id' => 2, 'label' => 'Beltrano', 'value' => 'Beltrano')
//        );
        
        echo json_encode($value);
        
    }
    
    // ------------------------------------------------------------------------
    /**
     * Recebe requisição AJAX para clonar as opções de um produto para outro.
     */
    public function cloneOptions(){
        
        // produto que será clonado
        $prod_clone_id = $this->input->post('prod_clone_id');
        
        // produto que receberá as opções
        $prod_ref_id = $this->input->post('prod_ref_id');
        
        $ret = $this->loja->clone_options_product($prod_clone_id, $prod_ref_id);
        
//        echo json_encode($ret);
        echo $ret;
        
    }
    
}