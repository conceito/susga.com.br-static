<!-- view da lista de menus -->
<?php $baseurl = base_url();
$pagination = $this->pagination->create_links();
?>

      
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl" class="table table-striped">
      <thead>
      <tr>
      	<?php if($listOptions['sortable']) echo '<th scope="col" class="sortable">&nbsp;</th>'?>
        <th scope="col" class="cb">&nbsp;</th>

        <th scope="col">ID</th>

        <th scope="col">Menu</th>

        <th scope="col">Itens</th>

        <th scope="col">Posição</th>
        
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
			// tipo de grupo
			if($tipoGrupo == 'pastas'){
				
				$resumo = $row['txt'];
			}else {
				
				$resumo = $row['resumo'];
			}
			
	  ?>
      <tr class="<?php echo $zebra;?>" id="<?php echo $row['id'];?>">
      <?php if($listOptions['sortable']) echo '<td valign="top" class="dragme"></td>'?>
        <td valign="top" class="td-cb"><input name="cb[]" type="checkbox" value="<?php echo $row['id'];?>" class="cb" /></td>
        
         <td valign="top"><?php echo $row['id'];?></td>

        <td valign="top"><?php echo barra_edicao($edicao, $row['titulo']);?>
        <!--resumo-->
            <div class="resumo-ler"><?php echo $resumo;?></div>
        </td>

        <td valign="top"><?php echo $row['ttlItens'];?></td>

        <td valign="top"><?php echo $resumo;?></td>
       
        <td valign="top"><?php echo link_status('-', $row['status']);?></td>
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
    