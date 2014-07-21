<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
* RSS : gera XML e exibe
*
* @package Library
* @author Bruno Barros
* @copyright Copyright (c) 2009
* @version 1.0
* @access public
*
* # Como usar #
* $rss['siteTitulo']    = 'titulo do site';
* $rss['siteUrl']       = 'endereço do site';
* $rss['siteDescricao'] = 'descrição do site para cabeçalho';

* $rss['itens'][] = array('data' => 'dd/mm/yyyy',
* 						'titulo' => 'titulo da materia',
* 						'resumo' => 'resumo',
* 						'link'   => 'link completo'
* 				  );
* $this->rss->init($rss);
*/
class Rss {

    var $siteTitulo = '';
    var $siteUrl;
    var $siteDescricao = '';
    var $data;
    var $ttl = 10;

	function Rss()
    {
        $this->CI = &get_instance();
    }

    function init($array = array())
    {
	   foreach($array as $c => $v)
	   {
			$this->$c = $v;
	   }

	   return $this->gera();
	}


    function gera()
    {
        header("Content-Type: application/xml; charset=utf-8");
        // Declaramos a data e hora de expiração deste documento (esta como sendo
		// 26/07/1997 para forçar a leitura deste  PHP sem que ele esteja em cache)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        // Declaramos a data e hora da última modificação deste documento
		// (sempre sendo a data e hora que ele estiver sendo acessado)
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Content-type: application/xml");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // cabeçalho do RSS
		$saida = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";

		$saida .= "<rss version=\"2.0\">
		       	<channel>
		        <title>".$this->siteTitulo."</title>
		        <link>".$this->siteUrl."</link>
		        <description>".$this->siteDescricao."</description>
		        <language>pt-br</language>
		        <copyright>".$this->siteUrl." - Todos os direitos reservados.</copyright>
		        <lastBuildDate>".date("d/m/Y")."</lastBuildDate>
		        <ttl>".$this->ttl."</ttl> ";

		        foreach($this->itens as $r) {

					$saida .= "<item>";
					$saida .= "<title>".$r['titulo']."</title>";
					$saida .= "<link>".$r['link']."</link>";
					$saida .= "<description>".$r['resumo']."</description>";
					$saida .= "<datePosted>".$r['data']."</datePosted>
					</item>";

		        }


		  $saida .= " </channel>
			</rss> ";

		return $saida;


    }

}
?>