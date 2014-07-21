<form action="<?=cms_url('cms/upload/fazUpload/')?>" method="post" enctype="multipart/form-data" name="modalform" id="modalform">
<input name="pasta_id" type="hidden" value="<?php echo $pasta['id'];?>">
<input name="tipo_id" type="hidden" value="<?php echo $co;?>">
<input name="conteudo_id" type="hidden" value="<?php echo $id;?>">
<input name="onde" type="hidden" value="<?php echo $onde;?>">

<fieldset class="modal-dicas"><legend>Dicas</legend>
<ul>

	<li>Fique atento aos tipos de arquivos aceitos e ao peso máximo permitido.</li>
	<li>Nomes de arquivos que contenham espaços e/ou caracteres especiais, como: ?, !, #, @, ç etc poderão causar erro.</li>

</ul>
</fieldset>

<h3>Pasta de destino:</h3>
<label>&nbsp;</label><strong><?php echo $pasta['titulo'];?></strong>,  Arquivos aceitos: <?php echo $ext;?> com no máximo <strong><?php echo $pesomax;?></strong>

<h3>Procure as imagens:</h3>

<label>Imagem #1:</label><input name="userfile1" type="file" size="50" class="input-longo" />

<br />

<label>Imagem #2:</label><input name="userfile2" type="file" size="50" class="input-longo" />

<br />

<label>Imagem #3:</label><input name="userfile3" type="file" size="50" class="input-longo" />

<br />

<?php if($co == 2):?>  
<h3>Arquivo externo:</h3>
<label>Link do arquivo</label><input name="externo" type="text" size="50" class="input-longo" value="http://" />
<br />
<?php endif;?>

<label>&nbsp;</label><input name="submit" type="submit" value="Enviar" class="buttom" />
<br />
</form>