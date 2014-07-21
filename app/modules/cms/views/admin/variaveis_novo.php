<?php echo validation_errors(); ?>


<label for="titulo" class="lb-inline">Título</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />

<br />

<label for="descricao" class="lb-inline">Descrição</label><textarea name="descricao" class="textarea-curto" id="descricao"><?php echo set_value('descricao');?></textarea>

  
<br />

<label for="tipo" class="lb-inline">Quem pode ver</label><div class="form-opcoes">
<?php echo form_radio('tipo', 0, true);?> God | 
<?php echo form_radio('tipo', 1);?> Super Admins | 
<?php echo form_radio('tipo', 2);?> Admins Segmentados</div>

<br />

<label for="status" class="lb-inline">Status</label><div class="form-opcoes"><?php echo form_radio('status', 1, true);?> ativo | <?php echo form_radio('status', 0);?> inativo | <?php echo form_radio('status', 2);?> editando</div>

<br />

