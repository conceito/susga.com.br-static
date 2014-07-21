<div class="voce-esta-aqui">Você está aqui: <strong><?php echo $tipoPasta;?></strong></div>
<hr color="#BFEAEA" size="1" noshade="noshade"/>
<ul class="lista-pastas">
<?php
if(! $pastas){
	echo '<strong>Não existem pastas disponíveis.</strong>';
} else {
	foreach($pastas as $pas):
		$idp = $pas['id'];
		$nome = $pas['titulo'];
?>
	<li><a href="<?php echo cms_url('cms/imagem/imgExplorer/pasta:'.$idp.'/id:'.$id);?>"><?php echo $nome;?></a></li>
<?php
	endforeach;
}
?>
	
	
</ul>