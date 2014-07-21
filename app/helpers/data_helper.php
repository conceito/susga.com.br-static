<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Minha Date Helpers
 *
 * @package	CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author	Bruno Barros
 * @link	http://codeigniter.com/user_guide/helpers/date_helper.html
 */
//------------------------------------------------------------------------
/**
 * Esta função pega o formato yyyy-mm-dd e coloca no padrão Brasil dd/mm/yyyy
 *
 *
 * @access	public
 * @return	string
 */
if (!function_exists('formaPadrao')) {

    function formaPadrao($dataPropria = '', $separador = "/") {
        $data = substr($dataPropria, -2) . "$separador" . substr($dataPropria, 5, 2) . "$separador" . substr($dataPropria, 0, 4);

        if ($data == "0000-00-00" or $data == "") {
            $data = "sem data";
        }
        return $data; //<<--
    }

}

// -----------------------------------------------------------------------------
/**
 * Converte data do formato SQL para brasileiro.
 * @param       datetime        $str
 * @param       string          $formato    "d/m/Y H:i"
 * @return      string
 */
if(!function_exists('datetime_br')){
    
    function datetime_br($str, $formato = "d/m/Y H:i"){
        return date($formato, strtotime($str));
    }
}


// -----------------------------------------------------------------------------
// Esta função pega o formato dd/mm/yyyy  e coloca no padrão yyyy-mm-dd
if (!function_exists('formaSQL')) {

    function formaSQL($dataPropria = '', $separador = "-") {
        $data = $dataPropria;

        if ($data == "00-00-0000" or $data == "") {
            $dataSaida = "0000-00-00";
        } else {
            $dataSaida = substr($data, -4) . "$separador" . substr($data, 3, 2) . "$separador" . substr($data, 0, 2);
        }
        return $dataSaida; //<<--
    }

}
// Função que subtrai dias de uma data
// voltadata("160","17/03/2006");
// Saída formato MySql
if (!function_exists('voltaData')) {

    function voltaData($datahoje, $dias, $formato = "mysql") {
        // Desmembra Data

        //if(ereg("abc", "nabc,nobc,treabc")) {  //faz se achar}
        //será substituido por:
//        if(preg_match("/abc/", "nabc,nobc,treabc"))

        if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $datahoje, $sep)) {
            $dia = $sep[1];
            $mes = $sep[2];
            $ano = $sep[3];
        } else {
            return utf8_decode("<b>Formato Inválido de Data - $datahoje</b><br>");
            exit;
        }
        // Meses que o antecessor tem 31 dias
        if ($mes == "01" || $mes == "02" || $mes == "04" || $mes == "06" || $mes == "08" || $mes == "09" || $mes == "11") {
            for ($cont = $dias; $cont > 0; $cont--) {
                $dia--;
                if ($dia == 00) { // Volta o dia para dia 31 .
                    $dia = 31;
                    $mes = $mes - 1; // Diminui um mês se o dia zerou .
                }
                if ($mes == 00) {
                    $mes = 12;
                    $ano = $ano - 1; // Se for Janeiro e subtrair 1 , vai para o ano anterior no mês de dezembro.
                }
            }
        }
        // Meses que o antecessor tem 30 dias -----------------------------------------
        elseif ($mes == "05" || $mes == "07" || $mes == "10" || $mes == "12") {
            for ($cont = $dias; $cont > 0; $cont--) {
                $dia--;
                if ($dia == 00) { // Volta o dia para dia 30 .
                    $dia = 30;
                    $mes = $mes - 1; // Diminui um mês se o dia zerou .
                }
                if ($mes == 00) {
                    $mes = 12;
                    $ano = $ano - 1; // Se for Janeiro e subtrair 1 , vai para o ano anterior no mês de dezembro.
                }
            }
        }
        // Mês que o antecessor é fevereiro
        // == Correção do voltadata no mês Abril/Março
        // Modificado por: Paulo Roberto Ens pauloens@bruc.com.br
        // Data: 11 Mai 2005
        else { // Else adicionado para funcionar o voltadata no mês de Abril/Março
            if ($ano % 4 == 0 && $ano % 100 != 0) { // se for bissexto
                if ($mes == "03") {
                    for ($cont = $dias; $cont > 0; $cont--) {
                        $dia--;
                        if ($dia == 00) { // Volta o dia para dia 30 .
                            $dia = 29;
                            $mes = $mes - 1; // Diminui um mês se o dia zerou .
                        }
                        if ($mes == 00) {
                            $mes = 12;
                            $ano = $ano - 1; // Se for Janeiro e subtrair 1 , vai para o ano anterior no mês de dezembro.
                        }
                    }
                }
            } //fecha se bissexto...
            else { // se não for bissexto
                if ($mes == "03") {
                    for ($cont = $dias; $cont > 0; $cont--) {
                        $dia--;
                        if ($dia == 00) { // Volta o dia para dia 30 .
                            $dia = 28;
                            $mes = $mes - 1; // Diminui um mês se o dia zerou .
                        }
                        if ($mes == 00) {
                            $mes = 12;
                            $ano = $ano - 1; // Se for Janeiro e subtrair 1 , vai para o ano anterior no mês de dezembro.
                        }
                    }
                }
            }
        } //Termina else dos meses
        // Confirma Saída de 2 dígitos
        if (strlen($dia) == 1) {
            $dia = "0" . $dia;
        }
        if (strlen($mes) == 1) {
            $mes = "0" . $mes;
        }
        // Monta Saída
        if ($formato == "mysql") {
            $nova_data = $ano . "-" . $mes . "-" . $dia;
        } else {
            $nova_data = $dia . "/" . $mes . "/" . $ano;
        }

        return $nova_data;
    }

