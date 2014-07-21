<?php

/**
 * Controller das operações do CMS3. Edita Menu, Backup, etc
 *
 * @version 3
 * @copyright 2009
 */
class Administracao extends Cms_Controller {

    function __construct()
    {

        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();


        $this->load->model(array('cms/admin_model'));
    }

    /**
     * Lista as opçoes do menu no Nível 0 RAIZ
     *
     * @return
     */
    function menu($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->title = 'Menu do Gerenciador';
        $this->tabela = 'cms_modulos';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt'));
        /*
         * ASSETS
         */
        $this->jquery = array('ui.datepicker.182', 'jquery.tablednd');
        $this->cmsJS = array('listas', 'datapicker_init');
        $this->css = array();
        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '', 'apagar' => '#', 'novo' => 'menuNovo');
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);
        /*
         * PROCESSA INFORMAÇÕES
         */
        $saida = $this->admin_model->menus_raiz($this->var);
        $dados['rows'] = $saida['rows']; // dados de conteúdo
        $dados['listOptions'] = $listOptions;
        $dados['linkEditar'] = 'cms/administracao/menuEdita';
        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/admin/menu_lista', $dados, true);
        $this->templateRender();
    }

    function menuNovo()
    {
        /*
         * VARIÁVEIS
         */
        $this->title = 'Novo Módulo';
        $this->tabela = 'cms_modulos';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt'));
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms', 'modulos');
        $this->css = array();
        /*
         * OPÇÕES
         */


        $this->load->model(array('cms/pastas_model'));


        $this->botoes = array('limpar' => 'cms/administracao/menuNovo',
            'voltar' => 'cms/administracao/menu',
            'salvar' => 'cms/administracao/menuSalva');
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);
        $dados['listOptions'] = $listOptions;
        // -
        // -- processa informações -- //
        // pega os itens do menu RAIZ
        // $saida = $this->admin_model->menus_raiz($var);
        // $dados['rows'] = $saida['rows']; // dados de conteúdo
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Menu principal';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/modulo_novo', '', true);
        // $dados['tab_title'][1] = 'Sub menus';
        // $dados['tab_contt'][1] = $this->load->view('cms/admin/modulosub_novo', '', true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    function menuEdita($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->title = 'Editando Módulo';
        $this->tabela = 'cms_modulos';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'id'));

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms', 'modulos');
        $this->css = array();
        $this->load->model(array('cms/pastas_model'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('apagar' => 'menuApaga/id:' . $this->var['id'], 'voltar' => 'menu', 'novo' => 'menuNovo',
            'salvar' => 'menuSalva/id:' . $this->var['id'], 'continuar' => 'menuSalva/id:' . $this->var['id'] . '/op:continua');
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);
        /*
         * PROCESSA INFORMAÇÕES
         */
        $saida = $this->admin_model->dados_menu($this->var);

        $dados['row'] = $saida; // dados de conteúdo
        $saida2 = $this->admin_model->dados_submenus($this->var);
        $dados['sub'] = $saida2;
        $dados['listOptions'] = $listOptions;
        $dados['camposExtra'] = $this->admin_model->extraCamposDados($saida['extra']);
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Menu principal';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/modulo_edita', $dados, true);
        $this->tabs['tab_title'][] = 'Sub menus';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/modulosub_lista', $dados, true);
        $this->tabs['tab_title'][] = 'Novo submenu';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/modulosub_novo', '', true);
        $this->tabs['tab_title'][] = 'Campos Extra';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/campoextra_lista', $dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    /**
     * Recebe submissão do form para salvar informaçoes no BD
     *
     * @return
     */
    function menuSalva()
    {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));

        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op'));
        $id = $var['id'];

