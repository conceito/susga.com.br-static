<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>


<div class="panel-left clearfix">
	
    
    <div class="ai-page clearfix">

        <div class="page">
        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Identifique as opções</label>
        <input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', $row['titulo']);?>" />
        </div>
        
    </div><!-- .ai-page -->

    <div class="control-group box">
    
    <label for="resumo" class="lb-full">Resumo</label>
    <textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo', $row['resumo']);?></textarea>
           
    </div><!-- .control-group -->
	
	
	<br class="clearfix" />
    
	<div class="control-group box ">
    
    	<?php if($related != false){?>
        <div class="control-group">
        
        <label for="rel" class="lb-full">Relacionado à</label>
        <?php echo (! $related)? 'Não existem.<br />' : $related;?>
        
        </div><!-- .control-group -->

        <script type="text/javascript">
        $(function(){
            // fix dropsize inside a tab
            setTimeout(function(){

                var rel_chzn = $('#rel_chzn');
                rel_chzn.width( '100%' );
                var chzn_drop = $('#chzn-drop');
                chzn_drop.width( rel_chzn.width() );

            }, 500);
        });
        </script>
        <?php }?>
	
	</div><!-- .control-group -->
	
   
	
	
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	
	

	
	
</div><!-- .panel-right -->


   