//fecha função
}
// ----------------------------------------------------------------------------------------------
// Função que soma dias em uma data
// somadata("390","17/03/2006");
// saída no formato MySql
if (!function_exists('somaData')) {

    function somaData($datahoje, $dias, $formato = "mysql") {
        // Desmembra Data ----
        if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $datahoje, $sep)) {
            $dia = $sep[1];
            $mes = $sep[2];
            $ano = $sep[3];
        } else {
            return utf8_decode("<b>Formato Inválido de Data - $datahoje</b><br>");
            exit;
        }

        $i = $dias;

        for ($i = 0; $i < $dias; $i++) {
            if ($mes == "01" || $mes == "03" || $mes == "05" || $mes == "07" || $mes == "08" || $mes == "10" || $mes == "12") {
                if ($mes == 12 && $dia == 31) {
                    $mes = 01;
                    $ano++;
                    $dia = 00;
                }
                if ($dia == 31 && $mes != 12) {
                    $mes++;
                    $dia = 00;
                }
            } //fecha if geral
            if ($mes == "04" || $mes == "06" || $mes == "09" || $mes == "11") {
                if ($dia == 30) {
                    $dia = 00;
                    $mes++;
                }
            } //fecha if geral
            if ($mes == "02") {
                if ($ano % 4 == 0 && $ano % 100 != 0) { // ano bissexto
                    if ($dia == 29) {
                        $dia = 00;
                        $mes++;
                    }
                } else {
                    if ($dia == 28) {
                        $dia = 00;
                        $mes++;
                    }
                }
            } //FECHA IF DO MÊS 2
            $dia++;
        } //fecha o for()
        // Confirma Saída de 2 dígitos
        if (strlen($dia) == 1) {
            $dia = "0" . $dia;
        };
        if (strlen($mes) == 1) {
            $mes = "0" . $mes;
        };
        // Monta Saída
        if ($formato == "mysql") {
            $nova_data = $ano . "-" . $mes . "-" . $dia;
        } else {
            $nova_data = $dia . "/" . $mes . "/" . $ano;
        }

        return $nova_data;
    }

