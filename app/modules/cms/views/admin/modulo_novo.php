
<?php echo validation_errors(); ?>

<label for="label" class="lb-inline">Label</label><input name="label" id="label" type="text" class="input-longo input-titulo" value="<?php echo set_value('label');?>" />

<br />

<label for="uri" class="lb-inline">URI</label><input name="uri" id="uri" type="text" class="input-longo" value="<?php echo set_value('uri');?>" />

<br />

<label for="front_uri" class="lb-inline">Front-end URI</label><input name="front_uri" id="front_uri" type="text" class="input-longo" value="<?php echo set_value('front_uri', 'controller/[grupo/[nick]/[id]');?>" />

<br />

<label for="tipo" class="lb-inline">Quem pode ver</label><div class="form-opcoes"><?php echo form_radio('tipo', 0);?> God | <?php echo form_radio('tipo', 1, true);?> Admins</div>

<br />

<label for="tabela" class="lb-inline">Tabela</label><input name="tabela" id="tabela" type="text" class="input-curto" value="<?php echo set_value('tabela');?>" />

<br />

<label for="ordenavel" class="lb-inline">Ordenável</label><div class="form-opcoes"><?php echo form_radio('ordenavel', 1);?> sim | <?php echo form_radio('ordenavel', 0, true);?> não </div>

<br />

<label for="comments" class="lb-inline">Aceita comentários</label><div class="form-opcoes"><?php echo form_radio('comments', 1);?> sim | <?php echo form_radio('comments', 0, true);?> não </div>

<br />

<label for="destaques" class="lb-inline">Tem destaques</label><div class="form-opcoes"><?php echo form_radio('destaques', 1);?> sim | <?php echo form_radio('destaques', 0, true);?> não </div>

<br />

<label for="inscricao" class="lb-inline">Aceita inscrição</label><div class="form-opcoes"><?php echo form_radio('inscricao', 1);?> sim | <?php echo form_radio('inscricao', 0, true);?> não </div>

<br />

<label for="modulos" class="lb-inline">Se relaciona com</label><?php echo $this->admin_model->combo_modulos('', true, array(' -- ninguém -- ' => 0));?> 
<!-- grupo <span class="cb-grupos"></span>-->

<br />

<label for="pastas_0" class="lb-inline">Pasta de Imagens</label><?php echo $this->pastas_model->combo_pastas(0);?>

<br />

<label for="pastas_2" class="lb-inline">Pasta de Arquivos</label><?php echo $this->pastas_model->combo_pastas(2);?>

<br />

<label for="pastaAjuda" class="lb-inline">Pasta de Ajuda</label><?php echo $this->pastas_model->combo_pastas(2, '', 'pastaAjuda', true);?>

<br />

<label for="status" class="lb-inline">Status</label><div class="form-opcoes"><?php echo form_radio('status', 1, true);?> ativo | <?php echo form_radio('status', 0);?> inativo | <?php echo form_radio('status', 2);?> editando</div>

<br />
