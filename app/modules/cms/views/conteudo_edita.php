<?php echo validation_errors(); ?>

<div class="panel-left clearfix">
	
    
    <?php ////// REVISÕES BAR ///////	
	echo $this->cms_libs->output_revision_options_bar($row);	
	?>
    
    <div class="ai-page">
    	
        <div class="ai">
        <label for="grupos" class="lb-full"><?php echo $modulo['id']==6?'Arquitetura':'Grupo';?></label>
		<?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
        </div>
        
        <div class="page">
        <label for="titulo" class="lb-full">Título</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', form_prep($row['titulo']));?>" />
        </div>
        
    </div><!-- .ai-page -->
    
        
<input name="nick_edita" id="nick_edita" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo set_value('nick', $row['nick']);?>" readonly="readonly"/>
	
	
	<br />
    
    <?php // exibe bloco de alerta com link para o módulo que gera o conteúdo desta página
	echo module_content($row);
    ?>
    
	<div class="control-group">
    
	<label class="lb-full" for="txt">Conteúdo principal <?php if($multicontent):?>| <a href="#" onclick="javascript:$('#tabs').tabs('select',1); return false;">Mais conteúdos</a><?php endif;?></label>
	<div class="content-ctrl">
            
            <a href="<?php echo cms_url($linkAddImage);?>" class="ico-img nyroModal" target="_blank">Subir imagem</a>
            <!--<a href="<?php echo cms_url($linkAddArq);?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">arquivo</b></span></a>-->
	</div>
	<textarea name="txt" class="textarea-longo" id="txt" style="width:100%"><?php echo set_value('txt', $row['txt']);?></textarea>
    
    </div><!-- .control-group -->
    
    <?php if($rel != false){?>
    <div class="control-group">
    
	<label for="rel" class="lb-full">Relacionado à</label>
	<?php echo (! $rel)? 'Não existem.<br />' : $rel;?>
	
	</div><!-- .control-group -->
	<?php }?>
	
	<?php 
	if($swfUplForm){
		echo '<div class="control-group box"><div class="attached-box">';
	  	echo $swfUplForm;	
		echo '</div></div>';
	}
	?>
    
    <?php ////// REVISÕES ///////	
	echo $this->cms_libs->output_revisions($row);	
	?>
    
		
</div><!-- .panel-left -->


<div class="panel-right clearfix">
    
    <?php if(isset($this->widgets['img-choose'])): ?>
	<div class="control-group box">
    <?php echo $this->widgets['img-choose']; ?>
    </div><!-- .control-group --> 
    <?php endif; ?>   



    <div class="control-group box">
    
        <label for="status" class="lb-full">Status</label>
        <div class="form-opcoes group-buttons">        
            <?php echo inputs_status($row['status']);?>
        </div>
    <div class="help-block">Última atualização em <?php echo datetime_br($row['atualizado']);?></div>
    </div><!-- .control-group -->    
   

	<div class="control-group box">
    
    <label for="dt1" class="lb-full">Data</label>
    <input name="dt1" id="dt1" type="text" class="input-curto" value="<?php echo set_value('dt1', $row['dt1']);?>" />
	
	</div><!-- .control-group -->

    <?php if($this->config->item('post_priority')):?>
	<div class="control-group box">
    
    <label for="prioridade" class="lb-full">Prioridade</label>
    <?php echo  priority_combobox($row['prioridade']); ?>
    <!-- <input name="dt1" id="dt1" type="text" class="input-curto" value="<?php echo set_value('dt1', $row['dt1']);?>" /> -->
	
	</div><!-- .control-group -->
    <?php endif;?>
	
	
	<?php viewGetTags();?>
    	
	
	<div class="control-group box">
	
	<label for="resumo" class="lb-full">Resumo</label>
    <textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo', $row['resumo']);?></textarea>
	       
	</div><!-- .control-group -->
    
    <div class="control-group box">
	
	<label for="tags" class="lb-full">Palavras-chave</label>
    <textarea name="tags" class="textarea-tags" id="tags"><?php echo set_value('tags', $row['tags']);?></textarea>
	
	
	 </div><!-- .control-group -->

	<?php
    if(in_array($row['modulo_id'], $this->config->item('modulo_paginas'))):
	?>
    <div class="control-group box">
    
	<label for="" class="lb-full">Exibir no menu</label>
    <div class="form-opcoes group-buttons">
	<?php echo inputs_show($row['show']);?>
    </div>
    
    </div><!-- .control-group -->
    <?php endif;?>
	



</div><!-- .panel-right -->       
              
         


