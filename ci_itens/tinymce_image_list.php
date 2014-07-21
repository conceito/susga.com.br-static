<?php
include("config.php");

$item_id = $_GET['id'];
$tb = $_GET['tb'];


// Pesquisa imagens na pasta padr�o
$sqlpasta = mysql_query("select * from cms_pastas WHERE tipo='0' AND grupo!='0' order by ordem,titulo");


echo 'var tinyMCEImageList = new Array(';/// --- N�O REMOVER
								   
	
	$saida = '';
	// se a pagina for setada, lista primeiro a galeria particular
	if(strlen($item_id) > 0){
		$sqlitem = mysql_query("select galeria from $tb where id='$item_id'");
		$item = mysql_fetch_assoc($sqlitem);
		$galeria = $item['galeria'];
		
		// echo // Name, URL
		$saida .= '["", ""],[" \[v\] galeria deste item ", ""],["", ""],';
		// se exisitir uma pageria
		if(strlen($galeria) == 0){
			// echo
			$saida .= '["0 imagens...", ""],';
		} else {
			$listaids = explode('|', $galeria);
			$i = 1;
			foreach($listaids as $id){
				// pesquisa dados dos arquivos desta pasta
				$sqlarq = mysql_query("select * from cms_arquivos WHERE id='$id'");				
				if(mysql_num_rows($sqlarq) > 0){
					$ar = mysql_fetch_assoc($sqlarq);
				
					$idp = $ar['id'];
					$nome = $ar['nome'];
					$label = '  '. stripFolders($nome);
					$urlimg = $imgs . $nome;
					// echo
					$saida .= '["'.$label.'", "'.$urlimg.'"],';
					$i++;
				}
				
				
			}
		}
		
		
	}
	
	
	// escreve dados da pasta
	while($p = mysql_fetch_assoc($sqlpasta)){
		$idp = $p['id'];
		$nomep = $p['titulo'];
		
		// echo // Name, URL
		$saida .= '["", ""],[" \[v\] '.$nomep.' ", ""],["", ""],';
		// pesquisa dados dos arquivos desta pasta
		$sql = mysql_query("select * from cms_arquivos WHERE pasta='$idp' order by nome");
		$quant = mysql_num_rows($sql);
		if($quant == 0){
			 // echo
			$saida .= '["0 imagens...", ""],';
		} else {
			
			$i = 1;
			while($a = mysql_fetch_assoc($sql)){
				$idp = $a['id'];
				$nome = $a['nome'];
				$label = '  '. stripFolders($nome);
				$urlimg = $imgs . $nome;
				// echo
				$saida .= '["'.$label.'", "'.$urlimg.'"],';
				$i++;
			}
			
		}
		
	}
	
	echo trim($saida, ',');

	
	
echo ');';/// --- N�O REMOVER


function stripFolders($str)
{
	$pieces = explode('/', $str);
	$last = $pieces[count($pieces)-1];
	return $last;
}
?>