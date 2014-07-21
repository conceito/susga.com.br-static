<?php
if(isset($arquivos)):

	foreach($arquivos as $a):
	
	$desc = $a['descricao'];
	$arq = $a['nome'];
	$id = $a['id'];
	
	// verifica se o arquivo tem path absoluto
	if(substr($arq, 0, 4) == 'http'){
		$url = $arq;
	} else {
		$url = $bs . $pastaUpl . '/'. $arq;
	}
?>
	<a name="id<?php echo $id;?>"></a>
    <h3><?php echo $desc;?></h3>

      <div id="flasplayerBig<?php echo $id;?>" align="center" style="z-index:1;"><a href="http://www.macromedia.com/go/getflashplayer">Você precisa instalar/atualizar o Flash Player</a></div>
<script type="text/javascript">
	var s1 = new SWFObject("<?php echo $bs;?>ci_itens/vplay/player.swf","CI<?php echo $id;?>","100%","500","9","#000");
	s1.addParam("allowfullscreen","true");
	s1.addParam("wmode","transparent");
	s1.addParam("allowscriptaccess","always");
	s1.addParam("flashvars","file=<?php echo $url;?>&image=<?php echo $bs;?>ci_itens/vplay/preview.jpg&stretching=fill&frontcolor=990000&lightcolor=ffcc00&backcolor=999999&screencolor=000");
	s1.write("flasplayerBig<?php echo $id;?>");
</script> 

	
<?php
	
	echo $botoes;
	
	endforeach;

endif;
?>