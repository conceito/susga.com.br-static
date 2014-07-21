<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

	<div class="clearfix">
   
        
        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Nome do passo</label>
        <input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />
        
    </div><!-- .ai-page -->
	
	<br />
    
  	<div class="control-group box">
	
		<label for="ordem" class="lb-full">Ordem</label>
		<input name="ordem" id="ordem" type="text" class="input-curto" value="<?php echo set_value('ordem', '0');?>" />
	    
	       
	</div><!-- .control-group -->

	<br />
    
  	<div class="control-group box">
	
		<label for="resumo" class="lb-full">Informações complementares</label>
	    <textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo');?></textarea>
	       
	</div><!-- .control-group -->
    
  
	
	
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">

    <div class="alert alert-info">
    <p><strong>Entre com as informações básicas do conteúdo e... </strong></p>
    <br />

    <a href="<?php echo cms_url('cms/'.$c.'/'.$botoes['continuar']);?>" class="btn btn-info btt-salva"><i class="icon-arrow-right icon-white "></i> Continue editando</a>
    </div>
	

</div><!-- .panel-right -->

        
      
