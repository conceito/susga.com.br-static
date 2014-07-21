<h1><span class="h1 <?php echo $css;?>"><?php echo ellipsize($title, 60, .5);?></span>

<?php if(isset($front_uri) && $front_uri != ''):?>
<div class="link-to-fontend"><a href="<?php echo cms_url($front_uri);?>">&larr; ver no site</a></div>
<?php endif;?>

<?php if($modulo['pasta_ajuda'] != 0):?>
<span class="ico-ajuda"><a href="<?php echo cms_url('cms/administracao/ajudaModulo/co:'.$modulo['id']);?>" title="Ajuda deste módulo" class="nyroModal" target="_blank">Ajuda do módulo</a></span>
<?php endif;?>
</h1>