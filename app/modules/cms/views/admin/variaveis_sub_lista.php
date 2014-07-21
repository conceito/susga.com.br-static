<?php
if(!$sub): echo '<strong>Não existem itens disponíveis.</strong>';

else:

	// percorre cada submenu e preenche o formulário
	$i = 1;
	foreach($sub as $s):
		$id = $s['id'];
		$label = $s['titulo'];
		$valor = trim($s['valor']);
		$status = $s['status'];
		$ordem = $s['ordem'];
		//
		if(strlen($valor) == 0)$valor = $id;

?>
<fieldset><legend><?php echo $label;?></legend>

<span style="float:right;"> : ordem</span><input name="ordem_<?php echo $id;?>" id="ordem_<?php echo $id;?>" type="text" class="input-curto" value="<?php echo set_value('ordem_'.$id, $ordem);?>" style="width:30px; float:right;" />

<label for="titulo_<?php echo $id;?>" class="lb-inline">Opção #<?php echo $i;?></label><input name="titulo_<?php echo $id;?>" id="titulo_<?php echo $id;?>" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo_'.$id, $label);?>" />

=

<input name="valor_<?php echo $id;?>" id="valor_<?php echo $id;?>" type="text" class="input-curto" value="<?php echo set_value('valor_'.$id, $valor);?>" />

<br />


<label for="status_<?php echo $id;?>" class="lb-inline">Status</label><div class="form-opcoes"><?php echo form_radio('status_'.$id, 1, ($status==1));?> ativo | <?php echo form_radio('status_'.$id, 0, ($status==0));?> inativo | <?php echo form_radio('status_'.$id, 2, ($status==2));?> editando</div>

<br />
</fieldset>
<?php $i++;
	endforeach;
endif;
?>