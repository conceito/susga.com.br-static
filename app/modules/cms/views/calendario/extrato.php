<div id="accordion">
	
    <?php
    if(isset($extrt) && $extrt):
	
	foreach($extrt as $id => $row):
	?>


	<h3><a href="#">Pedido # <?php echo $row['id'];?></a></h3>
  <div class="content">
    
    	
    <table width="100%" border="1" class="table  table-striped table-condensed">
          <thead>
            <tr>
              <th scope="col">Produto</th>
              <th scope="col">Qtd</th>
              <th scope="col">Desconto</th>
              <th scope="col">Valor</th>
            </tr>
          </thead>
          <tbody>
          	<tr>
          	  <td><?php echo $row['titulo'];?></td>
          	  <td><?php echo $row['quantidade'];?></td>
              <td><?php echo $row['descontos'];?></td>
          	  <td>R$ <?php echo number_format($row['valor_total'], 2, ',', '.');?></td>
          	</tr>          	
          </tbody>
        </table>

        
	  <table width="100%" border="1" class="table  table-striped table-condensed">
        <thead>
          <tr>
            <th colspan="2" scope="col">Detalhes</th>
          </tr>
        </thead>
        <tbody>
       	  <tr>
       	    <td><strong>Método</strong></td>
       	    <td><?php echo $row['metodo'];?></td>
   	      </tr>          
       	  <tr>
       	    <td><strong>Tipo de pagamento</strong></td>
       	    <td><?php echo $row['tipo_pagamento'];?></td>
   	      </tr>
       	  <tr>
       	    <td><strong>Código da transação</strong></td>
       	    <td><?php echo $row['transacao_id'];?></td>
   	      </tr>
       	  <tr>
       	    <td><strong>Parcelas</strong></td>
       	    <td><?php echo $row['parcelas'];?></td>
   	      </tr>
       	  <tr>
       	    <td><strong>Valor final</strong></td>
       	    <td>R$ <?php echo number_format($row['valor_total'], 2, ',', '.');?></td>
   	      </tr>
       	  <tr>
       	    <td><strong>Descontos</strong></td>
       	    <td><?php echo $row['descontos'];?></td>
   	      </tr>
       	  <tr>
       	    <td><strong>Tipo de frete</strong></td>
       	    <td><?php echo $row['tipo_frete'];?></td>
   	      </tr>
       	  <tr>
       	    <td><strong>Valor do frete</strong></td>
       	    <td><?php echo $row['valor_frete'];?></td>
   	      </tr>
        </tbody>
      </table>
      
      <?php
      if(isset( $row['historico']) && $row['historico']):
	  ?>
      <table width="100%" border="1" class="table  table-striped table-condensed">
        <thead>
          <tr>
            <th colspan="3" scope="col">Histórico de atualizações</th>
          </tr>
        </thead>
        <tbody>
        <?php
        foreach($row['historico'] as $his):
		?>
       	  <tr>
       	    <td><?php echo formaPadrao($his['data']);?></td>
       	    <td><?php echo $his['hora'];?></td>
       	    <td><?php echo $his['anotacao'];?></td>
       	  </tr>
       	 <?php
         endforeach;
		 ?>
        </tbody>
    </table>
    <?php
    endif;
	?>
    
    <table width="100%" border="1" class="table  table-striped table-condensed">
        <thead>
          <tr>
            <th scope="col">Comprovante</th>
          </tr>
        </thead>
        <tbody>
       
       	  <tr>
       	    <td>
			<?php if($row['comprovante']):?>
			<a href="<?php echo base_url().$this->config->item('upl_arqs').'/'.$row['comprovante']['nome'];?>" target="_blank">abrir em nova janela</a>
			<br>
			<img src="<?php echo base_url().$this->config->item('upl_arqs').'/'.$row['comprovante']['nome'];?>" alt="" style="width:100%; height:auto;">
			<?php endif;?>
			</td>       	   
       	  </tr>
       	 
        </tbody>
    </table>
        
        
  </div><!-- .content -->
  <?php
  endforeach;
  
  endif;
  ?>
    
   
    
</div><!-- #accondion -->