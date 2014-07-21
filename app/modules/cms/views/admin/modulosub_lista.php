<?php
if(!$sub): echo '<strong>Não existem itens disponíveis.</strong>';

else:

	// percorre cada submenu e preenche o formulário
	foreach($sub as $s):
		$id = $s['id'];
		$label = $s['label'];
		$tipo = $s['tipo'];
		$uri = $s['uri'];
		$tb = $s['tabela'];
		$status = $s['status'];
		$acao = $s['acao'];
		$ordem = $s['ordem'];

?>
<fieldset><legend><?php echo $label;?></legend>

<span style="float:right;"> : ordem</span><input name="ordem_<?php echo $id;?>" id="ordem_<?php echo $id;?>" type="text" class="input-curto" value="<?php echo set_value('ordem_'.$id, $ordem);?>" style="width:30px; float:right;" />

<br>
<label for="label_<?php echo $id;?>" class="lb-inline">Label</label><input name="label_<?php echo $id;?>" id="label_<?php echo $id;?>" type="text" class="input-longo input-titulo" value="<?php echo set_value('label_'.$id, $label);?>" />

<br />

<label for="uri_<?php echo $id;?>" class="lb-inline">URI</label><input name="uri_<?php echo $id;?>" id="uri_<?php echo $id;?>" type="text" class="input-longo" value="<?php echo set_value('uri_'.$id, $uri);?>" />

<br />

<label for="acao_<?php echo $id;?>" class="lb-inline">Tipo de ação</label><div class="form-opcoes">
<?php echo form_radio('acao_'.$id, 'a', ($acao=='a'));?> apagar | 
<?php echo form_radio('acao_'.$id, 'c', ($acao=='c'));?> criar/editar | 
<?php echo form_radio('acao_'.$id, 'l', ($acao=='l'));?> listar | 
<?php echo form_radio('acao_'.$id, 'r', ($acao=='r'));?> gerar relatórios</div>

<br />

<label for="tipo_<?php echo $id;?>" class="lb-inline">Quem pode ver</label><div class="form-opcoes"><?php echo form_radio('tipo_'.$id, 0, ($tipo==0));?> God | <?php echo form_radio('tipo_'.$id, 1, ($tipo==1));?> Admins</div>

<br />

<label for="tabela_<?php echo $id;?>" class="lb-inline">Tabela</label><input name="tabela_<?php echo $id;?>" id="tabela_<?php echo $id;?>" type="text" class="input-curto" value="<?php echo set_value('tabela_'.$id, $tb);?>" />

<br />

<label for="status_<?php echo $id;?>" class="lb-inline">Status</label><div class="form-opcoes"><?php echo form_radio('status_'.$id, 1, ($status==1));?> ativo | <?php echo form_radio('status_'.$id, 0, ($status==0));?> inativo | <?php echo form_radio('status_'.$id, 2, ($status==2));?> editando</div>

<br />
</fieldset>
<?php 
	endforeach;
endif;
?>