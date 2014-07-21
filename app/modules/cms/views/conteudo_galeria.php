<?php
$baseurl = base_url();
$imgcms = cms_img();
$path = $baseurl . $this->config->item('upl_imgs');
// pega segmento para saber se está no míodulo de pastas
if($this->uri->segment(2) == 'pastas'){
	// link para remover será para APAGAR
	$link_remove = '<a href="#" class="apagar-img">Apagar</a>';
	$class_nao = 'nao-apaga';
	$link_addImgFolder = '';
} else {
	$link_remove = '<a href="#" class="remover-img">Remover</a>';
	$class_nao = 'nao-confirma';
	$link_addImgFolder = 'ou <a href="'.cms_url($addImgFromFolder).'" class="nyroModal" target="_blank"><img src="'.$imgcms.'/ico-addfotopasta.png" alt="+ foto" width="20" height="21" style="position:absolute; margin-top:3px; margin-right:5px;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Importar de uma pasta</a>';
}
?>

<div class="gallery-top-bar">

	<div class="cell">
    <img src="<?php echo $imgcms;?>/step1.gif" width="25" height="24" alt="Passo - 1" align="left" style="margin-right:10px;" />
	<img src="<?php echo $imgcms;?>/ico-addfoto.png" alt="+ foto" width="18" height="18" align="left" style="margin-top:5px; margin-right:5px;" />
    <a href="<?php echo cms_url($linkAddImage);?>" target="_blank" class="nyroModal"><?php echo $labelAddImage;?></a> <?php echo $link_addImgFolder;?>
    </div><!-- .cell -->
    
    <div class="cell">
    	
        <img src="<?php echo $imgcms;?>/step2.gif" width="25" height="24" alt="Passo - 2" align="left" style="margin-right:10px; margin-top:0;" />
        <!--<span style="margin:0;">Após enviar suas imagens</span>-->
        <img src="<?php echo $imgcms;?>/ico-atualizar.png" width="17" height="17" alt="atualizar" style="margin-top:5px; margin-right:2px;" />
        <a href="<?php echo cms_url($linkReload);?>"> Atualizar alterações</a>
        
    </div><!-- .cell -->
    
    <div class="cell">
    	<span style="float:left;" title="Para alterar a tag de uma imagem vá na opção 'Editar' da imagem.">Tags: </span>
        <?php echo list_gallery_tags();?>
    </div><!-- .cell -->


</div><!-- .gallery-top-bar -->




<?php
		if(!$galery){
			echo '<div class="como-editar-img" align="center" style="clear:both;"><img src="'.cms_img().'como_editar_img.jpg" width="621" height="214" alt="como editar imagens" /></div>';
		}

	?>

<ul id="galeria" class="clear">
        <?php
		if($galery){
			$x = 1;
			foreach($galery as $i):

				if(is_array($i)){

				$id = $i['id'];
				$nome = $i['nome'];
				$thumb = thumb($nome);
				$compl = $path.'/'.$thumb;
				$desc = $i['descricao'];
				$input_desc = (strlen($desc) == 0) ? 'descreva...' : $desc;
				$pos = $i['pos'];
				$dim = ($pos == 'h') ? 'width="110"': 'height="110"';
				// controles
				$zoom = $path.'/'.$nome;
				$tag_id = $i['tag_opt'];

		?>
        <!--unidade de manipular fotos-->
        <li class="unidade-foto tag-opt-<?php echo $tag_id;?>" id="<?php echo $id;?>">
        <div class="drag"><?php echo $id;?></div>
        <div class="crop">
        	<div class="controle">
            	<a href="<?php echo $zoom;?>" class="nyroModal">Zoom</a>
                <a href="<?php echo cms_url('cms/imagem/editar/id:'.$id);?>" target="_blank" class="nyroModal">Editar</a>
				<?php echo $link_remove;?></div>
        	<div class="img" style="">            	
            	<img src="<?php echo $compl;?>" <?php echo $dim;?> alt=" " />
            </div>
        </div>
        <div class="desc"><textarea name="descricao" cols="" rows="" class="descricao"><?php echo $input_desc;?></textarea></div>
        <div class="confirma">Confirma?<br /><a href="#" class="<?php echo $class_nao;?>">não</a></div>
        </li>
        <!--unidade de manipular fotos fim-->
        <?php
		//////////////////////////////////////////////////////////
			} else {// a aimagem foi apagada
			$id = $i;
		?>
		<li class="unidade-foto" id="<?php echo $id;?>">
        <div class="drag"><?php echo $x;?></div>
        <div class="crop">
        	<div class="controle"><br /><?php echo $link_remove;?></div>
        	<div class="img" style="color:#C00;">Esta imagem foi apagada.</div>
        </div>
        <div class="desc"></div>
        <div class="confirma"></div>

        </li>
		<?php

			}
			$x++;
			endforeach;
		}
		?>

        </ul>