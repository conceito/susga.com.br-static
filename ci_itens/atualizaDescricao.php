<?php
include("config.php");
$op = (isset($_REQUEST['op'])) ? $_REQUEST['op'] : null;
$id = $_REQUEST['id'];
$desc = (isset($_REQUEST['desc'])) ? $_REQUEST['desc'] : null;
$comment = (isset($_REQUEST['comment'])) ? $_REQUEST['comment'] : null;
$rel = (isset($_REQUEST['rel'])) ? $_REQUEST['rel'] : 0;
$var = (isset($_REQUEST['var'])) ? $_REQUEST['var'] : 0;

if($op == 'comentario'){
	$sql = mysql_query("UPDATE cms_comentarios set comentario='$comment' WHERE id='$id'");
	echo $sql;	

} else if($op == 'inscrito'){
	$sql = mysql_query("UPDATE cms_inscritos set comentario='$comment' WHERE id='$id'");
	echo $sql;	

} else if($op == 'rel') {
	$sql = mysql_query("UPDATE cms_arquivos set rel='$rel' WHERE id='$id'");
	echo $sql;
} else if($op == 'tag_opt') {
	$sql = mysql_query("UPDATE cms_arquivos set tag_opt='$var' WHERE id='$id'");
	echo $sql;
} else {
	$sql = mysql_query("UPDATE cms_arquivos set descricao='$desc' WHERE id='$id'");
	echo $sql;	
}


?>