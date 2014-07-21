<?php
/******************************************
*  Template: produtos vendidos 
*  Controller: cms/loja/vendaEdita
*/

$baseurl = base_url();

$ex = $extr['produtos'];

?>

<table class="table table-striped" id="meu-carrinho">
<thead>
<tr>
  <th></th>
  <th>Quantidade</th>
  <th>Produtos</th>
  <th class="valor">Valor</th>
  <th class="valor">Sub-Total</th>
  
</tr>
</thead>
<tbody>
<?php $i = 1;

	if(empty($ex)):
	?>
    <tr class="total">
    <td colspan="5" style="text-align:center">Nenhum produto foi adicionado!</td>
    </tr>
    <?php
	else:

	foreach ($ex as $items): ?>	

	<tr>
      <td>#<?php echo $i;?></td>
	  <td>
	  <?php echo $items['quantidade']; ?>	
      </td>
	  <td>
		<a href="<?php echo cms_url('cms/loja/edita/co:'.$modulo['id'].'/id:'.$items['conteudo_id']);?>"><?php echo $items['conteudo_titulo']; ?></a>
        	<?php if(strlen($items['more']['codigo']) > 0): ?>
                (<?php echo $items['more']['codigo']; ?>)
            <?php endif;?>

			<?php if ($items['opcoes']): ?>

				<p>
				<?php foreach ($items['opcoes'] as $opt): ?>

                    <strong><?php echo $opt['opcao']['titulo']; ?>:</strong> 
					<?php echo $opt['valor']['titulo']; ?> 
                    <?php if(strlen($opt['valor']['codigo']) > 0): ?>
                    	(<?php echo $opt['valor']['codigo']; ?>)
                    <?php endif;?>
                    
                    <br />

                <?php endforeach; ?>
				</p>

			<?php endif;
			
			?>

	  </td>
	  <td class="valor"><?php echo number_format($items['valor'], 2, ',', '.'); ?></td>
	  <td class="valor"><?php echo number_format($items['subtotal'], 2, ',', '.'); ?></td>
      
	</tr>

<?php $i++; 
	  endforeach;
	  
	  endif; 
?>
</tbody>
<tfoot>
<?php ///////////////////////////   SE EXISTIR EXIBE DESCONTOS /////////////////////////
if($extr['descontos']):
	foreach($extr['descontos'] as $des):
?>
<tr class="total">
  <td colspan="3"></td>
  <td class="valor"><?php echo $des['titulo'];?> (<?php echo $des['opcao'];?>)</td>
  <td class="valor">
  <?php if($des['regra'] == '%'):?>
  
  	<?php echo $des['valor'];?>% -
    
  <?php else: ?>
  
  	R$ <?php echo moneyBR($des['valor']);?> -
  
  <?php endif;?>
  </td>
  <td></td>
</tr>
<?php 
	endforeach;
	
endif;?>
<tr class="total">
  <td colspan="3"></td>
  <td class="valor"><strong>Total</strong></td>
  <td class="valor">R$ <?php echo moneyBR($extr['extrato']['valor_total']);?></td>
  <td></td>
</tr>
</tfoot>
</table>

