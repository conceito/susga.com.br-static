<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// --------------------------------------------------------------------------

/**
 * 
 */
if (!function_exists('link_ver_arquivo')) {

    function link_ver_arquivo($path = '', $nome = '', $ext = '', $tipo = 0) {
        // tipos
        if (($ext == 'flv' || $ext == 'mp4') && $tipo == 0) {// flv local
            $modal = 'nyroModal';
            $url = cms_url('cms/pastas/player/v:' . $nome);
        } else if ($tipo == 2) {// YouTube
            $modal = 'nyroModal';
            $url = $nome;
        } else if ($tipo == 3) {// externo
            $modal = 'nyroModal';
            $url = $nome;
        } else { // padrçao
            $modal = '';
            $url = $path . '/' . $nome;
        }
        $link = '<a href="' . $url . '" target="_blank" class="ver-arq ' . $modal . '"></a>';
        return $link; //<<--
    }

}

// --------------------------------------------------------------------------

/**
 * Imagem de ajuda
 */
if (!function_exists('i')) {

    function i($str = '', $mt = 0) {
        $mt = ' style="margin-top:' . $mt . 'px;"';
        $img = '<img src="' . cms_img() . 'ico-i.png" ' . $mt . ' width="21" height="20" alt="informações" class="ico-i" title="' . $str . '" />';
        return $img; //<<--
    }

}

// --------------------------------------------------------------------------

/**
 * 
 */
if (!function_exists('barra_edicao')) {

    function barra_edicao($linkEdicao, $labelLnk, $tipo = 'conteudo', $quantidade = 0) {
        $ci = &get_instance();
        $uri = $ci->uri->dash_to_array($linkEdicao);
        $acoes = $ci->cms_libs->gera_array_acoes();
        
        // alguns módulos não tem metadados
        if(isset($uri['co']) && $uri['co'] != 18 && $uri['co'] != 1){
            $md = $ci->cms_libs->get_meta_by_id($uri['id']);
//            mybug($uri);
        }

        
        // se existe um módulo específico que gera o conteúdo desta página/post
        $h4 = '';
        if (isset($md['meta_modulo_content']) && is_numeric($md['meta_modulo_content'])) {
            $modulo = $ci->cms_libs->dados_menus_raiz($md['meta_modulo_content']);
            $h4 = '<span class="editar"> | <a href="' . cms_url($modulo['uri']) . '" title="Editar conteúdos" class="editar-rapido-item">módulo de conteúdo</a></span> ';
        }

        // se não houver conteúdo na pasta não deixa apagar!
        if ($quantidade > 0 && $tipo == 'pasta') {
            $h5 = '<span class="apagar"> | <a href="#" title="Antes a pasta deve estar vazia!" class="apagar-item-blocked">apagar</a></span>';
            $h5b = '';
        } else {
            $h5 = '<span class="apagar"> | <a href="#" title="Apagar este item" class="apagar-item">apagar</a></span>';
            $h5b = '<span class="confirma"><a href="#" class="nao-item">não</a> / <a href="#" class="sim-item">sim</a></span>';
        }

        $h1 = '<a href="' . $linkEdicao . '" title="Editar este item" class="edit">' . $labelLnk . '</a>';
        // se existir um módulo de conteúdo, acrescenta * para sinalização
        if($h4 != ''){
            $h1 .= ' *';
        }
        
        $h1b = '<span class="edit-off">' . $labelLnk . '</span>';
        $h2 = '<div class="opcoes">';
        // ler resumo
        if ($tipo != 'variaveis') {
            $h8 = '<span class="resumo"><a href="#" class="resumo-item" title="Ler resumo" class="editar-item">ler resumo</a></span> ';
        } else {
            $h8 = '';
        }

        $h3 = '<span class="editar"> | <a href="' . $linkEdicao . '" title="Editar este item" class="editar-item">editar</a></span> ';

        $h7 = '</div>';

        $saida = ''; // init
        $saida .= $h1;
        $saida .= $h2;
        $saida .= $h8;
        $saida .= ( $acoes['c']) ? $h3 : '';
        $saida .= $h4;
        $saida .= ( $acoes['a'] && (!isset($md) || $md['meta_no_delete'] == 0) ) ? $h5 : '';
        $saida .= ( $acoes['a']) ? $h5b : '';
        $saida .= $h7;

        return $saida;
    }

}

// ---------------------------------------------------------------------------
/**
 * Gera o alerta com link para o módulo responsável pelo conteúdo da página.
 */
if (!function_exists('module_content')) {

    function module_content($row) {
        $saida = '';
        
        if (isset($row['metadados']) && $row['metadados']){
            
            $ci = &get_instance();
            
            $md = $ci->cms_libs->str_to_metadados($row['metadados']);
            $modulo_id = (isset($md['meta_modulo_content'])) ? $md['meta_modulo_content'] : '';

            if (is_numeric($modulo_id)){
                
                $modulo = $ci->cms_libs->dados_menus_raiz($modulo_id); 
                
                $saida = '<div class="alert">
                <p><strong>O conteúdo desta página é gerado pelo módulo "'.$modulo['label'].'".</strong>
                <a href="'.cms_url($modulo['uri']).'" class="">&rarr; Editar conteúdos</a></p>
                </div>';
                
            }
            

        }
        
        return $saida;
    }

}

