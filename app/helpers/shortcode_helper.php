<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * As funções devem começar com 'sc_' para não haver colisões com outras funções.
 * As chamadas aos shortcodes NÃO devem conter o prefixo 'sc_', pois será
 * acrescentado em cms_conteudo.
 */

// -----------------------------------------------------------------------------
/**
 * Exemplo de um shortcode com dois argumentos.
 * $atts é um array com os atributos da tag
 * $content é o conteúdo da tag shortcode
 */
if (!function_exists('sc_myCodeParser')) {

    function sc_myCodeParser($atts, $content='') {

        return '<strong>' . $content . ' (' . $atts['tag'] . ')</strong>';
    }

}

// -----------------------------------------------------------------------------
/**
 * Shortcode para gerar listagem de imagens com links.
 */
if (!function_exists('sc_slide')) {

    function sc_slide($atts = array(), $content='') {
//mybug($content);
        if(strlen(trim($content)) == 0){            
            return '';
        }
        
        $ci = &get_instance();        
      
        // remove tudo, menos alfanuméricos e ','
        $content = preg_replace("/[^a-zA-Z0-9,]/", "", $content);
        
        $ids = explode(',', trim($content));        
        
        $gallery = $ci->site_utils->get_arquivos_from_array($ids);
        $p = base_url().$ci->config->item('upl_imgs').'/';
        
        $html = '<div class="slider-content"><ul class="unstyled thumbnails">';
        
        foreach($gallery as $row){
            
            $imgThumb = thumb($row['nome']);
            $imgBig = grande($row['nome']);
            
            
            $html .= '<li class="span3">';
            $html .= '<a href="'.$p.$imgBig.'" class="thumbnail">';
            $html .= '<img src="' .$p.$imgThumb. '" />';
            $html .= '<p>'.$row['descricao'].'</p>';
            $html .= '</a>';
            $html .= '</li>';
        }
        
        $html .= '</ul></div>';
        
        return $html;
    }

}
?>
