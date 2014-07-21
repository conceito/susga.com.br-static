<?php echo validation_errors(); ?>

<label for="tipo" class="lb-inline">Tipo de admin</label>
<div class="form-opcoes">
<?php if($this->phpsess->get('admin_tipo', 'cms')==0)echo form_radio('tipo', 0, ($row['tipo']==0)) . ' God |';?>  
<?php if($this->phpsess->get('admin_tipo', 'cms')<2)echo form_radio('tipo', 1, ($row['tipo']==1)) . ' Superadmin |';?> 
<?php echo form_radio('tipo', 2, ($row['tipo']==2));?> Segmentado
<?php echo i('Super admin: Poderá acessar qualquer módulo e poderá adicionar novos administradores.<br />Admin segmentado: Poderá ter sua atuação segmentada de acordo com as permições selecionadas.<br />Não poderá adicionar novos administradores.', -1);?></div>


<br />

<div <?php echo ($row['tipo']==2)?'':'style="display:none;"';?> class="div-modulos"><label for="modulos" class="lb-inline">Módulos</label>
<?php echo $comboModulos;?><?php echo i('Escolha os Módulos que o administrador terá acesso.');?></div>

<br />

<label for="acoes" class="lb-inline">Ações permitidas</label>
<div class="form-opcoes">
<?php echo form_checkbox('acoes_a', 'a', $row['apagar']);?> Apagar | 
<?php echo form_checkbox('acoes_c', 'c', $row['criar']);?> Criar conteúdo | 
<?php echo form_checkbox('acoes_r', 'r', $row['relatorio']);?> Gerar relatórios
<?php echo i('O que o administrador poderá fazer no sistema.', -1);?></div>

<br />

<label for="status" class="lb-inline">Status</label>
<div class="form-opcoes"><?php echo form_radio('status', 1, ($row['status']==1));?> ativo | 
<?php echo form_radio('status', 0, ($row['status']==0));?> inativo</div>

<br />

