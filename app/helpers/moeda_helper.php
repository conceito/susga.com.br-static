<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/////// para salvar no BD
if ( ! function_exists('padraoSQL'))
{
    function padraoSQL($param)
    {
        // verifica se tem centavos com vírgula, 999,99
        if(substr($param, -3, 1) == ','){
            $param = str_replace(array(','), '.', $param);
        }
        // primeiro retira
        $param = str_replace(array(','), '', $param);
        return number_format((double)$param, 2, '.', '');

    }
}
// ----------------------------------------------------------------------------

//////    formato brasilleiro
if ( ! function_exists('formaBR'))
{
	function formaBR($str)
	{
		// primeiro retira
		$str = number_format($str, 2, ',', '.');
		return $str;
	}
}
//////    formato brasilleiro
if ( ! function_exists('quanto_custa'))
{
	function quanto_custa($tipo_user, $valor, $desconto, $quantidade = 1)
	{
		if($tipo_user == 2){ // assinante
		   $tt = ($valor - $desconto) * $quantidade;
		} else {
		   $tt = $valor * $quantidade;
		}
		// verifica
		$saida = ($tt < 0) ? formaBR(0) : formaBR($tt);
		return $saida;
	}
}
//////    formato brasilleiro
if ( ! function_exists('soma_array'))
{
	function soma_array($array)
	{
		if(count($array) == 0) return formaBR(0);
		// soma
		$sub = 0;
		foreach($array as $v){

		 $sub = sprintf("%.2f",($sub + padraoSQL($v)));

		}
		// verifica
		$saida = ($sub < 0) ? formaBR(0) : formaBR($sub);
		return $sub;
	}
}
//////    formato brasilleiro
if ( ! function_exists('subtotal'))
{
	function subtotal($valor, $quantidade = 1)
	{
		$_v = array();
		// acrecenta valores para somatório
       	for($x=0; $x<$quantidade; $x++){
		   $_v[] = $valor;
		}
		$tt = soma_array($_v);
		// verifica
		$saida = ($tt < 0) ? formaBR(0) : formaBR($tt);
		return $saida;
	}
}

//////    divide o valor total pelas parcelas
if ( ! function_exists('divtotal'))
{
	function divtotal($valor, $quantidade = 1)
	{
		$_v = padraoSQL($valor);
		$_v2 = $_v / $quantidade;

		// verifica
		$saida = formaBR($_v2);
		return $saida;
	}
}


/* End of file array_helper.php */
/* Location: ./system/helpers/array_helper.php */