<form action="<?=cms_url("cms/enquete/salva/".$per['id'])?>" method="post">
<input name="submit" type="image" src="<?=base_url()?>cms_itens/img/cms_bt_salvar.jpg" alt="salvar" class="submit" />
<br /><br />

<!--<label for="lang" class="label">Idioma:</label>
<?php //echo $this->lingua_model->combo_linguas($per['lang']);?>
<br /><br />-->

<label for="data" class="label">Data</label>
<input name="data" id="data" class="curto" type="text" value="<?=formaPadrao($per['data'])?>">
<img src="<?=base_url()?>cms_itens/img/cms_ico_calendar.jpg" alt="obs" class="obs" align="absmiddle" title="Calendário|Clique no campo e escolha a data." />
<br /><br />

<label for="pergunta" class="label">Pergunta</label>
<input name="pergunta" id="pergunta" class="texto" type="text" value="<?=htmlentities(utf8_decode($per['pergunta']))?>">
<br /><br />

<?php
$i = 1;
foreach($opc as $row):

	$id_opc = $row['id'];
	$opcao = $row['opcao'];
	$votos = $row['votos'];
	$label_id = "opc".$i;
	$autor = $this->sessao_model->user_infos($per['autor']);

?>

<label class="label">Resposta #<?=$i?>: </label>
<input name="resposta[<?=$id_opc?>]" id="<?=$label_id?>" type="text" size="50" maxlength="100" value="<?=htmlentities(utf8_decode($opcao))?>" class="texto" />(<?=$votos?> votos)
<br />
<br />

<?php
	$i++;
endforeach;
?>

<label for="autor" class="label">Publicado por</label>
<input name="autor" id="autor" class="texto" type="text" readonly="readonly" value="<?=$autor['nome']?>">
<img src="<?=base_url()?>cms_itens/img/cms_ico_obs.jpg" alt="obs" class="obs" align="absmiddle" title="Autor|Administrador do sistema que publicou a enquete pela primeira vez." />
<br /><br />


<?php echo validation_errors(); ?>
<br />

<input name="submit" type="image" src="<?=base_url()?>cms_itens/img/cms_bt_salvar.jpg" alt="salvar" class="submit" />
</form>
<br />
<script type="text/javascript">
$(function() {
	$("a.nyroModal").click(function(){
		$(".atualizar_pag").slideDown("slow");
	});

	$(".comentario_box div").hide("slow");
	$(".comentario_box a.comentario_link").click(function(){
		var cont = $(this).attr('alt');
		$(".comentario_box div").slideUp("slow");	
		$("#"+cont).slideDown("slow");
		return false;
	});
});
</script>
<label class="label">Cometários</label><br /><br />
<div class="atualizar_pag" style="display:none;"><a href="<?=cms_url(uri_string())?>"><img src="<?=base_url()?>cms_itens/img/cms_ico_atualizar.jpg" alt="Atualizar" align="absmiddle" /> Atualize a página para que as alterações tenham efeito.</a></div>
<div class="comentario_box">

<?php 
foreach($coments as $row):
	
	$id = $row['id'];
	$ip_p = $row['pergunta'];
	$data = $row['data'];
	$comentario = $row['comentario'];
	$ip = $row['ip'];
	$nome = $row['nome'];
	$email = $row['email'];
	$controle = 'controle_'.$id;
	//
	$del = $this->opcoes->deleta($id, 'aw_enquete_com', 'cms/enquete/edita/'.$ip_p);
?>

<span class="comentario_nome"><a href="#" class="comentario_link" alt="<?=$controle?>"><?=$data?> <?=$nome?></a>   
<span class="conetario_opcoes">
<a href="<?=cms_url('cms/enquete/coment_edita/'.$id)?>" class="nyroModal" target="_blank"><img src="<?=base_url()?>cms_itens/img/cms_ico_mini_edit.gif" alt="editar" /></a>
<?=$del?>
<img src="<?=base_url()?>cms_itens/img/cms_ico_mini_infos.gif" alt="infos" class="obs" title="<?=$nome?>|<?=$ip?>|<?=$email?>" /></span>
 </span>



<div class="comentario_texto" id="<?=$controle?>"><?=$comentario?></div>

<?php 
endforeach;
?>


</div>