//        mybug($this->input->post());
        // -- processa informações -- //
        $this->form_validation->set_rules('label', 'Label', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('uri', 'URI', 'trim');
        $this->form_validation->set_rules('tipo', 'Quem pode ver', 'trim');
        $this->form_validation->set_rules('tabela', 'Tabela', 'trim');
        $this->form_validation->set_rules('ordenavel', 'Ordenável', 'trim');
        $this->form_validation->set_rules('comments', 'Comentários', 'trim');
        $this->form_validation->set_rules('destaques', 'Destaques', 'trim');
        $this->form_validation->set_rules('inscricao', 'Inscrição', 'trim');
//        $this->form_validation->set_rules('modulos', 'Se relaciona com', 'trim');
        $this->form_validation->set_rules('pastas_0', 'Pasta de imagens', 'trim');
        $this->form_validation->set_rules('pastas_2', 'Pasta de arquivos', 'trim');
        $this->form_validation->set_rules('pastaAjuda', 'Pasta de ajuda', 'trim');
        $this->form_validation->set_rules('status', 'Status', 'trim');


        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {
            if ($id == '')
            {
                $this->menuNovo();
            }
            else
            {
                $this->menuEdita('id:' . $id); // editando
            }
        }
        // OK VÁLIDO !!!
        else
        {
            if ($id == '')
            {
                $ret = $this->admin_model->menu_salva_novo();
                if ($ret)
                {
                    redirect('cms/administracao/menu');
                }
                else
                {
                    redirect('cms/administracao/menuNovo/tip:erro');
                }
            }
            else
            {
                $ret = $this->admin_model->menu_atualiza($var);
                $this->admin_model->extras_atualiza($var);
                
                if ($ret)
                {
                    if ($var['op'] == 'continua')
                    {
                        redirect('cms/administracao/menuEdita/id:' . $id);
                    }
                    else
                    {
                        redirect('cms/administracao/menu');
                    }
                }
                else
                {
                    redirect('cms/administracao/menu/tip:erro');
                }
            }
        }
    }

    /**
     * Lista os administradores do sistema
     *
     * @return
     */
    function admins($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->title = 'Administradores';
        $this->tabela = 'cms_admin';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'co', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip'));
        $this->_var = $_var;
        /*
         * ASSETS
         */
        $this->jquery = array('ui.datepicker.182', 'jquery.tablednd', 'jquery.nyroModal');
        $this->cmsJS = array('listas', 'datapicker_init', 'nyroModal_init');
        $this->css = array('nyroModal');
        /*
         * OPÇÕES
         */
        $this->botoes = array('check' => '',
            'apagar' => '#',
            'novo' => 'adminNovo/co:' . $this->var['co']);
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 0);
        /*
         *  PROCESSA INFORMAÇÕES
         */
        $saida = $this->admin_model->lista_administradores($this->var);
        $dados['rows'] = $saida['rows']; // dados de conteúdo
        $dados['listOptions'] = $listOptions;
        $dados['linkEditar'] = 'cms/administracao/adminEdita/co:' . $this->var['co'];
        /**
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/admin/admins_lista', $dados, true);
        $this->templateRender();
    }

    function adminNovo($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->title = 'Novo Administrador';
        $this->tabela = 'cms_modulos';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip'));
        $this->_var = $_var;
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'jquery.nyroModal');
        $this->cmsJS = array('tabs-forms', 'admins', 'nyroModal_init');
        $this->css = array('nyroModal');
        /*
         * OPÇÕES
         */
        $this->botoes = array('limpar' => 'adminNovo',
            'voltar' => 'admins',
            'salvar' => 'adminSalva');
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 0);
        /*
         *  PROCESSA INFORMAÇÕES
         */
        $dados['listOptions'] = $listOptions;
        $dados['comboModulos'] = $this->admin_model->combo_modulos();
        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Informações do Admin';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/admin_novo', '', true);
        $this->tabs['tab_title'][] = 'Permissões';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/admin_novo_perm', $dados, true);

        /**
         * TEMPLATE
         */
        $this->templateRender();
    }

    function adminEdita()
    {
        /*
         * VARIÁVEIS
         */
        $this->title = 'Editando Administrador';
        $this->tabela = 'cms_admin';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'id'));
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'jquery.nyroModal');
        $this->cmsJS = array('tabs-forms', 'admins', 'nyroModal_init');
        $this->css = array('nyroModal');
        /*
         * OPÇÕES
         */
        $this->botoes = array('apagar' => 'adminApaga/id:' . $this->var['id'],
            'voltar' => 'admins',
            'novo' => 'adminNovo',
            'salvar' => 'adminSalva/id:' . $this->var['id'],
            'continuar' => 'adminSalva/id:' . $this->var['id'] . '/op:continua');
        /*
         *  PROCESSA INFORMAÇÕES
         */
        $admin_id = $this->phpsess->get('admin_id', 'cms');
        $admin_tipo = $this->phpsess->get('admin_tipo', 'cms');
        // pega os dados deste item
        $saida = $this->admin_model->dados_administrador($this->var);
        $dados['row'] = $saida; // dados de conteúdo
        $dados['comboModulos'] = $this->admin_model->combo_modulos($saida['mod']);

        /*
         * ABAS
         */
        $this->tabs['tab_title'][] = 'Informações do Admin';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/admin_edita', $dados, true);
        // Apenas o admin GOD ou o criador deste admn pode editar suas permissões
        if ($saida['criador'] == $admin_id || $admin_tipo == 0)
        {
            $this->tabs['tab_title'][] = 'Permissões';
            $this->tabs['tab_contt'][] = $this->load->view('cms/admin/admin_perm_edita', $dados, true);
        }

