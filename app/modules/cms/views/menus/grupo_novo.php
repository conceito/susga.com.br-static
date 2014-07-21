<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>

<label for="titulo" class="lb-full"><b class="obr">[!]</b> Nome do menu</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />
<input name="nick" id="nick" type="text" class="input-apelido" value="<?php echo set_value('nick');?>" />

<br />

<label for="cor">Cor do texto</label>
<div class="input-prepend color" data-color="rgb(102,102,102)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor1" value="<?php echo set_value('cor1', '#666666');?>" >
  <span class="add-on"><i style="background-color: #666666"></i></span>
</div>

<br />

<label for="cor">Cor do fundo</label>
<div class="input-prepend color" data-color="rgb(255,255,255)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor2" value="<?php echo set_value('cor2', '#ffffff');?>" >
  <span class="add-on"><i style="background-color: #ffffff"></i></span>
</div>


<br />

<label for="resumo" class="lb-full">Posição</label><input type="text" name="resumo" class="input-longo" id="resumo" value="<?php echo set_value('resumo');?>" />

  
<br />




