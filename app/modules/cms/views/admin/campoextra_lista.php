<script type="text/javascript">
$(document).ready(function(){
	
	$(".maisCampo").live("click", function(){
		
		var parent = $(this).parent();
		var q = parent.find("input[name^=extraOp]").length;
		var name = $(this).attr('rel');
		
		
		var html = '<label for="extraOp_'+name+'_'+q+'">#'+(q+1)+'</label><input type="text" value="" class="input-longo" id="extraOp_'+name+'_'+q+'" name="extraOp_'+name+'_'+q+'"><input type="text" value="" class="input-curto" id="extraClass_'+name+'_'+q+'" name="extraClass_'+name+'_'+q+'"><br>';
		
		$(this).before(html);
		
		return false;
		
	});
	
	
	$(".maisCampoNovo").live("click", function(){
		
		var parent = $(this).parent();
		var q = parent.find("input[name^=extraOp]").length;
		var name = 'novo';
		
		
		var html = '<label for="extraOp_'+name+'_'+q+'">#'+(q+1)+'</label><input type="text" value="" class="input-longo" id="extraOp_'+name+'_'+q+'" name="extraOp_'+name+'_'+q+'"><br>';
		
		$(this).before(html);
		
		return false;
		
	});
	
	
	$(".extraNovoOpcoes").hide();
	
	$("input[name=extraType_novo]").bind("click", function(){
		
		var v = $(this).val();
		
		if(v == 'input' || v == 'text' || v == 'arq' || v == 'img'){
			$(".extraNovoOpcoes").slideUp();
		} else {
			$(".extraNovoOpcoes").slideDown();
		}
		
	});
	
});
</script>
<?php
if(!$camposExtra): echo '<strong>Não existem campos extra.</strong>';

else:
	foreach($camposExtra as $extra):
		
		$campo = $extra['name'];
		$name = $extra['id'];
		$type = $extra['type'];
		$dados = $extra['data'];
		
		if($extra['type'] == 'input'){
			$class = 'input-longo';
			
		} else if($extra['type'] == 'text'){
			$class = 'textarea-curto';
		}
?>
<fieldset><legend><?php echo $campo;?> (<?php echo strtoupper($type);?>)</legend>
	
    <label for="extraCampo_<?php echo $name;?>" class="lb-inline">Nome do campo</label><input name="extraCampo_<?php echo $name;?>" id="extraCampo_<?php echo $name;?>" type="text" class="input-longo input-titulo" value="<?php echo set_value('extraCampo_'.$name, $campo);?>" />
    
    <br />

    
    <label for="extraId_<?php echo $name;?>" class="lb-inline">Identificador</label><input name="extraId_<?php echo $name;?>" id="extraId_<?php echo $name;?>" type="text" class="input-longo" value="<?php echo set_value('extraId_'.$name, $name);?>" />
    
    <input name="extraType_<?php echo $name;?>" type="hidden" value="<?php echo $type;?>" />
    
    <br />
    <br />
    
   
    <?php
    if(is_array($dados)):
	
	echo '<label for="" class="lb-inline"><strong>Opções / classe:</strong></label>
    
   <br /><br />';
		
		for($x = 0; $x < count($dados); $x++):
			
			$dds = explode('|', $dados[$x]);
			$op = $dds[0];
			$cl = (isset($dds[1])) ? $dds[1] : '';
			
			$lbform = 'extraOp_'.$name.'_'.$x;
			$lbClass = 'extraClass_'.$name.'_'.$x;
	?>
    
    <label for="<?php echo $lbform;?>" class="lb-inline">#<?php echo $x + 1;?></label><input name="<?php echo $lbform;?>" id="<?php echo $lbform;?>" type="text" class="input-longo" value="<?php echo set_value('$lbform', $op);?>" />
    <input name="<?php echo $lbClass;?>" id="<?php echo $lbClass;?>" type="text" class="input-curto" value="<?php echo set_value('$lbClass', $cl);?>" />
    
    <br />
    
    	
   
    
    <?php
		endfor;
		
		echo '<a href="#" class="maisCampo" rel="'.$name.'">+ adicionar opção</a>';
		
    endif;
	?>

	
    
    

</fieldset>
<?php 
	endforeach;
endif;
?>



<h3>Novo campo extra:</h3>

<label for="extraCampo_novo" class="lb-inline">Nome do campo</label><input name="extraCampo_novo" id="extraCampo_novo" type="text" class="input-longo input-titulo" value="" />
    
    <br />

    
    <label for="extraId_novo" class="lb-inline">Identificador</label><input name="extraId_novo" id="extraId_novo" type="text" class="input-longo" value="" />
    
<br />
<br />

    
    <label for="tipo" class="lb-inline">Tipo</label><div class="form-opcoes">
	<?php echo form_radio('extraType_novo', 'input', true);?> input | 
    <?php echo form_radio('extraType_novo', 'text');?> text | 
    <?php echo form_radio('extraType_novo', 'radio');?> radio | 
    <?php echo form_radio('extraType_novo', 'check');?> check | 
    <?php echo form_radio('extraType_novo', 'combo');?> combo | 
    <?php echo form_radio('extraType_novo', 'multi');?> multi |
    <?php echo form_radio('extraType_novo', 'arq');?> arq |
    <?php echo form_radio('extraType_novo', 'img');?> img 
    
    </div>

<br />

<div class="extraNovoOpcoes">
<label for="" class="lb-inline"><strong>Opções:</strong></label>

<br />
<br />



<label for="extraOp_novo_0" class="lb-inline">#1</label><input name="extraOp_novo_0" id="extraOp_novo_0" type="text" class="input-longo" value="" />

<br />

<label for="extraOp_novo_1" class="lb-inline">#2</label><input name="extraOp_novo_1" id="extraOp_novo_1" type="text" class="input-longo" value="" />

<br />


<a href="#" class="maisCampoNovo" rel="'.$name.'">+ adicionar opção</a>

</div>