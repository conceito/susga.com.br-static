<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


// -----------------------------------------------------------------------------
/**
 * Formata RG para BD
 */
if (!function_exists('fix_rg')) {

    function fix_rg($str) {
        //return iconv('ISO-8859-1', 'UTF-8', $str);
    }

}

// -----------------------------------------------------------------------------
/**
 * Retorna a imagem baseado no e-mail da pessoa
 */
if (!function_exists('cf_foto')) {

    function cf_foto($user_array) {

        // sem o email não é possível recuperar o gravatar
        if (!isset($user_array['email'])) {
            return '';
        }

        $ci = &get_instance();

        $ci->load->spark('gravatar/1.1.1');

        $gravatar = gravatar($user_array['email'], 80, false, 'mm', 'g');

        return $gravatar;
    }

}

// --------------------------------------------------------------------------

/**
 * transforma telefone input '(nn)nnnn-nnnn' para SQL 'nn|nnnn-nnnn'
 */
if (!function_exists('tel_to_sql')) {

    function tel_to_sql($tel) {
        $numbers = str_replace(array(' ', '.', '-', '(', ')'), '', $tel);

        // with ddd - nnNNNNNNN...
        if ($numbers < 10) {
            return '';
        }

        // ddd | n números - 4 últimos
        $saida = substr($numbers, 0, 2) . '|' . substr($numbers, 2, -4) . '-' . substr($numbers, -4);
        return $saida;
    }

}

// --------------------------------------------------------------------------

/**
 * Valida tamanho da senha e converte para hash.
 */
if (!function_exists('cf_password')) {

    function cf_password($pass = '', $min = 6, $max = 8) {
        
        if (strlen(trim($pass)) < $min && strlen(trim($pass)) > $max){
            // fora do padrão
            $ci = &get_instance();
            $ci->load->helper('string');
            $rand = random_string('alnum', 8);
            $senha = md5($rand);
            
        } else {
            $senha = md5($pass);
        }
        
        return $senha;
    }

}

// --------------------------------------------------------------------------

/**
 * Limpa RG para banco.
 */
if (!function_exists('cf_rg')) {

    function cf_rg($rg = '') {
        
        $return = str_replace(array(',', '.', '-'), '', $rg);
        
        return $return;
    }

}

// --------------------------------------------------------------------------

/**
 * Limpa CPF para banco.
 */
if (!function_exists('cf_cpf')) {

    function cf_cpf($cpf = '') {
        
        $return = str_replace(array(',', '.', '-'), '', $cpf);
        
        return $return;
    }

}

// --------------------------------------------------------------------------

/**
 * Remove outras strings, deixa só números.
 */
if (!function_exists('cf_cep')) {

    function cf_cep($cep = '') {
        
        $cep = str_replace(array(',', '.', '-'), '', $cep);
        
        return $cep;
    }

}
?>
