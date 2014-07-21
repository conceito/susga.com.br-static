<?php
/******************************************
*  Template: edição de conteúdo de produto
*  Controller: cms/loja/edita
*/
?>
<?php echo validation_errors(); ?>

<div class="limiter">


<div class="option-panel clearfix">

	<div class="option-btns">
    	<div class="btn-group">
    	<a href="#" class="btn add-new-option"><i class="icon-plus"></i> Adicionar opção</a>
        <a href="#" class="btn add-import-option" title="Importar de outro produto"><i class="icon-arrow-down"></i> Importar</a>
        </div>
    </div><!-- .option-btns -->
    
    <div class="control-group box import-box">
    
	<label for="status" class="lb-full">Pesquisar produto...</label>
	<div class="form-opcoes">    
		<input type="text" class="input-curto" id="search-prod-opt" style="width:94%" />
        <button class="btn import-opt">importar opções</button>
        
        <div class="alert">Aguarde...</div>
        
     </div>
	
	</div><!-- .control-group -->
    
    <div class="option-select">
    	<select name="option" id="option" size="20">
        <?php if($options):
			foreach($options as $row):
			
			$id     = $row['id'];
			$titulo = $row['titulo'];
			$ordem  = $row['ordem'];
			$prod_opt_value = $row['prod_opt_value'];
		?>
        <option value="<?php echo 'option_'.$id;?>" class="opt"><?php echo $titulo;?></option>
        <?php 
			// percorre os valores da opção
			foreach($prod_opt_value as $row):
			$oid     = $row['id'];
			$otitulo = $row['titulo'];
			?>
            <option value="<?php echo 'option_'.$id.'_'.$oid;?>" class="val">— <?php echo $otitulo;?></option>
            <?php
			endforeach;
			
			endforeach;
		endif;?>
        </select>
    </div><!-- .option-select -->

</div><!-- .option-panel -->


<div class="values-panel clearfix">
<?php if($options):
	foreach($options as $row):
	
	$id     = $row['id'];
	$titulo = $row['titulo'];
	$ordem  = $row['ordem'];
	$diminuir = $row['destaque'];
	$prod_opt_value = $row['prod_opt_value'];
	
	
?>
	<div id="option_<?php echo $id;?>" class="option" style="display:none">
    	<input type="hidden" name="option_<?php echo $id;?>" value="<?php echo $id;?>">
        <table width="100%" border="0" class="table">
          <tr>
            <th scope="row">Opção</th>
            <td><input type="text" name="prod_option[<?php echo $id;?>][nome]" class="input-curto nm-opt" value="<?php echo $titulo;?>"></td>
          </tr>
          <tr class="estoque-row">
            <th scope="row">Diminuir estoque</th>
            <td><select name="prod_option[<?php echo $id;?>][diminuir]" class="input-mini">
            <option value="0" <?php echo ($diminuir=='0')?'selected="selected"':'';?>>Não</option>
            <option value="1" <?php echo ($diminuir=='1')?'selected="selected"':'';?>>Sim</option></select>
            </td>
          </tr>
          <tr>
            <th scope="row">Ordem</th>
            <td><input type="text" name="prod_option[<?php echo $id;?>][ordem]" class="input-mini" value="<?php echo $ordem;?>"></td>
            
          </tr>
          <tr>
            <th scope="row">&nbsp;</th>
            <td><a href="#" class="btn btn-success add-new-value"><i class="icon-plus icon-white"></i> Adicionar valor para opção</a> &nbsp;
            <a href="#" class="btn btn-warning remove-option"><i class="icon-minus-sign icon-white"></i> Remover</a></td>
          </tr>
        </table>        
    </div>
