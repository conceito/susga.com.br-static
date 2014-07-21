<?php

/**
 * Executa as funções de edição de imagens no CMS
 *
 * @version $Id$
 * @copyright 2010
 */
class Imagem extends Cms_Controller {

    function __construct() {
        parent::__construct();

        /*
         * FAZ VERIFICAÇÃO DE USUÁRIO
         */
        $this->logado = $this->sessao_model->controle_de_sessao();

        $this->pastaImg = fisic_path() . $this->config->item('upl_imgs');
        $this->pastaArq = fisic_path() . $this->config->item('upl_arqs');
        $this->pesoMaxImg = $this->config->item('peso_max_upl');
        $this->pesoMaxArq = $this->config->item('peso_max_arqs');
        $this->extImg = $this->config->item('upl_ext');
        $this->extArq = $this->config->item('upl_ext_arqs');
        $this->formaUpl = 0; //$this->cms_libs->conf(7); // pegar no BD 0 ou 1
    }

    function explorer($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Pastas de Imagens';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'arqs', 'imgs'));
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

        $this->botoes = array('Pastas de imagens' => 'cms/imagem/explorer/co:0/id:' . $this->var['id'],
            'Álbuns de fotos' => 'cms/imagem/explorer/co:1/id:' . $this->var['id']);
        // -
        // -- processa informações -- //
        //$arquivo = $this->cms_libs->arquivo_dados($this->var['id']);
        $pastas = $this->cms_libs->pastas_lista($this->var['co']);
        // -
        // -- chama as views complementares -- //
        $dados['id'] = $this->var['id'];
        $dados['co'] = $this->var['co']; // este co se refere as pastas
        $dados['pastas'] = $pastas;
        $dados['tipoPasta'] = ($this->var['co'] == 0) ? 'Pastas de Imagens' : 'Pastas de Álbuns';
        // echo '<pre>';
        // var_dump($this->var);
        // exit;
        // -
        // -- chama as views -- //
        $this->corpo = $this->load->view('cms/uploads/pasta_explorer', $dados, true);
        $this->modalRender();
    }

    function imgExplorer($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Imagens disponíveis';
        $this->tabela = 'cms_pastas';
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'arqs', 'imgs'));
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
        // -- carrega classes -- //
        $this->load->model(array('cms/pastas_model'));

        $this->botoes = array('Pastas de imagens' => 'cms/imagem/explorer/co:0/id:' . $this->var['id'],
            'Álbuns de fotos' => 'cms/imagem/explorer/co:1/id:' . $this->var['id']);
        // -
        // -- processa informações -- //
        $pasta = $this->cms_libs->pasta_dados($this->var['pasta']);
        $conteudo = $this->cms_libs->conteudo_dados($this->var['id']);
        $arquivos = $this->pastas_model->arquivos_dados($this->var['pasta']);
        $arqParsed = $this->parse_imagens_conteudo($conteudo['galeria'], $arquivos);

        // -
        // -- chama as views complementares -- //
        $this->tmp['item_id'] = $this->var['id'];
        $dados['id'] = $this->var['id']; // ID do conteudo
        $dados['co'] = $pasta['tipo']; // este co se refere as pastas
        $dados['pasta'] = $pasta;
        $dados['arquivos'] = $arqParsed;
        $dados['tipoPasta'] = ($pasta['tipo'] == 0) ? 'Pastas de Imagens' : 'Pastas de Álbuns';
        // echo '<pre>';
        // var_dump($this->var);
        // exit;
        // -
        // -- chama as views -- //
        $this->corpo = $this->load->view('cms/uploads/imagem_explorer', $dados, true);
        $this->modalRender();
