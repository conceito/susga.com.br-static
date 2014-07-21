<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>


<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

	<div class="control-group">
    
	<label for="titulo" class="lb-full"><b class="obr">[!]</b> Nome</label>
    <input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />

	<input name="nick" id="nick" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo set_value('nick');?>" />
	
	</div><!-- .control-group -->
    
    <div class="control-group">
	
	<label for="txt" class="lb-full"><b class="obr">[!]</b> Descrição</label>
    <textarea name="txt" class="textarea-curto" id="txt"><?php echo set_value('txt');?></textarea>
    
    </div><!-- .control-group -->

</div><!-- .panel-left -->


<div class="panel-right clearfix">	
    
    <div class="control-group box">
    
    <label for="status" class="lb-full">Status</label>
	<div class="form-opcoes group-buttons">
		<?php echo inputs_status();?>
    </div>

	</div><!-- .control-group -->

	<div class="control-group box">
    
	<label for="dt1" class="lb-full">Data</label>
    <input name="dt1" id="dt1" type="text" class="input-curto" value="<?php echo set_value('dt1', date("d/m/Y"));?>" />
    
    </div><!-- .control-group -->
	
    
	<div class="control-group box">
	
	<label for="tipo" class="lb-full">Tipo de pasta: <?php echo $tipo;?></label>
	
	</div><!-- .control-group -->
    
    <div class="control-group box">
	
	<label for="grupos" class="lb-full"><b class="obr">[!]</b> Grupo</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
	
	</div><!-- .control-group -->
    
    <div class="control-group box">
	
	
	
	<div class="padrao-img" <?php if($tipo=='Arquivos')echo 'style="display:none;"';?>>
	<label for="padrao" class="lb-full"><b class="obr">[!]</b> Padrão de imagens</label>Mini W:<input name="mini_w" id="mini_w" type="text" class="input-mini" value="<?php echo set_value('mini_w', $this->config->item('imagem_mini_w'));?>" /> 
	&nbsp;&nbsp;&nbsp;Mini H:<input name="mini_h" id="mini_h" type="text" class="input-mini" value="<?php echo set_value('mini_h', $this->config->item('imagem_mini_h'));?>" />
	
	<br />
	
	<label for="padrao2" class="">&nbsp;</label>Med W:<input name="med_w" id="med_w" type="text" class="input-mini" value="<?php echo set_value('med_w', $this->config->item('imagem_med_w'));?>" />
	&nbsp;&nbsp;&nbsp;Med H:<input name="med_h" id="med_h" type="text" class="input-mini" value="<?php echo set_value('med_h', $this->config->item('imagem_med_h'));?>" />
	<br />
	
	<label for="padrao2" class="">&nbsp;</label>Max W:<input name="max_w" id="max_w" type="text" class="input-mini" value="<?php echo set_value('max_w', $this->config->item('imagem_max_w'));?>" /> 
	&nbsp;&nbsp;&nbsp;Max H:<input name="max_h" id="max_h" type="text" class="input-mini" value="<?php echo set_value('max_h', $this->config->item('imagem_max_h'));?>" />
	<br />
	
	</div>
	 
     
     </div><!-- .control-group -->
	 
	


</div><!-- .panel-right -->



 