<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>


<div class="panel-left clearfix">
	
	
	<label for="nome" class="lb-full"><b class="obr">[!]</b> Nome</label><input name="nome" id="nome" type="text" class="input-longo input-titulo" value="<?php echo set_value('nome');?>" />
	
	<br />
	
	
	<label for="email" class="lb-full"><b class="obr">[!]</b> E-mail</label><input name="email" id="email" type="text" class="input-longo" value="<?php echo set_value('email');?>" />
	
	<br />
    
    <label for="grupos" class="lb-full">Grupo</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
	
	
	<br />
    
    
	
	<label for="obs" class="lb-full">Observações</label><textarea name="obs" class="textarea-curto" id="obs"><?php echo set_value('obs');?></textarea>
    
    
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">
		
	
	<div class="alert alert-info">
    <p><strong>Entre com as informações básicas da pessoa e... </strong></p>
    <a href="<?php echo cms_url('cms/'.$c.'/'.$botoes['continuar']);?>" class="btn btn-info btt-salva"><i class="icon-arrow-right icon-white "></i> Continue editando</a>
    </div>

	
</div><!-- .panel-right -->










       


