
  <div class="content">
    
    	
    <table width="100%" border="1" class="table  table-striped">
          <!-- <thead>
            <tr>
              <th scope="col">Produto</th>
              <th scope="col">Qtd</th>
              <th scope="col">Desconto</th>
              <th scope="col">Valor</th>
            </tr>
          </thead> -->
          <tbody>
            <?php 
            foreach ($answers as $opt):
            ?>
          	<tr>
          	  <td>
                <strong><?php echo $opt['titulo'] ?></strong>
                <br>
                <?php echo $opt['resumo'] ?>
              </td>
          	  <td><?php echo $opt['valor'] ?></td>
          	</tr>          	
            <?php endforeach; ?>
          </tbody>
        </table>

        
        
        
  </div><!-- .content -->
  <?php

  ?>