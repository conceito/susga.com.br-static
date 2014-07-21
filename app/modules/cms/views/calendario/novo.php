<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>


<div class="panel-left clearfix">
	
    
    <div class="ai-page">
    	
        <div class="ai">
        <label for="grupos" class="lb-full"><?php echo $modulo['id']==6?'Arquitetura':'Grupo';?></label>
		<?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
        </div>
        
        <div class="page">
        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Título</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />
        </div>
        
    </div><!-- .ai-page -->
	
	<input name="nick" id="nick" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo set_value('nick');?>" />
	
	<br />
    
	<div class="control-group box">
    
	<label for="status" class="lb-full">Status</label>
	<div class="form-opcoes group-buttons">
		<?php echo inputs_status(2);?>
     </div>
	
	</div><!-- .control-group -->
	
    <div class="subpanel-50-50">
    
	<div class="control-group box">
    
	<label for="from" class="lb-full">Data início</label>
    <input name="dt1" id="from" type="text" class="input-curto" value="<?php echo set_value('dt1');?>" />
    
	<label for="to" class="lb-full">Data término</label>
	<input name="dt2" id="to" type="text" class="input-curto" value="<?php echo set_value('dt2');?>" />
    
    </div><!-- .control-group -->
    
    </div><!-- .subpanel-50-50 -->
    
    <div class="subpanel-50-50">
    
    <div class="control-group box">
	
	<label for="hora1" class="lb-full">Hora início</label>
    <input name="hora1" id="hora1" type="text" class="input-curto" value="<?php echo set_value('hora1');?>" />
	<label for="hora2" class="lb-full">Hora término</label>
	 <input name="hora2" id="hora2" type="text" class="input-curto" value="<?php echo set_value('hora2');?>" />
	
    </div><!-- .control-group -->
    
    </div><!-- .subpanel-50-50 -->
	
	
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	
	
	<div class="alert alert-info">
<p><strong>Entre com as informações básicas do conteúdo e... </strong></p>
<br />

<a href="<?php echo cms_url($botoes['continuar']);?>" class="btn btn-info btt-salva"><i class="icon-arrow-right icon-white "></i> Continue editando</a>
</div>
	
	
</div><!-- .panel-right -->


   