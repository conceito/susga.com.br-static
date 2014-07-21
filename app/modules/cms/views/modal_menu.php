
<?php 
if(count($menu)){
	
	echo '<div class="barra-menu">
<span>Opções:</span>';
	
	foreach($menu as $lbl => $link):
	
	if(substr($link, 0, 1) == '#'){
		$link = $link;
	} else {
		$link = cms_url($link);
	}
	
	?>
	<a href="<?php echo $link;?>"><?php echo $lbl;?></a>
	<?php 
	endforeach;
	
	echo '</div>';
}
?>
