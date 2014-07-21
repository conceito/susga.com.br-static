<?php echo validation_errors(); ?>

<label for="tipo" class="lb-inline">Tipo de admin</label>
<div class="form-opcoes">
<?php if($this->phpsess->get('admin_tipo', 'cms')==0)echo form_radio('tipo', 0) . ' God | ';?>
<?php if($this->phpsess->get('admin_tipo', 'cms')<2)echo form_radio('tipo', 1, true) . ' Superadmin | ';?>  
<?php echo form_radio('tipo', 2);?> Segmentado 
<?php echo i('Super admin: Poderá acessar qualquer módulo e poderá adicionar novos administradores.<br />Admin segmentado: Poderá ter sua atuação segmentada de acordo com as permições selecionadas.<br />Não poderá adicionar novos administradores.', -1);?></div>

<br />

<div style="display:none;" class="div-modulos"><label for="modulos" class="lb-inline">Módulos</label>
<?php echo $comboModulos;?><?php echo i('Escolha os Módulos que o administrador terá acesso.');?></div>

<br />


<label for="acoes" class="lb-inline">Ações permitidas</label>
<div class="form-opcoes">
<?php echo form_checkbox('acoes_a', 'a', true);?> Apagar | 
<?php echo form_checkbox('acoes_c', 'c', true);?> Criar/Editar conteúdo | 
<?php echo form_checkbox('acoes_r', 'r', true);?> Gerar relatórios <?php echo i('O que o administrador poderá fazer no sistema.', -1);?></div>

<br />

<label for="status" class="lb-inline">Status</label>
<div class="form-opcoes"><?php echo form_radio('status', 1, true);?> ativo | 
<?php echo form_radio('status', 0);?> inativo <?php echo i('Usuários INativos não poderão se logar no CMS.', -1);?></div>

<br />


