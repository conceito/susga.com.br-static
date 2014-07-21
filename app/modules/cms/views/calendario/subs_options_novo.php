<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>


<div class="panel-left clearfix">
	
    
    <div class="ai-page clearfix">
    	
        <!-- <div class="ai">
        <label for="grupos" class="lb-full"><?php echo $modulo['id']==6?'Arquitetura':'Grupo';?></label>
		<?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
        </div> -->
        
        <div class="page">
        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Identifique as opções</label>
        <input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />
        </div>
        
    </div><!-- .ai-page -->
	
	
	<br class="clearfix" />
    
	<div class="control-group box ">
    
    	<?php if($related != false){?>
        <div class="control-group">
        
        <label for="rel" class="lb-full">Relacionado à</label>
        <?php echo (! $related)? 'Não existem.<br />' : $related;?>
        
        </div><!-- .control-group -->
        <?php }?>
	
	</div><!-- .control-group -->
	
   
	
	
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	
	
	<div class="alert alert-info">
<p><strong>Entre com as informações básicas do conteúdo e... </strong></p>
<br />

<a href="<?php echo cms_url($botoes['continuar']);?>" class="btn btn-info btt-salva"><i class="icon-arrow-right icon-white "></i> Continue editando</a>
</div>
	
	
</div><!-- .panel-right -->


   