<form action="<?=cms_url("cms/enquete/salva")?>" method="post">
<input name="submit" type="image" src="<?=base_url()?>cms_itens/img/cms_bt_salvar.jpg" alt="salvar" class="submit" />
<br /><br />

<!--<label for="lang" class="label">Idioma:</label>
<?php //echo $this->lingua_model->ico_lang($this->session->userdata('lang'));?>
<br /><br /><br />-->

<label for="data" class="label">Data</label>
<input name="data" id="data" class="curto" type="text" value="<?=set_value('data')?>"> <img src="<?=base_url()?>cms_itens/img/cms_ico_calendar.jpg" alt="obs" class="obs" align="absmiddle" title="Calendário|Clique no campo e escolha a data." />
<br /><br />


<label for="pergunta" class="label">Pergunta</label>
<input name="pergunta" id="pergunta" class="texto" type="text" value="<?=set_value('pergunta')?>">
<br /><br />

<label class="label"> </label><label class="label">Opções</label><br /><br />
<?php 
for($i = 1; $i<11 ; $i++):
?>

<label class="label">Resposta #<?=$i?>: </label>
<input name="resposta[]" type="text" size="50" maxlength="100" class="texto" /><br /><br />
<?php 
endfor;
?>


<br /><br />


<?php echo validation_errors(); ?>


<input name="submit" type="image" src="<?=base_url()?>cms_itens/img/cms_bt_salvar.jpg" alt="salvar" class="submit" />
</form>
