<?php $baseurl = base_url();
?>

      
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl" class="table table-striped">
      <thead>
      <tr>
      	<?php if($listOptions['sortable']) echo '<th scope="col" class="sortable">&nbsp;</th>'?>
        <th scope="col" class="cb">&nbsp;</th>
        <th scope="col">Newsletter</th>
        <th scope="col">Disparar</th>
        <?php if($listOptions['email']) echo '<th scope="col">Teste</th>'?>
        <?php if($listOptions['inscricao']) echo '<th scope="col">Inscritos</th>'?>
        <?php if($listOptions['destaque']) echo '<th scope="col">Destaque</th>'?>
        <th scope="col">Grupo</th>
        <?php if(isset($listOptions['comments']) && $listOptions['comments']) echo '<th scope="col">Comentários</th>'?>
       
       
        <th scope="col" class="data">Status</th>
      </tr>
      </thead>
      <tbody>
      
      <?php 
	  if(! $rows):
	  	echo '<tr><td colspan="3"><strong>Não existem registros.</strong></td></tr>';
	  else:
	  	$i = 0;
		foreach($rows as $row):
			
			// linhas pares-impares
			if($i%2 == 0)$zebra = 'even';
			else $zebra = '';
			// links
			$edicao = cms_url($linkEditar . '/id:' . $row['id']);
			
	  ?>
      <tr class="<?php echo $zebra;?>" id="<?php echo $row['id'];?>">
      <?php if($listOptions['sortable']) echo '<td valign="top" class="dragme"></td>'?>
        <td valign="top" class="td-cb"><input name="cb[]" type="checkbox" value="<?php echo $row['id'];?>" class="cb" /></td>
        
        <td valign="top"><?php echo barra_edicao($edicao, $row['titulo']);?>
        <!--resumo-->
            <div class="resumo-ler"><?php echo $row['resumo'];?></div>
        </td>
        <td valign="top"><a href="<?php echo cms_url('cms/news/agendar/id:'.$row['id']);?>" class="enviar-mensagem" title="clique para enviar mensagem">enviar</a></td> 
        <?php if($listOptions['email']) echo '<td valign="top"><a href="'.cms_url($listOptions['email'].'/id:'.$row['id']).'" class="enviar-mensagem nyroModal" target="_blank" title="clique para enviar mensagem">enviar</a></td>';?> 
        <?php if($listOptions['inscricao']) echo '<td valign="top">'.link_inscritos($row['insc_ttl'], $row['insc_new'], $edicao).'</td>';?>
        <?php if($listOptions['destaque']) echo '<td valign="top">'.link_destaque($row['destaque']).'</td>';?>      
        <td valign="top"><?php echo grupoSimplesNome($row['grupo'], $row['grupoCor1'], $row['grupoCor2']);?></td> 
        <?php if(isset($listOptions['comments']) && $listOptions['comments']) echo '<td valign="top">'.link_comments($row['comm_ttl'], $row['comm_new'], $edicao).'</td>';?>       
        
        <td valign="top"><?php echo link_status($row['dt_ini'], $row['status']);?></td>
      </tr>
      <?php 
	  	$i++;
	  	endforeach;
	  endif;
	  ?>
      
     
      </tbody>
    </table>
    </form>
    