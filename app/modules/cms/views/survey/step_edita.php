<?php 
/** ========================================================================
 * 	Edição de step
 * ------------------------------------------------------------------------
 */
echo validation_errors(); 
?>

<div class="panel-left clearfix">	
   
    <div class="ai-page">
        
        <div class="-page">
        	<label for="titulo" class="lb-full">Título</label>
        	<input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', form_prep($row['titulo']));?>" />
        </div>
        
    </div><!-- .ai-page -->
	
	<br />

    <div class="control-group box">
	
		<label for="ordem" class="lb-full">Ordem</label>
		<input name="ordem" id="ordem" type="text" class="input-curto" value="<?php echo set_value('ordem', $row['ordem']);?>" />
	    
	       
	</div><!-- .control-group -->

    
	<br />    
  
	<div class="control-group box">
	
		<label for="resumo" class="lb-full">Informações complementares</label>
	    <textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo', $row['resumo']);?></textarea>
	       
	</div><!-- .control-group -->
    

  
		
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	<?php 	if($survey == 0):	?>
	<div class="control-group box">
    
        <label for="status" class="lb-full">Remover passo</label>
       <a href="<?php echo cms_url('cms/survey/deleteStep/'. $row['id'])?>" class="btn btn-danger">Remover passo</a>
    	<div class="help-block">Antes de remover um passo mova todas as questões para outro passo.</div>
    </div><!-- .control-group -->  
   <?php endif; ?>
	
 



</div><!-- .panel-right -->       
              
         


