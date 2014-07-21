<div class="voce-esta-aqui">Você está aqui: <strong><?php echo $tipoPasta;?> &raquo; <?php echo $pasta['titulo'];?></strong></div>

<hr color="#BFEAEA" size="1" noshade="noshade"/>
Basta clicar na foto para adicioná-la ou removê-la do conteúdo.

<div class="clear" align="center"></div>


<ul class="lista-imagens">

<?php
if(! $arquivos){
	echo '<strong>Não existem imagens disponíveis.</strong>';
} else {
	$bu = base_url();
	foreach($arquivos as $arq):
		$id = $arq['id'];
		$nome = $arq['nome'];
		$select = $arq['selected'];
		$thumb = $bu . $this->config->item('upl_imgs').'/'.thumb($nome);
		// controla a exibição das imagens		
		if($arq['pos'] == 'q'){
			$medida = 'width="70"';
			$mt = '5';
			$ml = '5';
		} else if($arq['pos'] == 'h') {
			$medida = 'width="70"';
			$proporcao = floor(7000 / $arq['width']);
			$alt = ($arq['height']*$proporcao)/100;
			$mt = (80-$alt)/2;
			$ml = '5';
		} else {
			$medida = 'height="70"';
			$mt = '5';
			$proporcao = floor(7000 / $arq['height']);
			$alt = ($arq['width']*$proporcao)/100;
			$ml = (80-$alt)/2;
		}
		// div sinaliza clique
		if($select == 'selected'){
			$click = '<div class="click-del">removido</div>';
		} else {			
			$click = '<div class="click-add">inserido</div>';	
		}
		
?>
	<li class="<?php echo $select;?>" id="<?php echo $id;?>">
	<?php echo $click;?>
    <a href="#" title="<?php echo $nome;?>"><img src="<?php echo $thumb;?>" <?php echo $medida;?> alt=" " style="margin-top:<?php echo $mt;?>px; margin-left:<?php echo $ml;?>px;" /></a>
    </li>
    
	
<?php
	endforeach;
}
?>
	
	
</ul>