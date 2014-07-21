<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

	<div class="ai-page clearfix">
   
        <div class="ai">
        <label for="grupos" class="lb-full">Passos</label>
		<?php echo (! $comboSteps)? 'Não existem.<br />' : $comboSteps;?>
        </div>

        <div class="page">
        <label for="grupos" class="lb-full">Grupos</label>
        <?php echo (! $comboGroups)? 'Não existem.<br />' : $comboGroups;?>
    	</div>
        
    </div><!-- .ai-page -->
	
    <br />
    
    <label for="titulo" class="lb-full"><b class="obr">[!]</b> Enunciado da pergunta</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', $row['titulo']);?>" />
    
    <br />

    <div class="control-group box">
    
        <label for="ordem" class="lb-full">Ordem</label>
        <input name="ordem" id="ordem" type="text" class="input-curto" value="<?php echo set_value('ordem', $row['ordem']);?>" />
        
           
    </div><!-- .control-group -->

    <br />

    <label for="" class="lb-full"><b class="obr">[!]</b> Tipo de questão</label>
    <?php echo $row['tags']?>

    <br>
    <br class="clearfix" style="clear:both;" />
    
    <div class="multi-options">
        
        <?php 
        /** ========================================================================
         *     Se houver opções de resposta
         * ------------------------------------------------------------------------
         */
        if(isset($row['answer_options']) && $row['answer_options']):
            foreach ($row['answer_options'] as $k => $v):
        ?>
        <div class="multi-option">
            <input name="old_multi" type="text" class="input-longo" value="<?php echo $v ?>" readonly="readonly" />
            <!-- <a href="#" class="remove-multi"><i class="icon-trash"></i></a> -->
        </div>
        <?php 
            endforeach;
        ?>
        
        <div class="multi-option">
            <input name="multi_1" type="text" class="input-longo" value="" />
            <a href="#" class="add-multi"><i class="icon-plus"></i></a>
        </div>

        <?php 

        endif;
        ?>

    </div><!-- multi-options -->

    <br class="clearfix" style="clear:both;" />
    <br />
	<br />
    
  	<div class="control-group box">
	
	<label for="resumo" class="lb-full">Texto de apoio</label>
    <textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo', $row['resumo']);?></textarea>
	       
	</div><!-- .control-group -->
	
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">

    <?php   if($survey == 0):   ?>
    <div class="control-group box">
    
        <label for="status" class="lb-full">Remover questão</label>
       <a href="<?php echo cms_url('cms/survey/deleteQuery/'. $row['id'])?>" class="btn btn-danger">Remover questão</a>
        <div class="help-block">Ao remover todas as estatísticas desta questão serão perdidas.</div>
    </div><!-- .control-group --> 
    <?php endif; ?>
	

</div><!-- .panel-right -->

        
      
