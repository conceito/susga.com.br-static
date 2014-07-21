<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * MINHA EXTENÇÃO DA CLASSE URI
 * Pega a URI assim: controller/metodo/param:valor/param:valor
 * E retorna array assim: $v['param'] = valor, $v['param'] = valor
 */
class MY_URI extends CI_URI{

  function to_string($array = array())
    {
        $saida = '';
        foreach($array as $c => $v) {
            $saida .= $c . ':' . $v . '/';
        }

        $saida = trim($saida, '/');

        return $saida;
    }

    /**
     * Transforma a string URI em array
     * A variável $n foi depreciada. Não é mais necessária.
     *
     * @param string/array $default
     * @return
     */
    function to_array($default = NULL)
    {
       
        //if (! is_numeric($n)) $n = 3;
        // pega o URI
        $uri = $this->uri_string();

        // separa pelas barras
        $uri2 = explode('/', trim($uri, '/'));
        // se não existe retorna
        if (count($uri2) == 0) return '';
        // verifica o ponto de início das variáveis
        $n = 0;
        for($x = 0; $x < count($uri2); $x++){
            $p = explode(':', $uri2[$x]);
            if(count($p) == 2){
                $n = $x;
                break;
            }
        }
//        print_r($uri2);
//        exit();
        // array sa saida
        $saida = array();
        // se existe Default faz a comparação e retorna tudo
        if(! is_array($default)){
            $default = explode(',', $default);
        }
        if (count($default) > 0) {
            // $array_resultante = array_merge($uri2, $default);
            $saida = $this->_monta_array_comparando($n, $uri2, $default);
        } else {
            // senão monta o array com o que existir na URI
            $saida = $this->_monta_array($n, $uri2);
        }

        return $saida;
    }

    /**
     * Geralmente para preparar URI via AJAX.
     * Pega uma string como isso n:1_x:2_y:3 => array(n => 1, x => 2, y => 3);
     *
     * @param mixed $var
     * @param string $sep
     * @return
     **/
    function dash_to_array($var = '', $sep='/')
    {
		if($var == '')return false;
		// separa pelo underline
        $uri2 = explode($sep, trim($var, $sep));
        // se não existe retorna
        if (count($uri2) == 0) return '';
        // array sa saida
        $saida = array();
        $saida = $this->_monta_array(0, $uri2);
        return $saida;
	}

    function _monta_array($n, $uri2)
    {
        // array sa saida
        $saida = array();
        // percorre o array
        for($x = $n; $x < count($uri2); $x++) {
            $item = explode(':', $uri2[$x]);
            // se o item tem mais separações : só separa pelo primeiro
            if (count($item) > 2) $item = $this->_separa_em_dois($uri2[$x]);
            // coloca no array de saida
            $c = $item[0];
            // se não existe valor
            if (! isset($item[1])) $v = '';
            else $v = $item[1];
            // coloca na saida
            $saida[$c] = $v;
        }

        return $saida;
    }

    function _monta_array_comparando($n, $uri2 = array(), $default = array())
    {
        // array sa saida
        $saida = array();
        $default2 = array();
        // uri saida
        $uri3 = array();
        // percorre o array
        for($x = $n; $x < count($uri2); $x++) {
            $item = explode(':', $uri2[$x]);
            // se o item tem mais separações : só separa pelo primeiro
            if (count($item) > 2) $item = $this->_separa_em_dois($uri2[$x]);
            // coloca no array de saida
            $c = $item[0];
            // se não existe valor
            if (! isset($item[1])) $v = '';
            else $v = $item[1];

            $uri3[$c] = $v;
        }

        foreach($default as $vlr) {
            $vlr = trim($vlr);
            $default2[$vlr] = '';
        }

        $combina = array_merge($default2, $uri3);

        return $combina;
    }

    function _separa_em_dois($str)
    {
        $parts = explode(':', $str);
        $p1 = $parts[0];
		$p2 = '';
        // une o resto
        for($x = 1; $x<count($parts); $x++) {
           	$p2 .= $parts[$x].':';
        }

        $p2 = trim($p2, ':');

        return array($p1, $p2);
    }

}
