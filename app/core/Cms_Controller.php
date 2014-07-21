<?php

/*
 * CONTROLLER GENÉRICO DO CMS
 */

class Cms_Controller extends MY_Controller {

    public $tmp; // dados despejados no template
    public $var; // variaveis via URI
    public $_var; // variaveis via argumento
    public $tit_css = ''; // css aplicado no título das páginas
    public $modulo = NULL; // dados sobre módulo, se existir
    public $botoes = NULL; // parte do template com botões de ação no CMS
    public $tabs = NULL; // parte do template com as tabs de conteúdo
    public $corpo = '';
    public $conteudo = NULL;
    public $css = array(); // array final de css
    public $cmsJS = array(); // array final do cms
    public $cmsJSextra = array('bootstrap/bootstrap-dropdown'); // arqvs JS inseridos extra controller
    public $json_vars = array();  // JSON variaveis
    public $cssExtra = array(); // arqvs CSS inseridos extra controller
    public $dados = array(); // array com todo conteúdo que será utilizado nas views
    public $tb = 'cms_conteudo'; // tabela de trabalho do módulo
    public $setSwfUpl = false; // init campo swf upload
    public $c = array(); // uri segments
    protected  $namespace = '/';
    
    /**
     * Armazena os widgets que serão expostos para as views
     * @var array
     */
    public $widgets = array();
            

