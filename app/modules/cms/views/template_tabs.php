<?php
$baseurl = base_url();
?>
<form action="" method="post" enctype="multipart/form-data" name="formulario" id="formulario">
      <input name="conteudo_id" type="hidden" value="<?php if(isset($row['id']))echo $row['id'];?>" />
      
<div id="tabs">
	<ul>
    <?php /////////////// abas
	$nt = 1;
	foreach($tab_title as $t):
    ?>
            <li><a href="#tabs-<?php echo $nt;?>"><?php echo $t?></a><img src="<?php echo cms_img();?>ico-salvar.gif" width="8" height="8" alt="atualizado!" title="Não esqueça de salvar!" class="ico-atualizar" /></li>
    <?php
		$nt++;
	endforeach;
	?>
   
	</ul>


    
    <?php ////////////  conteudos
	// navegação interda das abas
	$total_abas = count($tab_title);
	$nc = 1;
	foreach($tab_contt as $c):
    ?>
   <div id="tabs-<?php echo $nc;?>"><!--ABA -->
	
    	<?php echo $c;?>
        <!--ABA fim-->
    	<div class="clear debug"></div>
    
       <div class="tab-nav-system">
       <?php 
	   /*
	   if($total_abas > 1){
		   if($nc == 1){// primeiro
			   echo '<a href="#" class="tabs-go-1">próxima aba <span class="courier">&gt;</span></a>';
		   } else if($nc == $total_abas){// último
			   echo '<a href="#" class="tabs-go-'.($nc-2).'"><span class="courier">&lt;</span> aba anterior</a>';
		   } else {// no meio
			   echo '<a href="#" class="tabs-go-'.($nc-2).'"><span class="courier">&lt;</span> aba anterior</a> | 
			<a href="#" class="tabs-go-'.($nc).'">próxima aba <span class="courier">&gt;</span></a>';
		   }
	   }
	   */
	   ?>
       
        </div>
	</div>
    <?php
		$nc++;
	endforeach;
	?>
    
	
	
</div>      


</form>