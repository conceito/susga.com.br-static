<?php
/******************************************
*  Template: detalhe do cliente 
*  Controller: cms/loja/vendaEdita
*/

$baseurl = base_url();

$ex = $extr['usuario'];
?>

<table width="100%" border="0" class="table table-hover">
  <tr>
    <th scope="row" style="width:150px; text-align:right">Nome</th>
    <td><?php echo $ex['nome'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">E-mail</th>
    <td><?php echo $ex['email'];?> <a href="<?php echo cms_url('cms/usuarios/mensagemForm/id:'.$ex['id']);?>" class="enviar-mensagem nyroModal" target="_blank" title="clique para enviar mensagem" style="position:absolute; display:inline; margin-top:5px; margin-left:10px;">enviar</a></td>
  </tr>
  <?php if(strlen($ex['razao']) > 1):?>
  <tr>
    <th scope="row" style="text-align:right;">Razão social</th>
    <td><?php echo $ex['razao'];?></td>
  </tr>
  <?php endif;?>
  <?php if(strlen($ex['fantasia']) > 1):?>
  <tr>
    <th scope="row" style="text-align:right;">Nome fantasia</th>
    <td><?php echo $ex['fantasia'];?></td>
  </tr>
  <?php endif;?>
  <?php if(strlen($ex['cnpj']) > 1):?>
  <tr>
    <th scope="row" style="text-align:right;">CNPJ</th>
    <td><?php echo $ex['cnpj'];?></td>
  </tr>
  <?php endif;?>
  <?php if(strlen($ex['insc_estadual']) > 1):?>
  <tr>
    <th scope="row" style="text-align:right;">Inscrição Estadual</th>
    <td><?php echo $ex['insc_estadual'];?></td>
  </tr>
  <?php endif;?>
  <?php if(strlen($ex['insc_municipal']) > 1):?>
  <tr>
    <th scope="row" style="text-align:right;">Inscrição Municipal</th>
    <td><?php echo $ex['insc_municipal'];?></td>
  </tr>
  <?php endif;?> 
  <tr>
    <th scope="row" style="text-align:right;">Nascimento</th>
    <td><?php echo $ex['nasc'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">CEP</th>
    <td><?php echo $ex['cep'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Logradouro</th>
    <td><?php echo $ex['logradouro'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Número / Complemento</th>
    <td><?php echo $ex['num'];?> / <?php echo $ex['compl'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Cidade</th>
    <td><?php echo $ex['cidade'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Bairro</th>
    <td><?php echo $ex['bairro'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">UF</th>
    <td><?php echo $ex['uf'];?></td>
  </tr>
  <tr>
    <th scope="row" style="text-align:right;">Telefone</th>
    <td><?php echo $ex['tel1'];?> / <?php echo $ex['tel2'];?></td>
  </tr>
  <?php if(strlen($ex['rg']) > 1):?>
  <tr>
    <th scope="row" style="text-align:right;">RG</th>
    <td><?php echo $ex['rg'];?></td>
  </tr>
  <?php endif;?>
  <?php if(strlen($ex['cpf']) > 1):?>
  <tr>
    <th scope="row" style="text-align:right;">CPF</th>
    <td><?php echo $ex['cpf'];?></td>
  </tr>
  <?php endif;?>
</table>