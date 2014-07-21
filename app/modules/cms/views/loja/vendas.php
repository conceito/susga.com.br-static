<?php
/******************************************
*  Template: listagem de vendas realizadas
*  Controller: cms/loja/vendas
*/

$baseurl = base_url();
//$pagination = $this->pagination->create_links();
?>

      
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl" class="table table-striped">
      <thead>
      <tr>
      	<?php if($listOptions['sortable']) echo '<th scope="col" class="sortable">&nbsp;</th>'?>
        <th scope="col" class="cb">&nbsp;</th>
        <th scope="col">Nº da venda</th>
        <th scope="col">Cliente</th>
        <th scope="col">Situação</th>
        <th scope="col" style="text-align:right;">Total</th>
        <th scope="col" class="" style="text-align:right;">Data</th>
        <th scope="col" class="" style="text-align:right;">Ação</th>
      </tr>
      </thead>
      <tbody>
      
      <tr class="local-search">
      	<td><a href="<?php echo cms_url($linkFilter);?>" title="limpar filtros" style="display: block; margin: 7px 0 0 10px;"><i class="icon-ban-circle icon-white"></i></a></td>
        <td><?php echo local_search(array(
			'campo' => 'id', 
			'type'  => 'int',
			'class' => 'filter_id', 
			'style' => 'width:60px'
			));?></td>
        <td><?php echo local_search(array(
			'campo' => 'usuario_id',
			'options' => false, 
			'type'  => 'int',
			'class' => 'input-medium clientes-populate',
			'style' => '',
			'id'    => 'user_id'
		));?>
        </td>
        <td><?php echo local_search(array(
			'campo' => 'status',
			'options' => $this->config->item('status_transacao'), 
			'type'  => 'int',
			'class' => 'input-small',
			'style' => 'width:100%'
		));?></td>
        <td style="text-align:right;"></td>
        <td style="text-align:right;">
		<?php echo local_search(array(
			'campo' => 'data', 
			'type'  => 'date',
			'class' => 'input-small',
			'id'    => 'dt1'
		));?></td>
        <td style="text-align:right;"><a href="<?php echo cms_url($linkFilter);?>" class="btn btt-filter">filtrar</a></td>
      </tr>
      
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
        
        <td valign="top"><?php echo $row['id'];?></td>
        <td valign="top"><?php echo barra_edicao($edicao, $row['user_nome']);?>
        <!--resumo-->
            <div class="resumo-ler"><?php //echo $row['resumo'];?></div>
        </td> 
        <td valign="top"><?php echo $row['anotacao'];?></td>
        <td valign="top" style="text-align:right;">R$ <?php echo $row['valor_total'];?></td> 
             
                
        <td valign="top" style="text-align:right;"><?php echo $row['data'] . ' <br> ' . $row['hora'];?></td>
        <td valign="top" style="text-align:right;"><a href="<?php echo $edicao;?>">editar</a></td>
      </tr>
      <?php 
	  	$i++;
	  	endforeach;
	  endif;
	  ?>
      
     
      </tbody>
    </table>
    </form>
    