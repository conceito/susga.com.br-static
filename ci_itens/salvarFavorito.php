<?php
include("config.php");
session_start();
$nome = $_REQUEST['nome'];
$urilimpa = $_REQUEST['urilimpa'];
$uri = str_replace('_', '/', $urilimpa);
$uri = trim($uri, '/');
$admin_id = $_SESSION['cms']['admin_id'];

// verifica se já existe
$sql = mysql_query("SELECT * FROM cms_favoritos where uri='$uri' AND admin_id='$admin_id'");

if(mysql_num_rows($sql) > 0){
	echo '0';
} else {

	// salva
	$sql2 = mysql_query("INSERT INTO cms_favoritos (titulo, uri, admin_id) VALUES('$nome', '$uri', '$admin_id')");
	echo $sql2;
}


?>