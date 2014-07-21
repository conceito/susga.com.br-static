<?php
$bs = base_url();
?>
 <!-- coluna esquerda do gerenciamento de itens de Menu -->
<div class="col-esq">
        
        <div id="add-page" class="box-menu-ajax">
        	
            <h3>Módulos</h3>
            
            <div class="box-menu-tabs">
            	<ul>
                	<li><a href="#tab-modulos">Selecionar</a></li>
                    <li><a href="#tab-paginas" class="tabitem-pagina">Páginas</a></li>
                    <li><a href="#tab-pesquisar">Pesquisar</a></li>
                </ul>
                
                <div id="tab-modulos">                	
                    
                    
                    <?php echo $modulosMenu;?>
                    
                    
                </div><!-- .tab-recentes -->
                
                <div id="tab-paginas">
                
                  <img src="<?php echo cms_img();?>loader.gif" width="43" height="43" alt="loading" class="loading" style="float:left;" />
                  
                  <select id="conteudo" class="input-combo " name="conteudo" multiple="multiple" style="width:100%;">
                    
                   </select>
                  
                  <a href="#" class="botao bot-add-blankpage">Adicionar em branco</a>
                  <a href="#" class="botao bot-add-page">Adicionar ao menu</a> 
                  
                  </div><!-- .tab-paginas -->
                
                <div id="tab-pesquisar">
                
                	<label for="palavrachave" style="width:auto;">Palavra-chave: </label><input name="palavrachave" id="palavrachave" type="text" class="input-longo palavrachave" />
                    
                    <img src="<?php echo cms_img();?>loading.gif" width="16" height="16" alt="loading" class="loading" />
                    
			<div class="pc-resultado">
                    	
          
                        
                  </div><!-- .pc-resultado -->
                  
                  <a href="#" class="botao bot-add-blankpage">Adicionar em branco</a>
                  
                  <a href="#" class="botao bot-add-page">Adicionar ao menu</a> 
                
              </div>
                
                
            </div><!-- .box-menu-tabs -->
            
           <!--<a href="#" class="botao bot-add-page">Adicionar ao menu</a>
           <a href="#" class="botao bot-add-blankpage">Adicionar em branco</a>-->
           
           
            
        </div><!-- .box-menu-ajax -->
        
</div><!-- .col-esq -->
        
        <!-- coluna direita do gerenciamento de itens de Menu -->
        <div class="col-dir">
        
        <div class="field-title">Arraste para reordenar o menu</div>
        
        
        <ul class="menu-sortable">
        
        <?php
        if(isset($menuDados) && $menuDados):
		
		foreach($menuDados as $item):
			
			$id = $item['id'];
			//$conteudo_id = $item['visitas'];
			$rotulo = $item['titulo'];
			$title = $item['txt'];
			$url = $item['nick'];
			$css = $item['resumo'];
			$target = $item['tags'];
			$nivel = $item['nivel']; 
		?>
            <li id="<?php echo $id;?>" class="menu-depth-<?php echo $nivel;?>">
            <div class="title drag"><?php echo $rotulo;?></div>
            <a href="#" class="options" title="detalhes">+</a>
            
            	<!-- dados do item de menu -->
                <div class="menu-dados menu-item-2">
                	
                    <label class="lb-menu">URL:</label>
                    <input name="url" type="text" value="<?php echo $url;?>" class="input-menu" />
                    
                    <div class="item-info-metade">                    
                    <label class="lb-menu">Rótulo:</label>
                    <input name="rotulo" type="text" value="<?php echo $rotulo;?>" class="input-menu" />
                    </div>
                    
                    <div class="item-info-metade">
                    <label class="lb-menu">Title:</label>
                    <input name="title" type="text" value="<?php echo $title;?>" class="input-menu" />
                    </div>
                    
                    <div class="item-info-metade">                    
                    <label class="lb-menu">CSS:</label>
                    <input name="css" type="text" value="<?php echo $css;?>" class="input-menu" />
                    </div>
                    
                    <div class="item-info-metade">
                    <label class="lb-menu">Target:</label>
                    
                    <select name="target" class="input-menu">
                    	<?php echo $this->menus_model->comboTarget($target);?>
                    </select>
                    </div>
                    
                    
                    
                    <div class="md-options"><a href="#" class="opt-remover">&raquo; remover do menu</a> | <a href="#" class="opt-atualizar">&raquo; atualizar dados</a></div>
                    
                </div><!-- .menu-dados -->
            
            </li>
            
            <?php
			endforeach;
			
            endif;
			?>
            
            
            
        </ul>
        
        
        </div><!-- .col-dir -->