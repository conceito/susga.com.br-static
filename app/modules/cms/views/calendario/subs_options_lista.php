<?php $baseurl = base_url();
$pagination = $this->pagination->create_links();
?>

      
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl" class="table table-striped">
      <thead>
      <tr>
        <th scope="col" class="cb">&nbsp;</th>

        <th scope="col">Título</th>
         <th scope="col">Início</th>
          <th scope="col">Término</th>
               
        <th scope="col" class="data">Status</th>
        <th scope="col" class=""></th>
      </tr>
      </thead>
      <tbody>
      
      <tr class="local-search">
        
        <td><a href="<?php echo cms_url($linkFilter);?>" title="limpar filtros" style="display: block; margin: 7px 0 0 10px;"><i class="icon-ban-circle icon-white"></i></a></td>
        
 
        
        <td>
        <?php echo local_search(array(
            'campo' => 'titulo', 
            'type'  => 'like',
            'class' => '', 
            'style' => 'width:85%'
        ));?>
        </td>
        
        <td style="">
        <?php echo local_search(array(
            'campo' => 'dt_ini', 
            'type'  => 'date',
            'class' => 'input-small',
            'id'    => 'dt1',
            'style' => 'width:70px'
        ));?>
        </td>
        
        <td style="">
        <?php echo local_search(array(
            'campo' => 'dt_fim', 
            'type'  => 'date',
            'class' => 'input-small',
            'id'    => 'dt2',
            'style' => 'width:70px'
        ));?>
        </td>
                
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

        <td valign="top" class="td-cb"><input name="cb[]" type="checkbox" value="<?php echo $row['id'];?>" class="cb" /></td>
       
        <td valign="top"><?php echo barra_edicao($edicao, $row['titulo']);?>
        <!--resumo-->
            <div class="resumo-ler"><?php echo $row['resumo'];?></div>
        </td> 
        <td valign="top"><?php echo formaPadrao($row['dt_ini']);?></td>
        <td valign="top"><?php echo formaPadrao($row['dt_fim']);?></td> 
           
                
        <td valign="top"><?php echo link_status('', $row['status']);?></td>
        
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
    