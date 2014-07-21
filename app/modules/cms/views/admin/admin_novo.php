<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<label for="nome" class="lb-inline"><b class="obr">[!]</b> Nome</label><input name="nome" id="nome" type="text" class="input-longo" value="<?php echo set_value('nome');?>" />

<br />

<label for="email" class="lb-inline"><b class="obr">[!]</b> E-mail</label><input name="email" id="email" type="text" class="input-longo" value="<?php echo set_value('email');?>" />

<br />

<label for="nick" class="lb-inline"><b class="obr">[!]</b> Apelido</label><input name="nick" id="nick" type="text" class="input-curto" value="<?php echo set_value('nick');?>" />
<?php echo i('Nome como os usuários e o sistema identificarão você. Não confunda com seu login!');?>


<br /><br />

<label for="login" class="lb-inline"><b class="obr">[!]</b> Login</label><input name="login" id="login" type="text" class="input-curto" value="<?php echo set_value('login');?>" />
<?php echo i('Será o login para acessar a administração. <br />Deve ter entre 5 e 15 caracteres. <br />Não pode ter espaço e caracteres especiais!');?>


<br /><br />

<label for="senha" class="lb-inline"><b class="obr">[!]</b> Senha</label><input name="senha" id="senha" type="password" class="input-curto" value="<?php echo set_value('senha');?>" />
<?php echo i('Será a senha para acessar a administração. <br />Deve ter entre 5 e 15 caracteres. <br />Não pode ter espaço e caracteres especiais!');?>

<br /><br />

<label for="confirmar" class="lb-inline"><b class="obr">[!]</b> Confirmar Senha</label><input name="confirmar" id="confirmar" type="password" class="input-curto" value="<?php echo set_value('confirmar');?>" />
<?php echo i('Deve ser exatamente igual a senha.');?>

<br />

<label>&nbsp;</label>Ao salvar o cadastro o administrador receberá um e-mail com seus dados para login.
<br />



<?php echo validation_errors(); ?>