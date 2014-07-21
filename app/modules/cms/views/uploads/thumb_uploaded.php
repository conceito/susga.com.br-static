<div class="preview">
	<ul>
    <?php
    if(count($listaImgs)){
		foreach($listaImgs as $img):
			$id = $img['id'];
			$nome = $img['nome'];
			$pos = $img['pos'];
			$pod = ($pos == 'h') ? 'width="90"' : 'height="90"';
			$thumb = thumb($nome);
			$path = base_url() . $this->config->item('upl_imgs') . '/'.$thumb;
	?>
    <li class="img" id="<?php echo $id;?>">
    <img src="<?php echo $path;?>" <?php echo $pod;?> alt=" " />
    	<div class="controles"><a href="#" class="apagar-arq" title="apagar">apagar</a><br /><a href="<?php echo cms_url('cms/imagem/editar/id:'.$id);?>" class="editar-arq nyroModal" target="_blank" title="editar">editar</a></div>
    </li>
    <?php
		endforeach;
	}
	?>
   
    </ul>
    <div class="clear"></div>
</div>