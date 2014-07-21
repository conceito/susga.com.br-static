<?php
include("config.php");


$path = $UrlSite.'assets/cms/templates/';

echo 'var tinyMCETemplateList = [';
	// Name, URL, Description
echo '["Slides de imagens", "'.$path.'slider.html", "Insira os IDs as imagens para criar exibição especial."],
	["Tabela com duas colunas", "'.$path.'tabela_2col.html", "Tabela com duas colunas."],
	["Tabela com três colunas", "'.$path.'tabela_3col.html", "Tabela com três colunas."]';
echo '];';

?>