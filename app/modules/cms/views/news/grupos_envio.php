<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>
<h3>Mensagem selecionada</h3>
<label style="margin-top:-5px;" class="lb-full">Assunto</label><strong><?php echo $news['titulo'];?></strong> (<a href="<?php echo cms_url('cms/news/view/'.$news['id']);?>" class="nyroModal" target="_blank">exemplo</a>)

<br /><br />

<label for="dt1" class="lb-full"><b class="obr">[!]</b> Data de envio</label><input name="dt1" id="dt1" type="text" class="input-curto" value="<?php echo set_value('dt1', date("d/m/Y"));?>" />

<br />

<label for="titulo" class="lb-full"><b class="obr">[!]</b> Nome do agendamento</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', $news['titulo']);?>" />





<h3>Selecione os grupos de usuários</h3>
<label for="grupos" class="lb-full"><b class="obr">[!]</b> Grupos</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>

<h3>Selecione os filtros de usuários</h3>
<label for="filtros" class="lb-full">Filtros</label><?php echo (! $filtros)? 'Não existem.<br />' : $filtros;?>

<br />

<label class="lb-full">&nbsp;</label><a href="<?php echo cms_url($lnkDisparar);?>" class="bot-verde btt-salva"><span><b class="ico-ok">Disparar</b></span></a>
<div class="" style="position:absolute; display:inline; padding:0 0 0 10px; color:#900;"> (Atenção: após o disparo NÃO feche a janela do navegador!)</div>

<?php echo validation_errors(); ?>