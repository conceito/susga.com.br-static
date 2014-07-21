<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Title modal</title>
<script type="text/javascript" src="../../js/jquery-1.3.2.min.js"></script>

<script type="text/javascript" src="../js/padrao-modal.js"></script>


<link href="../css/modal.css" rel="stylesheet" type="text/css" />

<link href="../css/jquery.jcrop.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="titulo">Título modal</div>
<div class="barra-menu">
  <a href="#">Opção1</a> <a href="#">Opção2</a> <a href="#">Opção 3</a>
</div>


<div class="voce-esta-aqui">Você está aqui: <strong>Imagens</strong></div>

<ul class="lista-pastas">
	<li><a href="#">Nome da pasta Nome da pasta Nome da pasta</a></li>
	<li><a href="#">Pasta de Imagens</a></li>
	<li><a href="#">padrão para tudo</a></li>
	<li><a href="#">Nome da pasta</a></li>
</ul>

<div class="voce-esta-aqui clear">Você está aqui: <strong>Imagens &raquo; NOme</strong>


</div>
Basta clicar na foto para adicioná-la ao conteúdo. Ao clicar novamente a foto será removida.
<div class="clear"></div>

<ul class="lista-imagens">
	<li class="selected" id="1">
    <div class="click-add">inserido</div>
  <a href="#" title="nome foto"><img src="../../upl/imgs/__exemplo01_thumb.jpg" width="70" alt=" " style="margin-top:20px; margin-left:5px;" /></a></li>
  <li class="" id="2">
  <div class="click-del">removido</div>
  <a href="#"></a></li>
  <li class="" id="3"><a href="#"><img src="../../upl/imgs/__exemplo01_thumb.jpg" width="50" height="70" alt=" " style="margin-top:5px; margin-left:15px;" /></a></li>
	<li class="" id="4"><a href="#"></a></li>
</ul>


</body>
</html>