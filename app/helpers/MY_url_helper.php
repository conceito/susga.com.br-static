<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Para sites multilingues sobrepõe função original.
 * @param type $uri
 * @return type
 */
/*
function site_url($uri = '') {

    $CI = & get_instance();

    if (is_array($uri)) {
        $uri = implode('/', $uri);
    }

    if (function_exists('get_instance')) {

//            $uri = $CI->my_lang->localized($uri);
        $langSeg = $CI->uri->segment(1);
        if (strlen($langSeg) != 2) {
            $langSeg = 'pt';
        }
        $uri = $langSeg . '/' . $uri;
    }

    $index = ($CI->config->item('index_page') == '') ? '' : $CI->config->item('index_page') . '/';


    // @todo: remover index.php
    return base_url() . $index . trim($uri, '/');
}
 * 
 */


// ------------------------------------------------------------------------

/**
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the method: location or redirect
 * @return	string
 */
if (!function_exists('redirect'))
{

    function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        if (!preg_match('#^https?://#i', $uri))
        {
            $uri = site_url($uri);
        }

        switch ($method)
        {
            case 'refresh' : header("Refresh:0;url=" . $uri);
                break;
            case 'meta' : 
                echo '<script type="text/javascript">';
                echo 'window.location.href="' . $uri . '";';
                echo '</script>';
                echo '<noscript>';
                echo '<meta http-equiv="refresh" content="0;url=' . $uri . '" />';
                echo '</noscript>';
                break;
            default : header("Location: " . $uri, TRUE, $http_response_code);
                break;
        }
        exit;
    }

}
