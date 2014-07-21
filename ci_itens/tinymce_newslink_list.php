<?php
include("config.php");

$id = $_GET['id'];// ID da mensagem

// Pesquisa imagens na pasta padrão
$sql = mysql_query("select * from cms_news_links WHERE mens_id='$id' order by 'titulo'");




echo 'var tinyMCELinkList = new Array(';/// --- NÃO REMOVER
									  
									  
	$saida = '';
	
	$quant = mysql_num_rows($sql);
	if($quant == 0){
		 // echo
		$saida .= '["0 arquivos...", ""],';
	} else {
		
		$i = 1;
		// escreve dados da pasta
		while($p = mysql_fetch_assoc($sql)){
			$idp = $p['id'];
			$titulo = $p['titulo'];
			$url = $p['url'];	
			$label = $i .') '. $titulo;
			$linkNews = $UrlSite.'ci_itens/newsletter_link.php?l=[IDMEN]-'.$idp.'-[USER]';
				
			// echo
			$saida .= '["'.$label.'", "'.$linkNews.'"],';
			$i++;
			
			
		}
		
	}
	
	echo trim($saida, ',');

	
	
	
	
	
echo ');';/// --- NÃO REMOVER

?>