<?php
/*************************************
*	Template: edita região de entrega
*	Controller: loja/entregaEdita
*/

echo validation_errors(); 
?>

<div class="panel-left clearfix">
	
    <div class="control-group">
        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Título</label>
        <input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', form_prep($row['titulo']));?>" style="width:97%" />
       
        
    </div><!-- .ai-page -->    
	
	<input name="nick_edita" id="" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo set_value('nick', $row['nick']);?>" readonly="readonly"/>
	
	
	<br />
    
    
	
    <div class="control-group">
	
	<label for="txt" class="lb-full">Conteúdo principal <?php if($multicontent):?>| <a href="#" onclick="javascript:$('#tabs').tabs('select',1); return false;">Mais conteúdos</a><?php endif;?></label>
	<div class="content-ctrl">
	<a href="<?php echo cms_url($linkAddImage);?>" class="ico-img nyroModal" target="_blank">Subir imagem</a>
	<!--<a href="<?php echo cms_url($linkAddArq);?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">arquivo</b></span></a>-->
	</div>
	<textarea name="txt" class="textarea-longo" id="txt" style="width:100%"><?php echo set_value('txt', $row['txt']);?></textarea>
	
	</div><!-- .control-group -->
    
    <?php if($rel != false){?>
    <div class="control-group">
    
	<label for="rel" class="lb-full">Relacionado à</label><?php echo (! $rel)? 'Não existem.<br />' : $rel;?>
	
	</div><!-- .control-group -->
	<?php }?>
	
	
	

</div><!-- .panel-left -->


<div class="panel-right clearfix">


	<div class="control-group box">
    
	<label for="status" class="lb-full">Status</label>
	<div class="form-opcoes group-buttons">
		<?php echo inputs_status($row['status']);?>
     </div>
	
	</div><!-- .control-group -->
	
   
	
	<div class="control-group box">
	
	<label for="semana" class="lb-full">Dias da semana</label>
	<div class="form-opcoes group-buttons">
	<?php echo $row['cbSemana'];?>
	</div>
	
	</div><!-- .control-group -->
	
	
	 <div class="control-group box">
    
	<label for="hora1" class="lb-full">Hora início</label>
    <input name="hora1" id="hora1" type="text" class="input-curto" value="<?php echo set_value('hora1', $row['hora1']);?>" />
	<label for="hora2" class="lb-full">Hora término</label>
	<input name="hora2" id="hora2" type="text" class="input-curto" value="<?php echo set_value('hora2', $row['hora2']);?>" />
	
    </div><!-- .control-group -->
	
	
 


</div><!-- .panel-right -->

        
              