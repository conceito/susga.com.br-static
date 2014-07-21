
	
    <div class="status-display">
    	<a href="#" class="btn img-circle"><i class="icon-shopping-cart"></i> 
        <?php if($itens == 0):?>
        <span class="display-label">Meu carrinho está vazio</span>
        <?php else:?>
        <span class="display-label">Meu carrinho: <?php echo $itens;?> ite<?php echo ($itens==1)?'m':'ns';?></span>
        <?php endif;?>
        </a>
    </div><!-- .status-display -->
    
    <div class="cart-panel">
    	
        <div class="cart-prods-wrap">
        	
            <?php if(! $cart): // carrinho vazio! ?>
            <!-- @tip: se não existem produtos exibe -->
            <p class="empty">Adicione produtos ao seu carrinho.</p>
            <?php else: ?>
            
            <table class="cart-prods-grid">
              
              <?php foreach($cart as $p):?>
              
              <tr class="prod-row">
                <td class="thumb">
                	<a href="<?php echo site_url($p['uri']);?>"><img src="<?php echo $p['thumb'];?>" alt=""></a>
                </td>
                <td class="desc"><a href="<?php echo site_url($p['uri']);?>"><?php echo $p['name'];?></a> <span class="qty">(<?php echo $p['qty'];?>)</span>
                <?php  // se existem opções... exibe
					if(isset($p['options']) && $p['options']):?>
                <ul class="unstyled options">
                	<?php foreach($p['options'] as $opt):?>
                	<li><?php echo $opt['opcao']['titulo'].' '.$opt['valor']['titulo'];?></li>
                    <?php endforeach;?>
                </ul>
                <?php endif;?>
                </td>
                <td class="valor">R$ <?php echo formaBR($p['price']);?></td>
              </tr>
              
              <?php endforeach;?>              
                            
            </table>
            
            
        </div><!-- .cart-prods-wrap -->
        
        
        <div class="cart-subtotal-wrap">
        	
            <table class="cart-subtotal">
              <tr>
                <td>Subtotal:</td>
                <td class="valor">R$ <?php echo formaBR($total);?></td>
              </tr>
            </table>
            
            <a href="<?php echo site_url('loja/carrinho');?>" class="btn btn-info finalizar"><i class="icon-shopping-cart icon-white"></i> Finalizar compra</a>
            <?php endif;?>
        </div><!-- .cart-subtotal-wrap -->
    	
    </div><!-- .cart-panel -->
    