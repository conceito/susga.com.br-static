<?php
/*********************************************
*	Template: modelo de cada fatura
*	Controller: cms/loja/imprimir_fatura
*/

$extrato   = $f['extrato'];
$historico = $f['historico'];
$usuario   = $f['usuario'];
$produtos  = $f['produtos'];
$descontos = $f['descontos'];
?>

<div style="page-break-after: <?php echo $pageBreakAfter?>;">
  <h1>Fatura: <?php echo $extrato['fatura'];?></h1>
  <div class="div1">
    <table width="100%">
      <tbody>
        <tr>
          <td>
		  <?php echo $this->config->item('title');?><br>           
          <?php echo nl2br($loja['loja52_dados']);?><br> 
		  <?php echo base_url();?>
          </td>
          <td align="right" valign="top"><table>
              <tbody>
                <tr>
                  <td><b>Data da venda:</b></td>
                  <td><?php echo formaPadrao($extrato['data']);?> - <?php echo $extrato['hora'];?></td>
                </tr>
                <tr>
                  <td><b>Nº da fatura:</b></td>
                  <td><?php echo $extrato['fatura'];?></td>
                </tr>
                
                <tr>
                  <td><b>Nº da venda:</b></td>
                  <td><?php echo $extrato['id'];?></td>
                </tr>
              </tbody>
            </table></td>
        </tr>
      </tbody>
    </table>
  </div>
  <table class="address">
    <tbody>
      <tr class="heading">
        <td width="50%"><b>Para</b></td>
        <td width="50%"><b>Endereço</b></td>
      </tr>
      <tr>
        <td valign="top"><?php echo $usuario['nome'];?><br>
        	<?php echo $usuario['email'];?><br>
       
			<?php if(strlen($usuario['razao']) > 1):?>
            <?php echo $usuario['razao'];?><br>
            <?php endif;?>
            
            <?php if(strlen($usuario['fantasia']) > 1):?>
            <?php echo $usuario['fantasia'];?><br>
            <?php endif;?>
            
            <?php if(strlen($usuario['cnpj']) > 1):?>
            <?php echo $usuario['cnpj'];?><br>
            <?php endif;?>
            
            <?php if(strlen($usuario['insc_estadual']) > 1):?>
            Inscrição Estadual: <?php echo $usuario['insc_estadual'];?><br>
            <?php endif;?>
            
            <?php if(strlen($usuario['insc_municipal']) > 1):?>
            Inscrição Municipal: <?php echo $usuario['insc_municipal'];?><br>
            <?php endif;?>
            
			<?php if(strlen($usuario['rg']) > 1):?>
            RG: <?php echo $usuario['rg'];?><br>
            <?php endif;?>
            
            <?php if(strlen($usuario['cpf']) > 1):?>
            CPF: <?php echo $usuario['cpf'];?><br>
            <?php endif;?>
            
            <?php echo $usuario['tel1'];?> / <?php echo $usuario['tel2'];?>
        
      
        </td>
        <td valign="top">
        <?php echo $usuario['cep'];?><br>
		<?php echo $usuario['logradouro'];?><br>
		<?php echo $usuario['num'];?> / <?php echo $usuario['compl'];?><br>
		<?php echo $usuario['cidade'];?> - 
		<?php echo $usuario['bairro'];?> - <?php echo $usuario['uf'];?>
        
        </td>
      </tr>
    </tbody>
  </table>
  <table class="product">
    <tbody>
      <tr class="heading">
        <td><b>Produto</b></td>
        <td align="right"><b>Quantidade</b></td>
        <td align="right"><b>Preço unitário</b></td>
        <td align="right"><b>Sub-Total</b></td>
      </tr>
      
      <?php foreach($produtos as $items):?>
      <tr>
        <td><?php echo $items['conteudo_titulo']; ?> <br>
        	
            <?php if ($items['opcoes']): ?>

				<small>
				<?php foreach ($items['opcoes'] as $opt): ?>

                    <strong> - <?php echo $opt['opcao']['titulo']; ?>:</strong> 
					<?php echo $opt['valor']['titulo']; ?> 
                    <?php if(strlen($opt['valor']['codigo']) > 0): ?>
                    	(<?php echo $opt['valor']['codigo']; ?>)
                    <?php endif;?>
                    
                    <br />

                <?php endforeach; ?>
				</small>

			<?php endif;?>
        
        </td>        
        <td align="right"><?php echo $items['quantidade']; ?></td>
        <td align="right">R$<?php echo number_format($items['valor'], 2, ',', '.'); ?></td>
        <td align="right">R$<?php echo number_format($items['subtotal'], 2, ',', '.'); ?></td>
      </tr>
      <?php endforeach;?>
      
      <?php ///////////////////////////   SE EXISTIR EXIBE DESCONTOS /////////////////////////
		if($descontos):
			foreach($descontos as $des):
		?>
      
      <tr>
        <td align="right" colspan="3"><b><?php echo $des['titulo'];?> (<?php echo $des['opcao'];?>)</b></td>
        <td align="right">
		<?php if($des['regra'] == '%'):?>
      
        <?php echo $des['valor'];?>% -
        
       <?php else: ?>
      
        R$ <?php echo moneyBR($des['valor']);?> -
      
      <?php endif;?>
      </td>
      </tr>
      <?php 
			endforeach;			
		endif;?>
      
      <tr>
        <td align="right" colspan="3"><b>Total:</b></td>
        <td align="right">R$<?php echo moneyBR($extrato['valor_total']);?></td>
      </tr>
    </tbody>
  </table>
</div>
