<?php
/******************************************
*  Template: criação de produto
*  Controller: cms/loja/novo
*/
?>
<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>


<div class="panel-left clearfix">
	
    
    <div class="ai-page">
    	
        <div class="ai">
        <label for="grupos" class="lb-full">Categoria</label>
		<?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
        </div>
        
        <div class="page">
        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Nome do produto</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />
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
	
   
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	
	
	<div class="alert alert-info">
<p><strong>Entre com as informações básicas do conteúdo e... </strong></p>
<br />

<a href="<?php echo cms_url($botoes['continuar']);?>" class="btn btn-info btt-salva"><i class="icon-arrow-right icon-white "></i> Continue editando</a>
</div>
	
	
</div><!-- .panel-right -->


   