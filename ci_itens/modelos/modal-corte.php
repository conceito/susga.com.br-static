<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Title modal</title>
<script type="text/javascript" src="../../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery.Jcrop.js"></script>
<script type="text/javascript" src="../js/jcrop_livre.js"></script>

<script type="text/javascript">
$(document).ready(function(){
						   
});
</script>
<link href="../css/modal.css" rel="stylesheet" type="text/css" />

<link href="../css/jquery.jcrop.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="titulo">Título modal</div>
<div class="barra-menu">
  <a href="#">Opção1</a> <a href="#">Opção2</a> <a href="#">Opção 3</a>
</div>



<h4>Corte livre: Você pode usar qualquer proporção ao cortar a imagem.</h4>


<form action="" method="post" name="modalform" id="modalform" onsubmit="return checkCoords();">
<div class="botoes-modal"> 
<a href="#"><img src="../img/bot-cancelar.jpg" width="91" height="22" alt="cancelar" /></a>
<input name="" type="image" src="../img/bot-cortar.jpg" alt="cortar" />
</div>
<div class="nova-dims">Nova largura: <input name="w" id="w" type="text" value="xxx" class="input-num" readonly="readonly" /> Nova altura: <input name="h" id="h" type="text" value="xxx" class="input-num" readonly="readonly" /></div>
<input name="arquivo_id" type="hidden" value="99" />


<input name="x" id="x" type="hidden" value="" />
<input name="y" id="y" type="hidden" value="" />

</form>
<div class="clear"></div>
<!-- This is the image we're attaching Jcrop to -->
<div align="center">
<img src="../../upl/imgs/1121187_10122008.jpg" width="300" height="300" alt=" " id="cropbox" /> 
</div>



</body>
</html>