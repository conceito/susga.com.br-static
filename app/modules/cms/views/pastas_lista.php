<?php $baseurl = base_url();
$pagination = $this->pagination->create_links();
?>

       
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl" class="table table-striped">
      <thead>
      <tr>
      	<?php if($listOptions['sortable']) echo '<th scope="col" class="sortable">&nbsp;</th>'?>
        <th scope="col" class="cb">&nbsp;</th>
        <th scope="col"><?php echo ucfirst($rows[0]['termo']);?></th>
        <th scope="col">Pasta</th>
        <th scope="col">Grupo</th>
        <th scope="col">Quantidade</th>
        <?php if($listOptions['destaque']) echo '<th scope="col">Destaque</th>'?>
        <th scope="col">Padrões</th>
       
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
        <td valign="top"><a href="<?php echo $edicao;?>" title="Ver arquivos desta pasta"><img src="<?php echo cms_img();?>pasta-<?php echo $row['termo'];?>.png" width="32" height="30" alt="<?php echo $row['termo'];?>" /></a></td>
        <td valign="top"><?php echo barra_edicao($edicao, $row['titulo'], 'pasta', $row['quantidade']);?>
        <!--resumo-->
            <div class="resumo-ler"><?php echo $row['txt'];?></div>
        </td> 
        <td valign="top"><?php echo grupoSimplesNome($row['grupo'], $row['grupoCor1'], $row['grupoCor2']);?></td> 
        <td valign="top"><?php echo $row['quantidade'];?></td> 
        <?php if($listOptions['destaque']) echo '<td valign="top">'.link_destaque($row['destaque']).'</td>';?>      
        <td valign="top"><?php echo $row['padroes'];?></td> 
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
    Páginas: <?php echo $pagination;?>
    