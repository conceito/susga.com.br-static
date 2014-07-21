<?php $bu = base_url();
// tipo 
?>
<h4><?php echo $tipoCrop;?></h4>


<form action="<?php echo $linkForm;?>" method="post" name="modalform" id="modalform" onsubmit="return checkCoords();">
<div class="botoes-modal"> 
<a href="<?php echo $linkRetorno;?>"><img src="<?php echo cms_img()?>bot-cancelar.jpg" width="91" height="22" alt="cancelar" /></a>
<input name="" type="image" src="<?php echo cms_img()?>bot-cortar.jpg" alt="cortar" />
</div>
<div class="nova-dims">Nova largura: <input name="w" id="w" type="text" value="0" class="input-num" readonly="readonly" /> Nova altura: <input name="h" id="h" type="text" value="0" class="input-num" readonly="readonly" /></div>
<input name="arquivo_id" type="hidden" value="99" />
<input name="pasta_id" type="hidden" value="<?php echo $pasta['id'];?>" />


<input name="x" id="x" type="hidden" value="" />
<input name="y" id="y" type="hidden" value="" />

</form>
<div class="clear"></div>
<!-- This is the image we're attaching Jcrop to -->
<div align="center">

<img src="<?php echo $bu.$path;?>" alt="<?php echo $arq['nome'];?>" id="cropbox" /> 
</div>