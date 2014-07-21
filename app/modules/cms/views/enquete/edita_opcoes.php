<?php
// cal
$ttlvotos = $row['ttlvotos'];
$i = 1;
foreach($row['opcoes'] as $o):
	$id = $o['id'];
	$opcao = $o['opcao'];
	$votos = $o['votos'];
	$cor = $o['cor'];	
	$input = 'opc_'.$id;
	$plu = ($votos > 1) ? 's' : '';
	$perc = percentual($ttlvotos, $votos);// dobra
	

?>

<label for="<?php echo $input;?>" class="lb-full">Opção #<?php echo $i;?></label><input name="<?php echo $input;?>" id="<?php echo $input;?>" type="text" class="input-curto" value="<?php echo set_value($input, $opcao);?>" />
<div class="enquete-votos"><div class="bar" style="width:<?php echo ($perc * 2);?>px;"><?php echo $perc;?> %</div><?php echo $votos;?> voto<?php echo $plu;?></div>
<br />

<?php
	$i++;
endforeach;
?>






        
              
         
        <br />

<?php echo validation_errors(); ?>