<?php echo validation_errors(); ?>

<div class="panel-left clearfix">
	
	
	<label for="logradouro" class="lb-full">Logradouro</label><input name="logradouro" id="logradouro" type="text" class="input-longo" value="<?php echo set_value('logradouro', form_prep($row['logradouro']));?>" />

	<br />
	
	
	<label for="cep" class="lb-full">CEP</label><input name="cep" id="cep" type="text" class="input-curto" value="<?php echo set_value('cep', $row['cep']);?>" />
	
	<br />
	
	<label for="uf" class="lb-full">Estado</label><?php echo $row['combo_uf']?>
	
	<br />
	
	<label for="cidade" class="lb-full">Cidade</label><div id="combo_cidade"><?php echo $row['combo_cidade']?></div>
	
	<br />
	
	<label for="bairro" class="lb-full">Bairro</label><input name="bairro" id="bairro" type="text" class="input-longo" value="<?php echo set_value('bairro', $row['bairro']);?>" />
	
	<br />
	

</div><!-- .panel-left -->


<div class="panel-right clearfix">
	
	
	<label for="num" class="lb-full">Número</label><input name="num" id="num" type="text" class="input-mini" value="<?php echo set_value('num', $row['num']);?>" />

	<br />
	
	<label for="compl" class="lb-full">Complemento</label><input name="compl" id="compl" type="text" class="input-mini" value="<?php echo set_value('compl', $row['compl']);?>" />
	
	<br />
	

</div><!-- .panel-right -->






              
         
   

