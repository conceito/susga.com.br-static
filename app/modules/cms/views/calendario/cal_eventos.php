<div id="alertas"></div>
<div id="painel-mensagens">
<ul class="lista">
<?php 
if(! $eventos):
?>
<li><span class="imp">&nbsp;</span>Não existem eventos!
        	<div class="mensagem"> Não existem eventos!</div>
        </li>
<?php
else:
	foreach($eventos as $row):
	$id = $row['id'];
	$tipo = $row['tipo'];
	$dt_ini = formaPadrao($row['dt_ini']);
	$dt_fim = formaPadrao($row['dt_fim']);
	$hr_ini = substr($row['hr_ini'], 0, 5);
	$hr_fim = substr($row['hr_fim'], 0, 5);
	$titulo = $row['titulo'];
	$txt = $row['txt'];
	$nick = $row['nick'];
	// prepara saida
	//$class = ($lido == 0) ? '' : 'lido';
	//$title = 'em '.$data.' às '.$hora;
	
?>
	
 <li id="mens-<?php echo $id;?>">
 
 <span class="imp"></span> <a href="#" class="assunto" title="<?php echo $titulo;?>"><?php echo $titulo;?>
 <span class="apelido"> - <?php echo $dt_ini;?> até <?php echo $dt_fim;?> de <?php echo $hr_ini;?> às <?php echo $hr_fim;?></span></a>
    <div class="mensagem"> <?php echo $txt;?>
    </div>
   
</li>
<?php
	endforeach;
endif;
?>
     
        <div class="clear"></div>    
    	</ul>
        </div>