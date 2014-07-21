<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

	<div class="ai-page">
    	
        <div class="ai">
        <label for="grupos" class="lb-full"><?php echo $modulo['id']==6?'Arquitetura':'Grupo';?></label>
		<?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
        </div>
        
        <div class="page">
        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Título</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', 'Cópia de '.$row['titulo']);?>" />
        </div>
        
    </div><!-- .ai-page -->
	

	
	
	<!--<label for="nick">Endereço amigável</label>-->
	<input name="nick" id="nick" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo set_value('nick');?>" />
	
	<br />
    
    <div class="subpanel-50-50">
	<label for="dt1" class="lb-full">Data</label><input name="dt1" id="dt1" type="text" class="input-curto" value="<?php echo set_value('dt1', date("d/m/Y"));?>" />
	</div><!-- .subpanel-50-50 -->
	<div class="subpanel-50-50">
	<label for="status" class="lb-full">Status</label>
	<div class="form-opcoes group-buttons">
		<?php echo inputs_status(2);?>
     </div>
	
	</div><!-- .subpanel-50-50 -->
	
	<br />
	
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">

    <div class="alert alert-info">
    <p><strong>Altere o título do conteúdo e... </strong></p>
    <a href="<?php echo cms_url('cms/'.$c.'/'.$botoes['continuar']);?>" class="btn btn-info btt-salva"><i class="icon-arrow-right icon-white "></i> Continue editando</a>
    </div>
	

</div><!-- .panel-right -->

        
      
