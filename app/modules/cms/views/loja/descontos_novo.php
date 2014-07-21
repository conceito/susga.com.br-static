<?php
/*************************************
*	Template: form nova promoção
*	Controller: cms/loja/descontosNovo
*/
?>
<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<?php echo validation_errors(); ?>


<div class="panel-left clearfix">
	
    
    <div class="ai-page">
    	
        <div class="ai">
        <label for="grupos" class="lb-full two-col">Tipo de desconto</label>
		<select name="tipo" id="tipo" class="input-combo ">
        	<option value="cupom">Cupom</option>
            <option value="desconto">Desconto</option>
        </select>
        </div>
        
        <div class="page">
        <label for="titulo" class="lb-full two-col"><b class="obr">[!]</b> Nome para identificar o desconto</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />
        </div>
        
    </div><!-- .ai-page -->
	
	
	
	<br />
    
	<div class="control-group box" style="clear:both">
    
	<label for="status" class="lb-full two-col">Status</label>
	<div class="form-opcoes group-buttons">
		<?php echo inputs_status(2);?>
     </div>
	
	</div><!-- .control-group -->
	
   
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	
	
	<div class="alert alert-info">
<p><strong>Entre com as informações básicas e... </strong></p>
<br />

<a href="<?php echo cms_url($botoes['continuar']);?>" class="btn btn-info btt-salva"><i class="icon-arrow-right icon-white "></i> Continue editando</a>
</div>
	
	
</div><!-- .panel-right -->

