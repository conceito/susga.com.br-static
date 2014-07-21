<form action="<?=cms_url('cms/usuarios/mensagemEnvia/id:'.$row['id'])?>" method="post" name="modalform" id="modalform">
<input name="user_id" type="hidden" value="<?php echo $row['id'];?>">


<h3>Dados do usuário:</h3>

<label for="nome">Nome</label><input name="nome" id="nome" type="text" class="input-longo" value="<?php echo set_value('nome', form_prep($row['nome']));?>" readonly="readonly" />

<br />

<label for="email">E-mail</label><input name="email" id="email" type="text" class="input-longo" value="<?php echo set_value('email', $row['email']);?>" readonly="readonly" />

<br />



<h3>Escreva sua mensagem:</h3>

<label for="assunto">Assunto</label><input name="assunto" id="assunto" type="text" class="input-longo" value="<?php echo set_value('assunto');?>" />

<br />


<label for="mensagem">Mensagem</label><textarea name="mensagem" class="textarea-curto" id="mensagem"><?php echo set_value('mensagem');?></textarea>

       


<br />
<?php echo validation_errors(); ?>
<label>&nbsp;</label><input name="submit" type="submit" value="Enviar" class="btn buttom" />
</form>