//		$tmp['item_id'] = $var['id'];
//        $tmp['tabela'] = 'cms_pastas';
//        $tmp['title'] = $title;
//        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
//        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
//        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
//        $tmp['menu'] = $this->layout_cms->menu_modal($botoes);
//        $tmp['resposta'] = $this->layout_cms->modal_resposta($var, $_var);
//        // -
//        // -- descarrega no template -- //
//        $this->load->view('cms/template_modal', $tmp);
    }

    function parse_imagens_conteudo($galeria_conteudo = '', $arqs = array()) {

        // prepara galeria
        //if(strlen(trim($galeria_conteudo)) == 0) return $arqs;// deprecated
        $galeria = explode('|', $galeria_conteudo);
        if (!$arqs)
            return false;

// echo '<pre>';
//         var_dump($arqs);
//         exit;
        $saida = array();
        // percorre cada imagem da pasta
        foreach ($arqs as $img) {

            if (array_search($img['id'], $galeria) !== false) {
                $img['selected'] = 'selected';
            } else {
                $img['selected'] = '';
            }
            $saida[] = $img;
        }


        return $saida;
    }

    function editar($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Editando Imagem';
        $this->tabela = 'cms_arquivos';
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'arqs', 'imgs'));
        $this->_var = $_var;

        /*
         * Usa geração de combo de Módulos direto na view.
         */
        $this->load->model(array('cms/admin_model', 'cms/cmsutils_model'));
        /*
         * ASSETS
         */
        $this->jquery = array('ui.button.182');
        $this->cmsJS = array();
        $this->css = array();

        /*
         * OPÇÕES
         */
        $this->botoes = array();


        // -
        // -- processa informações -- //
        $arquivo = $this->cms_libs->arquivo_dados($this->var['id']);
        $pasta = $this->cms_libs->pasta_dados($arquivo['pasta']);
//        mybug($arquivo);
        // -
        // -- chama as views complementares -- //
        $dados['tag_opt'] = $arquivo['tag_opt'];
        $dados['comboRelacionado'] = $this->cms_libs->getComboImgRel($arquivo);
        $dados['id'] = $this->var['id'];
        $dados['co'] = $this->var['co'];
        $dados['arq'] = $arquivo;
        $dados['pasta'] = $pasta;
        $ext = ($this->var['co'] == 0 || $this->var['co'] == 1) ? $this->extImg : $this->extArq;
        $dados['ext'] = str_replace('|', ', ', $ext);


        // -
        // -- chama as views -- //
        $this->corpo = $this->load->view('cms/uploads/imagem_edita', $dados, true);
        $this->modalRender();
    }

    function girar() {
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'imgs', 'arqs', 'op', 'step'));
        $angulo = $var['op']; // angulo
        $id = $var['id'];

        $arquivo = $this->cms_libs->arquivo_dados($id);
        if ($var['step'] == 2
            )$nomeArq = thumb($arquivo['nome']);
        else
            $nomeArq = $arquivo['nome'];
        // caminho completo
        $src = $this->pastaImg . '/' . $nomeArq;

        $this->load->library('image_lib');
        // $angulo = $this->input->post('angulo');
        if ($angulo != 'hor' || $angulo != 'vrt')
            $int_angulo = (int) $angulo;
        else
            $int_angulo = (string) $angulo;
        if ($angulo == 'hor' || $angulo == 'vrt'
            )$int_angulo = (string) $angulo;
        // echo '<pre>';
        // var_dump($int_angulo);
        // exit;
        $config['image_library'] = 'GD';
        // $config['library_path'] = '/usr/bin/';
        $config['source_image'] = $src;
        $config['rotation_angle'] = $int_angulo;

        $this->image_lib->initialize($config);

        if (!$this->image_lib->rotate()) {
            // return $this->image_lib->display_errors();
            echo '<pre>';
            var_dump($this->image_lib->display_errors());
            exit;
        } else {
            $this->image_lib->clear();
            // ----------------------------
            if ($var['step'] == 2)
                redirect('cms/imagem/editar/id:' . $id . '/tip:opok');
            else
                redirect('cms/imagem/girar/id:' . $id . '/op:' . $angulo . '/step:2');
        }
    }

    function crop($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Cortando Imagem';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'imgs', 'arqs', 'op', 'step'));
        $this->_var = $_var;
        /*
         * ASSETS
         */
        // define o tipo de crop
        if ($this->var['op'] == 'livre') {
            $opjs = 'jcrop_livre';
            $dados['tipoCrop'] = 'Corte livre: Você pode usar qualquer proporção ao cortar a imagem.';
        } else if ($this->var['op'] == 'quadrado') {
            $opjs = 'jcrop_quadrado';
            $dados['tipoCrop'] = 'Corte quadrado: O corte será restrito a proporção de um quadrado.';
        } else if ($this->var['op'] == '43') {
            $opjs = 'jcrop_43';
            $dados['tipoCrop'] = 'Corte retangular 4/3: O corte será restrito a proporção de um retângulo deitado.';
        } else if ($this->var['op'] == '34') {
            $opjs = 'jcrop_34';
            $dados['tipoCrop'] = 'Corte retangular 3/4: O corte será restrito a proporção de um retângulo em pé.';
        }
        $this->jquery = array('jquery.Jcrop');
        $this->cmsJS = array($opjs);
        $this->css = array('jquery.jcrop');

        /*
         * OPÇÕES
         */
        $this->botoes = array();


        // -
        // -- processa informações -- //
        $arquivo = $this->cms_libs->arquivo_dados($this->var['id']);
        $pasta = $this->cms_libs->pasta_dados($arquivo['pasta']);
        // -
        // -- chama as views complementares -- //
        $dados['id'] = $this->var['id'];
        $dados['co'] = $this->var['co'];
        $dados['arq'] = $arquivo; // array
        $dados['pasta'] = $pasta; // array
        $dados['path'] = $this->config->item('upl_imgs') . '/' . $arquivo['nome'];
        $dados['linkRetorno'] = cms_url('cms/imagem/editar/id:' . $this->var['id']);
        $dados['linkForm'] = cms_url('cms/imagem/fazCrop/id:' . $this->var['id'] . '/op:' . $this->var['op']);

//        mybug($dados);

        // -
        // -- chama as views -- //
        $this->corpo = $this->load->view('cms/uploads/imagem_corta', $dados, true);
        $this->modalRender();

//        $tmp['tabela'] = 'cms_conteudo';
//        $tmp['title'] = $title;
//        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
//        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
//        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
//        $tmp['menu'] = $this->layout_cms->menu_modal($botoes);
//        $tmp['resposta'] = $this->layout_cms->modal_resposta($var, $_var);
//        // -
//        // -- descarrega no template -- //
//        $this->load->view('cms/template_modal', $tmp);
    }

    function fazCrop() {
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'imgs', 'arqs', 'op', 'step'));
        $op = $var['op']; // angulo
        $id = $var['id'];
        $novaLarg = $this->input->post('w');
        $novaAlt = $this->input->post('h');
        $eixoX = $this->input->post('x');
        $eixoY = $this->input->post('y');

        // seta as qualidades da pasta
        $pasta = $this->cms_libs->pasta_dados($this->input->post('pasta_id'));



        $arquivo = $this->cms_libs->arquivo_dados($id);
        $nomeArq = $arquivo['nome'];
        $ext = $arquivo['ext'];

        

        // dados do aruivo de imagem
