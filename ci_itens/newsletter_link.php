<?php 
///////////////////////////////  CMS3 v 3.21 ///////////////////////////////////////
include("config.php");

//////////////////////////
// variaveis GET
$l = $_REQUEST['l'];
$uris = explode("-", $l);// separa as partes para salvar
$idMen = $uris[0];
$idLink = $uris[1];
$quem = $uris[2];

//////////////////////////////
// Valida as variaveis
if($idLink == "" or !$idLink or !is_numeric($idLink) ){
	echo "Link desconhecido";
	exit;
}
if($quem == "" or !$quem or $quem == "[USER]" or $quem == "[USER"){
	$quem = 0;
}

////////////////////////////////////////////////////////
// Pesquisa infos do link
$sql_link = mysql_query("select * from cms_news_links where id='$idLink'");
$link = mysql_fetch_assoc($sql_link);

//////////////////////////////////////////////////////
// Cria estatística deste clique

salva_stats($idMen, $quem, 1, $idLink);
// salva estatística
	// 1 = click em link
	// 2 = abertura de email
	// 3 = envio ok
	// 4 = erro envio
	// 5 = removido
function salva_stats($id_m, $id_u, $acao, $dado = ''){

	$d = date("Y-m-d");
	$h = date("H:i:s");
	
	if(strlen($acao) == 0) return;
	
	if( ! is_numeric($id_m)) return;
	
	// senão grava
	mysql_query("insert into cms_news_stats (mens_id, user_id, data, hora, acao, link) values ('$id_m', '$id_u', '$d', '$h', '$acao', '$dado')");
	
}

/////////////////////////////////////////////////
// Redireciona para destino
header("location: ".$link['url']);
?>