<?php $baseurl = base_url();
$pagination = $this->pagination->create_links();
?>

      
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl" class="table table-striped">
      <thead>
      <tr>
      	<?php if($listOptions['sortable']) echo '<th scope="col" class="sortable">&nbsp;</th>'?>
        <th scope="col" class="cb">&nbsp;</th>
        <th scope="col"></th>
        <th scope="col">Título</th>
        <th scope="col">Limite</th>
        <th scope="col">Views</th>        
        <th scope="col">Clicks</th>
        <th scope="col">CTR</th>
        <?php if($listOptions['destaque']) echo '<th scope="col">Destaque</th>'?>
        <th scope="col"><?php echo ($modulo['id']==6)?'Arquitetura':'Grupo';?></th>
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
			// grupo
			$grupo = $row['grupoParents'][0];
			
			
	  ?>
      
      <tr class="<?php echo $zebra;?> tr-group-color" id="<?php echo $row['id'];?>" style="border-left:none;">
      <?php if($listOptions['sortable']) echo '<td valign="top" class="dragme"></td>'?>
        <td valign="top" class="td-cb"><input name="cb[]" type="checkbox" value="<?php echo $row['id'];?>" class="cb" /></td>
        <td valign="top"><?php echo $this->banner_model->getBannerThumb($row);?></td>
        <td valign="top">
        <?php // se existe hierarquia de páginas
        if($listOptions['sortable'] && $row['rel'] != 0):?><!--&#8212;&#8250;--><?php endif;?>
        
        <?php echo barra_edicao($edicao, $row['titulo']);?>
        <!--resumo-->
            <div class="resumo-ler"><?php echo $row['resumo'];?></div>
        </td> 
        <td valign="top"><?php echo $row['limit'];?></td>
        <td valign="top"><?php echo $row['views'];?></td>        
        <td valign="top"><?php echo $row['clicks'];?></td>
        <td valign="top"><?php echo ctr($row['clicks'], $row['views']);?>%</td>
        <?php if($listOptions['destaque']) echo '<td valign="top">'.link_destaque($row['destaque']).'</td>';?>      
        <td valign="top"><?php echo grupoSimplesNome($grupo['titulo']);?>(<?php echo $grupo['tags'];?>)</td> 
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
    Páginas: <?php echo $pagination;?>
    