//fecha a funçâo data
}
// -------------------------------------------------------------------------------------------------------------
// dia da semana e data por extenso
if (!function_exists('dataPorExtenso')) {

    function dataPorExtenso($sqlDate = null) {
        $meses = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
        $diasemana = array("Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado");
        
        if($sqlDate != null){
            $_ano = substr($sqlDate, 0, 4);
            $_mes = substr($sqlDate, 5, 2);
            $_dia = substr($sqlDate, -2);
        } else {
            $_ano = date('Y');
            $_mes = date('n');
            $_dia = date('d');
            $diasem = date('w');
        }
        
        $hora = getdate();
        // inicio da Modificação
        if ($hora['minutes'] < 10) {
            $hora['minutes'] = "0" . $hora['minutes'];
        }
        // fim da Modificação
        $horacerta = ($hora['hours'] . ':' . $hora['minutes']);
        // Como ficará a data e hora
        // $data_hora = $diasemana[$diasem].', '.$_dia.' de '.$meses[$_mes-1].' de '.$_ano;
        $data_hora = $_dia . ' de ' . $meses[$_mes - 1] . ' de ' . $_ano;
        return $data_hora;
    }

}

// dia da semana e data por extenso
if (!function_exists('mesNome')) {

    function mesNome($mes = '', $tipo = 'curto') {
        $mesesC = array("Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez");
        $mesesL = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");



        $_mes = (strlen($mes) == 0) ? date('m') : $mes;


        if ($tipo == 'curto') {
            $saida = $mesesC[$_mes - 1];
        } else {
            $saida = $mesesL[$_mes - 1];
        }

        return $saida;
    }

}

// calcula a primeira menos a segunda data
if (!function_exists('diasEntre')) {

    function diasEntre($data1 = '', $data2 = '') {
        $data1 = ($data1 == '') ? date("Y-m-d") : $data1;
        $adata1 = explode('-', $data1);
        $data1 = $adata1[2] . "-" . $adata1[1] . "-" . $adata1[0];

        $adata2 = explode('-', $data2);
        $data2 = $adata2[2] . "-" . $adata2[1] . "-" . $adata2[0];

        //
        $dataCmp1 = mktime(0, 0, 0, $adata1[1], $adata1[2], $adata1[0]);
        $dataCmp2 = mktime(0, 0, 0, $adata2[1], $adata2[2], $adata2[0]);

        // Faz o calculo da diferença em dias entre as duas datas
        //24 horas * 60 Min * 60 seg = 86400
        $dias = ($dataCmp1 - $dataCmp2) / 86400;
        $dias = ceil($dias);

        return $dias;
    }

}
/**
 * A função identifica o tipo de data entrado e retorna no final um valor 
 * para ela NULL caso o valor entrado for uma data inválida ou um 
 * valor padrão definido caso NULL ou em caso de data correta, 
 * retorna a mesma no padrão banco de dados como 2011-05-30.
 */
if(!function_exists('magic_valid_data')){    

  function magic_valid_data($data,$padrao=false){
    $dt   = str_replace(array("-","/","."),"",$data);
    $tam  = strlen($dt);
    if($tam==8){
      $v = substr($dt,4,2);
      if($v > 12){ /*28041990*/
        $dia = substr($dt,0,2);
        $mes = substr($dt,2,2);
        $ano = substr($dt,4,4);
      }else{ /*19900428*/
        $dia = substr($dt,6,2);
        $mes = substr($dt,4,2);
        $ano = substr($dt,0,4);
      }
      if($ano < 1900 || $mes > 12){ 
        $r = NULL;        
      }else{
        if(!checkdate($mes,$dia,$ano)){
          $r = NULL;
        }else{
          $r = "$ano-$mes-$dia";
        }
      }      
    }else{
      $r = NULL;
    }
    if($r == NULL && $padrao){ $r = $padrao;}
    return $r;      
  }
}


if (!function_exists('years_old'))
{

    /**
     * Retorna a idade
     * @param string $sqlDate
     * @return int
     */
    function years_old($sqlDate = '')
    {
        if(magic_valid_data($sqlDate) === null)
        {
            return '?';
        }
        
        $birthDate = explode("-", magic_valid_data($sqlDate));
        $d = $birthDate[2];
        $m = $birthDate[1];
        $y = $birthDate[0];
        //get age from date or birthdate
        $age = (date("md", date("U", mktime(0, 0, 0, $m, $d, $y))) > date("md") ? ((date("Y") - $y) - 1) : (date("Y") - $y));
        return $age;
    }

}