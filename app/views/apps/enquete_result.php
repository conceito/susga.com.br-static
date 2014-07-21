<div class="resultado">Resultado parcial</div>
<?php
foreach($opcoes as $op):
	$id = $op['id'];
	$quant = $op['quant'];
	$perc = $op['perc'];
	$label = $op['opcao'];
?>

<p><span class="perc"><?=$perc?>%</span> <span class="opc"><?=$label?></span></p>
	  
<?php
endforeach;
?>
<br /> 
