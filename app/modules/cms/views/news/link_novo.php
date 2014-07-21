<form action="<?=cms_url('cms/news/linkSalva/id:'.$id)?>" method="post" name="modalform" id="modalform">
<input name="mens_id" type="hidden" value="<?php echo $id;?>" />

<h3>Descrição e endereço (URL):</h3>

<label for="desc1">Descrição #1</label><input name="desc1" id="desc1" type="text" class="input-longo" value="<?php echo set_value('desc1');?>" />
<br />
<label for="link1">Endereço #1</label><input name="link1" id="link1" type="text" class="input-longo" value="<?php echo set_value('link1', 'http://');?>" />

<br />
<h3></h3>

<label for="desc2">Descrição #2</label><input name="desc2" id="desc2" type="text" class="input-longo" value="<?php echo set_value('desc2');?>" />
<br />
<label for="link2">Endereço #2</label><input name="link2" id="link2" type="text" class="input-longo" value="<?php echo set_value('link2', 'http://');?>" />

<br />

<h3></h3>

<label for="desc3">Descrição #3</label><input name="desc3" id="desc3" type="text" class="input-longo" value="<?php echo set_value('desc3');?>" />
<br />
<label for="link3">Endereço #3</label><input name="link3" id="link3" type="text" class="input-longo" value="<?php echo set_value('link3', 'http://');?>" />

<br />

  


<br />
<?php echo validation_errors(); ?>
<label>&nbsp;</label><input name="submit" type="submit" value="Enviar" class="buttom" />
</form>