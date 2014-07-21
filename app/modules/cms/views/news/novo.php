<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>
<label for="status" class="lb-full">Status</label>
<div class="form-opcoes">
<?php echo form_radio('status', 1, true);?> ativo | 
<?php echo form_radio('status', 0);?> inativo | 
<?php echo form_radio('status', 2);?> editando</div>

<br />



<label for="grupos" class="lb-full"><b class="obr">[!]</b> Grupo</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>


<br />


<label for="titulo" class="lb-full"><b class="obr">[!]</b> Assunto</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />

<br />

<input name="nick" id="nick" type="hidden" class="input-apelido" value="<?php echo set_value('nick');?>" />


<label for="status" class="lb-full">Usará editor gráfico?</label>
<div class="form-opcoes">
<?php echo form_radio('destaque', 1, true);?> Sim | 
<?php echo form_radio('destaque', 0);?> Não
<?php echo i('O &#8220;editor gráfico&#8221; permite montar a newsletter no próprio sistema, mas não permite tanto controle e precisão se for feito por um profissional.');?>
</div>

<br />



<?php echo validation_errors(); ?>