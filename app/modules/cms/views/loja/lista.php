<?php
/******************************************
*  Template: listagem de produtos
*  Controller: cms/loja/index
*/

$baseurl = base_url();
$pagination = $this->pagination->create_links();
?>

      
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl" class="table table-striped">
      <thead>
      <tr>
      	<?php if($listOptions['sortable']) echo '<th scope="col" class="sortable">&nbsp;</th>'?>
        <th scope="col" class="cb">&nbsp;</th>
        <th scope="col"></th>
        <th scope="col">Categoria</th>
        <th scope="col">Nome</th>
         <th scope="col" style="text-align:right;">Preço</th>
          <th scope="col">Estoque</th>
        <?php if($listOptions['inscricao']) echo '<th scope="col">Inscritos</th>'?>
        <?php if($listOptions['destaque']) echo '<th scope="col">Destaque</th>'?>
       
        <th scope="col" class="data">Status</th>
        <th scope="col" class=""></th>
      </tr>
      </thead>
      <tbody>
      
      <tr class="local-search">
          
        <?php if($listOptions['sortable']) echo '<td valign="top" class=""></td>'?>
          
      	<td><a href="<?php echo cms_url($linkFilter);?>" title="limpar filtros" style="display: block; margin: 7px 0 0 10px;"><i class="icon-ban-circle icon-white"></i></a></td>
        
        <td></td>
        <td>
        <?php echo local_search(array(
			'campo' => 'grupo',
			'options' => false, 
			'type'  => 'int',
			'class' => 'input-small grupo-populate',
			'style' => '',
			'id'    => 'grupo_id'
		));?>
        </td>
        
        <td>
		<?php echo local_search(array(
			'campo' => 'titulo', 
			'type'  => 'text',
			'class' => '', 
			'style' => 'width:85%'
		));?>
        </td>
        
        <td style="text-align:right;">
		</td>
        
        <td title="Use + ou - após o número para aumentar a abrangência da pesquisa"><?php echo local_search(array(
			'campo' => 'estoque', 
			'type'  => 'text',
			'class' => 'input-small',
			'style' => ''
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
        <td valign="top" class="td-cb"><input name="cb[]" type="checkbox" value="<?php echo $row['id'];?>" class="cb" /></td>
        <td valign="top"><?php echo conteudoThumb($row['galeria']);?></td>
        <td valign="top"><?php echo grupoNome($row['grupoParents']);?></td>
        <td valign="top"><?php echo barra_edicao($edicao, $row['titulo']);?>
        <!--resumo-->
            <div class="resumo-ler"><?php echo $row['resumo'];?></div>
        </td> 
        <td valign="top" style="text-align:right;">R$ <?php echo $row['preco'];?></td>
        <td valign="top"><?php echo show_estoque($row);?></td> 
        <?php if($listOptions['inscricao']) echo '<td valign="top">'.link_inscritos($row['insc_ttl'], $row['insc_new'], $edicao).'</td>';?> 
        <?php if($listOptions['destaque']) echo '<td valign="top">'.link_destaque($row['destaque']).'</td>';?>      
                
        <td valign="top"><?php echo link_status('-', $row['status']);?></td>
        <td class=""></td>
      </tr>
      <?php 
	  	$i++;
	  	endforeach;
	  endif;
	  ?>
      
     
      </tbody>
    </table>
    </form>
    