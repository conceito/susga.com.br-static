<?php
include("config.php");


// Pesquisa imagens na pasta padr�o
$sqlpasta = mysql_query("select * from cms_pastas WHERE tipo='2' AND grupo!='0' order by 'ordem'");




echo 'var tinyMCELinkList = new Array(';/// --- N�O REMOVER
									  
									  
	$saida = '';
	// escreve dados da pasta
	while($p = mysql_fetch_assoc($sqlpasta)){
		$idp = $p['id'];
		$nomep = $p['titulo'];
		
		// echo // Name, URL
		$saida .= '["", ""],[" \[v\] '.$nomep.'  ", ""],["", ""],';
		// pesquisa dados dos arquivos desta pasta
		$sql = mysql_query("select * from cms_arquivos WHERE pasta='$idp' order by 'ordem'");
		$quant = mysql_num_rows($sql);
		if($quant == 0){
			 // echo
			$saida .= '["0 arquivos...", ""],';
		} else {
			
			$i = 1;
			while($a = mysql_fetch_assoc($sql)){
				$idp = $a['id'];
				$nome = $a['nome'];
				$label = ' &nbsp;&nbsp;&nbsp; ' . $nome;
				$tipo = $a['img'];
				// se for arquivo externo
				if($tipo > 1){
					$urlimg = $nome;// sem caminho do servidor
				} else {
					$urlimg = $arqs . $nome;
				}
				
				// echo
				$saida .= '["'.$label.'", "'.$urlimg.'"],';
				$i++;
			}
			
		}
		
	}
	
	echo trim($saida, ',');

	
	
	
	
	
echo ');';/// --- N�O REMOVER

?>