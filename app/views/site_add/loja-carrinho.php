
<ul class="breadcrumb">
  
    <li>1. Meu carrinho <span class="divider">&gt;</span></li>
    <li class="active">2. Identificação <span class="divider">&gt;</span></li>
    <li class="active">3. Entrega <span class="divider">&gt;</span></li>
    <li class="active">4. Pagamento <span class="divider">&gt;</span></li>
    <li class="active">5. Confirmação</li>
  
</ul>

<?php echo form_open('loja/atualiza_carrinho'); ?>

<table class="table table-striped" id="meu-carrinho">
<thead>
<tr>
  <th>Quantidade</th>
  <th>Produtos</th>
  <th class="valor">Valor</th>
  <th class="valor">Sub-Total</th>
  <th></th>
</tr>
</thead>
<tbody>
<?php $i = 1;

	if(empty($cart)):
	?>
    <tr class="total">
    <td colspan="5" style="text-align:center">Adicione produtos ao seu carrinho.</td>
    </tr>
    <?php
	endif;


	foreach ($cart as $items): ?>

	

	<tr>
	  <td>
	  <?php echo form_hidden($i.'[rowid]', $items['rowid']); ?>
	  <?php echo form_input(array('name' => $i.'[qty]', 'value' => $items['qty'], 'type' => 'number', 'maxlength' => '3', 'size' => '5', 'min' => 1, 'max' => $this->config->item('max_per_prod'), 'class' => 'qty' )); ?>	
      </td>
	  <td>
		<?php 
			// existe thumb
			if(strlen($items['thumb']) > 5){
			?>
            <a href="<?php echo site_url($items['uri']);?>" class="thumb"><img src="<?php echo $items['thumb'];?>" alt=""></a>
            <?php
			}
			echo '<div class="prod-desc">';
			echo $items['name']; ?>

			<?php if ($this->cart->has_options($items['rowid']) == TRUE): ?>

				<p>
				<?php foreach ($this->cms_loja->get_options_by_array($this->cart->product_options($items['rowid'])) as $opt): ?>

                    <strong><?php echo $opt['opcao']['titulo']; ?>:</strong> <?php echo $opt['valor']['titulo']; ?><br />

                <?php endforeach; ?>
				</p>

			<?php endif;
			echo '</div>';
			?>

	  </td>
	  <td class="valor"><?php echo formaBR($items['price']); ?></td>
	  <td class="valor"><?php echo formaBR($items['subtotal']); ?></td>
      <td class="remover"><a href="<?php echo site_url('loja/remover_produto/'.$items['rowid']);?>"><i class="icon-trash"></i></a></td>
	</tr>

<?php $i++; ?>

<?php endforeach; ?>
</tbody>
<tfoot>

<?php /////////////////////// SE EXISTE DESCONTOS NO CARRINHO /////////////
if($cart_off):
?>
<tr class="descontos">
  <td colspan="2"></td>
  <td class="valor">Descontos: <span class="cart-desconto-label">(<?php echo $cart_off['titulo'];?>)</span></td>
  <td class="valor">R$ <?php echo formaBR($cart_off['valor']); ?></td>
  <td></td>
</tr>
<?php endif;
////////////////////////// FIM DESCONTOS //////////////////////////////?>

<?php /////////////////////// CAMPO PARA INSERIR CUPOM /////////////////
?>
<tr class="cupom">
  <td colspan="2"></td>
  <td class="valor">
    <div class="input-append">
    <input value="<?php if(isset($cart_cupom['verificador'])) echo $cart_cupom['verificador'];?>" type="text" class="input-medium" name="cupom" id="cupom" placeholder="entre com cupom válido" />
    <button class="btn" type="submit">OK</button>
    </div>
  
  </td>
  <td class="valor"> 
  <?php if(isset($cart_cupom['valor'])){
	if($cart_cupom['regra'] == 'R$') { echo 'R$ '.formaBR($cart_cupom['valor']);}   	
	if($cart_cupom['regra'] == '%') {echo $cart_cupom['valor'].'%';}
  } ?></td>
  <td></td>
</tr>
<?php
////////////////////////// FIM CUPOM //////////////////////////////?>

<tr class="total">
  <td colspan="2"><button class="btn btn-small" type="submit"><i class="icon-refresh"></i> Atualizar quantidades</button></td>
  <td class="valor"><strong>Total</strong></td>
  <td class="valor">R$ <?php echo formaBR($this->cms_loja->cart_total()); ?></td>
  <td></td>
</tr>
</tfoot>
</table>


<div class="form-actions">
	<a href="<?php echo site_url('loja');?>" class="btn"><i class="icon-arrow-left"></i> Escolher mais produtos</a>
    
    <?php /////// Se pode fechar o pagamento ////////////
	if($this->cms_loja->cart_rules('continuar')):?>
    
    <a href="<?php echo site_url('loja/identificacao');?>" class="btn btn-primary pull-right">Continuar <i class="icon-arrow-right icon-white"></i></a>
    
    <?php else: ?>
    <a href="#" class="btn btn-primary pull-right disabled">Continuar <i class="icon-arrow-right icon-white"></i></a>
	<?php endif;?>

  
</div>

<?php echo form_close();?>