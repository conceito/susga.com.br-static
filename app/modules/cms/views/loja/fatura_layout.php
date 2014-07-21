<?php
/*********************************************
*   Template: base para impressão de faturas
*   Controller: cms/loja/imprimir_fatura
*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<style type="text/css">
body {
    background: #FFFFFF;
}
body, td, th, input, select, textarea, option, optgroup {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 12px;
    color: #000000;
}
h1 {
    text-transform: uppercase;
    color: #CCCCCC;
    text-align: right;
    font-size: 24px;
    font-weight: normal;
    padding-bottom: 5px;
    margin-top: 0px;
    margin-bottom: 15px;
    border-bottom: 1px solid #CDDDDD;
}
.div1 {
    width: 100%;
    margin-bottom: 20px;
}
.div2 {
    float: left;
    display: inline-block;
}
.div3 {
    float: right;
    display: inline-block;
    padding: 5px;
}
.heading td {
    background: #E7EFEF;
}
.address, .product {
    border-collapse: collapse;
}
.address {
    width: 100%;
    margin-bottom: 20px;
    border-top: 1px solid #CDDDDD;
    border-right: 1px solid #CDDDDD;
}
.address th, .address td {
    border-left: 1px solid #CDDDDD;
    border-bottom: 1px solid #CDDDDD;
    padding: 5px;
}
.address td {
    width: 50%;
}
.product {
    width: 100%;
    margin-bottom: 20px;
    border-top: 1px solid #CDDDDD;
    border-right: 1px solid #CDDDDD;
}
.product td {
    border-left: 1px solid #CDDDDD;
    border-bottom: 1px solid #CDDDDD;
    padding: 5px;
}
</style>
</head>

<body onload="window.print()">

<?php 
if(isset($faturas)){
    echo $faturas;
}
?>

</body>
</html>