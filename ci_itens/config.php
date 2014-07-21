<?php

// The name of THIS file
if($_SERVER['HTTP_HOST'] == 'localhost'){
    define('SELF', 'ci_itens\\' . pathinfo(__FILE__, PATHINFO_BASENAME));
} else{
    define('SELF', 'ci_itens/config.php');
}
//

// Path to the front controller (this file)
define('FCPATH', str_replace(SELF, '', __FILE__));
// Path to the system folder
define('BASEPATH', str_replace("\\", "/", FCPATH . 'ci/'));
// The path to the "application" folder
define('APPPATH', str_replace("\\", "/", FCPATH) . 'app/');
// Ambiente
define('ENVIRONMENT', ($_SERVER['HTTP_HOST'] == 'localhost') ? 'development' : 'production');

//define('APPPATH', '/home/conrerp1/www/app/');

//echo '<pre>';
//echo '$self: '.$self;
//echo '<br>';
//echo 'SELF: '.SELF;
//echo '<br>';
//echo 'FCPATH: '.FCPATH;
//echo '<br>';
//echo 'BASEPATH: '.BASEPATH;
//echo '<br>';
//echo 'APPPATH: '.APPPATH;

require_once APPPATH . 'config/config.php';
require_once APPPATH . 'config/myConfig.php';
require_once APPPATH . 'config/database.php';



/*
 * Dados do site
 */
$TituloSite = $config['title'];
$EmailSite = $config['email1']; // retorno da newsletter
$index = "index.php";
$UrlSite = str_replace('ci_itens/', '', $config['base_url']);
$arqs = $UrlSite . $config['upl_arqs'].'/';
$imgs = $UrlSite . $config['upl_imgs'].'/';


//echo $arqs;



/*
 * Conexão
 */
//$host = $db['production']['hostname'] = 'mysql.conrerp1.org.br';
//$usuario_db = $db['production']['username'] = 'conrerp1';
//$senha_db = $db['production']['password'] = 'dbconrerp1';
//$nome_db = $db['production']['database'] = 'conrerp1';

$host = $db[ENVIRONMENT]['hostname'];
$usuario_db = $db[ENVIRONMENT]['username'];
$senha_db = $db[ENVIRONMENT]['password'];
$nome_db = $db[ENVIRONMENT]['database'];

$conn = mysql_connect($host, $usuario_db, $senha_db);
if (function_exists('mysql_set_charset') === false) {
    mysql_query('SET NAMES "utf8"');
} else {
    mysql_set_charset('utf8', $conn);
}
mysql_select_db($nome_db, $conn);

if (!$conn) {
    echo "Não foi possível estabelecer a conecção com o servidor.";
}
?>