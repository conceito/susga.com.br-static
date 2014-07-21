<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Gera código HTML do AddThis
 *
 * Como usar: $this->load->library('addthis');
 * $addthis = $this->addthis->init('icons', array('email', 'gmail'));
 * $addthis = $this->addthis->init('button');
 *
 * Possible icons:
    email
	print
    twitter
    tweetmeme
    facebook
    myspace
    stumbleupon
    digg
    delicious
	eucliquei
    favorites
    friendfeed
    gabbr
    gmail
    google
    googlereader
    hotmail
    igoogle
    linkedin
    live
    multiply
    netvibes
    reddit
    technorati
    googletranslate
    tumblr
    yahoomail
    *
    * more: http://www.addthis.com/help/
 */

class Addthis {
    var $tipo = 'button'; // tipo de icone : button , short , long , icons
    var $icons = array('email', 'print', 'twitter', 'facebook', 'gmail', 'googletranslate'); // icones default
    var $linkOpen = '<a href="http://www.addthis.com/bookmark.php" class="addthis_button">';// link para botão simples
    var $linkClose = '</a>';
    var $divOpen = '<div class="addthis_toolbox addthis_default_style">';// div para pequenos icones
    var $divClose = '</div>';

    function Addthis()
    {
    }
    function init($tipo = 'button', $icons2 = array())
    {
	   	if(count($icons2) > 0) $this->icons = $icons2;
	   	$this->tipo = $tipo;

        $botoes = $this->geracod($tipo);
        $script = $this->scriptAddthis();

        return $botoes . $script;

    }

    function geracod($tipo)
    {
        if ($tipo == 'button') {
           	$saida = $this->linkOpen;
			$saida .= '<img src="http://s7.addthis.com/static/btn/sm-plus.gif" width="16" height="16" border="0" alt="Share" />';
			$saida .= $this->linkClose;
        } else if ($tipo == 'short') {
           $saida = $this->linkOpen;
		   $saida .= '<img src="http://s7.addthis.com/static/btn/v2/sm-share-pt.gif" width="83" height="16" border="0" alt="Share" />';
		   $saida .= $this->linkClose;
        } else if ($tipo == 'long') {
           	$saida = $this->linkOpen;
			$saida .= '<img src="http://s7.addthis.com/static/btn/v2/lg-share-pt.gif" width="125" height="16" border="0" alt="Share" />';
			$saida .= $this->linkClose;
        } else if ($tipo == 'icons') {
           $saida = $this->divOpen;
		   $saida .= $this->_geraicones();
		   $saida .= $this->divClose;
        }

        // monta botão
        return $saida;
    }

    function scriptAddthis()
    {
	  $sc = '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pub=xa-4b028f2c1e9e41ab"></script>';
	  return $sc;
	}

	function _geraicones(){
		$saida = '';
		foreach($this->icons as $tag)
		{
		$saida .= '<a class="addthis_button_'.$tag.'"></a>';
		}

		return $saida;
	}
}

?>