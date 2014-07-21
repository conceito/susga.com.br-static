<?php echo validation_errors(); ?>
<?php
// tipo de grupo

?>


<label for="titulo" class="lb-full"><b class="obr">[!]</b> Título</label>
<input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />
<br />

<label for="" class="lb-full">&nbsp;</label>
<input name="nick" id="nick" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo set_value('nick');?>" />

<br />


<label for="cor" class="lb-full">Cor do texto</label>
<div class="input-prepend color" data-color="rgb(<?php echo hex2rgb('#666666');?>)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor1" value="<?php echo set_value('cor1',  '#666666');?>" >
  <span class="add-on"><i style="background-color: #666666"></i></span>
</div>

<br />

<label for="cor" class="lb-full">Cor do fundo</label>
<div class="input-prepend color" data-color="rgb(<?php echo hex2rgb('#ffffff');?>)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor2" value="<?php echo set_value('cor2',  '#ffffff');?>" >
  <span class="add-on"><i style="background-color:#ffffff"></i></span>
</div>


<label for="txt" class="lb-full">Descrição</label><textarea name="txt" class="textarea-curto" id="txt"><?php echo set_value('txt');?></textarea>

  
<br />


