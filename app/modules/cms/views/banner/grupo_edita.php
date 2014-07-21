<?php
// tipo de grupo

	$titulo = set_value('titulo', $row['titulo']);
	$nick = set_value('nick', $row['nick']);
	$desc = set_value('txt', $row['resumo']);
	

?>

<?php echo validation_errors(); ?>



<label for="titulo" class="lb-full">Nome</label>
<input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo $titulo;?>" />

<br />

<label for="resumo" class="lb-full">Resumo</label>
<textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo', $row['resumo']);?></textarea>

  
<br />
        
    <label for="banners_type" class="lb-full">Tamanho</label>

    
    
    <div class="small-input-group">
        <input name="banner_width" id="banner_width" type="text" class="input-mini" value="<?php echo set_value('banner_width', $row['banner_width']);?>">
        <div class="divide">x</div>
        <input name="banner_height" id="banner_height" type="text" class="input-mini" value="<?php echo set_value('banner_height', $row['banner_height']);?>">
    </div>
    
    <br />
    
    <label for="ordem" class="lb-full">Exibição</label>
    <select name="ordem" id="ordem" class="input-combo">
        <option value="rand" <?php echo ($row['txt']=='rand')?'selected':''; ?>>Aleatória</option>
        <option value="ordem" <?php echo ($row['txt']=='ordem')?'selected':''; ?>>Ordenada</option>                
    </select>
    



