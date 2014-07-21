<?php 
///////////////////////////////  Script que acusa a visualizaзгo pelo usuбrio - CMS3 v 3.10 ///////////////////////////////////////
// Por Bruno Barros - bruno@brunobarros - 2009

// includes necessбrios
include("config.php");

$image = $UrlSite . 'ci_itens/img/spacer.gif';

$i = $_REQUEST['i'];
$uris = explode("-", $i);// separa as partes para salvar

salva_stats($uris[0], $uris[1], 2);

$src = imagecreatefromgif($image);
imagegif($src);
//imagedestroy($src);

// salva estatнstica

	// 1 = click em link
	// 2 = abertura de email
	// 3 = envio ok
	// 4 = erro envio
	// 5 = removido
function salva_stats($id_m, $id_u, $acao, $dado = ''){

	$d = date("Y-m-d");
	$h = date("H:i:s");
	
	// pesquisa para saber se este usuбrio jб gravou dentro desta hora e dia
	$sql = mysql_query("select * from cms_news_stats where mens_id='$id_m' AND user_id='$id_u' AND data='$d' AND acao='$acao'");
	
	if(mysql_num_rows($sql) == 0){
		// senгo grava
		mysql_query("insert into cms_news_stats (mens_id, user_id, data, hora, acao) values ('$id_m', '$id_u', '$d', '$h', '$acao')");
	}	
}
?>