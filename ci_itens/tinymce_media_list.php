<?php
include("config.php");



// Pesquisa imagens na pasta padr�o
$sqlpasta = mysql_query("select * from cms_pastas WHERE tipo='2' AND grupo!='0' order by 'ordem'");

//$sql = mysql_query("UPDATE cms_arquivos set descricao='$desc' WHERE id='$id'");

echo 'var tinyMCEMediaList = [';
	// Name, URL
	$saida = '';
	// escreve dados da pasta
	while($p = mysql_fetch_assoc($sqlpasta)){
		$idp = $p['id'];
		$nomep = $p['titulo'];
		
		// echo // Name, URL
		$saida .= '["", ""],[" \[v\] '.$nomep.'  ", ""],["", ""],';
		// pesquisa dados dos arquivos desta pasta
		$sql = mysql_query("select * from cms_arquivos WHERE pasta='$idp' AND (ext='swf' OR ext='avi' OR ext='mov') order by 'ordem'");
		$quant = mysql_num_rows($sql);
		if($quant == 0){
			 // echo
			$saida .= '["0 arquivos...", ""],';
		} else {
			
			$i = 1;
			while($a = mysql_fetch_assoc($sql)){
				$idp = $a['id'];
				$nome = $a['nome'];
				$ext = $a['ext'];
				$label = ' . '. $nome . ' ('.$ext.')';
				$urlimg = $arqs . $nome;
				// echo
				$saida .= '["'.$label.'", "'.$urlimg.'"],';
				$i++;
			}
			
		}
		
	}
	
	echo trim($saida, ',');
	
	
echo '];';

?>