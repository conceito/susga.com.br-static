
<?php echo validation_errors(); ?>

<div class="panel-left clearfix">
	
    <div class="control-group">
	
	<label for="titulo" class="lb-full">Nome</label>
    <input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', form_prep($row['titulo']));?>" />

	<input name="" id="" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo set_value('nick', $row['nick']);?>" readonly="readonly" />
	
	</div><!-- .control-group -->
    
    
    <div class="control-group">	
	 
	<label for="txt" class="lb-full">Descrição</label>
    <textarea name="txt" class="textarea-curto" id="txt"><?php echo set_value('txt', $row['txt']);?></textarea>
    
    </div><!-- .control-group -->
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">
	
	
    <div class="control-group box">	
    
    <label for="status" class="lb-full">Status</label>
    <div class="form-opcoes group-buttons">
        <?php echo inputs_status($row['status']);?>
     </div>
     
    </div><!-- .control-group -->

    <div class="control-group box">	
    
	<label for="dt1" class="lb-full">Data</label>
    <input name="dt1" id="dt1" type="text" class="input-curto" value="<?php echo set_value('dt1', $row['dt1']);?>" />	
	
	</div><!-- .control-group -->
    
    <div class="control-group box">

	<label for="tipo" style="margin-top:;" class="lb-full">Tipo de pasta: <?php echo $tipo;?></label>
	
	</div><!-- .control-group -->
    
    <div class="control-group box">
	
	<label for="grupos" class="lb-full">Grupo</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
	
	</div><!-- .control-group -->
	
	
    <div class="control-group box">
	
	<div class="padrao-img" <?php if($tipo=='Arquivos')echo 'style="display:none;"';?>>
	<label for="padrao" class="lb-full">Padrão de imagens</label>Mini W:<input name="mini_w" id="mini_w" type="text" class="input-mini" value="<?php echo set_value('mini_w', $row['mini_w']);?>" /> 
	&nbsp;&nbsp;&nbsp;Mini H:<input name="mini_h" id="mini_h" type="text" class="input-mini" value="<?php echo set_value('mini_h',$row['mini_h']);?>" />
	
	<br />
	
	<label for="padrao2" class="">&nbsp;</label>Med W:<input name="med_w" id="med_w" type="text" class="input-mini" value="<?php echo set_value('med_w', $row['med_w']);?>" />
	&nbsp;&nbsp;&nbsp;Med H:<input name="med_h" id="med_h" type="text" class="input-mini" value="<?php echo set_value('med_h', $row['med_h']);?>" />
	<br />
	
	<label for="padrao2" class="">&nbsp;</label>Max W:<input name="max_w" id="max_w" type="text" class="input-mini" value="<?php echo set_value('max_w', $row['max_w']);?>" /> 
	&nbsp;&nbsp;&nbsp;Max H:<input name="max_h" id="max_h" type="text" class="input-mini" value="<?php echo set_value('max_h', $row['max_h']);?>" />
	<br />
	
	</div>
    
    </div><!-- .control-group -->


</div><!-- .panel-right -->
 


