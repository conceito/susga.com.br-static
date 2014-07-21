<?php

/**
 * Controla todas as funções de Upload de arquivos e imagens com redimencionamento e via AJAX
 *
 * @version 3
 * @copyright 2010
 */
class Upload extends Cms_Controller {

    function __construct()
    {
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
        $this->formaUpl = $this->cms_libs->conf(7); // pegar no BD 0 ou 1
    }

    /**
     * Abre formulário para fazer o upload de imagens
     *
     * @return
     */
    function img($_var = '')
    {

        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Adicionando Imagens ';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'imgs', 'arqs', 'onde'));
        $this->_var = $_var;
        /*
         * ASSETS
         */
        $this->jquery = array('jquery.nyroModal');
        $this->cmsJS = array('padrao-modal', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array();
        /*
         * PROCESSA
         */
        $pasta = $this->cms_libs->pasta_dados($this->var['pasta']);
        // -
        // -- chama as views complementares -- //
        $dados['id'] = $this->var['id'];
        $dados['co'] = $this->var['co'];
        $dados['onde'] = $this->var['onde'];
        $dados['pasta'] = $pasta;
        $ext = ($pasta['tipo'] == 1 || $pasta['tipo'] == 0) ? $this->extImg : $this->extArq;
        $dados['ext'] = str_replace('|', ', ', $ext);
        $dados['pesomax'] = $this->config->item('peso_max_upl') / 1000 . 'Mb';



        // -
        // -- chama as views -- //
        if ($this->formaUpl == 0)
        { // upload tradicional
            $this->corpo = $this->load->view('cms/uploads/form_tradicional', $dados, true);
        }
        else if ($this->formaUpl == 1)
        { // via SWF upload
            $this->setSwfUpl = true;

            // carrega a classe SWF Upload
            $configs = array('url_swf' => "libs/swfupload", // onde estão os arquivos do sistema swf
                'mb' => $this->pesoMaxImg / 1000,
                'file_types' => $this->extImg, // jpg|gif|...
                'upload_proprio' => true, // se usa o controller, ou upload.php
                'pasta_destino' => 'co:0',
                'controller_upl' => cms_url('cms/swfupl/fazSwfUpload'), // se for próprio -- 'cms/upload/fazSwfUpload'
                'file_upload_limit' => 5, // limite de imagens em uma seção
                'file_queue_limit' => 0, // quantas sobe ao mesmo tempo '0' padrão
                'tipo_swf' => 'multi', // se permite multiplos uploads
                'debug' => false);

            $this->load->library('cms/swf_upload', $configs);

            $this->corpo = $this->load->view('cms/uploads/form_swfupload', $dados, true);
        }

        // se o upload foi realizado com sucesso, não exibe o form. Apenas botão
        // para subir novamente mais imagens. Prevenção de erros so SwfUpload.
        if ($this->var['tip'] == 'ok')
        {
            $this->corpo = $this->load->view('cms/uploads/upload_sucesso', $dados, true);
        }

        $this->modalRender();
    }

    /**
     * Sistema de upload jQuery Upload
     * Utilizado apenas para imagens
     * @param <type> $_var
     */
    function multImg($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Adicionando arquivos ';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'imgs', 'arqs', 'onde'));
        $this->_var = $_var;
        /*
         * ASSETS
         */
        $this->jquery = array('jquery.nyroModal');
        $this->cmsJS = array('padrao-modal', 'nyroModal_init');
        $this->css = array('groundwork', 'nyroModal', 'bootstrap-image-gallery.min', 'jquery.fileupload-ui');

//         mybug($this->jquery, false);

        /*
         * OPÇÕES
         */
        $this->botoes = array();
        /*
         * PROCESSA
         */
        $pasta = $this->cms_libs->pasta_dados($this->var['pasta']);
        // -
        // -- chama as views complementares -- //
        $dados['id'] = $this->var['id'];
        $dados['co'] = $this->var['co'];
        $dados['onde'] = $this->var['onde'];
        $dados['pasta'] = $pasta;
        $ext = ($pasta['tipo'] == 1 || $pasta['tipo'] == 0) ? $this->extImg : $this->extArq;
        $dados['ext'] = str_replace('|', ', ', $ext);
        $dados['pesomax'] = $this->config->item('peso_max_upl') / 1000 . 'Mb';

        $this->corpo = $this->load->view('cms/uploads/form_jqueryUpload', $dados, true);

        $this->modalRender();
    }

    /**
     * Abre formulário para fazer o upload de imagens
     *
     * @return
     */
    function arquivo($_var = '')
    {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Adicionando Arquivos ';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'imgs', 'arqs', 'onde'));
        $this->_var = $_var;
        /*
         * ASSETS
         */
        $this->jquery = array('jquery.nyroModal');
        $this->cmsJS = array('padrao-modal', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */


        // -
        // -- processa informações -- //
        $pasta = $this->cms_libs->pasta_dados($this->var['pasta']);
        // -
        // -- chama as views complementares -- //
        $dados['id'] = $this->var['id'];
        $dados['co'] = $this->var['co'];
        $dados['onde'] = $this->var['onde'];
        $dados['pasta'] = $pasta;
        $ext = ($pasta['tipo'] == 1 || $pasta['tipo'] == 0) ? $this->extImg : $this->extArq;
        $dados['ext'] = str_replace('|', ', ', $ext);
        $dados['pesomax'] = $this->config->item('peso_max_arqs') / 1000 . 'Mb';
//         echo '<pre>';
//         var_dump($this->pastaArq);
//         exit;
        // -
        // -- chama as views -- //
        if ($this->formaUpl == 0)
        { // upload tradicional
            $this->corpo = $this->load->view('cms/uploads/form_tradicional', $dados, true);
        }
        else if ($this->formaUpl == 1)
        { // via SWF upload
            $this->setSwfUpl = true;

            // carrega a classe SWF Upload
            $configs = array('url_swf' => "libs/swfupload", // onde estão os arquivos do sistema swf
                'mb' => $this->pesoMaxArq / 1000,
                'file_types' => $this->extArq, // jpg|gif|...
                'upload_proprio' => true, // se usa o controller, ou upload.php
                'pasta_destino' => 'co:2',
                'controller_upl' => cms_url('cms/swfupl/fazSwfUpload'), // se for próprio
                'file_upload_limit' => 5, // limite de imagens em uma seção
                'file_queue_limit' => 0, // quantas sobe ao mesmo tempo '0' padrão
                'tipo_swf' => 'multi', // se permite multiplos uploads
                'debug' => false);

            $this->load->library('swf_upload', $configs);

            $this->corpo = $this->load->view('cms/uploads/form_swfupload', $dados, true);
        }
        $this->modalRender();
    }

    /**
     * Executa o upload de imagens no modo tradicional, depois leva para as miniaturas
     *
     * @return
     */
    function fazUpload()
    {
        // dados da pasta de destino //
        $onde = $this->input->post('onde'); // 'pasta' ou 'conteudo'
        $pasta = $this->cms_libs->pasta_dados($this->input->post('pasta_id'));
        $tipo_id = $this->input->post('tipo_id'); // var 'co'
        $conteudo_id = $this->input->post('conteudo_id');
        $externo = trim($this->input->post('externo')); // links externos
        // tipo de arquivo
        $contoller = ($pasta['tipo'] == 2) ? 'arquivo' : 'img';
        if ($pasta == false)
        { // se falhar não deixa entrar imagens enormes
            $tamanho_max_w = 600;
            $tamanho_max_h = 600;
        }
        else
        {
            $tamanho_max_w = $pasta['max_w'];
            $tamanho_max_h = $pasta['max_h'];
        }
        // limpa a session
        $this->phpsess->save('upls', '', 'cms');
        $imgs_para_session = array(); // init
        // configura upload
        $config['upload_path'] = ($pasta['tipo'] == 2) ? $this->pastaArq : $this->pastaImg;
        $config['allowed_types'] = ($pasta['tipo'] == 2) ? $this->extArq : $this->extImg;
        $config['max_size'] = ($pasta['tipo'] == 2) ? $this->pesoMaxArq : $this->pesoMaxImg;
        $config['remove_spaces'] = true;


        for ($i = 1; $i <= 3; $i++)
        {
            $campo = 'userfile' . $i;
            $nomef = $_FILES[$campo]['name'];
            $size = $_FILES[$campo]['size'];
            $type = $_FILES[$campo]['type'];
            $tmp_name = $_FILES[$campo]['tmp_name'];

            // carrega classes
            $config['file_name'] = $this->name_cleaner($nomef);
            $this->load->library('upload', $config);

            if (strlen(trim($nomef)) > 4)
            {
                // erro ao subir !!!
                if (!$this->upload->do_upload($campo))
                {
                    $error = $this->upload->display_errors();
                    echo '<pre>';
                    echo $error;
                    exit;
                    // carrega página de final de acao
                    redirect('cms/upload/' . $contoller . '/tip:erro/erro:' . $error);
                }
                else
                {
                    // sucesso
                    $data = $this->upload->data();
                    // echo '<pre>';
                    // print_r($data);
                    // echo '---------xxx--------';
                    // exit;
                    if ($contoller == 'img')
                    {
                        if ($data['image_width'] > $tamanho_max_w || $data['image_height'] > $tamanho_max_h)
                        {
                            $this->cms_libs->redimenciona($data['full_path'], $tamanho_max_w, $tamanho_max_h);
                        }
                    }
                    // salva dados da imagem no banco: cms_arquivos
                    $id_img = $this->cms_libs->salva_img_dados($data, $pasta['id']);
                    // gera um array para salvar as imagens na session
                    $imgs_para_session[] = $id_img; //$data['file_name'];
                }
            }
        } // for
        // coloca na session
        $this->phpsess->save('upls', $imgs_para_session, 'cms');
        // se for dentro de um conteúdo salva nele também!
        if ($onde == 'conteudo' && $contoller == 'img'
        )
            $this->cms_libs->atualiza_galeria($conteudo_id, $imgs_para_session, 'cms_conteudo');
        // verifica e processa links externos
        if (strlen($externo) > 15)
        {
            $this->cms_libs->salva_link_externo($externo, $pasta['id']);
        }
        // echo '<pre>';
        // var_dump($imgs_para_session);
        // exit;
        // $this->img();
        // se for imagem faz mini, senão volta
        if ($contoller == 'img')
        {
            redirect('cms/upload/fazMini/tip:ok/co:' . $tipo_id . '/id:' . $conteudo_id . '/pasta:' . $pasta['id'] . '/imgs:' . implode('-', $imgs_para_session));
        }
        else
        {
            redirect('cms/upload/arquivo/tip:ok/co:' . $tipo_id . '/id:' . $conteudo_id . '/pasta:' . $pasta['id'] . '/arqs:' . implode('-', $imgs_para_session));
        }
    }

    /**
     * ATENÇÃO!!! ESTE MÉTODO FOI MOVIDO PARA O CONTROLLER 'SWFUPL' POR COMPATIBILIDADE COM O CI2.0
     *
     * Faz o upload de imagens vindo do SWF upload.
     * Requisitado via AJAX.
     * Carrega Library: upload.
     *
     * @param string $onde : pasta de destino das imagens
     * @return string
     */
    function fazSwfUpload($var = '')
    {
        // -- recebe variaveis -- //
        // $var = $this->cms_uri->to_array(3, array('co', 'id', 'pasta', 'onde'));
        $ext = ($var == 'co:0') ? $this->extImg : $this->extArq;
        $pasta = ($var == 'co:0') ? $this->pastaImg : $this->pastaArq;
        $peso = ($var == 'co:0') ? $this->pesoMaxImg : $this->pesoMaxArq;
        // recomponho entrada da pasta

        $config['upload_path'] = 'C:\apache2triad\htdocs\cms4.com.br/upl/imgs';
        $config['allowed_types'] = 'jpg|gif'; // jpg|gif|...
        $config['max_size'] = '10000000000';
        $config['remove_spaces'] = true;
        $this->load->library('upload', $config);

        $this->upload->do_upload('Filedata'); // sobre o arquivo
        $res = $this->upload->data();

        $error = $this->upload->display_errors();

//        echo 'teste';
//        exit;

        echo $error; //$res['file_name']; // retorno para o SWF upload
    }

    /**
     * Depois de subir as imagens redimensiona e salva nas pastas
     *
     * @return
     */
    function processaSwfupload()
    {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Adicionando Imagens';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('co', 'id', 'pasta', 'onde'));

        /*
         * ASSETS
         */


        // dados da pasta de destino //
        $onde = $this->var['onde']; // 'pasta' ou 'conteudo'
        $pasta = $this->cms_libs->pasta_dados($this->var['pasta']);
        $tipo_id = $this->var['co']; // var 'co'
        $conteudo_id = $this->var['id'];
        $externo = trim($this->input->post('externo')); // links externos
        // tipo de arquivo
        $contoller = ($pasta['tipo'] == 2) ? 'arquivo' : 'img';
//        if ($pasta == false) { // se falhar não deixa entrar imagens enormes
//            $tamanho_limite_w = $this->config->item('imagem_limit_w');
//            $tamanho_limite_h = $this->config->item('imagem_limit_h');
//        } else {
//            $tamanho_limite_w = $pasta['max_w'];
//            $tamanho_limite_h = $pasta['max_h'];
//        }
        $tamanho_limite_w = $this->config->item('imagem_limit_w');
        $tamanho_limite_h = $this->config->item('imagem_limit_h');
        // limpa a session
        $this->phpsess->save('upls', '', 'cms');
        $imgs_para_session = array(); // init
        // lista de arquivos subidos
        $arquivos = $this->input->post('arquivos');
        $lista_arquivs = explode(',', $arquivos);
        // carrega página de erro
        if (count($lista_arquivs) < 2 && strlen($externo) < 15)
        {
            redirect('cms/upload/' . $contoller . '/tip:erro/erro:Arquivo inexistente/co:' . $this->var['co'] . '/id:' . $this->var['id'] . '/pasta:' . $this->var['pasta'] . '/onde:' . $this->var['onde']);
            exit;
        }
//echo '<pre>';
//var_dump($lista_arquivs);
//exit;
        // percorre o array de arquivos
        foreach ($lista_arquivs as $arq)
        {
            // limpa
            $arq = trim($arq);
            // adiciona no array se for válido
            if (strlen($arq) > 4)
            {
                $data = $this->cms_libs->dados_arquivo($arq, $contoller);

                if ($contoller == 'img')
                {
                    // dados detalhados

                    if ($data['image_width'] > $tamanho_limite_w || $data['image_height'] > $tamanho_limite_h)
                    {
                        $this->cms_libs->redimenciona($data['full_path'], $tamanho_limite_w, $tamanho_limite_h);
                    }
                }
                // salva dados da imagem no banco: cms_arquivos
                $id_img = $this->cms_libs->salva_img_dados($data, $pasta['id']);
                // gera um array para salvar as imagens na session
                $imgs_para_session[] = $id_img; //$data['file_name'];
            }
        }

        // coloca na session
        $this->phpsess->save('upls', $imgs_para_session, 'cms');
        // se for dentro de um conteúdo salva nele também!
        if ($onde == 'conteudo' && $contoller == 'img')
            $this->cms_libs->atualiza_galeria($conteudo_id, $imgs_para_session, 'cms_conteudo');
        // verifica e processa links externos
        if (strlen($externo) > 15)
        {
            $this->cms_libs->salva_link_externo($externo, $pasta['id']);
        }
        // echo '<pre>';
        // var_dump($imgs_para_session);
        // exit;
        // $this->img();
        // se for imagem faz mini, senão volta

        if ($contoller == 'img')
        {
            // monta URI
            $uri = $this->uri->to_string(array(
                'tip' => 'ok',
                'co' => $tipo_id,
                'id' => $conteudo_id,
                'pasta' => $pasta['id'],
                'imgs' => implode('-', $imgs_para_session),
                'onde' => $onde
            ));
            redirect('cms/upload/fazMini/' . $uri);
        }
        else
        {
            // monta URI
            $uri = $this->uri->to_string(array(
                'tip' => 'ok',
                'co' => $tipo_id,
                'id' => $conteudo_id,
                'pasta' => $pasta['id'],
                'arqs' => implode('-', $imgs_para_session),
                'onde' => $onde
            ));
            redirect('cms/upload/arquivo/' . $uri);
        }
    }

    /**
     * Processa upload do plugin jQuery Upload
     */
    function processajQupload()
    {
        /*
         * VARIÁVEIS
         */
        $this->var = $this->uri->to_array(array('co', 'id', 'pasta', 'onde'));
        $conteudo_id = $this->var['id'];
        $pasta_id = $this->var['pasta'];
        $co = $this->var['co'];

        // alguns módulos dão tratamento aos anexos como "arquivos"
        if (in_array($co, $this->config->item('modulo_usuarios')))
        {
            $co = 2;
        }

//        log_message('error', 'URI: ' . $this->uri->to_string($this->var));

        $ext_permited = ($co != 2) ? $this->extImg : $this->extArq;
        $url_pasta = ($co != 2) ? $this->pastaImg : $this->pastaArq;
        $peso_maximo = ($co != 2) ? $this->pesoMaxImg : $this->pesoMaxArq;
        $path_relativo = ($co != 2) ? $this->config->item('upl_imgs') : $this->config->item('upl_arqs');
        
        // acrescenta subpastas
        $url_pasta = append_upload_folder($url_pasta);
        $path_relativo = append_upload_folder($path_relativo);

        /*
         * Faz upload
         */
        $dados = $this->fazJupload($url_pasta, $peso_maximo, $ext_permited);

        if ($dados)
        {


            //  é imagem. Processa multiplos tamanhos             
            if ($co != 2)
            {
                $this->geraMultipolosTamanhos($this->var, $dados);
                $thumb = base_url() . $path_relativo . '/' . thumb($dados['file_name']);
            }
            // é um arquivo
            else
            {
                $thumb = base_url() . 'ci_itens/img/icon-file.png';
            }

            // se for cms_usuarios liga o arquivo/imagem ao usuário
            if ($this->var['onde'] == 'conteudo' && $co == 25)
            {
                $dados['conteudo_id'] = $conteudo_id;
            }
            // salva dados da imagem no banco: cms_arquivos
            $id_arq = $this->cms_libs->salva_img_dados($dados, $pasta_id);

            // se for dentro de um conteúdo salva nele também!
            if ($this->var['onde'] == 'conteudo' && $dados['is_image'] == 1)
            {
                $this->cms_libs->atualiza_galeria($conteudo_id, $id_arq, 'cms_conteudo');
            }


            // prepara saída
            //Get info
            $info = new stdClass();
            $info->name = $dados['file_name'];
            $info->size = $dados['file_size'];
            $info->type = $dados['file_type'];
            $info->url = base_url() . $path_relativo . '/' . $dados['file_name'];
            $info->thumbnail_url = $thumb; //I set this to original file since I did not create thumbs.  change to thumbnail directory if you do = $upload_path_url .'/thumbs' .$name
            $info->delete_url = base_url() . 'cms/upload/deleteJimg/' . $id_arq . '/' . $conteudo_id . '/' . $this->var['onde'];
            $info->delete_type = 'DELETE';

            //Return JSON data
            if (IS_AJAX)
            {   //this is why we put this in the constants to pass only json data
                echo json_encode(array($info));
                //this has to be the only the only data returned or you will get an error.
                //if you don't give this a json array it will give you a Empty file upload result error
                //it you set this without the if(IS_AJAX)...else... you get ERROR:TRUE (my experience anyway)
            }
            else
            {   // so that this will still work if javascript is not enabled
                $file_data['upload_data'] = $this->upload->data();
                echo json_encode(array($info));
            }

            exit;
        }

        // erro
        else
        {

            $error = array('error' => $this->upload->display_errors('', ''));
            echo json_encode(array($error));
            exit;
        }
    }

//    public function implode_key($glue = "", $pieces = array())
//    {
//        $keys = array_keys($pieces);
//        return implode($glue, $keys);
//    }

    /**
     * Faz o upload para o servidor.
     * Não faz nenhum tratamento, retorna array com dados da imagem.
     *
     * @param <type> $url_pasta
     * @param <type> $peso_maximo
     * @return array
     */
    function fazJupload($url_pasta, $peso_maximo, $ext_permited = 'gif|jpg|png|JPG|GIF|PNG')
    {


//        log_message('error', var_dump($_FILES));

        $name = $_FILES['userfile']['name'];
        $name = $this->name_cleaner($name);
//        log_message('error', '$name: '.$name);
        //Your upload directory, see CI user guide
        $config['upload_path'] = $url_pasta;

        $config['allowed_types'] = $ext_permited;
        $config['max_size'] = $peso_maximo;
        $config['file_name'] = $name;

        //Load the upload library
        $this->load->library('upload', $config);



        if ($this->upload->do_upload('userfile'))
        {
//            log_message('error', 'Sucesso: ' . $this->upload->data());
            return $this->upload->data();
        }
        else
        {
            log_message('error', 'Fracasso: ' . $this->upload->display_errors('', ''));
            return false;
        }
    }


    /**
     * Recebe dados vindos do action do form e array da imagem que subiu.
     *
     * @param <type> $vars | array('co', 'id', 'pasta', 'onde')
     * @param <type> $dadosImagem | Array (
      [file_name]    => mypic.jpg
      [file_type]    => image/jpeg
      [file_path]    => /path/to/your/upload/
      [full_path]    => /path/to/your/upload/jpg.jpg
      [raw_name]     => mypic
      [orig_name]    => mypic.jpg
      [client_name]  => mypic.jpg
      [file_ext]     => .jpg
      [file_size]    => 22.2
      [is_image]     => 1
      [image_width]  => 800
      [image_height] => 600
      [image_type]   => jpeg
      [image_size_str] => width="800" height="200"
      )
     */
    function geraMultipolosTamanhos($vars, $dadosImagem)
    {

        // seta as qualidades da pasta
        $pasta = $this->cms_libs->pasta_dados($vars['pasta']);


        if ($pasta == false)
        { // se falhar não deixa entrar imagens enormes
            $tamanho_mini_w = $this->config->item('imagem_mini_w');
            $tamanho_mini_h = $this->config->item('imagem_mini_h');
            $tamanho_med_w = $this->config->item('imagem_med_w');
            $tamanho_med_h = $this->config->item('imagem_med_h');
            $tamanho_max_w = $this->config->item('imagem_max_w');
            $tamanho_max_h = $this->config->item('imagem_max_h');
        }
        else
        {
            $tamanho_mini_w = $pasta['mini_w'];
            $tamanho_mini_h = $pasta['mini_h'];
            $tamanho_med_w = $pasta['med_w'];
            $tamanho_med_h = $pasta['med_h'];
            $tamanho_max_w = $pasta['max_w'];
            $tamanho_max_h = $pasta['max_h'];
        }

        // tamanhos limite para imagem original
        $imagem_limit_w = $this->config->item('imagem_limit_w');
        $imagem_limit_h = $this->config->item('imagem_limit_h');

        // instancia Wideimage
        $this->load->spark('wideimage/11.02.19');

        // monta caminho completo
        $caminho_completo = $dadosImagem['full_path'];
        // gera miniatura
        $this->cms_libs->redimenciona($caminho_completo, $tamanho_mini_w, $tamanho_mini_h, 'thumb');
        // gera tamanho médio
        $this->cms_libs->redimenciona($caminho_completo, $tamanho_med_w, $tamanho_med_h, 'med');
        // gera tamanho grande
        $this->cms_libs->redimenciona($caminho_completo, $tamanho_max_w, $tamanho_max_h, 'max');

        // se imagem original ultrapassar limites, redimensiona
        if ($dadosImagem['image_width'] > $imagem_limit_w || $dadosImagem['image_height'] > $imagem_limit_h)
        {
            $this->cms_libs->redimenciona($caminho_completo, $imagem_limit_w, $imagem_limit_h, 'limite');
        }
    }

    /**
     * Apaga a imagem fisicamente
     */
    function deleteJimg($img_id = '', $conteudo_id = '', $onde = 'conteudo')
    {

        /*
         * pode ser de um conteúdo, ou pasta de imagens
         */
        if ($onde == 'conteudo')
        {

            // pega os IDs da galeria
            $this->db->where('id', $conteudo_id);
            $this->db->select('galeria');
            $sql = $this->db->get('cms_conteudo');
            $row = $sql->row_array();
            $galeria = explode('|', $row['galeria']);

            // percorre todos os itens e compara para ver se existe... e remove
            $nova_gal = array();
            foreach ($galeria as $ft_id)
            {
                if ($ft_id != $img_id)
                {
                    $nova_gal[] = $ft_id; // ficou
                }
            }

            $lista2 = implode('|', $nova_gal);
            // atualiza  o conteudo
            $this->db->where('id', $conteudo_id);
            $this->db->update('cms_conteudo', array('galeria' => $lista2));
        }
        else
        {
            
        }

        // pega o nome dos arquivos
        $this->db->where('id', $img_id);
        $sql = $this->db->get('cms_arquivos');
        $imagem = $sql->row_array();

        // apaga o arquivo físico e BD
        $success = $this->cms_libs->deleta_arquivo($imagem);



        $path = fisic_path() . $this->config->item('upl_imgs');



        // apaga sua miniatura
        //@unlink($path . '/' . thumb($this->var['file']));
        // apaga sua media
        //@unlink($path . '/' . med($this->var['file']));
        // apaga sua grande
        //@unlink($path . '/' . grande($this->var['file']));
        // apaga arquivo principal
        //$success = @unlink($path . '/' . $this->var['file']);
        //info to see if it is doing what it is supposed to
        $info = new stdClass();
        $info->sucess = $success;
        $info->path = $path . '/' . $imagem['nome'];
        $info->file = is_file($path . '/' . $imagem['nome']);
        if (IS_AJAX)
        {//I don't think it matters if this is set but good for error checking in the console/firebug
            echo json_encode(array($info));
        }
        else
        {     //here you will need to decide what you want to show for a successful delete
            var_dump($info);
        }
    }

    /**
     * É chamado depois de subir as imagens. Gera miniaturas das imagens.
     * Se não vier na variável imgs: busca na session
     *
     * @param integer $id
     * @param string $onde
     * @param string $tabela
     * @return view modal
     */
    function fazMini($id, $onde, $tabela)
    {
        $var = $this->uri->to_array(array('tip', 'co', 'id', 'pasta', 'erro', 'imgs', 'onde'));
        $onde = $var['onde'];
        // verifica ids na variável
        $imgs = $this->phpsess->get('upls', 'cms'); // array
        // seta as qualidades da pasta
        $pasta = $this->cms_libs->pasta_dados($var['pasta']);


        if ($pasta == false)
        { // se falhar não deixa entrar imagens enormes
            $tamanho_mini_w = $this->config->item('imagem_mini_w');
            $tamanho_mini_h = $this->config->item('imagem_mini_h');
            $tamanho_med_w = $this->config->item('imagem_med_w');
            $tamanho_med_h = $this->config->item('imagem_med_h');
            $tamanho_max_w = $this->config->item('imagem_max_w');
            $tamanho_max_h = $this->config->item('imagem_max_h');
        }
        else
        {
            $tamanho_mini_w = $pasta['mini_w'];
            $tamanho_mini_h = $pasta['mini_h'];
            $tamanho_med_w = $pasta['med_w'];
            $tamanho_med_h = $pasta['med_h'];
            $tamanho_max_w = $pasta['max_w'];
            $tamanho_max_h = $pasta['max_h'];
        }

//        mybug($imgs);
        // se ainda existir imagem no array gera mini
        if (count($imgs) > 0)
        {
            // retira o primeito item do array
            $img_id = array_shift($imgs);
            // pega dados da imagem
            $img = $this->cms_libs->arquivo_dados($img_id);



            // monta caminho completo
            $caminho_completo = $this->pastaImg . '/' . $img['nome'];
            // gera miniatura
            $this->cms_libs->redimenciona($caminho_completo, $tamanho_mini_w, $tamanho_mini_h, 'thumb');
            // gera tamanho médio
            $this->cms_libs->redimenciona($caminho_completo, $tamanho_med_w, $tamanho_med_h, 'med');
            // gera tamanho grande
            $this->cms_libs->redimenciona($caminho_completo, $tamanho_max_w, $tamanho_max_h, 'max');
            // recoloca o array sem a imagem processada
            $this->phpsess->save('upls', $imgs, 'cms');
            // reenvia para esta mesma função
            // monta URI
            $uri = $this->uri->to_string(array(
                'tip' => 'ok',
                'co' => $var['co'],
                'id' => $var['id'],
                'pasta' => $var['pasta'],
                'imgs' => $var['imgs'],
                'onde' => $onde
            ));
            redirect('cms/upload/fazMini/' . $uri);
        }
        // terminou!!!
        // monta URI
        $uri = $this->uri->to_string(array(
            'tip' => 'ok',
            'co' => $var['co'],
            'id' => $var['id'],
            'pasta' => $var['pasta'],
            'imgs' => $var['imgs'],
            'onde' => $onde
        ));
        redirect('cms/upload/img/' . $uri);
    }

    function name_cleaner($texto = '')
    {
        $this->load->helper('text');
        // remove a extenção
        $ponto = explode('.', $texto);
//        $noext = array_pop($ponto);
        unset($ponto[count($ponto) - 1]);
        $texto = implode('-', $ponto);

        $texto = str_replace(' ', '-', convert_accented_characters($texto));
        $texto = strtolower($texto);
        $texto = url_title($texto);

        return $texto;
    }

}

?>