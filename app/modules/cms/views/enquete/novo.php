<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>
<label for="status" class="lb-full">Status</label>
<div class="form-opcoes">
<?php echo form_radio('status', 1, true);?> ativo | 
<?php echo form_radio('status', 0);?> inativo | 
<?php echo form_radio('status', 2);?> editando</div>

<br />

<?php if($rel != false){?>
<label for="rel" class="lb-full">Relacionado à</label><?php echo (! $rel)? 'Não existem.<br />' : $rel;?>

<br />
<?php }?>


<label for="titulo" class="lb-full"><b class="obr">[!]</b> Pergunta</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />

<br />

<?php
for($x = 1; $x <10 ; $x++):
	if($x < 3)$obr = '<b class="obr">[!]</b> ';
	else $obr = '';
?>

<label for="opc_<?php echo $x;?>" class="lb-full"><?php echo $obr;?>Opção #<?php echo $x;?></label><input name="opc_<?php echo $x;?>" id="opc_<?php echo $x;?>" type="text" class="input-curto" value="<?php echo set_value('opc_'.$x);?>" />

<br />

<?php
endfor;
?>

<label></label>* Após salvar a enquete não será possível adicionar novas opções.
        
              
         
<br />

<?php echo validation_errors(); ?>