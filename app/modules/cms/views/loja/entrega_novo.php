<?php
/*************************************
*	Template: nova região de entrega
*	Controller: loja/entregaNovo
*/
?>
<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>


<div class="panel-left clearfix">


<label for="titulo" class="lb-full"><b class="obr">[!]</b> Nome da região</label>
<input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />
<br />
<input name="nick" id="nick" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo set_value('nick');?>" />

<br />

<!--<label for="cor" class="lb-full">Cor do texto</label>
<div class="input-prepend color" data-color="rgb(102,102,102)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor1" value="<?php echo set_value('cor1', '#666666');?>" >
  <span class="add-on"><i style="background-color: #666666"></i></span>
</div>

<br />

<label for="cor" class="lb-full">Cor do fundo</label>
<div class="input-prepend color" data-color="rgb(255,255,255)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor2" value="<?php echo set_value('cor2', '#ffffff');?>" >
  <span class="add-on"><i style="background-color: #ffffff"></i></span>
</div>-->

	<div class="subpanel-50-50">
    
        <div class="control-group box">
        
        <label for="status" class="lb-full">Status</label>
        <div class="form-opcoes group-buttons">
        <?php echo inputs_status(2);?>
        </div>
        
        </div>
	
	</div><!-- .subpanel-50-50 -->

</div><!-- .panel-left -->

<div class="panel-right clearfix">

    <div class="alert alert-info">
    <p><strong>Entre com as informações básicas do conteúdo e... </strong></p>
    <br />

    <a href="<?php echo cms_url('cms/'.$c.'/'.$botoes['continuar']);?>" class="btn btn-info btt-salva"><i class="icon-arrow-right icon-white "></i> Continue editando</a>
    </div>
	

</div><!-- .panel-right -->
