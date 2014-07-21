<table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl_tab">
      <thead>
      <tr>
       
        <th scope="col">Agendamento</th>
         <th scope="col">Assunto</th>
        <th scope="col">Quantidade</th>
        <th scope="col" class="data">Data</th>
      </tr>
      </thead>
      <tbody>
      
      <?php 
	  if(! $agenda):
	  	echo '<tr><td colspan="3"><strong>Não existem registros.</strong></td></tr>';
	  else:
	  	$i = 0;
		foreach($agenda as $row):
			$id = $row['id'];
			// linhas pares-impares
			if($i%2 == 0)$zebra = 'even';
			else $zebra = 'odd';
			$linkMessage = cms_url('cms/paginas/mensagemForm/id:'.$id);
		 ?>
          <tr class="<?php echo $zebra;?>" id="<?php echo $id;?>">
      	
        <td valign="top"><?php echo $i+1?>º) <?php echo $row['titulo'];?>
        <br />
        	<a href="<?php echo cms_url('cms/news/edita/co:'.$co.'/id:'.$news['id'].'/tab:4');?>" title="ver estatísticas" class="">ver estatísticas</a>
            <!--<div class="opcoes">
                    
            <span class="apagar"><a href="#" title="Apagar este item" class="apagar-item">apagar</a></span>
            <span class="confirma"><a href="#" class="nao-item">não</a> / <a href="#" class="sim-item">sim</a></span>
            </div>-->
        </td>
        
        <td valign="top"><?php echo $row['mensagem']['titulo'];?></td>
        <td valign="top"><?php echo $row['quant'];?></td>
        <td valign="top"><?php echo news_status($row['data'], $row['status']);?></td>
      </tr>
      
         
          <?php 
	  	$i++;
	  	endforeach;
	  endif;
	  ?>
      
    
      </tbody>
    </table>
 
   
    <iframe src="<?php echo base_url().'ci_itens/newsletter_batch.php?idNews='. $news['id'].'&idAge='.$agenda[0]['id'].'&co='.$co;?>" frameborder="0" width="100%" height="300"></iframe>