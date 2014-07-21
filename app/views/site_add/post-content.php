<div id="page" class="outra-classe" <?php echo $this->pagina['adminbar'];?>>
	
    <h1><?php echo $this->pagina['titulo'];?></h1>		
	
    <?php 
	if(isset($this->pagina['children'])):		
		echo $this->pagina['children'];		
	endif;
	?>
    
    <?php // Se existe preço //////////////////////////////////////
		if(isset($this->pagina['preco_final'])):?>
    
    <form class="well pull-right" action="<?php echo site_url('loja/add/'.$this->pagina['id']);?>" method="post" id="product-options">
    
    	<input type="hidden" name="product_id" value="<?php echo $this->pagina['id'];?>" />
        <input type="hidden" name="product_uri" value="<?php echo $this->uri->uri_string();?>" />
        <input type="hidden" name="rowid" value="<?php echo '';?>" />
    	
        
    	<p>Preço R$ <?php echo formaBR($this->pagina['preco_final']);?></p>
        <hr>
    	
    
    
    	<?php // Se o produto tem opções //////////////////////////////
		if(isset($this->pagina['opcoes']) && $this->pagina['opcoes']):?>
        <p><strong>Opções disponíveis</strong></p>
        
        <?php foreach($this->pagina['opcoes'] as $opt):?>
        <div class="control-group">
            <div class="controls">
            <label class=""><?php echo $opt['titulo'];?></label>
            <select name="option[<?php echo $opt['id'];?>]" class="span10">
              <?php foreach($opt['prod_opt_value'] as $oval):?>
              <option value="<?php echo $oval['id'];?>"><?php echo $oval['titulo'].' '.$oval['valor_mod'];?></option>
              <?php endforeach;?>
            </select>
            </div>
        </div>
        <?php endforeach;?>
        <?php endif;?>
        
        <div class="input-append">
          <input class="span3" size="2" type="number" min="1" max="<?php echo $this->config->item('max_per_prod');?>" name="qty" value="1"><button type="submit" class="btn btn-success"><i class="icon-shopping-cart icon-white"></i> Adicionar</button>
        </div>
        
        
    </form><!-- .well -->
    <?php endif;?>
    
	<?php // Conteúdo parseado
	echo $this->pagina['txt'];?>
    
    
            
            
</div>