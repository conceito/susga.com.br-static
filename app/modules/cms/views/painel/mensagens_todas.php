<div id="alertas"></div>
<div id="painel-mensagens">
<ul class="lista">
<?php 
if(! $mens):
?>
<li><span class="imp">&nbsp;</span>Não existem mensagens!
        	<div class="mensagem"> Não existem mensagens!</div>
        </li>
<?php
else:
	foreach($mens as $row):
	$id = $row['id'];
	$data = formaPadrao($row['data']);
	$hora = substr($row['hora'], 0, 5);
	$titulo = $row['assunto'];
	$txt = $row['txt'];
	$imp = $row['imp'];// importancia 0 ou 1
	$visitas = $row['visitas'];
	$status = $row['status'];
	$lido = $row['lido'];// já leu 0 ou 1
	$nick = $row['nick'];
	// prepara saida
	$mensagem = auto_link(nl2br($txt));
	$class = ($lido == 0) ? '' : 'lido';
	$title = 'em '.$data.' às '.$hora;
	$nivel = ($imp == 0) ? '' : '!';
?>
	<?php if($tipo == '' || ($tipo != '' && $tipo == $lido)):?>
 <li id="mens-<?php echo $id;?>"><span class="imp"><?php echo $nivel;?> </span> <a href="#" class="assunto <?php echo $class;?>" title="<?php echo $title;?>"><?php echo $titulo;?><span class="apelido"> - <?php echo $nick;?></span></a>
    <div class="mensagem"> <?php echo $mensagem;?>
    </div>
    <?php endif;?>
</li>
<?php
	endforeach;
endif;
?>
     
        <div class="clear"></div>    
    	</ul>
        </div>