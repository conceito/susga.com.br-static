
 <ul class="campo_ajuda">
 <li class="info">
<?php echo  $this->box_infos; ?>


 </li>
 <li class="busca">
 <img src="<?=base_url()?>cms_itens/img/cms_ico_duvida1.jpg" alt="Ajuda" class="obs" title="Busca interna|Use palavras chave para filtrar os registros." align="right" />


 <form action="<?=$action?>" method="post" name="Busca">
		<input name="busca" id="busca" type="text" size="20" maxlength="50" class="campobusca" value="<?=$this->campo_busca?>" />
		<input name="submit" type="submit" value="buscar" class="botbusca" />
        </form>
        <?=$this->link_tudo?>
 </li>
 </ul>

 <table border="1" cellspacing="0" cellpadding="0" class="tablesorter" id="tblSorter">
  <thead>
  <tr>
    <th>Data</th>
    <th></th>
    <th>Pergunta</th>
    <th>Opções</th>
    <th>Coment.</th>
    <th>Status</th>
    <th class="{sorter: false}">Apagar</th>
  </tr>
  </thead>
  <tbody>
	<?php
    foreach($listaItens as $row):

		$data = $row['data'];
		$titulo = $row['pergunta'];
		$status = $this->opcoes->status($row['id'], $row['status'], 'aw_enquete_per', $this->redirecionamento);
		$del = $this->opcoes->deleta($row['id'], 'aw_enquete_per', $this->redirecionamento);
		$opcoes = count($this->enquete_model->opcoes_dados($row['id']));
		$comments = count($this->enquete_model->comentarios_dados($row['id']));
		$iconeLang = $this->lingua_model->ico_lang($row['lang']);
    ?>
    <tr>
    <td><?=$data?></td>
    <td><?=$iconeLang?></td>
    <td><a href="<?=cms_url("cms/enquete/edita/id:".$row['id'])?>" title="Clique para editá-la"><?=$titulo?></a></td>
    <td><?=$opcoes?></td>
    <td><?=$comments?></td>
    <td><?=$status?></td>
    <td><?=$del?></td>
    </tr>
    <?php
    endforeach;
    ?>
  </tbody>
</table>

<?php echo $this->pagination->create_links();?>