//        $img_data = $this->cms_libs->dados_arquivo($nomeArq, 'img');

        // caminho completo
        $jpeg_quality = 100;

        $src = $this->pastaImg . '/' . $nomeArq;

        
//mybug($src);
        // nome da função que manulupa
        if ($ext == 'jpg') {
            $imagecreatefrom = 'imagecreatefromjpeg';
            $image = 'imagejpeg';
        } else if ($ext == 'png') {
            $imagecreatefrom = 'imagecreatefrompng';
            $image = 'imagepng';
        } else if ($ext == 'gif') {
            $imagecreatefrom = 'imagecreatefromgif';
            $image = 'imagegif';
        }
        $img_r = $imagecreatefrom($src);
        $dst_r = ImageCreateTrueColor($novaLarg, $novaAlt);
        // $newFile = $src;
        imagecopyresampled($dst_r, $img_r, 0, 0, $eixoX, $eixoY,
                $novaLarg, $novaAlt, $novaLarg, $novaAlt);

        $image($dst_r, $src, $jpeg_quality);
        imagedestroy($dst_r); // limpa da memória
        // faz miniatura
        $this->cms_libs->redimenciona($src,$novaLarg, $novaAlt);

        // coloca na session
        $this->phpsess->save('upls', array($id), 'cms');

        // se for imagem faz mini

            // monta URI
            $uri = $this->uri->to_string(array(
                'tip' => 'ok',
                'co' => '0',
                'id' => $id,
                'pasta' => $pasta['id'],
                'imgs' => $id
            ));

//            mybug($uri);

            redirect('cms/upload/fazMini/' . $uri);


//        redirect('cms/imagem/editar/id:' . $id . '/tip:opok');
    }

}

?>