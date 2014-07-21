<?php
$id = $_REQUEST['id'];
$ordem = $_REQUEST['o'];
$op = $_REQUEST['op'];

if(isset($ordem)){
	echo 'ID: ' . $id . ', ordem: '. $ordem;
}


if($op == 'del'){
	echo '1';	
}
if($op == 'desativa')echo '1';
if($op == 'ativa')echo '1';
if($op == 'remove')echo '1';
?>