<?php
/******************************************
*  Template: listagem de descontos e cupons
*  Controller: cms/loja/descontos
*/

$baseurl = base_url();
?>

      
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl" class="table table-striped">
      <thead>
      <tr>
      	<?php if($listOptions['sortable']) echo '<th scope="col" class="sortable">&nbsp;</th>'?>
        <th scope="col" class="cb">&nbsp;</th>
        <th scope="col" title="Prioridade">Prio.</th>
        <th scope="col">Nome</th>
        <th scope="col">Tipo</th>
        <th scope="col">Regra</th>
        <th scope="col" style="text-align:right;">Valor desconto</th>
        <th scope="col" class="" >Grupo de clientes</th>
        <th scope="col" class="" >Início / Término</th>
        <th scope="col" class="data">Status</th>
        <th scope="col" class=""></th>
      </tr>
      </thead>
      <tbody>
      
      <tr class="local-search">
      	<td><a href="<?php echo cms_url($linkFilter);?>" title="limpar filtros" style="display: block; margin: 7px 0 0 10px;"><i class="icon-ban-circle icon-white"></i></a></td>
        
        <td></td>
        <td></td>
        
        <td><?php echo local_search(array(
			'campo' => 'titulo', 
			'type'  => 'text',
			'class' => 'filter_id', 
			'style' => 'width:85%'
			));?></td>
        <td><?php echo local_search(array(
			'campo' => 'tipo',
			'options' => array('desconto' => 'Desconto', 'cupom' => 'Cupom'), 
			'type'  => 'text',
			'class' => 'input-small',
			'style' => '',
			'id'    => ''
		));?>
        </td>
        <td><?php echo local_search(array(
			'campo' => 'regra',
			'options' => $this->config->item('regra_tipo_desconto')+$this->config->item('regra_tipo_cupom'), 
			'type'  => 'text',
			'class' => 'input-small',
			'style' => 'width:100%'
		));?></td>
        <td style="text-align:right;">
		<?php echo local_search(array(
			'campo' => 'valor',
			'type'  => 'money',
			'class' => 'input-small type-valor',
			'style' => 'width:60px'
		));?></td>
        <td>
        <?php echo local_search(array(
			'campo' => 'grupo',
			'options' => false, 
			'type'  => 'int',
			'class' => 'input-small grupoclientes-populate',
			'style' => '',
			'id'    => 'grupo_id'
		));?>
        </td>
        <td style="text-align:right;">
		<?php echo local_search(array(
			'campo' => 'data', 
			'type'  => 'date',
			'class' => 'input-small',
			'id'    => 'dt1',
			'style' => 'width:70px'
		));?></td>
        
        <td style="text-align:right;">
		<?php echo local_search(array(
			'campo' => 'status', 
			'options' => $this->config->item('post_status'),
			'type'  => 'int',
			'class' => 'input-small',
			'id'    => '',
			'style' => 'width:100%'
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
      
        <td valign="top" class="td-cb">
        <input name="cb[]" type="checkbox" value="<?php echo $row['id'];?>" class="cb" />
        </td>
        
        <td valign="top"><?php echo $row['ordem'];?></td>
        <td valign="top"><?php echo barra_edicao($edicao, $row['titulo']);?>
        <!--resumo-->
            <div class="resumo-ler"></div>
        </td> 
        <td valign="top"><?php echo ucfirst($row['tipo']);?></td>
        <td valign="top"><?php echo $row['regra'];?>: <?php echo $row['verificador'];?></td>
        <td valign="top" style="text-align:right;"><?php echo $row['valor'];?></td> 
        <td valign="top"><?php echo $row['grupo_nome'];?></td> 
        <td valign="top"><?php echo $row['data'];?> <br /> <?php echo $row['termino'];?></td>
          
        <td valign="top"><?php echo link_status('-', $row['status']);?></td>
        <td></td>
      </tr>
      <?php 
	  	$i++;
	  	endforeach;
	  endif;
	  ?>
      
     
      </tbody>
    </table>
    </form>
    