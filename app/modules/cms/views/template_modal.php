<?php $baseurl = base_url();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
<head>
<?php if(isset($metatags))echo $metatags;?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<script type="text/javascript">
//variavel global para os JS
var V = new Array();
V['base_url'] = '<?php echo base_url() . app_folder();?>';
V['site_url'] = '<?php echo trim(cms_url(), '/').'/';?>';
V['uri'] = '<?php echo trim(uri_string(), '/');?>';//  sempre quarda a uri para os favoritos
V['tb'] = '<?php echo $tabela;?>';// tabela que será usada pelas funçoes ajax
V['var'] = '';// init var util
V['item_id'] = '<?php if(isset($item_id))echo $item_id;?>';// ID do item aberto
<?php
if(isset($json_vars)) echo 'var CMS = '.json_indent($json_vars).';';
?>
</script>

<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>libs/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>libs/jquery/jquery.delay.js"></script><!-- 2-->
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>ci_itens/js/funcoes-cms.js"></script><!-- 2-->
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>libs/jquery/ui.core.182.js"></script><!-- 4-->
<?php 
if(isset($scripts))echo $scripts;
?><script type="text/javascript" src="<?php echo $baseurl . app_folder();?>ci_itens/js/padrao-modal.js"></script>


<?php
if(isset($estilos))echo $estilos;
?>
<link rel="stylesheet" href="<?php echo $baseurl . app_folder();?>ci_itens/css/groundwork.css" />
<link href="<?php echo $baseurl . app_folder();?>ci_itens/css/ui/jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" media="screen" />
<link href="<?php echo $baseurl . app_folder();?>ci_itens/css/modal.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="titulo"><?php echo $title;?></div>

<?php if(isset($menu))echo $menu;?>

<?php if(isset($resposta))echo $resposta;?>

<?php if(isset($corpo))echo $corpo;?>

</body>
</html>