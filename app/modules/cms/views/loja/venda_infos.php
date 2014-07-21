<?php
/******************************************
*  Template: detalhe da venda 
*  Controller: cms/loja/vendaEdita
*/

$baseurl = base_url();

$ex = $extr['extrato'];
?>

<table width="100%" border="0" class="table table-hover">
  <tr>
    <th scope="row" style="width:150px; text-align:right">Nº da venda</th>
    <td><?php echo $ex['id'];?></td>
  </tr>
  <tr>
    <th scope="row" style="width:150px; text-align:right">Fatura</th>
    <td>
	<?php if(strlen($ex['fatura']) < 4):?>
    <a href="#" class="btn btn-small gerar-fatura" data-id="<?php echo $ex['id'];?>">Gerar fatura</a>
    <?php else:?>
    <a href="<?php echo cms_url('cms/loja/imprimir_fatura/extrato:'.$ex['id']);?>" target="_blank"><?php echo $ex['fatura']; endif;?></a>
    </td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Método</th>
    <td><?php echo $ex['metodo'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Tipo de pagamento</th>
    <td><?php echo $ex['tipo_pagamento'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Código da transação</th>
    <td><?php echo $ex['transacao_id'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Parcelas</th>
    <td><?php echo $ex['parcelas'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Total da venda</th>
    <td>R$ <?php echo moneyBR($ex['valor_total']);?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Descontos</th>
    <td>R$ <?php echo  moneyBR( (float)$ex['descontos']);?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Tipo de frete</th>
    <td><?php echo $ex['tipo_frete'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Valor do frete</th>
    <td><?php echo $ex['valor_frete'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Data/Hora do pedido</th>
    <td><?php echo formaPadrao($ex['data']);?> / <?php echo $ex['hora'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Situação da venda</th>
    <td><?php echo $ex['anotacao'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Comprovante</th>
    <td><?php if($ex['comprovante']):?>
			<a href="<?php echo base_url().$this->config->item('upl_arqs').'/'.$ex['comprovante']['nome'];?>" target="_blank">abrir em nova janela</a>
			<br>
			<img src="<?php echo base_url().$this->config->item('upl_arqs').'/'.$ex['comprovante']['nome'];?>" alt="" style="width:100%; height:auto;">
			<?php endif;?></td>
  </tr>
  
</table>