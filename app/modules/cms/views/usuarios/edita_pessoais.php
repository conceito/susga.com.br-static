<?php echo validation_errors(); ?>


<div class="panel-left clearfix">
	
	
	<label for="nome" class="lb-full">Nome</label><input name="nome" id="nome" type="text" class="input-longo input-titulo" value="<?php echo set_value('nome', form_prep($row['nome']));?>" />
	
	<br />

    <?php
    // nome crachá
    $cracha = get_meta($row['metas'], 'nome_cracha', null, true);
    if($cracha):
    ?>
        <label for="nome" class="lb-full">Nome do crachá</label>
        <b><?php echo $cracha?></b>

        <br />
        <br />
    <?php endif;?>
	
	
	<label for="email" class="lb-full">E-mail principal</label><input name="email" id="email" type="text" class="input-longo" value="<?php echo set_value('email', $row['email']);?>" />
	<a href="<?php echo cms_url('cms/usuarios/mensagemForm/id:'.$row['id']);?>" class="enviar-mensagem nyroModal" target="_blank" title="clique para enviar mensagem" style="position:absolute; display:inline; margin-top:5px; margin-left:10px;">enviar</a>
	<br />
	
	<label for="email2" class="lb-full">E-mail alternativo</label><input name="email2" id="email2" type="text" class="input-longo" value="<?php echo set_value('email2', $row['email2']);?>" />
	
	<br />
	
	<label for="nasc" class="lb-full">Data nascimento</label><input name="nasc" id="nasc" type="text" class="input-curto" value="<?php echo set_value('nasc', $row['nasc']);?>" />
	
	<br />
	
	<div class="subpanel-50-50">
	<label for="tel1" class="lb-full">Telefone #1</label>
	<input name="tel1" id="tel1" type="text" class="input-curto" value="<?php echo set_value('tel1', $row['tel1']);?>" />
	</div><!-- .subpanel-50-50 -->
	
	<div class="subpanel-50-50">
	<label for="tel2" class="lb-full">Telefone #2</label>
	<input name="tel2" id="tel2" type="text" class="input-curto" value="<?php echo set_value('tel2', $row['tel2']);?>" />
	</div><!-- .subpanel-50-50 -->	
	
	<br />
	
	<div class="subpanel-50-50">
	<label for="cpf" class="lb-full">CPF</label>
	<input name="cpf" id="cpf" type="text" class="input-curto" value="<?php echo set_value('cpf', $row['cpf']);?>" />
	</div><!-- .subpanel-50-50 -->
	
	<div class="subpanel-50-50">
	<label for="rg" class="lb-full">RG</label>
	<input name="rg" id="rg" type="text" class="input-curto" value="<?php echo set_value('rg', $row['rg']);?>" />
	</div><!-- .subpanel-50-50 -->	
	
	<br />
	
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">
		
	
	<div class="control-group box">
    
    <label for="status" class="lb-full">Status</label>
    <div class="form-opcoes group-buttons">
		<?php echo inputs_status($row['status']);?>
     </div>
     
     </div><!-- .control-group -->

    <div class="control-group box">
    
	<label for="sexo" class="lb-full">Sexo</label>
	<div class="form-opcoes group-buttons">
	<?php echo form_radio(array(
    'name'        => 'sexo',
    'id'          => 'sexo0',
    'value'       => '0',
    'checked'     => ($row['sexo']==0),
    'style'       => '',
    ));?><label for="sexo0">Masculino</label> 
	<?php echo form_radio(array(
    'name'        => 'sexo',
    'id'          => 'sexo1',
    'value'       => '1',
    'checked'     => ($row['sexo']==1),
    'style'       => '',
    ));?><label for="sexo1">Feminino</label>
    </div>
	
	
	</div><!-- .control-group -->



    <div class="control-group box">
	
	<label for="grupos" class="lb-full">Grupo</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
	
	</div><!-- .control-group -->
    
    <div class="control-group" style="min-height: 121px;">
	
	<label for="dt_ini" class="lb-full">Data cadastro</label><?php echo $row['dt_ini'];?>
    
    <br /><br />

	<div class="form-opcoes" style="">Idioma: 
	<?php echo $row['lang'];?></div>
    
    <div class="user-foto-id"><div class="foto">
	<?php if(strlen($row['foto']) > 5 ){?>
	<img src="<?php echo $row['foto'];?>" alt="<?php echo $row['nome'];?>" width="100%" height="auto">
	<?php }?></div></div>
	
    
    </div><!-- .control-group -->
    
    
    <div class="control-group box">	
	
	<label for="obs" class="lb-full">Observações</label>
    <textarea name="obs" class="textarea-curto" id="obs"><?php echo set_value('obs', $row['obs']);?></textarea>
    
    </div><!-- .control-group -->
	
	
	
	
	
	
</div><!-- .panel-right -->










       


