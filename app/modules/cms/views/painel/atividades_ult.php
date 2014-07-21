<div id="painel-ult-atividades">
     	<h2>Últimas atividades </h2>
        
        <div class="painel-content"><!--painel-content-->
        
        <table width="96%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <th align="left" scope="col">data</th>
            <th align="left" scope="col">quem</th>
            <th align="left" scope="col">oquê</th>
          </tr>
          <?php
		  $i = 0;
          foreach($ativs as $atv):
		  	
			$class = ($i%2 == 0) ? 'even' : 'odd';
			$data = $atv['data'];
			$quem = $atv['quem'];
			$oque = $atv['oque'];
		  ?>
          
          <tr class="<?php echo $class;?>">
            <td><?php echo $data;?></td>
            <td><?php echo $quem;?></td>
            <td><?php echo $oque;?></td>
          </tr>
          <?php
		  $i++;
          endforeach;
		  ?>
     
        </table>
        <br />
		<a href="#" class="ml10"><span class="courier">&gt;</span> ver todas</a>
        <br />
<br />
		 </div><!--painel-content fim-->
        </div>