<?php

/**
 * Sistema genérico para criar conteúdo. Passando o ID do conteúdo todo restante será modificado.
 * A variavel 'co' deverá sempre estar presente.
 *
 * @version 3
 * @copyright 2010
 */
class Banner extends Cms_Controller {

    protected $namespace;

    function __construct()
    {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model(array('cms/paginas_model', 'cms/admin_model', 'cms/banner_model'));

        /*
         * Permite separar os arquivos de VIEW para personalizalos
         */
        $this->namespace = "banner/"; // ao preencher usar "/" após a string
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
        $this->title = 'Lista de ' . $modulo['label'];
        $this->tabela = 'cms_conteudo';
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
            'novo' => 'novo/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => $modulo['ordenavel'], 'destaque' => $modulo['destaques'],
            'comments' => $modulo['comments'], 'inscricao' => $modulo['inscricao'], 'email' => 0);
        $dados['listOptions'] = $listOptions;

        /*
         * PROCESSA INFORMAÇÕES
         */
//        $saida = $this->paginas_model->lista_conteudos($this->var, 'conteudo', $modulo); // lista do conteúdo
        $saida = $this->banner_model->lista_banners($this->var, $modulo); // lista do conteúdo
//        mybug($saida['rows']);
        $dados['rows'] = $saida['rows']; // dados de conteúdo
        $dados['linkEditar'] = 'cms/banner/edita/co:' . $this->var['co'];
        $dados['modulo'] = $modulo;
        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/' . $this->namespace . 'conteudo_lista', $dados, true);
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
        $this->title = 'Novo ' . $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'jquery.validate', 'jquery.charlimit', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo', 'banner.novo');
//        $this->css = array('');
        $this->setNewPlugin(array('datepicker', 'chosen'));


        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co']);
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['swfUplForm'] = $this->getSwfUplForm();

        /*
         * PROCESSA
         */
//        // combo de conteudos relacionados
//        $dados['rel'] = $this->cms_libs->combo_relacionados($this->modulo);
        $dados['listOptions'] = $listOptions;
        $this->getDadosConteudo();



        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Novo Banner';
        $this->tabs['tab_contt'][] = $this->load->view('cms/' . $this->namespace . 'conteudo_novo', $this->dados, true);
//        $this->tabs['tab_title'][] = 'Galeria';
//        $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_sem_galeria', $this->dados, true);

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
    function edita($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id', 'tab'));
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->banner_model->conteudo_dados($this->var); // dados deste conteúdo reprocessados
        $this->title = 'Editando ' . $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'jquery.charlimit', 'ui.sortable.182', 'ui.button.182');
        $this->cmsJS = array('tabs-forms', 'conteudo');
//        $this->css = array();
        $this->setNewPlugin(array('nyromodal', 'datepicker', 'chosen', 'tinymce'));

        /*
         * OPÇÕES
         */
        $this->botoes = array(
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id']
        );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        $this->dados['linkReload'] = 'cms/banner/editar/co:' . $this->var['co'] . '/id:' . $this->var['id'];
        $this->dados['listOptions'] = $listOptions;
        $this->dados['swfUplForm'] = $this->setArqvs() . $this->getSwfUplForm();
        /*
         * PROCESSA INFORMAÇÕES
         */
        // pega os dados deste item
        $this->dados['row'] = $conteudo; // dados de conteúdo
//        mybug($conteudo);
        /**
         * TABS
         */
        $this->tabs['tab_title'][] = 'Informações';
        $this->tabs['tab_contt'][] = $this->load->view('cms/' . $this->namespace . 'conteudo_edita', $this->dados, true);




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
        $id = $var['id'];
        $co = $var['co'];


        $this->form_validation->set_rules('grupos', 'Grupo', 'trim|required');
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('txt', 'Link', 'trim');
        $this->form_validation->set_rules('rel', 'Conteúdo', 'trim');
        $this->form_validation->set_rules('target', 'Janela', 'trim');
        $this->form_validation->set_rules('dt1', 'Publicar', 'trim');
        $this->form_validation->set_rules('dt2', 'Remover', 'trim');
        $this->form_validation->set_rules('limit', 'Limite', 'trim');

        if ($id == '')
        {
            $this->form_validation->set_rules('hidFileID', 'Banner', 'trim|required');
        }

//        mybug($this->input->post());

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
            $ret = $this->banner_model->conteudo_salva($var);

            /*
             * Se existe arquivo insere
             */
            $this->cms_libs->salvaArquivo($ret, $this->modulo['pasta_arq']);

