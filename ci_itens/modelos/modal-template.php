<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Title modal</title>
<script type="text/javascript" src="../../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
						   
});
</script>
<link href="../css/modal.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="titulo">Título modal</div>
<div class="barra-menu">
  <a href="#">Opção1</a> <a href="#">Opção2</a> <a href="#">Opção 3</a>
</div>

<div class="resposta-ok">
Sistema de Gerenciamento de Conteúdo v3.0
</div>
<div class="resposta-erro">
Sistema de Gerenciamento de Conteúdo v3.0
</div>

<div class="preview"><!--preview para imagens-->
	<ul>
    <li class="img">
    <img src="../img/bot-form-pesquisar.gif" width="90" height="90" alt=" " />
    	<div class="controles"><a href="#" class="apagar-arq">del</a><br />c<br />s</div>
    </li>
    <li class="img">
    <img src="../img/bot-form-pesquisar.gif" width="90" height="90" alt=" " />
    	<div class="controles">c</div>
    </li>
    <li class="img">
    <img src="../img/bot-form-pesquisar.gif" width="90" height="90" alt=" " />
    	<div class="controles">c</div>
    </li>
    </ul>
    <div class="clear"></div>
</div>
<div class="preview"><!--preview para arquivos-->
	<ul>
    <li class="arq" id="">
    <div class="ext">DOCX | 999.999 Kb</div>
    <strong>nomedoarquivo.zip</strong>    
    <div class="controles"><a href="#" class="apagar-arq">apagar</a></div>
    </li>
    <li class="arq">
    <div class="ext">DOCX | 999.999 Kb</div>
    <strong>nomedoarquivo.zip</strong>    
    <div class="controles"><a href="#">apagar</a></div>
    </li>
    <li class="arq">
   <div class="ext">DOCX | 999.999 Kb</div>
    <strong>nomedoarquivo.zip</strong>    
    <div class="controles"><a href="#">apagar</a></div>
    </li>
    </ul>
    <div class="clear"></div>
</div>

<fieldset class="modal-dicas"><legend>Dicas</legend>
<ul>

	<li>iasdadasj</li>
	<li>ijaljsadasd</li>

</ul>
</fieldset>


<form action="" method="post" name="modalform" id="modalform">

<h3>Este é para isso:</h3>

<label for="nome">Campo</label><input name="nome" id="nome" type="text" value="valor" class="input-longo" />

<input name="ok" type="button" value="enviar" class="buttom" />

</form>

<!--opções de edição-->
<div class="img-infos">
  <img src="../img/bot-form-pesquisar.gif" width="90" height="90" alt="img" />
  <p><span class="nome-arquivo">nome-do-arquivo.jpg</span> foi postado em <strong>99/99/9999</strong></p>
<strong>Descrição:</strong><br />

<textarea name="descricao_modal" class="desc-model" cols="" rows="">If on the off-chance the image has been previously loaded then it should be in the buffer and changing the dimensions should be a lot quicker rather than having to use the very slow Manipulation library.</textarea>


</div>

<span class="cinza14">Opções de edição:</span>
<h4>Girar</h4>
<ul class="lista-opcoes-giro">
	<li><a href="#"><img src="../img/giro-90d.jpg" width="36" height="43" alt="90 direita" />90º para<br />direita</a></li>
	<li><a href="#"><img src="../img/giro-180.jpg" width="36" height="43" alt="180" />180º</a></li>
	<li style="width:100px;"><a href="#"><img src="../img/giro-90e.jpg" width="33" height="43" alt="90 esquerda" />90º para<br />esquerda</a></li>
	<li><a href="#"><img src="../img/giro-h.jpg" width="33" height="43" alt="inverter" />Inverter na<br />horizontal</a></li>
	<li><a href="#"><img src="../img/giro-v.jpg" width="33" height="43" alt="inverter" />Inverter na<br />vertical</a></li>
</ul>
<h4>Cortar</h4>
<ul class="lista-opcoes-corte">
	<li style="width:90px;"><a href="#"><img src="../img/corte-livre.jpg" width="38" height="42" alt="90 direita" />corte<br />livre</a></li>
	<li><a href="#"><img src="../img/corte-quadrado.jpg" width="40" height="42" alt="180" />corte<br />quadrado</a></li>
	<li><a href="#"><img src="../img/corte-h.jpg" width="41" height="42" alt="90 esquerda" />retângulo<br />deitado 4:3</a></li>
	<li><a href="#"><img src="../img/corte-v.jpg" width="38" height="42" alt="inverter" />retângulo<br />em pé 3:4</a></li>
	
</ul>

</body>
</html>