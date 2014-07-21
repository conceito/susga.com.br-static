<?php
$baseurl = base_url();
$imgcms = cms_img();
$path = $baseurl.$this->config->item('upl_arqs');
// pega segmento para saber se está no míodulo de pastas
if($this->uri->segment(2) == 'pastas'){
	// link para remover será para APAGAR
	$link_remove = '<a href="#" title="apagar arquivo" class="apagar-arq"></a>';
	$class_nao = 'nao-apaga';
} else {
	$link_remove = '<a href="#" title="remover arquivo" class="remover-arq"></a>';
	$class_nao = 'nao-confirma';
}
?>
<div class="gallery-top-bar">

	<div class="cell">
        <img src="<?php echo $imgcms;?>/step1.gif" width="25" height="24" alt="Passo - 1" align="left" style="margin-right:10px;" /> 
    <img src="<?php echo $imgcms;?>/ico-addarq.gif" alt="+ arquivo" width="19" height="18" align="left" style="margin-top:5px; margin-right:5px;" />
    <a href="<?php echo site_url($linkAddImage);?>" target="_blank" class="nyroModal"><?php echo $labelAddImage;?></a>
    </div><!-- .cell -->
    
    <div class="cell">
        <img src="<?php echo $imgcms;?>/step2.gif" width="25" height="24" alt="Passo - 2" align="left" style="margin-right:10px; margin-top:0;" />
    Após enviar seus arquivos        
    <img src="<?php echo $imgcms;?>/ico-atualizar.png" width="17" height="17" alt="atualizar" style="margin-top:5px; margin-right:2px;" />
    <a href="<?php echo site_url($linkReload);?>"> Atualize esta tela</a>
    </div><!-- .cell -->

</div><!-- .gallery-top-bar -->

	<?php 
		if(!$galery){
			echo '<div class="como-editar-img" align="center" style="clear:both;"><img src="'.cms_img().'como_editar_arq.jpg" width="676" height="214" alt="como editar arquivos" /></div>';	
		}
			
	?>

  <ul id="galeria" class="clear">
        <?php 
		if($galery){
			$x = 1;
			foreach($galery as $i):
				$id = $i['id'];
				$nome = $i['nome'];				
				$compl = $path.'/'.$nome;
				$desc = $i['descricao'];
				$input_desc = (strlen($desc) == 0) ? $nome : $desc;
				$ext = strtoupper($i['ext']);
				$peso = ($i['peso']==0) ? 'externo' : format_bytes($i['peso']);
				$link_ver = link_ver_arquivo($path, $nome, $i['ext'], $i['img']);
				$downl = $i['downloaded'];
				
				
				
		?>
        <!--unidade de manipular fotos-->
        <li class="unidade-arq" id="<?php echo $id;?>">
        <div class="drag"><?php echo $id;?></div>
       <div class="info">
        	<div class="ext"><?php echo $ext;?> &nbsp; <?php echo $peso;?></div>
            <div class="det"> <span title="Quantas vezes foi baixado"><b>&darr;</b> <?php echo $downl;?></span></div>
            <div class="del"><?php echo $link_remove;?><?php echo $link_ver;?></div>
        </div>
        <div class="desc"><textarea name="descricao" cols="" rows="" class="descricao"><?php echo $input_desc;?></textarea></div>
        <div class="confirma">Confirma?<br /><a href="#" class="<?php echo $class_nao;?>">não</a></div>
        </li>
        <!--unidade de manipular fotos fim-->
        <?php
			$x++;
			endforeach;
		}
		?>
        
        </ul>