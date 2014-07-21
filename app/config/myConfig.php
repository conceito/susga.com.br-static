<?php
/*
 * CONFIGURAÇÕES GERAIS
 */
$config['title'] = "Nome do cliente";
$config['email1'] = "brunodanca@gmail.com";// comunicação oficial
$config['email_debug'] = "brunodanca@gmail.com";// para debug do sistema
$config['description'] = ''; // descrição para as metatags
$config['keywords'] = 'palavras, chaves'; // palavras-chave pata metatags
$config['instalation_folder'] = '';

/*
 * DADOS DE AUTENTICAÇÃO DE EMAIL
 */
if(ENVIRONMENT == 'development'){
    $config['smtp_host'] = "mail.arteeweb.com.br";// em branco desativa
    $config['smtp_user'] = 'arteeweb@arteeweb.com.br';
    $config['smtp_pass'] = "aw2005";
    $config['smtp_erro'] = 'arteeweb@arteeweb.com.br'; // receberá retorno de erros
    $config['smtp_encr'] = "";// TLS (google), SSL, "" (locaweb)
    $config['smtp_port'] = 587; // 25 (default) || 587
} else {
    $config['smtp_host'] = "smtp..com.br";// em branco desativa
    $config['smtp_user'] = '.com.br';
    $config['smtp_pass'] = "";
    $config['smtp_erro'] = '.com.br'; // receberá retorno de erros
    $config['smtp_encr'] = "";// TLS (google), SSL, "" (locaweb)
    $config['smtp_port'] = 587; // 25 (default) || 587
}


/*
 * CONFIGURAÇÕES DO CMS
 */
$config['cms_ver'] = '4.48';
$config['upl_imgs'] = $config['instalation_folder'] . 'upl/imgs';
$config['upl_arqs'] = $config['instalation_folder'] . 'upl/arqs';



/***************************************
 * configurações dinâmicas dos módulos *
 **************************************/
include_once APPPATH . "cache/config/modulos.php";