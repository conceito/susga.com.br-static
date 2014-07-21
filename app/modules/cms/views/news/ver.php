<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$row['titulo']?></title>
<style type="text/css">
<!--
body {
	margin: 0px;
	padding: 0px;
}
img {border:0px;}
-->
</style>
</head>

<body>
<div align="center">
<?php 
	echo $row['txt'];

	echo '<br /><br /><table width="500" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td>';
	echo '<font size="2" face="Verdana">';
	echo nl2br($row['resumo']);
	echo '</td>  </tr></table>';

?></div>



</body>
</html>
