<?php

/**
 * Sistema gerador de menus
 * A variavel 'co' deverá sempre setar presente.
 *
 * @version 1.0
 * @author Bruno Barros
 * @copyright 2011
 */
class Menus extends Cms_Controller {

    function __construct() {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->load->model(array('cms/menus_model', 'cms/admin_model'));
    }


    



    /**
     * Nos MENUS os grupos representam os menus principais
     *
     * @return
     */
    function index($_var = '') {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co'));
        $this->var['g'] = 0;
        $this->_var = $_var;
        $this->tabela = 'cms_conteudo';
        $this->tit_css = 'lista';
        $modulo = $this->modulo; // infos do módulo
        $conteudo = $this->menus_model->lista_conteudos($this->var, 'grupo'); // dados deste conteúdo reprocessados
        $this->title = 'Gerenciador de Menus';

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
        $this->botoes = array('check' => '', 'apagar' => '#', 'novo' => 'novo/co:' . $this->var['co']);
        $navegaOff = array('dt1', 'dt2', 'grupos');
        $listOptions = array('sortable' => 1);


        /**
         * PROCESSA INFORMAÇÕES
         */
        $dados['rows'] = $conteudo['rows']; // dados de conteúdo

        $dados['linkEditar'] = 'cms/menus/edita/co:' . $this->var['co'];

        $dados['listOptions'] = $listOptions;
        $dados['tipoGrupo'] = 'conteudo';
        /*
         * TEMPLATE
         */
        $this->corpo = $this->layout_cms->barra_navegacao($this->var, $conteudo['ttl_rows'], $navegaOff);
        $this->corpo .= $this->load->view('cms/menus/grupo_lista', $dados, true);
        $this->templateRender();

    }

    function novo($_var = '') {
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
        $this->title = 'Novo Menu ';

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
        $this->botoes = array('limpar' => 'novo/co:' . $this->var['co'],
            'voltar' => 'index/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co']);
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
        $dados['tab_title'][] = 'Novo Menu';
        $dados['tab_contt'][] = $this->load->view('cms/menus/grupo_novo', $dados, true);


