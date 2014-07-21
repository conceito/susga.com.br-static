<?php
// tipo de grupo
if($tipoGrupo == 'pastas'){
	
	$titulo = set_value('titulo', $row['titulo']);
	$nick = set_value('nick', $row['nick']);
	$desc = set_value('txt', $row['txt']);
	
} else if($tipoGrupo == 'usuarios'){
	$titulo = set_value('titulo', $row['nome']);
	$nick = set_value('nick', $row['nome']);
	$desc = set_value('txt', $row['obs']);
	
} else {
	$titulo = set_value('titulo', $row['titulo']);
	$nick = set_value('nick', $row['nick']);
	$desc = set_value('txt', $row['resumo']);
	
}
?>

<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

<?php // se existe relacionamento de grupos
if( isset($rel) ):?>
<label for="rel" class="lb-full">Grupo relacionado</label><?php echo (! $rel)? 'Não existem.<br />' : $rel;?>

<br />
<?php endif; ?>

<label for="titulo" class="lb-full">Título</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo $titulo;?>" />
<br />

<label for="" class="lb-full">&nbsp;</label>
<input name="nick_edita" id="nick_edita" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo $nick;?>" readonly="readonly" />
<?php echo i('Não pode ser alterado.<br />Identificação deste registro.');?>

<br />


<label for="cor" class="lb-full">Cor do texto</label>
<div class="input-prepend color" data-color="rgb(<?php echo hex2rgb($row['cor1']);?>)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor1" value="<?php echo set_value('cor1',  $row['cor1']);?>" >
  <span class="add-on"><i style="background-color: <?php echo $row['cor1'];?>"></i></span>
</div>

<br />

<label for="cor" class="lb-full">Cor do fundo</label>
<div class="input-prepend color" data-color="rgb(<?php echo hex2rgb($row['cor2']);?>)" data-color-format="hex">
  <input type="text" class="input-cor" name="cor2" value="<?php echo set_value('cor2',  $row['cor2']);?>" >
  <span class="add-on"><i style="background-color: <?php echo $row['cor2'];?>"></i></span>
</div>


<label for="txt" class="lb-full">Descrição</label><textarea name="txt" class="textarea-curto" id="txt"><?php echo $desc;?></textarea>

  
</div><!-- .panel-left -->


