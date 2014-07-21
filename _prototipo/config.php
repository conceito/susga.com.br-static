<?php
session_start();

$server = explode(':',$_SERVER['HTTP_HOST']);
if($server[0]=='localhost') {
	define("ENVIROMENT", "development");
} else {
  define("ENVIROMENT", "production");
}

define('SELF', $_SERVER['PHP_SELF']);
define('FISICPATH', dirname(__FILE__));


/****************************************
 *  BASE URL
 */
$base_url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://';
$base_url .= $_SERVER['HTTP_HOST'] . str_replace('//', '/', dirname($_SERVER['SCRIPT_NAME']) . '/');
$base_url = str_replace('\\', '', $base_url);

/****************************************
 *  CLIENT CONFIG
 */
$titleSite = "Company Name";
$emailSite = "mail@mail.com";

/***************************************
 *   INIT ERRORS
 */
$error = (isset($_SESSION['error'])) ? $_SESSION['error'] : array();
unset($_SESSION['error']);

// se não hove retorno do formulário limpa campos
if(! isset($error['id']) || (isset($error['id']) && $error['id'] == 0) ){
	$_SESSION['post'] = NULL;
}

/**
 * Store the page name on $p
 */
$p2 = explode('/', $_SERVER['PHP_SELF']);
$p3 = $p2[count($p2)-1];// última parte
$p = substr($p3, 0, -4);// remove ".php"



/*===============================================
 * HELPERS
 * ==============================================
 */
include_once('inc/helpers.php');