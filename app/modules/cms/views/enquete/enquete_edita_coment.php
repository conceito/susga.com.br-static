<?php 
if(isset($resposta)):
	echo '<div class="resposta">';
	echo $resposta;
	echo '</div>';
endif;
?>
<form action="<?=cms_url('cms/enquete/coment_salva/'.$row[0]['id'])?>" method="post">
<input name="submit" type="image" src="<?=base_url()?>cms_itens/img/cms_bt_salvar.jpg" alt="salvar" class="submit" />
<br /><br />

<label for="data" class="label">Data</label>
<input name="data" id="data" class="curto" type="text" value="<?=formaPadrao($row[0]['data'])?>">
<img src="<?=base_url()?>cms_itens/img/cms_ico_calendar.jpg" alt="obs" class="obs" align="absmiddle" title="Calendário|Clique no campo e escolha a data." />

<br /><br />

<label for="nome" class="label">Nome</label>
<input name="nome" id="nome" class="texto" type="text" value="<?=htmlentities(utf8_decode($row[0]['nome']))?>">
<br /><br />

<label for="email" class="label">E-mail</label>
<input name="email" id="email" class="texto" type="text" value="<?=$row[0]['email']?>">

<br /><br />

<label class="label">Comentário:</label>

<textarea name="comment" id="comment" class="resumo" cols="" rows=""><?=$row[0]['comentario']?></textarea>
<br />
<br />
<input name="submit" type="image" src="<?=base_url()?>cms_itens/img/cms_bt_salvar.jpg" alt="salvar" class="submit" />
</form>
<?php echo validation_errors(); ?>