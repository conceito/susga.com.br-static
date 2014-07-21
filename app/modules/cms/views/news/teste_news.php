<form action="<?=cms_url('cms/news/testeEnvia/id:'.$row['id'])?>" method="post" name="modalform" id="modalform">

<h3>E-mail destinatário:</h3>


<label for="email">E-mail destino</label><input name="email" id="email" type="text" class="input-longo" value="<?php echo set_value('email');?>" />
<input name="submit" type="submit" value="Enviar" class="buttom" />

<br />
<?php echo validation_errors(); ?>

</form>

<?php echo $row['txt'];?>
<hr color="#CCCCCC" size="1" noshade="noshade" />
<?php echo nl2br($row['resumo']);?>