// --------------------------------------------------------------------------

/**
 * 
 */
if (!function_exists('grupoSimplesNome')) {

    function grupoSimplesNome($nome = '', $cor1 = '', $cor2 = '') {

        $saida = '<span style="background-color:' . $cor2 . '; color:' . $cor1 . '; padding-left:5px; padding-right:5px;"  class="bloco-cor">' . $nome . '</span>';


        return $saida;
    }

}

// --------------------------------------------------------------------------

/**
 * 
 */
if (!function_exists('grupoNome')) {

    function grupoNome($grupoParents) {

        if ($grupoParents === false) {
            $saida = '<span style="background-color:#fff; color:#000;"  class="bloco-cor">desconhecido</span>';
        } else {

            // percorre a lista de grupos, o último é o grupo selecionado
            $saida = '';
            $ttl = count($grupoParents);
            for ($x = 0; $x < $ttl; $x++) {
                // enquanto não chegar no final, exibe cor padrão
                if ($x < ($ttl - 1)) {
                    $cor1 = '';
                    $cor2 = '';
                    $class = 'lowlevel';
                } else {
                    $cor1 = $grupoParents[$x]['grupoCor1'];
                    $cor2 = $grupoParents[$x]['grupoCor2'];
                    $class = '';
                }

                $saida .= ' <span style="background-color:' . $cor2 . '; color:' . $cor1 . ';"  class="bloco-cor ' . $class . '">' . $grupoParents[$x]['titulo'] . '</span>';
                if ($x < ($ttl - 1)) {
                    $saida .= '<span class="bloco-lig"> › </span>';
                }
            }
        }

        return $saida;
    }

}

// --------------------------------------------------------------------------

/**
 * monta os links das listagens de ativação e desativação
 */
if (!function_exists('link_status')) {

    function link_status($data = '-', $status = 2) {
        $ci = &get_instance();
        $data = ($data == '-') ? '-' : formaPadrao($data);
        $acoes = $ci->cms_libs->gera_array_acoes();
//        $h1 = '<span class="data">' . $data . '</span><span class="status"><a href="#" class="' . $status . '-item" title="clique para trocar status">' . $status . '</a></span>';
//        $h2 = '<span class="data">' . $data . '</span><span class="status">' . $status . '</span>';
        $h1 = '<span class="status"><a href="#" class="' . $status . '-item" title="clique para trocar status">' . $status . '</a></span>';
        $h2 = '<span class="status">' . $status . '</span>';

        $saida = ''; // init
        $saida .= ( $acoes['c']) ? $h1 : $h2;

        return $saida;
    }

}

// --------------------------------------------------------------------------

/**
 * monta os links das listagens de ativação e desativação
 */
if (!function_exists('news_status')) {

    function news_status($data = '-', $status = 1) {
        $data = ($data == '-') ? '-' : formaPadrao($data);
        if ($status == 0) {
            $label = 'enviando';
        } else if ($status == 1) {
            $label = 'na fila de envio';
        } else if ($status == 2) {
            $label = 'terminado';
        }
        $saida = '<span class="data">' . $data . '</span><span class="status news-stt-' . $status . '">' . $label . '</span>';

        return $saida;
    }

}

// --------------------------------------------------------------------------

/**
 * monta os links das listagens de destaque
 */
if (!function_exists('link_destaque')) {

    function link_destaque($status = 0) {
        $ci = &get_instance();
        $acoes = $ci->cms_libs->gera_array_acoes();
        $stt = ($status == 0) ? 'nao' : 'sim';
        $lab = ($status == 0) ? 'não' : 'sim';
        $h1 = '<a href="#" class="destaque-' . $stt . '" title="clique para trocar status">' . $lab . '</a>';
        $h2 = $lab;

        $saida = ''; // init
        $saida .= ( $acoes['c']) ? $h1 : $h2;

        return $saida;
    }

}

// --------------------------------------------------------------------------

/**
 * 
 */
if (!function_exists('link_comments')) {

    function link_comments($total, $novos, $linkEdicao) {

        $saida = '<span class="comments-total">' . $total . '</span>';
        $saida .= ( $novos > 0) ? '<a href="' . $linkEdicao . '/tab:3" class="comments_new" title="Aguardando moderação">' . $novos . '</a>' : '';

        return $saida;
    }

}

// --------------------------------------------------------------------------

/**
 * 
 */
if (!function_exists('link_inscritos')) {

    function link_inscritos($total, $novos, $linkEdicao) {

        $saida = '<span class="comments-total">' . $total . '</span>';
        $saida .= ( $novos > 0) ? '<a href="' . $linkEdicao . '/tab:3" class="comments_new" title="Aguardando moderação">' . $novos . '</a>' : '';

        return $saida;
    }

}


