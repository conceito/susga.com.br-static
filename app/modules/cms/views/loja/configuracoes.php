<?php
/******************************************
*  Template: configurações gerais 
*  Controller: cms/loja/configuracoes
*/

$baseurl = base_url();
?>
<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

	
    <div class="control-group box">
	
	<label for="loja52_min_pf" class="lb-full two-col">Quantidade mínima de itens para PF</label>    
    <input name="loja52_min_pf" id="loja52_min_pf" type="text" class="input-curto" value="<?php echo set_value('loja52_min_pf', $con['loja52_min_pf']);?>" />
	<div class="help-block">Quantidade mínima de produtos para permitir fechar pedido.</div>
           
	</div><!-- .control-group -->
    
    <div class="control-group box">
	
	<label for="loja52_min_pj" class="lb-full two-col">Quantidade mínima de itens para PJ</label>
    <input name="loja52_min_pj" id="loja52_min_pj" type="text" class="input-curto" value="<?php echo set_value('loja52_min_pj', $con['loja52_min_pj']);?>" />
    <div class="help-block">Quantidade mínima de produtos para permitir fechar pedido.</div>
	       
	</div><!-- .control-group -->
    
    <div class="control-group box">
	
	<label for="loja52_dados" class="lb-full two-col">Dados da loja</label>
    <textarea name="loja52_dados" class="textarea-curto" id="loja52_dados"><?php echo set_value('loja52_dados', $con['loja52_dados']);?></textarea>
    <div class="help-block">Entre com o endereço da loja e dados de contato.</div>
	       
	</div><!-- .control-group -->
    

</div><!-- .panel-left -->


<div class="panel-right clearfix">



</div><!-- .panel-right -->