    function __construct() {
        parent::__construct();
//        $this->output->enable_profiler(TRUE);
        /*
         * CARREGA CONFIGURAÇÕES DO CMS
         */
        $this->load->config('cmsConfig');

        /*
         * CARREGA CLASSES GENÉRICAS PARA CMS
         */
        $this->load->model(array('cms/sessao_model', 'cms/paginas_model'));
        $this->load->helper(array('cms/cmshelper', 'cms/cmstemplate', 'text'));
        $this->load->library(array('cms/layout_cms', 'cms/cms_libs', 'cms/options_menu'));
        $this->formaUpl = $this->cms_libs->conf(7);

        /*
         * Verifica status do admin, mas não bloqueia. Bloqueio é feito nos controllers
         */
        $this->logado = $this->sessao_model->esta_logado();


        /**
         * PEGA VARIAVEIS COMUNS
         */
        $this->var = $this->uri->to_array(array('offset', 'pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'tip', 'co', 'id'));
        $this->c = $this->uri->segment(2);

        /*
         * TRATAMENTOS SOBRE MÓDULOS
         */
        if ($this->var['co'] != '') {

            $modId = $this->var['co'];

            if ($this->c == 'pastas' || $this->c == 'upload') {// sobreescreve o ID
                if ($this->var['co'] == 1) {
                    $modId = 13; // albuns fotos
                } else {
                    $modId = 4; // pastas arquivos
                }
            }



            $this->modulo = $this->cms_libs->dados_menus_raiz($modId);
            $this->tb = ($this->c == 'pastas') ? 'cms_pastas' : $this->modulo['tabela'];
            
            // JSON vars
            $this->json_vars('modulo', $this->modulo);
        }




        // pega dados padrão dos módulos
        if ($this->var['co'] != '' && $this->var['co'] > 0 && $this->var['id'] != '') {
            $this->tmp['item_id'] = $this->var['id'];

            $this->conteudo = $this->cms_libs->conteudo_dados_simples($this->var, $this->tb);
            

            $this->getDadosConteudo();
            $this->getGaleria();
        }



        // se tem comentários
        if ($this->modulo['comments'] == 1) {
            $this->getComentarios();
        }


        // se tem dados extra
        if (strlen($this->modulo['extra']) > 1) {
            $this->getCamposExtra();
        }

        // se aceita inscrições
        if ($this->modulo['inscricao'] == 1) {
            $this->getInscricoes();
        }

        // se tem dados multicontent? Este recurso é usado apenas pelos módulos da tabela cms_conteudo
        if($this->tb == 'cms_conteudo'){
           $this->getMultiContent(); 
        }
        
        
    }

    /*
     * RENDERIZA O TEMPLATE PADRÃO
     */

    function templateRender() {



        /*
         * ULTIMOS INCLUDES ANTES DE RENDERIZAR
         */
        $this->cmsJS = array_merge($this->cmsJS, $this->cmsJSextra);
        $this->css = array_merge($this->css, $this->cssExtra);
        $this->json_vars(null, array(
            'base_url' => base_url() . app_folder(),
            'site_url' => trim(cms_url(), '/').'/'
        ));
        
        /*
         * JOGA NO TEMPLATE
         */
        $this->tmp['tabela'] = $this->tabela;
        $this->tmp['title'] = $this->title;
        $this->tmp['json_vars'] = json_encode($this->json_vars);
        $this->tmp['scripts'] = $this->scripts($this->jquery, 'libs/jquery');
        $this->tmp['scripts'] .= $this->scripts($this->cmsJS, 'ci_itens/js');
        if ($this->formaUpl == 1 && $this->setSwfUpl === true) {
            $this->tmp['scripts'] .= $this->swf_upload->head_swfupload();
        }

//        mybug($this->dados);
        $this->tmp['estilos'] = $this->estilos($this->css, 'ci_itens/css');
        $this->tmp['head'] = $this->layout_cms->head($this->_var, $this->var);
        $this->tmp['menu'] = $this->layout_cms->menu($this->var);
        $this->tmp['corpo'] = $this->layout_cms->titulo($this->title, $this->dados, $this->tit_css, $this->modulo);
        if ($this->botoes != NULL) {
            // se for uma revisão, não exibe botões
            if(!isset($this->conteudo['tipo']) || strstr($this->conteudo['tipo'], 'revision') === FALSE ){
                $this->tmp['corpo'] .= $this->layout_cms->barra_botoes($this->botoes);
            } 
            
        }
        if ($this->tabs != NULL) {
            $this->tmp['corpo'] .= $this->load->view('cms/template_tabs', $this->tabs, true);
        }
        $this->tmp['corpo'] .= $this->corpo;
        $this->tmp['body_class'] = $this->body_class();
        // -
        // -- descarrega no template -- //
        $this->load->view('cms/template', $this->tmp);
    }

    /*
     * RENDERIZA O TEMPLATE PADRÃO DA JANELA MODAL
     */

    function modalRender() {
        /*
         * ULTIMOS INCLUDES ANTES DE RENDERIZAR
         */
        $this->cmsJS = array_merge($this->cmsJS, $this->cmsJSextra);
        $this->css = array_merge($this->css, $this->cssExtra);
        $this->json_vars(null, array(
            'base_url' => base_url(),
            'site_url' => trim(cms_url(), '/').'/'
        ));
        /*
         * JOGA NO TEMPLATE
         */
        $this->tmp['tabela'] = $this->tabela;
        $this->tmp['title'] = $this->title;
        $this->tmp['json_vars'] = json_encode($this->json_vars);
        $this->tmp['scripts'] = $this->scripts($this->jquery, 'libs/jquery');
        $this->tmp['scripts'] .= $this->scripts($this->cmsJS, 'ci_itens/js');

        if ($this->formaUpl == 1 && $this->setSwfUpl === true) {
            $this->tmp['scripts'] .= $this->swf_upload->head_swfupload();
        }

        $this->tmp['estilos'] = $this->estilos($this->css, 'ci_itens/css');
        $this->tmp['menu'] = $this->layout_cms->menu_modal($this->botoes);
        $this->tmp['resposta'] = $this->layout_cms->modal_resposta($this->var, $this->_var);

        $this->tmp['corpo'] = $this->corpo;
        // -
        // -- descarrega no template -- //
        $this->load->view('cms/template_modal', $this->tmp);
    }
    
    // -----------------------------------------------------------------------
    /**
     * Combina os arrays e converte em JSON na view.
     * São variáveis globais para serem usadas via JS nas views.
     * @param       array       $array
     * @return      array
     */
    public function json_vars($namespace = NULL, $array = NULL){
        if ($array == NULL){
            return false;
        }
        else if (!is_array($array)){
            $array = explode(',', $array);
        } 
        
        if($namespace !== NULL){
            $array_in = $array;
            unset($array);
            $array[$namespace] = $array_in;
        }
        
        $this->json_vars = array_merge($this->json_vars, $array);
        
    }

    /**
     * Se acionada carrega scripts JS dentro da pasta "js" na raiz do site,
     * ou outro local passado na variavel '$local'.
     *
     * @param array $lista : nome dos scripts sem extenção (js)
     * @param string $local : pasta padrão [js]
     * @return string
     */
    function scripts($lista, $local = 'js') {
        if (!is_array($lista)) {
            $lista = array($lista); // se não for, transforma em array
        }
        if (count($lista) == 0) {
            return '';
        }


        $saida = '';
        foreach ($lista as $lib => $nomejs) {

            if (!is_numeric($lib))
                $pasta = base_url() . app_folder () . 'libs/' . $lib;
            else
                $pasta = base_url() . app_folder () . $local;

            $saida .= "<script type=\"text/javascript\" src=\"" . $pasta . "/" . $nomejs . ".js\"></script>\n";
        }

        return $saida;
    }

    /**
     * Gera string com a URI para personalização da estilização.
     * Ignora o primeiro item "cms".
     * @return string
     */
    function body_class() {
        $uri = $this->uri->segment_array();
        $bc = '';
        for ($x = 0; $x < 3; $x++) {
            if (!isset($uri[$x + 2]))
                break;
            $bc .= str_replace(':', '-', $uri[$x + 2]) . ' ';
        }

        return $bc;
    }

    /**
     * Se acionada carrega estilos CSS dentro da pasta "css" na raiz do site,
     * ou outro local passado na variavel '$local'.
     *
     * @param array $lista : nome dos estilos sem extenção (css)
     * @param string $local : pasta padrão [css]
     * @param string $media : tipo de css, 'screen' é o padrão
     * @return string
     */
    function estilos($lista, $local = 'css', $media = 'screen') {
        if (!is_array($lista)) {
            $lista = array($lista); // se não for, transforma em array
        }
        if (count($lista) == 0) {
            return '';
        }

        $pasta = base_url() . app_folder() . $local;
        $saida = '';
        foreach ($lista as $nomes) {
            $saida .= "<link href=\"" . $pasta . "/" . $nomes . ".css\" rel=\"stylesheet\" type=\"text/css\" media=\"$media\" />\n";
        }

        return $saida;
    }

    /**
     * Retorna dados padrão dos módulos
     */
    function getDadosConteudo() {

        // trata a variavel 'grupo', que em alguns módulos não existe
        $this->conteudo['grupo'] = (!isset($this->conteudo['grupo'])) ? 0 : $this->conteudo['grupo'];
        // trata variável 'rel' para não ficar indefinida
        $this->conteudo['rel'] = (!isset($this->conteudo['rel'])) ? 0 : $this->conteudo['rel'];


        // combobox grupos
        $this->load->model('cms/paginas_model');
        $this->var['relacionamento'] = $this->conteudo['grupo'];
        
        // no módulo de páginas o "grupo" é a própria hierarquia de páginas
        // nos outros módulos grupos tem funcionamento padrão
        $this->dados['grupos'] = $this->paginas_model->getGrupoComboHierarchy($this->var, $this->c);
               

        // combo de conteudos relacionados
        // este relacionamento não pode existir quando o módulo por "adminstradores", "pastas"
        // ou enquete
        if ($this->modulo['id'] != 1 && $this->modulo['tabela'] != 'cms_enquete_per' && $this->modulo['tabela'] != 'cms_pastas') {
            $this->dados['rel'] = $this->cms_libs->combo_relacionados($this->modulo, $this->conteudo, $this->tb);
        } else {
            $this->dados['rel'] = false;
        }


//        mybug($this->modulo);
    }

    /**
     * RETORNA ARRAY COM TODAS AS VAIÁVEIS PARA A GALERIA DE IMAGENS DO CONTEÚDO
     */
    function getGaleria() {

        // controllers que não possuem galeria
        if($this->c == 'menus'){
            return;
        }
        
        
        if ($this->tb == 'cms_conteudo') {//  || $this->tb == 'cms_pastas'
            $dados['labelAddImage'] = 'Adicionar novas imagens';
            // Upload com jQuery Multi Upload
            $dados['linkAddImage'] = 'cms/upload/multImg/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $this->modulo['pasta_img'] . '/onde:conteudo';
//            Upload com SWF upload
//            $dados['linkAddImage'] = 'cms/upload/img/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $this->modulo['pasta_img'] . '/onde:conteudo';
            $dados['linkAddArq'] = 'cms/upload/arquivo/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $this->modulo['pasta_arq'] . '/onde:pasta';
            $dados['addImgFromFolder'] = 'cms/imagem/explorer/co:0/id:' . $this->var['id'];
//            $dados['linkReload'] = $this->botoes['limpar'] . '/tab:2';

            $dados['galery'] = false;
            
            if(isset($this->conteudo['galeria'])){
                $dados['galery'] = $this->cms_libs->arquivos_concat_dados(explode('|', $this->conteudo['galeria']));
            }
            


            $this->dados = array_merge($this->dados, $dados);
        }
    }

    /**
     * INSERE ABA DA GALERIA NO CONTROLLER PRINCIPAL
     */
    function setGaleria() {
        $this->setNewScript('galeria_init');
        $totalGalery = ($this->dados['galery']) ? count($this->dados['galery']) : 0;
        $this->tabs['tab_title'][] = 'Galeria (' . $totalGalery . ')';
        $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_galeria', $this->dados, true);
    }
    
    // ------------------------------------------------------------------------
  
    /**
     * insere a aba de anexos no conteúdo
     */
    public function setAttachments($conf){
        
        
        $this->load->model('cms/pastas_model', 'pastas');
                
        $this->setNewScript('galeria_init');
        
        $view['galery'] = $this->pastas->arquivos_dados($this->var['id'], 'conteudo_id');
        
        $view['labelAddImage'] = 'Adicionar novos arquivos';
        $view['linkAddImage'] = 'cms/upload/multImg/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $this->modulo['pasta_arq'] . '/onde:conteudo';
//        $dados['linkAddArq'] = 'cms/upload/arquivo/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $this->modulo['pasta_arq'] . '/onde:pasta';
        $view['linkReload'] = $this->dados['linkReload'].'/tab:'.$conf['tab'];
        
        $totalGalery = ($view['galery']) ? count($view['galery']) : 0;
                
//        mybug($view['galery']);
        
        $this->tabs['tab_title'][] = 'Anexos (' . $totalGalery . ')';
        $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_arquivos', $view, true);
    }

    /**
     * Retorna os dados dos comentários deste conteúdo
     */
    function getComentarios() {
        $this->load->model('cms/paginas_model');
        $dados['comments'] = $this->paginas_model->comentarios_dados($this->var['id']);

        $this->dados = array_merge($this->dados, $dados);
    }

    /**
     * Insere nas ABAS os comentários
     */
    function setComentarios() {
        if ($this->modulo['comments'] == 1) {
            $this->setNewScript('comentarios');
            $this->tabs['tab_title'][] = 'Comentários';
            $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_comments', $this->dados, true);
        }
    }
    
    /**
     * Insere nas ABAS 
     */
    function setPreco() {
        
        // este método está no model do calendario
        $this->load->model('cms/calendario_model');
        $this->dados['precos'] = $this->calendario_model->get_preco_desconto($this->conteudo['id']);
//        mybug($this->dados['precos']);
        
        $this->setNewScript('preco');
        $this->tabs['tab_title'][] = 'Preço e promoção';
        $this->tabs['tab_contt'][] = $this->load->view('cms/calendario/preco', $this->dados, true);
        
    }
    
    /**
     * insere as opções. Utilizado pelos produtos da loja
     */
    function getOpcoes(){
        // este método está no model do calendario
        $this->load->model('cms/loja_model', 'loja');
        
        $this->dados['options'] = $this->loja->get_options($this->conteudo['id']);
        $this->dados['options_estoque'] = $this->loja->get_estoque_from_options($this->dados['options']);
    }
    function setOpcoesTab(){ 
        
        $this->setNewScript('prod_opcoes');
        $this->tabs['tab_title'][] = 'Opções';
        $this->tabs['tab_contt'][] = $this->load->view('cms/loja/opcoes', $this->dados, true);
        
    }

    /**
     * Retorna os dados dos campos extra
     */
    function getCamposExtra() {
        $dados['camposExtra'] = $this->cms_libs->extraMontaArray($this->modulo['extra'], $this->conteudo['extra'], $this->tb);
        $this->dados = array_merge($this->dados, $dados);
    }

    /**
     * Retorna os dados dos multi contents
     */
    function getMultiContent() {
        // parseia dados do Módulo
        $itens = explode(',', $this->modulo['multicontent']);
        
//        mybug(count($itens));
        if(count($itens) < 2 && $itens[0] == ''){
            $this->dados['multicontent'] = FALSE;
            return;
        }
        
        $multi = array();
        foreach ($itens as $r) {            
            if($r != ''){                             
                $multi[] = $r;
            }             
        }
        
        // parseia o conteúdo txtmulti
        $c = $this->conteudo['txtmulti'];
        $multi_array = explode('<!--breakmulti-->', $c);
        
        // verifica o maior array para não perder conteúdo
        $ttl_rows = (count($multi) > count($multi_array)) ? count($multi) : count($multi_array); 
        
        // looping pelos arrays conbinando as informações de título e conteúdo
        $array_final = array();
        for($i = 0; $i < $ttl_rows; $i++){
            
            // título
            $tit = (isset($multi[$i])) ? $multi[$i] : 'sem título';
            
            // conteudo
            $content = (isset($multi_array[$i])) ? $multi_array[$i] : '';
            
            $array_final[] = array(
                'titulo' => $tit,
                'content' => $content
            );
        }
        
        if(strlen($this->modulo['multicontent']) < 1){
            $array_final = false;
        }
        
        
//        mybug($array_final);
        
        $dados['multicontent'] = $array_final;
        $this->dados = array_merge($this->dados, $dados);
    }

    /**
     * Insere nas ABAS os campos extra
     */
    function setCamposExtra() {
        if (strlen($this->modulo['extra']) > 1) {
            $this->tabs['tab_title'][] = 'Mais Campos';
            $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_extras', $this->dados, true);
        }
    }

    /**
     * Insere nas ABAS os multicontents
     */
    function setMultiContent() {
        if (strlen($this->modulo['multicontent']) > 1) {
            $this->tabs['tab_title'][] = 'Mais conteúdos';
            $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_multicontent', $this->dados, true);
        }
    }

    /**
     * Retorna os dados dos inscritos
     */
    function getInscricoes() {
        $this->load->model('cms/calendario_model');
        $dados['inscritos'] = $this->calendario_model->inscritos_dados($this->var['id']);
        $dados['link_planilha'] = site_url('cms/calendario/inscritosPlanilha/co:' . $this->var['co'] . '/id:' . $this->var['id']);
        $this->dados = array_merge($this->dados, $dados);
    }

    /**
     * Insere nas ABAS os inscritos
     */
    function setInscricoes() {
        if ($this->modulo['inscricao'] == 1) {
            if (!$this->dados['inscritos'])
                $count = 0;
            else
                $count = count($this->dados['inscritos']);
            $this->tabs['tab_title'][] = 'Inscritos (' . $count . ')';
            $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_inscritos', $this->dados, true);
        }
    }
    
    /**
     * Parseia metadados do conteúdo e monta view para admin
     */
    function setMetadados(){        
        
        
        if($this->phpsess->get('admin_tipo', 'cms') == 0 && isset($this->conteudo['metadados'])){
            
            $this->dados['metadados'] = $this->cms_libs->prep_metadados($this->conteudo['metadados']);
//            mybug($this->dados['metadados']);
            
        }
        
        $this->tabs['tab_title'][] = 'Head';
        $this->tabs['tab_contt'][] = $this->load->view('cms/conteudo_metadados', $this->dados, true);
        
    }

    /**
     * Gera campo swf upload e coloca scripts no HEADER
     */
    function getSwfUplForm() {
        $this->setSwfUpl = true;

        // carrega a classe SWF Upload
        $configs = array('url_swf' => app_folder() . "libs/swfupload", // onde estão os arquivos do sistema swf
            'mb' => $this->config->item('peso_max_arqs') / 1000,
            'file_types' => $this->config->item('upl_ext_arqs'), // jpg|gif|...
            'upload_proprio' => true, // se usa o controller, ou upload.php
            'pasta_destino' => 'co:2',
            'controller_upl' => site_url('cms/swfupl/fazSwfUpload'), // se for próprio
            'file_upload_limit' => 0, // limite de imagens em uma seção
            'file_queue_limit' => 1, // quantas sobe ao mesmo tempo '0' padrão
            'tipo_swf' => '', // se permite multiplos uploads
            'debug' => false);

        $this->load->library('swf_upload', $configs);

        $campo = '<div style="float:left;">';
        $campo .= $this->swf_upload->campo_swfupload();
        $campo .= '<a href="#" id="swfBtSend" class="btn">ENVIAR ARQUIVO</a></div><textarea name="arquivos" cols="" rows="" class="resumo"
                style="border:none; visibility:hidden;" readonly="readonly">...</textarea>
                <div style="clear:both;"></div>';

        return $campo;
    }

    /**
     * Se o conteúdo possui arquivos retorna uma lista de links
     * @return <type>
     */
    function setArqvs() {
        if (isset($this->conteudo)) {
            $id = $this->conteudo['id'];

            $lista = $this->cms_libs->getListaArquivosConteudo($id);
        }

        return $lista;
    }

    /*
     * ACRESCENTA SCRIPTS PARA SEREM ENVIADOS AO TEMPLATE
     */
    public function setNewScript($array) {
        if (!is_array($array))
            $array = explode(',', $array);
        $this->cmsJSextra = array_merge($this->cmsJSextra, $array);
    }
    
    public function setNewJquery($array) {
        if (!is_array($array))
            $array = explode(',', $array);
        $this->jquery = array_merge($this->jquery, $array);
    }

    /*
     * ACRESCENTA CSSs PARA SEREM ENVIADOS AO TEMPLATE
     */

    public function setNewEstyle($array) {
        if (!is_array($array))
            $array = explode(',', $array);
        $this->cssExtra = array_merge($this->cssExtra, $array);
    }
    
    /**
     * Plugins são conjuntos de JS e CSS que são injetados.
     * 
     * @param type $pluginName 
     */
    public function setNewPlugin($pluginName){
        
        if(! is_array($pluginName)){
            $pluginName = explode(',', $pluginName);
        } 
        
        foreach($pluginName as $plugin){
            
            if($plugin == 'colorpicker'){
                $this->setNewEstyle(array('colorpicker'));
                $this->setNewScript(array('colorpicker_init'));
                $this->setNewJquery(array('colorpicker'));
                
            } else if($plugin == 'datepicker'){                
                $this->setNewScript(array('datepicker_init'));
                $this->setNewJquery(array('ui.datepicker.182'));
            } else if($plugin == 'tinymce'){
                $this->setNewJquery(array('tiny_mce356' => 'jquery.tinymce'));
                $this->setNewScript(array('tinymce'));
            } else if($plugin == 'nyromodal'){
                $this->setNewEstyle(array('nyroModal'));
                $this->setNewScript(array('nyroModal_init'));
                $this->setNewJquery(array('jquery.nyroModal'));
            } else if($plugin == 'chosen'){
                $this->setNewEstyle(array('chosen'));
                $this->setNewScript(array('chosen'));
            } else if($plugin == 'maskedinput'){
                $this->setNewJquery(array('jquery.maskedinput'));
                $this->setNewScript(array('maskedinput_init'));
            
            } else if($plugin == 'angularjs'){
                $this->setNewScript(array('angular/1.2.13/angular.min'));
                $this->setNewScript(array('angular/1.2.13/angular-animate.min'));
                $this->setNewScript(array('angular/1.2.13/angular-resource.min'));
                $this->setNewScript(array('angular/1.2.13/angular-route.min'));
            
            } else if($plugin == 'angularjs-full'){
                $this->setNewScript(array('angular/1.2.13/angular.min'));
                $this->setNewScript(array('angular/1.2.13/angular-animate.min'));
                $this->setNewScript(array('angular/1.2.13/angular-resource.min'));
                $this->setNewScript(array('angular/1.2.13/angular-route.min'));
                $this->setNewScript(array('angular/1.2.13/angular-cookies.min'));
                $this->setNewScript(array('angular/1.2.13/angular-sanitize.min'));
            }
            
            
            
        }
        
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        if (strlen($namespace) > 0)
        {
            $this->namespace = '/' . trim($namespace, '/') . '/';
        }
    }

}