<!-- view do novo menu -->

<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>
<label for="status">Status</label>
<div class="form-opcoes">
<?php echo form_radio('status', 1, true);?> ativo | 
<?php echo form_radio('status', 0);?> inativo | 
<?php echo form_radio('status', 2);?> editando</div>

<br />

<label for="titulo"><b class="obr">[!]</b> Nome do menu</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />

<br />

<label for="nick">Apelido</label><input name="nick" id="nick" type="text" class="input-apelido" value="<?php echo set_value('nick');?>" />
<?php echo i('Identificação deste registro. <br />Deve ser único e NÃO pode conter espaços ou caracteres especiais.');?>

<br />

<label for="resumo">Posição</label>
<input name="resumo" id="resumo" type="text" class="input-longo" value="<?php echo set_value('resumo');?>" />

       
<br />
<br />

<?php echo validation_errors(); ?>