<?php
// valores dos itens
$enviados = count($stats['ok']);
$erros = count($stats['erros']);
$views = count($stats['views']);
$clicked = count($stats['clicked']);
$removed = count($stats['removed']);
$total = $enviados + $erros;
// percentuais
$percEnv = percentual($total, $enviados);
$percErr = percentual($total, $erros);
$percView = percentual($total, $views);
$percClick = percentual($total, $clicked);
$percRemoved = percentual($total, $removed);
//echo $total;
?>

<strong style="width: 100px; display: block; float: left;">Disparados</strong>
<div class="enquete-votos"><div class="bar" style="width:200px;">100 %</div><?php echo $total;?></div>

<br class="clear" /><br /><br />

<strong style="width: 100px; display: block; float: left;">Enviados</strong>
<div class="enquete-votos"><div class="bar" style="width:<?php echo $percEnv * 2;?>px;"><?php echo $percEnv;?> %</div><?php echo $enviados;?></div>

<br class="clear" /><br /><br />

<strong style="width: 100px; display: block; float: left;">Erros</strong>
<div class="enquete-votos"><div class="bar" style="width:<?php echo $percErr * 2;?>px;"><?php echo $percErr;?> %</div><?php echo $erros;?></div>

<br class="clear" /><br /><br />

<strong style="width: 100px; display: block; float: left;">Visualizações</strong>
<div class="enquete-votos"><div class="bar" style="width:<?php echo $percView * 2;?>px;"><?php echo $percView;?> %</div><?php echo $views;?></div>

<br class="clear" /><br /><br />

<strong style="width: 100px; display: block; float: left;">Clicks</strong>
<div class="enquete-votos"><div class="bar" style="width:<?php echo $percClick * 2;?>px;"><?php echo $percClick;?> %</div><?php echo $clicked;?></div>

<br class="clear" /><br /><br />

<strong style="width: 100px; display: block; float: left;">Removidos</strong>
<div class="enquete-votos"><div class="bar" style="width:<?php echo $percRemoved * 2;?>px;"><?php echo $percRemoved;?> %</div><?php echo $removed;?></div>

<br class="clear" /><br /><br />

<h3>Enviados</h3>
<a href="#" class="lnk-stt-showAll" style="float:left;">mostrar todos</a>

<table border="1" cellspacing="0" cellpadding="5" style="border-collapse:collapse; margin:0 0 0 140px;" bordercolor="#CCCCCC" class="stats">
<?php
foreach($stats['ok'] as $s):
	$id = $s['idUser'];
	$nome = $s['nome'];
	$email = $s['email'];
	$dt = $s['data'];
	$hr = $s['hora'];
?>

  <tr class="user-<?php echo $id?>">
    <td><?php echo $dt;?></td>
    <td><?php echo $hr;?></td>
    <td><a href="#" class="lnk-stt" title="clique para isolar este usuário"><?php echo $nome;?></a> </td>
    <td><?php echo $email;?></td>
  </tr>
<?php
endforeach;
?>
</table>


<h3>Visualizações</h3>
<a href="#" class="lnk-stt-showAll" style="float:left;">mostrar todos</a>

<table border="1" cellspacing="0" cellpadding="5" style="border-collapse:collapse; margin:0 0 0 140px;" bordercolor="#CCCCCC" class="stats">
<?php
foreach($stats['views'] as $s):
	$id = $s['idUser'];
	$nome = $s['nome'];
	$email = $s['email'];
	$dt = $s['data'];
	$hr = $s['hora'];
?>

  <tr class="user-<?php echo $id?>">
    <td><?php echo $dt;?></td>
    <td><?php echo $hr;?></td>
    <td><a href="#" class="lnk-stt" title="clique para isolar este usuário"><?php echo $nome;?></a> </td>
    <td><?php echo $email;?></td>
  </tr>
<?php
endforeach;
?>
</table>

<h3>Cliques em links</h3>
<a href="#" class="lnk-stt-showAll" style="float:left;">mostrar todos</a>

<table border="1" cellspacing="0" cellpadding="5" style="border-collapse:collapse; margin:0 0 0 140px;" bordercolor="#CCCCCC" class="stats">
<?php
foreach($stats['clicked'] as $s):
	$id = $s['idUser'];
	$nome = $s['nome'];
	$email = $s['email'];
	$dt = $s['data'];
	$hr = $s['hora'];
	$lnkNome = $s['linkNome'];
	$lnkUrl = $s['linkUrl'];
?>

  <tr class="user-<?php echo $id?>">
    <td><?php echo $dt;?></td>
    <td><?php echo $hr;?></td>
    <td><a href="#" class="lnk-stt" title="clique para isolar este usuário"><?php echo $nome;?></a> </td>
    <td><?php echo $email;?></td>
    
    <?php if($lnkUrl == 'desconhecido'):?>
    <td><?php echo $lnkNome;?> </td>
    <?php else :?>
    <td><a href="<?php echo $lnkUrl;?>" title="<?php echo $lnkNome;?>" target="_blank">link</a> </td>
    <?php endif;?>
  </tr>
<?php
endforeach;
?>
</table>


<h3>Removidos</h3>
<a href="#" class="lnk-stt-showAll" style="float:left;">mostrar todos</a>

<table border="1" cellspacing="0" cellpadding="5" style="border-collapse:collapse; margin:0 0 0 140px;" bordercolor="#CCCCCC" class="stats">
<?php
foreach($stats['removed'] as $s):
	$id = $s['idUser'];
	$nome = $s['nome'];
	$email = $s['email'];
	$dt = $s['data'];
	$hr = $s['hora'];
?>

  <tr class="user-<?php echo $id?>">
    <td><?php echo $dt;?></td>
    <td><?php echo $hr;?></td>
    <td><a href="#" class="lnk-stt" title="clique para isolar este usuário"><?php echo $nome;?></a> </td>
    <td><?php echo $email;?></td>
  </tr>
<?php
endforeach;
?>
</table>
