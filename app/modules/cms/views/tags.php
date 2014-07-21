<div class="control-group box">

<label for="" class="lb-full">Tags</label>
<div class="campo-tags"> 

	<!--<div class="tag-item">
    	<span class="tag-label">Nome da tag</span>
        <a href="#" title="remover" class="del">[x]</a>
        <input name="mytags[]" type="hidden" value="tagID" />
    </div>-->
    
    <?php
    if($conteudo_tags){
		
		foreach($conteudo_tags as $tag){
			
			$id = $tag['id'];
			$label = $tag['titulo'];
			$cor1 = $tag['cor1'];
			$cor2 = $tag['cor2'];
			?>
			<div class="tag-item" style="background-color:<?php echo $cor2;?>; color:<?php echo $cor1;?>;">
			<span class="tag-label"><?php echo $label;?></span>
			<a href="#" title="remover" class="del">[x]</a>
			<input name="mytags[]" type="hidden" value="<?php echo $id;?>" />
			</div>
			<?php
		}
			
	} else {
		echo '<span class="nenhuma-tag">- nenhuma -</span>';	
	}
	?>
    

</div><!-- .campo-tags -->

<div class="tags-disponiveis">
<label for="">Adicionar tag: </label> 

<?php
if($modulo_tags){
	
	foreach($modulo_tags as $tag){
		
		$id = $tag['id'];
		$label = $tag['titulo'];
	?>
    <a href="#" class="tag-add" rel="<?php echo $id;?>"><?php echo $label;?></a> | 
	<?php
			
	}
	
} else {
	echo '- tags -';
}
?>
</div><!-- .tags-disponiveis -->

</div><!-- .control-group --> 