<div class="preview">
	<ul>
    <?php
    if(count($listaArqs)){
		foreach($listaArqs as $arq):
			$id = $arq['id'];
			$nome = $arq['nome'];
			$ext = strtoupper($arq['ext']);			
			$peso = $arq['peso'];
			$path = base_url().'upl/arqs/'.$nome;
	?>
     <li class="arq" id="<?php echo $id;?>">
   <div class="ext"><?php echo $ext;?> | <?php echo $peso;?> Kb</div>
    <strong><?php echo $nome;?></strong>    
    <div class="controles"><a href="#" class="apagar-arq">apagar</a></div>
    </li>    
   
    <?php
		endforeach;
	}
	?>
   
    </ul>
    <div class="clear"></div>
</div>