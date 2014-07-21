<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

<!-- view editando Menu porncipal -->
<label for="titulo" class="lb-full">Título</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', $row['titulo']);?>" />

<input name="" id="" type="text" class="input-apelido" value="<?php echo set_value('nick', $row['nick']);?>" readonly="readonly" />



<br />


<label for="cor">Cor do texto</label>
<div class="input-prepend color" data-color="rgb(<?php echo hex2rgb($row['cor1']);?>)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor1" value="<?php echo set_value('cor1',  $row['cor1']);?>" >
  <span class="add-on"><i style="background-color: <?php echo $row['cor1'];?>"></i></span>
</div>

<br />

<label for="cor">Cor do fundo</label>
<div class="input-prepend color" data-color="rgb(<?php echo hex2rgb($row['cor2']);?>)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor2" value="<?php echo set_value('cor2',  $row['cor2']);?>" >
  <span class="add-on"><i style="background-color: <?php echo $row['cor2'];?>"></i></span>
</div>



<label for="txt" class="lb-full">Posição</label><input type="text" name="resumo" class="input-longo" id="resumo" value="<?php echo set_value('resumo', $row['resumo']);?>" />



</div><!-- .panel-left -->