<?php 
	/////// percorre os valores da opção ////////////////
		foreach($prod_opt_value as $row):
			$oid        = $row['id'];
			$otitulo    = $row['titulo'];
			$ooption_id = $row['grupo'];
			$oordem     = $row['ordem'];
			$oestoque   = $row['resumo'];
			$ovalor     = $row['txt'];
			$ocodigo    = $row['txtmulti'];
			$opreffix   = $row['tags'];
			$diminuir   = $row['destaque'];
			?>
			<div id="option_<?php echo $id;?>_<?php echo $oid;?>" class="option" style="display:none">
				<input type="hidden" name="option_<?php echo $id;?>_<?php echo $oid;?>" value="<?php echo $oid;?>">
				<table width="100%" border="0" class="table">
				  
				  <tr>
					<th scope="row">Código</th>
					<td><input type="text" name="prod_option[<?php echo $id;?>][prod_opt_value][<?php echo $oid;?>][codigo]" class="input-curto" value="<?php echo $ocodigo;?>"></td>
				  </tr>
				  <tr>
					<th scope="row">Valor da opção</th>
					<td><input type="text" name="prod_option[<?php echo $id;?>][prod_opt_value][<?php echo $oid;?>][titulo]" class="input-curto nm-opt" value="<?php echo $otitulo;?>"></td>
				  </tr>
				  <tr class="estoque-row">
					<th scope="row">Quantidade em estoque</th>
					<td><input type="text" name="prod_option[<?php echo $id;?>][prod_opt_value][<?php echo $oid;?>][estoque]" class="input-mini" value="<?php echo $oestoque;?>"></td>
				  </tr>
                  
				  <tr>
					<th scope="row">Modificador do preço</th>
					<td><select name="prod_option[<?php echo $id;?>][prod_opt_value][<?php echo $oid;?>][preffix]" class="input-mini">
					<option value="+" <?php echo ($opreffix=='+')?'selected="selected"':'';?>>+</option>
                    <option value="-" <?php echo ($opreffix=='-')?'selected="selected"':'';?>>-</option></select>
					</td>
				  </tr>
				  <tr>
					<th scope="row">Valor R$</th>
					<td><input type="text" name="prod_option[<?php echo $id;?>][prod_opt_value][<?php echo $oid;?>][valor]" class="input-mini type-valor" value="<?php echo $ovalor;?>"></td>
				  </tr>
				  <tr>
					<th scope="row">Ordem</th>
					<td><input type="text" name="prod_option[<?php echo $id;?>][prod_opt_value][<?php echo $oid;?>][ordem]" class="input-mini" value="<?php echo $oordem;?>"></td>
				  </tr>
				  <tr>
					<th scope="row">&nbsp;</th>
					<td><a href="#" class="btn btn-warning remove-option"><i class="icon-minus-sign icon-white"></i> Remover</a></td>
				  </tr>
				</table>        
			</div>
			<?php
		
		endforeach;
		
	endforeach;
endif;?>
<script id="tmp-value" type="text/x-handlebars-template">
	<div id="option_{{n}}_{{v}}" class="option">
    	<input type="hidden" name="option_{{n}}_{{v}}" value="novo">
        <table width="100%" border="0" class="table">
          
          <tr>
            <th scope="row">Código</th>
            <td><input type="text" name="prod_option[{{n}}][prod_opt_value][{{v}}][codigo]" class="input-curto" value=""></td>
          </tr>
          <tr>
            <th scope="row">Valor da opção</th>
            <td><input type="text" name="prod_option[{{n}}][prod_opt_value][{{v}}][titulo]" class="input-curto nm-opt" value="Valor da opção #{{v}}"></td>
          </tr>
          <tr class="estoque-row">
            <th scope="row">Quantidade em estoque</th>
            <td><input type="text" name="prod_option[{{n}}][prod_opt_value][{{v}}][estoque]" class="input-mini" value="0"></td>
          </tr>		  
          <tr>
            <th scope="row">Modificador do preço</th>
            <td><select name="prod_option[{{n}}][prod_opt_value][{{v}}][preffix]" class="input-mini">
            <option value="+" selected="selected">+</option><option value="-">-</option></select>
            </td>
          </tr>
          <tr>
            <th scope="row">Valor R$</th>
            <td><input type="text" name="prod_option[{{n}}][prod_opt_value][{{v}}][valor]" class="input-mini type-valor" value="0.00"></td>
          </tr>
          <tr>
            <th scope="row">Ordem</th>
            <td><input type="text" name="prod_option[{{n}}][prod_opt_value][{{v}}][ordem]" class="input-mini" value="0"></td>
          </tr>
          <tr>
            <th scope="row">&nbsp;</th>
            <td><a href="#" class="btn btn-warning remove-option"><i class="icon-minus-sign icon-white"></i> Remover</a></td>
          </tr>
        </table>        
    </div>
</script>

<script id="tmp-option" type="text/x-handlebars-template">
	<div id="option_{{n}}" class="option">
    	<input type="hidden" name="option_{{n}}" value="novo">
        <table width="100%" border="0" class="table">
          <tr>
            <th scope="row">Opção</th>
            <td><input type="text" name="prod_option[{{n}}][nome]" class="input-curto nm-opt" value="Opção #{{n}}"></td>
          </tr>
		  <tr class="estoque-row">
            <th scope="row">Diminuir estoque</th>
            <td><select name="prod_option[{{n}}][diminuir]" class="input-mini">
            <option value="0" selected="selected">Não</option><option value="1">Sim</option></select>
            </td>
          </tr>
          <tr>
            <th scope="row">Ordem</th>
            <td><input type="text" name="prod_option[{{n}}][ordem]" class="input-mini" value="0"></td>
          </tr>
          <tr>
            <th scope="row">&nbsp;</th>
            <td><a href="#" class="btn btn-success add-new-value"><i class="icon-plus icon-white"></i> Adicionar valor para opção</a> &nbsp;
            <a href="#" class="btn btn-warning remove-option"><i class="icon-minus-sign icon-white"></i> Remover</a></td>
          </tr>
        </table>        
    </div>
</script>

</div><!-- .values-panel -->

</div><!-- .limiter -->