        /*
         * TEMPLATE
         */
        $this->corpo = $this->load->view('cms/template_tabs', $dados, true);
        $this->templateRender();
    }

    function edita($_var = '') {
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
        $this->title = 'Editando Menu ';

        /*
         * ASSETS
         */
        $this->jquery = array('ui.tabs.182', 'ui.sortable.182');
        $this->cmsJS = array('tabs-forms', 'menus');
        $this->css = array('menus');
        $this->setNewPlugin(array('colorpicker'));

        /*
         * OPÇÕES
         */
        $this->botoes = array('apagar' => 'grupoApaga/co:' . $this->var['co'] . '/id:' . $this->var['id'],
            'voltar' => 'index/co:' . $this->var['co'],
            'novo' => 'novo/co:' . $this->var['co'],
            'salvar' => 'salva/co:' . $this->var['co'] . '/id:' . $this->var['id']
            );
        $navegaOff = array();
        $listOptions = array('sortable' => 0);
        /*
         * PROCESSA INFORMAÇÕES
         */
        $dados['row'] = $conteudo; // dados de conteúdo
        $dados['listOptions'] = $listOptions;
//        $dados['tipoGrupo'] = 'conteudo';
        $dados['menuDados'] = $this->menus_model->getMenusDados($this->var);
        $dados['modulosMenu'] = $this->menus_model->comboModulosToMenu();
//        mybug($conteudo);
        /*
         * TABS
         */
        
        $this->tabs['tab_title'][] = 'Gerenciar itens';
        $this->tabs['tab_contt'][] = $this->load->view('cms/menus/gerenciar', $dados, true);
        $this->tabs['tab_title'][] = $conteudo['titulo'];
        $this->tabs['tab_contt'][] = $this->load->view('cms/menus/grupo_edita', $dados, true);

        /*
         * TEMPLATE
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
        // echo '<pre>';
        // var_dump($_POST);
        // exit;
        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('titulo', 'Título', 'trim|required|min_length[3]');
        if ($id == ''){
            $this->form_validation->set_rules('nick', 'Apelido', 'trim|required|min_length[3]');
        }
            
        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');

        // NÃO VALIDOU !!!
        if ($this->form_validation->run() == false) {
            if ($id == ''){
                $this->novo('tip:faltaCampos');
            }
            else {
                $this->edita('id:' . $id); // editando
            }
                


        }
        // OK VÁLIDO !!!
        else {
            $ret = $this->menus_model->grupo_salva($var);
            if ($id == '') {
                if ($ret){
                    redirect('cms/menus/index/co:' . $var['co'].'/tip:ok');
                }
                else {
                    redirect('cms/menus/novo/tip:erroGravacao/co:' . $var['co']);
                }
                    
            } else {

                if ($ret) {
                    redirect('cms/menus/edita/id:' . $id . '/co:' . $var['co'].'/tip:ok'); 
                } else {
                    redirect('cms/menus/edita/id:' . $id . '/co:' . $var['co'].'/tip:erro');
                }
            }
        }
    }


   /**
    * requisição AJAX pelos conteudos do módulo
    */
    function getConteudosFromModuloId($modulo_id, $modulo_tabela = 'cms_conteudo'){

        $cont = $this->menus_model->getConteudosFromModulo($modulo_id, $modulo_tabela);

        /*
         * pega o array e monta OPTIONS
         */
        if($cont){

            $saida = '';
            foreach($cont as $dado){
                $id = $dado['id'];
                $nick = $dado['nick'];
                $titulo = $dado['titulo'];

                $saida .= '<option value="'.$id.'" title="'.$nick.'">'.$titulo.'</option>';

            }

            echo $saida;

        } else {
            echo 0;
        }

    }

    /**
     * Requisiçao AJAX para inserir itens de menu
     * Retorna HTML para inserir na lista
     * @param string $conteudo_id
     */
    function setItensMenu($conteudo_id, $grupo_id){

        // retorna array multi
        if($conteudo_id == 0){
            // insere item em branco
            $ret = $this->menus_model->insereItemBlankDeMenu($grupo_id);
        } else {
            // insere de um conteudo
            $ret = $this->menus_model->insereItensDeMenu($conteudo_id, $grupo_id);
        }
        

        // percorre array e gera saida
        $saida = '';
        foreach ($ret as $m){

            $saida .= $this->menus_model->modeloItemMenu($m);

        }

        echo $saida;

    }

    /**
     * recebe requisição AJAX para remover item do menu
     * @param <type> $item_id
     */
    function removeItemMenu($item_id){


        $dados['id'] = $item_id;
        $this->db->delete('cms_conteudo', $dados);

        echo 1;

    }

    /**
     * Recebe os dados do menu e atualiza
     */
    function atualizaDadosItemMenu(){


        $ret = $this->menus_model->salvaDadosItemMenu();

        echo $ret;

    }

    /**
     * Recebe os dados pesquisados e retorna o HTML
     */
    function pesquisaItensMenu(){

        $q = trim($this->input->post('q'));
        $grupo_id = $this->input->post('grupo_id');

        if(strlen(trim($q)) < 3){
            return 'nenhum resultado encontrado.';
        }

        $saida = $this->menus_model->getResultsPesquisa($q, $grupo_id);

        if(!is_array($saida)){
            echo 'nenhum resultado encontrado.';
            exit;
        }

        $html = '';
        foreach($saida as $r){

            $html .= '<label class="pc-respostas" title="'.$r['resumo'].'"><input name="conteudo" type="checkbox" value="'.$r['id'].'" /> '.$r['titulo'].' <span class="mod">('.$r['label'].')</span></label>';

        }

        echo $html;

//        echo ' <label class="pc-respostas"><input name="conteudo" type="checkbox" value="aa" /> Nome do conteúdo selecionado e agora sojé <span class="mod">(Páginas)</span></label>
//
//                    <label class="pc-respostas"><input name="conteudo" type="checkbox" value="bb" /> Nome do conteúdo selecionado e agora sojé <span class="mod">(Notícias)</span></label>
//
//                    <label class="pc-respostas"><input name="conteudo" type="checkbox" value="cc" /> Nome do conteúdo selecionado e agora sojé</label>
//
//          <label class="pc-respostas"><input name="conteudo" type="checkbox" value="dd" /> Nome do conteúdo selecionado e agora sojé</label>';

    }

}