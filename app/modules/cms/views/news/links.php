<?php
$baseurl = base_url() . app_folder();


?>
<label for="status">&nbsp;</label><a href="<?php echo cms_url('cms/news/linkNovo/id:'.$row['id']);?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">Adicionar links</b></span></a>
<img src="<?php echo cms_img();?>ico-atualizar.png" width="17" height="17" alt="atualizar" style="margin-left:10px; margin-top:5px; margin-right:2px;" />
<a href="<?php echo cms_url('cms/news/edita/id:'.$row['id'].'/co:'.$co.'/tab:3');?>"> Atualize esta tela</a>
<br /><br />

<h3>Links desta newsletter</h3>

<?php 
if(! $links['links']):
?>
<strong>Não existe link.</strong>
<?php else: 
	
	foreach($links['links'] as $row):
		$id = $row['id'];
		$tit = $row['titulo'];
		$url = $row['url'];
		$urlNews = $baseurl.'ci_itens/newsletter_link.php?l=[IDMEN]-'.$id.'-[USER]';
	?>
    <label style="width:40px;">&nbsp;</label><strong><?php echo $tit;?></strong> &raquo; <em><?php echo $url;?></em>
    <br class="clear" />
    <label>Link rastreável</label><input name="" id="" type="text" class="input-longo" value="<?php echo $urlNews;?>" />
    
    <br />
    <?php
	endforeach;
	
endif;
?>