// --------------------------------------------------------------------------

/**
 *
 */
if (!function_exists('viewGetTags')) {

    function viewGetTags() {

        $ci = &get_instance();
//        $var = $ci->uri->to_array(array('co', 'id'));
        echo $ci->paginas_model->get_view_tags();
    }

}

if (!function_exists('inputs_status')) {

    function inputs_status($status = '1') {

        //mybug($status);

        $r = form_radio(array(
            'name' => 'status',
            'id' => 'status1',
            'value' => '1',
            'checked' => ($status == 1),
            'style' => '',
                ));
        $r .= '<label for="status1">ativo</label>';
        $r .= form_radio(array(
            'name' => 'status',
            'id' => 'status0',
            'value' => '0',
            'checked' => ($status === '0'),
            'style' => '',
                ));
        $r .= '<label for="status0">inativo</label>';

        $r .= form_radio(array(
            'name' => 'status',
            'id' => 'status2',
            'value' => '2',
            'checked' => ($status == 2),
            'style' => '',
                ));
        $r .= '<label for="status2">editando</label>';

        return $r;
    }

}

if (!function_exists('inputs_show')) {

    function inputs_show($status = '1') {

        //mybug($status);

        $r = form_radio(array(
            'name' => 'show',
            'id' => 'show1',
            'value' => '1',
            'checked' => ($status == 1),
            'style' => '',
                ));
        $r .= '<label for="show1">sim</label>';
        $r .= form_radio(array(
            'name' => 'show',
            'id' => 'show0',
            'value' => '0',
            'checked' => ($status === '0'),
            'style' => '',
                ));
        $r .= '<label for="show0">não</label>';


        return $r;
    }

}

if (!function_exists('list_gallery_tags')) {

    function list_gallery_tags() {

        $ci = & get_instance();
        $t1 = $ci->config->item('tag_opt_1');
        $t2 = $ci->config->item('tag_opt_2');
        $t3 = $ci->config->item('tag_opt_3');
        $t4 = $ci->config->item('tag_opt_4');
        $t5 = $ci->config->item('tag_opt_5');

        $ret = '<ul class="tags-opts">';
        if ($t1)
            $ret .= '<li class="tag-opt-1 ico-i" title="' . $t1 . '"></li>';
        if ($t2)
            $ret .= '<li class="tag-opt-2 ico-i" title="' . $t2 . '"></li>';
        if ($t3)
            $ret .= '<li class="tag-opt-3 ico-i" title="' . $t3 . '"></li>';
        if ($t4)
            $ret .= '<li class="tag-opt-4 ico-i" title="' . $t4 . '"></li>';
        if ($t5)
            $ret .= '<li class="tag-opt-5 ico-i" title="' . $t5 . '"></li>';
        $ret .= '</ul>';

        return $ret;
    }

}

// --------------------------------------------------------------------------
/**
 * Recebe o array do arquivo e monta HTML para thumbnail
 */
if(!function_exists('conteudoThumb')){
   function conteudoThumb($conteudo_arq_array){
        
       if(!$conteudo_arq_array){
           return '';
       }
     
       $ci = & get_instance();
       
       $nome = $conteudo_arq_array['nome'];
       $thumb = thumb($nome);
        
        // arqs path
        $path = cms_url().$ci->config->item('upl_imgs').'/';
        
        $return = '<a href="'.$path.$nome.'" class="nyroModal" title="Ampliar" ><img src="'.$path.$thumb.'"  class="banner-thumb" /></a>';
        
        
//        mybug($path);
        return $return;
    } 
}

if(!function_exists('show_estoque')){
    function show_estoque($prod_array){
        $ci = & get_instance();
        
//        mybug($prod_array);
        
        $class = '';
        // produto físico
        if($prod_array['download'] == 0){
            
            // usa opções para estoque?
            if($prod_array['options_estoque']){
                $estoque = $prod_array['options_estoque']['estoque'];
            } else {
                 $estoque = $prod_array['estoque'];
            }

            // verifica se o estoque atingiu o limite
            if($estoque <= $ci->config->item('estoque_alert')){
                $class = 'label label-important';
            }
            
        }
        // digital
        else {
            $estoque = 'download';
        }
        
        
        $html = '<span class="'.$class.'">'.$estoque.'</span>';
        
        return $html;
    }
}


//-----------------------------------------------------------------------------

if(!function_exists('priority_combobox'))
{
    function priority_combobox($priorityId = NULL)
    {
        $ci =& get_instance();
        $options = $ci->config->item('post_priority');
        if(!$priorityId)
        {
            $priorityId = 3;
        }
        else if(is_array($priorityId))
        {
            $priorityId = get_meta($priorityId, 'priority', null, true);
        }
//        mybug($priorityId);
        
        return form_dropdown('prioridade', $options, $priorityId);
    }
}
?>
