<table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl_tab">
      <thead>
      <tr>
       
        <th scope="col">Nome</th>
         <th scope="col">E-mail</th>
        <th scope="col">IP</th>
       
        <th scope="col">Hora</th>
        <th scope="col" class="data">Data</th>
      </tr>
      </thead>
      <tbody>
      
      <?php 
	  if(! $comments):
	  	echo '<tr><td colspan="3"><strong>Não existem registros.</strong></td></tr>';
	  else:
	  	$i = 0;
		foreach($comments as $row):
			$id = $row['id'];
			// linhas pares-impares
			if($i%2 == 0)$zebra = 'even';
			else $zebra = 'odd';
			$linkMessage = cms_url('cms/paginas/mensagemForm/id:'.$id);
		 ?>
          <tr class="<?php echo $zebra;?>" id="<?php echo $id;?>">
      	
        <td valign="top"><a href="#" title="Editar este item" class="edit"><?php echo $row['nome'];?></a>
        	<div class="opcoes">
            <span class="editar"><a href="#" title="Editar este item" class="edit-opc">ver comentário</a> |</span>             
            <span class="apagar"><a href="#" title="Apagar este item" class="apagar-item">apagar</a></span>
            <span class="confirma"><a href="#" class="nao-item">não</a> / <a href="#" class="sim-item">sim</a></span>
            </div>
        </td>
         <td valign="top"><a href="<?php echo $linkMessage;?>" class="enviar-mensagem nyroModal" target="_blank" title="clique para enviar mensagem">enviar</a></td>
        <td valign="top"><?php echo $row['ip'];?></td>
        <td valign="top"><?php echo $row['hora'];?></td>
        <td valign="top"><?php echo link_status($row['data'], $row['status']);?></td>
      </tr>
      <tr class="<?php echo $zebra;?>" id="comment-<?php echo $id;?>">      	
        <td colspan="5" valign="top"><div class="comment-div cd-<?php echo $id;?>"><textarea name="comment-<?php echo $id;?>" cols="" rows=""><?php echo $row['comentario'];?></textarea></div></td>
        </tr>
         
          <?php 
	  	$i++;
	  	endforeach;
	  endif;
	  ?>
      
    
      </tbody>
    </table>