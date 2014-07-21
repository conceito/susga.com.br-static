<ul class="unstyled">
	
    <?php
    if(isset($posts) || $posts):
	
		foreach($posts as $post):
		
		$id = $post['id'];
		$nick = $post['nick'];
		$titulo = $post['titulo'];
		$resumo = $post['resumo'];
		$dt_ini = $post['dt_ini'];
		$galeria = $post['galeria'];
		$grupo_nome = $post['grupo_nome'];
		$grupo_nick = $post['grupo_nick'];
		$grupo_id = $post['grupo_id'];
		$adminbar = $post['adminbar'];
		$uri = $post['uri'];
		
		// prepara thumb
		$imgPath = base_url().$this->config->item('upl_imgs').'/';
		
	?>

	<li <?php echo $adminbar;?> style="clear:both">
    	
        <?php if($galeria): // se existe imagem de capa
			
			if(isset($galeria[0])){
				$galeria = $galeria[0];
			}
		?>
    	<div class="img-limit"><a href="<?php echo site_url($uri);?>" class="thumbnail">
        <img src="<?php echo $imgPath.thumb($galeria['nome']);?>" alt="" />
        </a></div>
        <?php endif;?>
        
    	<a href="<?php echo site_url($uri);?>"><?php echo $titulo;?></a>
        
        <?php if(isset($post['preco_final'])):?>
        <div>R$ <?php echo $post['preco_final'];?></div>
        <p><a href="<?php echo site_url($uri);?>" class="btn btn-mini" date-add-cart="<?php echo $id;?>">+ detalhes</a></p>
        <?php endif;?>
        
    	<?php //echo post_tags_html($post, $this->uri->segment(1), 'a');?>
        <?php echo post_tags_html($post);?>
    </li>
    <?php
		endforeach;
		
    endif;
	?>
</ul>

	<?php echo $pagination;?>