//        $dados['tab_title'][] = 'Outros dados';
//        $dados['tab_contt'][] = 'falta fazer isso...';
        /*
         *  TEMPLATE
         */
        $this->templateRender();
    }

    /**
     * Recebe submissão do form para salvar informaçoes no BD
     *
     * @return
     */
    function adminSalva()
    {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op'));
        $id = $var['id'];
//        echo '<pre>';
//        var_dump($var);
//        exit;
        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('nome', 'Nome', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');
        $this->form_validation->set_rules('nick', 'Apelido', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('login', 'Login', 'trim|required|min_length[5]|alpha_numeric');
        if (strlen(trim($this->input->post('senha'))) > 0)
        {
            $this->form_validation->set_rules('senha', 'Senha', 'trim|required|min_length[5]|alpha_numeric');
            $this->form_validation->set_rules('confirmar', 'Confirmar Senha', 'trim|required|min_length[5]|matches[senha]');
        }
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {
            if ($id == '')
                $this->adminNovo('tip:faltaCampos');
            else
                $this->adminEdita('id:' . $id); // editando
        }
        // OK VÁLIDO !!!
        else
        {
            if ($id == '')
            {
                $ret = $this->admin_model->administrador_salva($var);
                if ($ret)
                    redirect('cms/administracao/admins/tip:ok');
                else
                    redirect('cms/administracao/adminNovo/tip:erroGravacao');
            } else
            {
                $ret = $this->admin_model->administrador_salva($var);

                if ($ret)
                {
                    if ($var['op'] == 'continua')
                        redirect('cms/administracao/adminEdita/id:' . $id . '/tip:ok');
                    else
                        redirect('cms/administracao/admins/tip:ok');
                } else
                {
                    redirect('cms/administracao/admins/tip:erro');
                }
            }
        }
    }

    /**
     * Lista as combobox no Nível 0 RAIZ
     *
     * @return
     */
    function variaveis($_var = '')
    {

        /*
         * VARIÁVEIS
         */
        $this->title = 'Variáveis do Sistema';
        $this->tabela = 'cms_combobox';
        $this->tit_css = 'lista';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt'));
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
            'novo' => 'variaveisNovo');
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);
        /*
         * PROCESSA INFORMAÇÕES
         */
        $saida = $this->admin_model->variaveis_raiz($this->var);
        $dados['rows'] = $saida['rows']; // dados de conteúdo
        $dados['listOptions'] = $listOptions;
        $dados['linkEditar'] = 'cms/administracao/variaveisEdita';
        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $saida['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/admin/variaveis_lista', $dados, true);
        $this->templateRender();
    }

    function variaveisNovo()
    {
        /*
         * VARIÁVEIS
         */
        $this->title = 'Nova Variável de Sistema';
        $this->tabela = 'cms_combobox';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt'));
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
        $this->css = array();
        /*
         * OPÇÕES
         */

        $this->botoes = array('limpar' => 'cms/administracao/variaveisNovo',
            'voltar' => 'cms/administracao/variaveis',
            'salvar' => 'cms/administracao/variaveisSalva');
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);
        $dados['listOptions'] = $listOptions;


        // -
        // -- chama as views complementares -- //
        $this->tabs['tab_title'][] = 'Dados principais';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/variaveis_novo', '', true);

        $this->templateRender();
    }

    function variaveisEdita($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->title = 'Editando Variáveis';
        $this->tabela = 'cms_modulos';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'id'));
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms');
        $this->css = array();
        /*
         * OPÇÕES
         */
        $this->botoes = array('apagar' => 'variaveisApaga/id:' . $this->var['id'],
            'voltar' => 'variaveis',
            'novo' => 'variaveisNovo',
            'salvar' => 'variaveisSalva/id:' . $this->var['id'],
            'continuar' => 'variaveisSalva/id:' . $this->var['id'] . '/op:continua');
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);
        /*
         * PROCESSA INFORMAÇÕES
         */
        $saida = $this->admin_model->dados_variavel_raiz($this->var);
        $dados['row'] = $saida; // dados de conteúdo
        $saida2 = $this->admin_model->dados_subitens($this->var);
        $dados['sub'] = $saida2;
        $dados['listOptions'] = $listOptions;
        /*
         * TABS
         */
        $this->tabs['tab_title'][] = 'Subitens';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/variaveis_sub_lista', $dados, true);

        $this->tabs['tab_title'][] = '+ Novo subitem';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/variaveis_sub_novo', '', true);

        $this->tabs['tab_title'][] = 'Dados de: ' . $saida['titulo'];
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/variaveis_edita', $dados, true);
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    /**
     * Recebe submissão do form para salvar informaçoes no BD
     *
     * @return
     */
    function variaveisSalva()
    {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('id', 'op'));
        $id = $var['id'];
        // echo '<pre>';
        // var_dump($_POST);
        // exit;
        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('descricao', 'Descrição', 'trim|required|min_length[3]');
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {
            if ($id == '')
                $this->variaveisNovo();
            else
                $this->variaveisEdita('id:' . $id); // editando
        }
        // OK VÁLIDO !!!
        else
        {
            if ($id == '')
            {
                $ret = $this->admin_model->variaveis_salva_novo();
                if ($ret
                )
                    redirect('cms/administracao/variaveis');
                else
                    redirect('cms/administracao/variaveisNovo/tip:erro');
            } else
            {
                $ret = $this->admin_model->variaveis_atualiza($var);

                if ($ret)
                {
                    if ($var['op'] == 'continua'
                    )
                        redirect('cms/administracao/variaveisEdita/id:' . $id);
                    else
                        redirect('cms/administracao/variaveis');
                } else
                {
                    redirect('cms/administracao/variaveis/tip:erro');
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
     * Controller de configurações do Gerenciador
     * @param string $_var
     */
    function config($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $this->title = 'Configurações do CMS';
        $this->tabela = 'cms_config';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tab'));
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms', 'admins');
        $this->css = array();
        /*
         * OPÇÕES
         */
        $this->botoes = array('restaurar' => 'configPadroes', 'salvar' => 'configSalva');
        /*
         * PROCESSA INFORMAÇÕES
         */
        $this->load->model(array('cms/painel_model'));
        $dados['con'] = $this->admin_model->config_dados(); // dados de configuração
        $dados['swfUplForm'] = $this->getSwfUplForm();
        /*
         * TABS
         */
        $this->tabs['tab_title'][] = 'Principais';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/config_1', $dados, true);
        // apenas para GOD
        if ($this->phpsess->get('admin_tipo', 'cms') == 0)
        {
            $this->tabs['tab_title'][] = 'Newsletter';
            $this->tabs['tab_contt'][] = $this->load->view('cms/admin/config_news', $dados, true);
        }


        $this->tabs['tab_title'][] = 'Banco de Dados';
        $this->tabs['tab_contt'][] = $this->load->view('cms/admin/config_2', '', true);
        $this->tabs['tab_title'][] = 'Últimas atividades';
        $this->tabs['tab_contt'][] = $this->painel_model->painel_atividades();
        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    /**
     * Limpa valor e redireciona de volta 
     */
    function removeLogo()
    {

        // pega dados
        $this->db->where('campo', 'logotipo');
        $result = $this->db->get('cms_config');
        $config = $result->row_array();

        // remove da configuração
        $this->db->where('campo', 'logotipo');
        $this->db->update('cms_config', array('valor' => ''));

        // apaga arquivo
        $arq['img'] = 0;
        $arq['nome'] = $config['valor'];
        $this->cms_libs->deleta_arquivo($arq);

        redirect('cms/administracao/config/co:1');
    }

    function configSalva()
    {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));

        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('chave', 'Chave Geral', 'trim|required');

        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false)
        {
            $this->config('tip:faltaCampos');
        }
        // OK VÁLIDO !!!
        else
        {
            $ret = $this->admin_model->config_salva();
            redirect('cms/administracao/config/tip:ok');
        }
    }

    function configPadroes()
    {
        $ret = $this->admin_model->config_padroes();
        redirect('cms/administracao/config/tip:ok');
    }

    /**
     * Faz o backup do banco de dados. As opções estão disponíveis dentro da função.
     * Carrega dbutil.
     * Carrega helper: file.
     * Redireciona para Admins::backupbd().
     */
    function fazBackupbd()
    {
        $this->load->library(array('cms_libs'));

        $prefs2 = array('format' => 'gzip', // gzip, zip, txt
            'filename' => 'mybackup.sql', // File name - NEEDED ONLY WITH ZIP FILES
            'add_drop' => true, // Whether to add DROP TABLE statements to backup file
            'add_insert' => true, // Whether to add INSERT data to backup file
            'newline' => "\r\n" // Newline character used in backup file
        );
        // nome do arquivo
        $ver = $this->config->item('cms_ver');
        $nome_arq = date("Y-m-d") . '_MeuCMS_v' . $ver . '.gz';
        // Load the DB utility class
        $this->load->dbutil();
        // Backup your entire database and assign it to a variable
        $backup = &$this->dbutil->backup($prefs2);
        // Load the file helper and write the file to your server
        // $this->load->helper('file');
        // write_file('./upl/arqs/' . $nome_arq, $backup);
        // Load the download helper and send the file to your desktop
        $this->load->helper('download');
        force_download($nome_arq, $backup);

        $oque = "Realizou Backup do BD: <b>" . $nome_arq . "</b>";
        $this->cms_libs->faz_log_atividade($oque);
        // redirect('cms/admins/backupbd/ok');
    }

    function fazRestoreBd()
    {
        // configura upload
        $config['upload_path'] = fisic_path() . $this->config->item('upl_arqs');
        $config['allowed_types'] = 'sql|gz';
        $config['max_size'] = 50000;
        $config['remove_spaces'] = true;
        // carrega classes
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file'))
        {
            $error = $this->upload->display_errors();
            echo '<pre>';
            var_dump($error);
            exit;
        }
        else
        {
            // sucesso
            $data = $this->upload->data();
//        	 echo '<pre>';
//            var_dump($data);
//            exit;
            if ($data['file_ext'] == '.gz')
            {
                // descompacta GZ
                ob_start();
                readgzfile($data['full_path']);
                $gz = ob_get_clean();
                // salva em arquivo
                $this->load->helper('file');
                write_file('./upl/arqs/restore.sql', $gz);
                $caminhoBackup = $data['file_path'] . 'restore.sql';
                @unlink($data['full_path']); // arq original
            }
            else
            {
                $caminhoBackup = $data['full_path'];
            }



            $ret = $this->admin_model->restaura_bd($caminhoBackup);
            // apaga arquivo original GZ
            @unlink($caminhoBackup);
            if ($ret)
            {
                redirect('cms/administracao/config/tab:2/tip:ok');
            }
            else
            {
                redirect('cms/administracao/config/tab:2/tip:erro');
            }
        }
    }

    function ajudaModulo($_var = '')
    {

        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $pasta = $this->cms_libs->pasta_dados($modulo['pasta_ajuda']);
        $this->tabela = 'cms_pastas';
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'arqs', 'imgs'));
        $this->_var = $_var;
        $this->title = $pasta['titulo'];
        /*
         * ASSETS
         */
        $this->load->model('cms/pastas_model');
        $this->jquery = array('jquery.nyroModal');
        $this->cmsJS = array('swfobject', 'padrao-modal', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array();



        // -
        // -- processa informações -- //
        // retorna os arquivos desta pasta
        $arquivos = $this->pastas_model->arquivos_dados($pasta);

//        mybug($arquivos);
        // gera menu de nagevação da modal
        if ($arquivos)
        {
            $menu = array();
            foreach ($arquivos as $a)
            {

                $label = urlencode($a['descricao']);
                $label = (strlen($label) == 0) ? 'link' . $a['id'] : $label;

                $menu[$label] = '#id' . $a['id'];
            }
        }
        $this->botoes = $menu;
//        mybug($this->botoes);
        // -
        // -- chama as views complementares -- //
        $dados['bs'] = base_url();
        $dados['pastaUpl'] = $this->config->item('upl_arqs');
        $dados['pasta'] = $pasta;
        $dados['arquivos'] = $arquivos;
        $dados['botoes'] = $this->layout_cms->menu_modal($menu);
//        mybug($dados);
        // -
        // -- chama as views -- //
        $this->corpo = $this->load->view('cms/admin/ajuda_modulo', $dados, true);

        $this->modalRender();
    }

    /**
     * Controller de configurações especiais EDEM
     * 
     * @param string $_var
     */
    function prefs($_var = '')
    {
//        mybug('porra');
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Configurações do site';
        $this->tabela = 'cms_config';
        $this->tit_css = 'novo';
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tab'));
        $conteudo = $this->admin_model->get_prefs();
        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182');
        $this->cmsJS = array('tabs-forms', 'admins');
        $this->css = array();
        /*
         * OPÇÕES
         */
        $this->botoes = array('salvar' => 'prefsSalva/co:' . $this->var['co']);
        /*
         * PROCESSA INFORMAÇÕES
         */
//        $this->load->model(array('cms/painel_model'));
//        $dados['con'] = $this->admin_model->config_dados(); // dados de configuração
//        $dados['swfUplForm'] = $this->getSwfUplForm();
        /*
         * PROCESSA
         */
        $this->dados['con'] = $conteudo; // dados de conteúdo
        $this->dados['botoes'] = $this->botoes;
        $this->dados['modulo'] = $modulo;
        $this->dados['c'] = $this->c;
        /*
         * TABS
         */
        $this->tabs['tab_title'][] = 'Principais';
        $this->tabs['tab_contt'][] = $this->load->view('cms/prefs/principais', $this->dados, true);

        /*
         * TEMPLATE
         */
        $this->templateRender();
    }

    // -------------------------------------------------------------------------
    /**
     * Salva configurações
     */
    function prefsSalva()
    {

        $this->load->library(array('form_validation'));

        $var = $this->uri->to_array(array('co', 'tab'));
        $co = $var['co'];
        $tab = $var['tab'];

        $this->admin_model->prefs_salva($var);
        redirect('cms/administracao/prefs/co:' . $co . '/tip:ok/tab:' . $tab);
    }

}
