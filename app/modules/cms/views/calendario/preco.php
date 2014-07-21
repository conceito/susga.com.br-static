
<?php echo validation_errors(); ?>

<div class="subpanel-50-50 clearfix ">
	
    
    <?php //////// o módulo 'loja' usa um valor base com descontos
	if(isset($row['valor_base'])):
	?>
    
    <label for="valor_base" class="lb-full">Preço original</label>
    <div class="control-group box"><br />	
    R$ <input name="valor_base" id="valor_base" type="text" class="input-curto type-valor" value="<?php echo set_value('valor_base', $row['valor_base']);?>" />
		
    </div><!-- .control-group -->
    
	<label class="lb-full" for="txt">Promoção</label>
    
    <?php // o módulo 'calendario' usa apenas a tabela de preços e descontos
	else:?>
    <label class="lb-full" for="txt">Preço</label>
    <?php endif; ?>
	
    <div class="preco-group">
	
		<input type="hidden" name="precos_remove" class="toremove" value="">
    
    	<?php
        //// inicia looping pelos preços
		$precos_ttl = count($precos['precos']);
		for($x = 0; $x < $precos_ttl; $x++):
			
			$id    = $precos['precos'][$x]['id'];
			$valor = $precos['precos'][$x]['valor'];
			$regra = $precos['precos'][$x]['regra'];
			$data  = $precos['precos'][$x]['data'];
		?>
        <div class="group-option">
        	<div class="id">#<?php echo $x+1;?></div><input type="hidden" name="preco_opt_<?php echo $x;?>[]" value="<?php echo $id;?>" />
            
            <div class="td td-1"> <div class="lb-mo">Valor</div>
            <input name="preco_opt_<?php echo $x;?>[]" type="text" class="input-mini type-valor" value="<?php echo $valor;?>" />
            </div>
            
            <div class="td td-2"> <div class="lb-mo">Tipo de regra</div>
            <select name="preco_opt_<?php echo $x;?>[]" class="input-combo">
              <option value="ate-dia" <?php echo set_select('preco_opt_'.$x, 'ate-dia', ($regra=='ate-dia'));?>>até a data</option>
              <option value="no-dia" <?php echo set_select('preco_opt_'.$x, 'no-dia', ($regra=='no-dia'));?>>no dia</option>
            </select>
            </div>
            
            <div class="td td-3"> <div class="lb-mo">Condição</div>
            <input name="preco_opt_<?php echo $x;?>[]" type="text" class="input-mini type-data" value="<?php echo $data;?>" />
            </div>       
        	
            <div class="td td-4">
            <?php if($x + 1 == $precos_ttl):?>
        	<a href="#" class="add-multi"><i class="icon-plus"></i></a>
            <?php else:?>
            <a href="#" class="remove-multi"><i class="icon-trash"></i></a>
            <?php endif;?>
            </div>
            
        </div><!-- .group-option -->
        <?php
        endfor;
		?>
        
        
        
        
    
    </div><!-- .preco-group -->
    
    
    
	
	
    <br />
		
</div><!-- .panel-left -->


<div class="subpanel-50-50 clearfix">

	
	
	<label for="rel" class="lb-full">Cupom de desconto</label>
	
	<div class="cupom-group">
    	
		<input type="hidden" name="cupons_remove" class="toremove" value="">
		
        <?php
        //// inicia looping pelos cupons
		$cupons_ttl = count($precos['cupons']);
		for($x = 0; $x < $cupons_ttl; $x++):
			
			$id    = $precos['cupons'][$x]['id'];
			$valor = $precos['cupons'][$x]['valor'];
			$regra = $precos['cupons'][$x]['regra'];
			$verificador  = $precos['cupons'][$x]['verificador'];
			
			$typevalor = ($regra=='%') ? 'type-valor' : 'type-percentual';
			
		?>
        <div class="group-option">
        	<div class="id">#<?php echo $x+1;?></div><input type="hidden" name="cupom_opt_<?php echo $x;?>[]" value="<?php echo $id;?>" />
            
            <div class="td td-1"> <div class="lb-mo">Valor</div>
            <input name="cupom_opt_<?php echo $x;?>[]" type="text" class="input-mini regra-rule <?php echo $typevalor;?>" value="<?php echo $valor;?>" />
            </div>
            
            <div class="td td-2"> <div class="lb-mo">Tipo</div>
            <select name="cupom_opt_<?php echo $x;?>[]" class="input-combo type-regra">
              <option value="%" <?php echo set_select('cupom_opt_'.$x, '%', ($regra=='%'));?>>%</option>
              <option value="R$" <?php echo set_select('cupom_opt_'.$x, 'R$', ($regra=='R$'));?>>R$</option>
            </select>
            </div>
            
            <div class="td td-3"> <div class="lb-mo">Código</div>
            <input name="cupom_opt_<?php echo $x;?>[]" type="text" class="input-mini type-codigo" value="<?php echo $verificador;?>" />
            </div>       
        	
            <div class="td td-4">
        	<?php if($x + 1 == $cupons_ttl):?>
        	<a href="#" class="add-multi"><i class="icon-plus"></i></a>
            <?php else:?>
            <a href="#" class="remove-multi"><i class="icon-trash"></i></a>
            <?php endif;?>
            </div>
            
        </div><!-- .group-option -->
        <?php
        endfor;
		?>
        
        
        
    
    </div><!-- .cupom-group -->
	
	
	
	



</div><!-- .panel-right -->       
              
         


