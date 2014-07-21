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
	
	<label for="header_txt" class="lb-full two-col">texto cabeçalho</label> 
	<textarea name="header_txt" class="textarea-curto" id="header_txt"><?php echo set_value('header_txt', $con['header_txt']);?></textarea> 
	<div class="help-block">Não usar caracteres especiais e espaço.</div>
           
	</div><!-- .control-group -->

	<div class="control-group box">
	
	<label for="footer_txt" class="lb-full two-col">texto rodapé</label>   
	<textarea name="footer_txt" class="textarea-curto" id="footer_txt"><?php echo set_value('footer_txt', $con['footer_txt']);?></textarea> 
	<div class="help-block">Não usar caracteres especiais e espaço.</div>
           
	</div><!-- .control-group -->

    

</div><!-- .panel-left -->


<div class="panel-right clearfix">



</div><!-- .panel-right -->