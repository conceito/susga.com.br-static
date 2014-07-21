<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

	<div class="ai-page clearfix">
   
        <div class="ai">
        <label for="grupos" class="lb-full">Passos</label>
		<?php echo (! $comboSteps)? 'Não existem.<br />' : $comboSteps;?>
        </div>

        <div class="page">
        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Nome do grupo</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', $row['titulo']);?>" />
    	</div>
        
    </div><!-- .ai-page -->

    <br />

    <div class="control-group box">
    
        <label for="ordem" class="lb-full">Ordem</label>
        <input name="ordem" id="ordem" type="text" class="input-curto" value="<?php echo set_value('ordem', $row['ordem']);?>" />
        
           
    </div><!-- .control-group -->
	
	<br />
    
  	<div class="control-group box">
	
	<label for="resumo" class="lb-full">Descrição do grupo</label>
    <textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo', $row['resumo']);?></textarea>
	       
	</div><!-- .control-group -->
	
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">

    <?php   if($survey == 0):   ?>
    <div class="control-group box">
    
        <label for="status" class="lb-full">Remover grupo</label>
       <a href="<?php echo cms_url('cms/survey/deleteGroup/'. $row['id'])?>" class="btn btn-danger">Remover grupo</a>
        <div class="help-block">Antes de remover um grupo mova todas as questões para outro grupo ou passo.</div>
    </div><!-- .control-group --> 
    <?php endif; ?>
	

</div><!-- .panel-right -->

        
      
