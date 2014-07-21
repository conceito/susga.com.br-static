<?php $baseurl = base_url();

?>

  	<div class="banner-novo-form box-t-a clearfix">
    	
        <h2 class="title">Novo grupo</h2>
        
        <div class="banner-novo-wraper box-inner">        
        
        <form action="<?php echo cms_url('cms/banner/grupoSalva/co:'.$var['co']);?>" method="post" id="frm">
        	
        <div class="panel-left clearfix">
        	
            <label for="titulo">Nome</label>
            <input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="" />
            
            <br />
            
            <label for="resumo">Resumo</label>
            <textarea name="resumo" class="textarea-curto" id="resumo"></textarea>
            
            <br />
            
           
        
        </div><!-- .panel-left -->


		<div class="panel-right clearfix">
        
        	<label for="banners_type">Tamanho</label>
        	<select name="banners_type" id="banners_type" class="input-combo">
            	<option value="0">Personalizado</option>
                <option value="728x90">728 x 90 (half banner)</option>
                <option value="468x60">468 x 60 (half banner)</option>
                <option value="234x60">234 x 60 (half banner)</option>
                <option value="125x125">125 x 125 (half banner)</option>
                <option value="120x90">120 x 90 (half banner)</option>
                <option value="120x600">120 x 600 (half banner)</option>
                <option value="160x600">160 x 600 (half banner)</option>
                <option value="300x250">300 x 250 (half banner)</option>
                
            </select>
            
            <br />
            
            <div class="small-input-group">
            	<input name="banner_width" id="banner_width" type="text" class="input-mini" value="">
                <div class="divide">x</div>
            	<input name="banner_height" id="banner_height" type="text" class="input-mini" value="">
            </div>
            
            <br />
            
            <label for="ordem">Exibição</label>
        	<select name="ordem" id="ordem" class="input-combo">
                <option value="rand">Aleatória</option>
                <option value="ordem">Ordenada</option>                
            </select>
            
		
		</div><!-- .panel-right -->
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-save-new-group">Salvar novo grupo</button>            
            <a href="#" class="btn btn-cancel-new-group">Cancelar</a>
          </div>
		        
        
        </form>
        
        </div><!-- .banner-novo-wraper -->
        
    </div><!-- .banner-novo-form -->


      
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl" class="table table-striped">
      <thead>
      <tr>
      	<?php if($listOptions['sortable']) echo '<th scope="col" class="sortable">&nbsp;</th>'?>
        <th scope="col" class="cb">&nbsp;</th>
        <th scope="col">Nome do grupo</th>
        
        <th scope="col">Exibição</th>
        <th scope="col">Tamanho</th>
        
        <th scope="col" class="data">Status</th>
      </tr>
      </thead>
      <tbody>
      
      <?php 
	  if(! $rows):
	  	echo '<tr><td colspan="3"><strong>Não existem registros.</strong></td></tr>';
	  else:
	  	$i = 0;
		foreach($rows as $row):
			
			// linhas pares-impares
			if($i%2 == 0)$zebra = 'even';
			else $zebra = '';
			// links
			$edicao = cms_url($linkEditar . '/id:' . $row['id']);
			// tipo de grupo
			if($tipoGrupo == 'pastas'){
				
				$resumo = $row['txt'];
			}else {
				
				$resumo = $row['resumo'];
			}
			
	  ?>
      <tr class="<?php echo $zebra;?>" id="<?php echo $row['id'];?>">
      <?php if($listOptions['sortable']) echo '<td valign="top" class="dragme"></td>'?>
        <td valign="top" class="td-cb"><input name="cb[]" type="checkbox" value="<?php echo $row['id'];?>" class="cb" /></td>
        
        <td valign="top"><?php echo str_hierarchy($row['level']);?> <?php echo barra_edicao($edicao, $row['titulo']);?>
        <!--resumo-->
            <div class="resumo-ler"><?php echo $resumo;?></div>
        </td>
        
        <td valign="top"><?php echo $row['ordem'];?></td>
        <td valign="top"><?php echo $row['tags'];?></td>
       
        <td valign="top"><?php echo link_status('-', $row['status']);?></td>
      </tr>
	  
	  		
	  
      <?php 
	  	$i++;
	  	endforeach;
	  endif;
	  ?>
      
     
      </tbody>
    </table>
    </form>
	
    