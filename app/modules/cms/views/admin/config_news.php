
<?php echo validation_errors(); ?>

<h3>Configurações</h3>
<label for="porhora" class="lb-inline">Quant / por segundo</label><input name="porhora" id="porhora" type="text" class="input-mini" value="<?php echo set_value('porhora', $con[15]['valor']);?>" />

<br />

<label for="erros" class="lb-inline">Recebe erros</label><input name="erros" id="erros" type="text" class="input-longo" value="<?php echo set_value('erros', $con[11]['valor']);?>" />

<br />

<label for="optout" class="lb-inline">Mensagem descadastramento</label><textarea name="optout" class="textarea-curto" id="optout" style="height:50px;"><?php echo set_value('optout', $con[16]['valor']);?></textarea>

       
<br />

<h3>SMTP</h3>
<label for="email1" class="lb-inline">E-mail</label><input name="email1" id="email1" type="text" class="input-longo" value="<?php echo set_value('email1', $con[8]['valor']);?>" />

<br />

<label for="host1" class="lb-inline">Host</label><input name="host1" id="host1" type="text" class="input-longo" value="<?php echo set_value('host1', $con[9]['valor']);?>" />

<br />

<label for="senha1" class="lb-inline">Senha</label><input name="senha1" id="senha1" type="password" class="input-curto" value="<?php echo set_value('senha1', $con[10]['valor']);?>" />

<br />


<label for="email2" class="lb-inline">Porta</label><input name="email2" id="email2" type="text" class="input-curto" value="<?php echo set_value('email2', $con[12]['valor']);?>" /> (25, 587)

<br />

<label for="host2" class="lb-inline">SMTPSecure</label><input name="host2" id="host2" type="text" class="input-curto" value="<?php echo set_value('host2', $con[13]['valor']);?>" /> (TLS [google], SSL, "" [locaweb])

<br />

<!--<label for="senha2">Senha</label><input name="senha2" id="senha2" type="text" class="input-curto" value="<?php echo set_value('senha2', $con[14]['valor']);?>" />

<br />-->





