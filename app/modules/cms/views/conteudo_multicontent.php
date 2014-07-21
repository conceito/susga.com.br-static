
<div class="panel-left clearfix">
	
    <a href="#" onclick="javascript:$('#tabs').tabs('select',0); return false;">&larr; Conteúdo principal</a>
    <br />
	<br />

	<?php
    ///// looping pelos multicontents
	$i=1;
	foreach($multicontent as $content):
	?>
    <label for="txt" class="lb-full"><?php echo $content['titulo'];?></label>
	<div class="content-ctrl">            
            <a href="<?php echo cms_url($linkAddImage);?>" class="ico-img nyroModal" target="_blank">Subir imagem</a>            
	</div>
	<textarea name="txtmulti_<?php echo $i;?>" class="textarea-longo" id="txtmulti_<?php echo $i;?>" style="width:100%"><?php echo set_value('txtmulti_'.$i, $content['content']);?></textarea>
    
    <br /><br />
    <?php
	$i++;
    endforeach;
	?>
	
	
	
	
		
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	



</div><!-- .panel-right -->       
              
         