            if ($ret)
            {
                redirect('cms/banner/edita/id:' . $ret . '/co:' . $co . '/tip:ok');
            }
            else
            {
                redirect('cms/banner/edita/id:' . $ret . '/co:' . $co . '/tip:erro');
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
        $this->var = $this->uri->to_array(array('tip', 'co'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $modulo = $this->modulo; // infos do módulo
        $modulo['ordenavel'] = 1; // grupo é sempre ordenável
        $conteudo = $this->banner_model->lista_grupos($this->var); // dados deste conteúdo reprocessados
//        $conteudo = $this->paginas_model->flatMultidimensionalArray($conteudo, 'sub');// flat hierarchy
//        mybug($conteudo, true);

        $this->title = 'Grupos de: ' . $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('jquery.validate', 'jquery.tablednd');
        $this->cmsJS = array('listas', 'banner.novo');
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
        $dados['rows'] = $conteudo; // dados de conteúdo
        $dados['var'] = $this->var;
        $dados['linkEditar'] = 'cms/banner/grupoEdita/co:' . $this->var['co'];

        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TEMPLATE
         */
//        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $conteudo['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/' . $this->namespace . 'grupo_novo_lista', $dados, true);
        $this->templateRender();
    }

    /**
     * @tip: depreciado. form de criação na view de listagem
     * Função genérica para inserir um novo Grupo para o módulo passado como referência
     * @param string $_var
     */
    function grupoNovo($_var = '')
    {
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
        $this->title = 'Novo Grupo de: ' . $modulo['label'];

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
//        $this->css = array('');
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
    function grupoEdita($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('tip', 'co', 'id'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'novo';
        $this->conteudo = $this->banner_model->_parse_grupo($this->conteudo);
        $this->title = 'Editando Grupo ';


        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
//        $this->css = array('');

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
        $this->dados['row'] = $this->conteudo; // dados de conteúdo
        $this->dados['listOptions'] = $listOptions;


        /*
         * TABS
         */
        $this->dados['tab_title'][] = $this->conteudo['titulo'];
        $this->dados['tab_contt'][] = $this->load->view('cms/' . $this->namespace . 'grupo_edita', $this->dados, true);
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
    function grupoSalva()
    {
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
        $this->form_validation->set_rules('resumo', 'Resumo', 'trim');
        $this->form_validation->set_rules('banner_width', 'banner_width', 'trim');
        $this->form_validation->set_rules('banner_height', 'banner_height', 'trim');
        $this->form_validation->set_rules('banners_type', 'banners_type', 'trim');
        $this->form_validation->set_rules('ordem', 'Exibição', 'trim');

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
                $this->grupoEdita('id:' . $id); // editando
            }
        }
        // OK VÁLIDO !!!
        else
        {
            $ret = $this->banner_model->grupo_salva($var);
            if ($id == '')
            {
                if ($ret)
                {
                    redirect('cms/banner/grupos/co:' . $var['co'] . '/tip:ok');
                }
                else
                {
                    redirect('cms/banner/grupos/tip:erroGravacao/co:' . $var['co']);
                }
            }
            else
            {
                if ($ret)
                {
                    if ($var['op'] == 'continua')
                    {
                        redirect('cms/banner/grupoEdita/id:' . $id . '/co:' . $var['co'] . '/tip:ok');
                    }
                    else
                    {
                        redirect('cms/banner/grupos/co:' . $var['co'] . '/tip:ok');
                    }
                }
                else
                {
                    redirect('cms/banner/grupos/tip:erro/co:' . $var['co']);
                }
            }
        }
    }

    public function bannerize()
    {
        $var = $this->uri->to_array(array('id', 'op', 'co'));
        
        $post = $this->cms_libs->conteudo_dados($var);
        
        $grupos = $this->banner_model->lista_grupos(array('co' => 40));
        
//        dd($grupos);

        $dados['titulo'] = $post['titulo'];
        $dados['txtmulti'] = $post['resumo'];

        $dados['dt_ini'] = date("Y-m-d");
        $dados['dt_fim'] = "2200-12-30";
        $dados['grupo'] = $grupos[0]['id'];
        $dados['tipo'] = 'banner';

        $dados['txt'] = $post['full_uri'];
        $dados['rel'] = $post['id'];


        $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
        $dados['hr_ini'] = date("H:i:s");
        $dados['lang'] = get_lang();
        $dados['modulo_id'] = 40;
//        $dados['resumo'] = prepend_upload_file($arquivoNovo);
        $dados['status'] = 2;
        
//        dd($dados);
        $sql = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();

        // faz atualização da tabela complementar cms_banner

        $banner['target'] = '_top';
        $banner['limit'] = 0;
        $banner['conteudo_id'] = $esteid;
        $this->db->insert('cms_banner', $banner);


        // -- >> LOG << -- //
        $oque = "Novo Banner: <a href=\"" . cms_url('cms/banner/edita/co:' . $dados['modulo_id'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
        $this->cms_libs->faz_log_atividade($oque);
        
        
        redirect('cms/banner/edita/id:' . $esteid . '/co:' . $dados['modulo_id'] . '/tip:ok');
    }

}