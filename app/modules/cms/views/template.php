<?php $baseurl = base_url();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
<head>
<?php if(isset($metatags)):	echo $metatags; endif; ?>
<meta http-equiv="content-language" content="pt" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?> - Gerenciador de Conteúdo versão <?php echo $this->config->item('cms_ver');?></title>
<!--padrão CMS-->
<script type="text/javascript">
//variavel global para os JS
var V = new Array();
V['base_url'] = '<?php echo base_url() . app_folder();?>';
V['site_url'] = '<?php echo trim(cms_url(), '/').'/';?>';
V['uri'] = '<?php echo trim(uri_string(), '/');?>';//  sempre quarda a uri para os favoritos
V['tb'] = '<?php echo $tabela;?>';// tabela que será usada pelas funçoes ajax
V['item_id'] = '<?php if(isset($item_id))echo $item_id;?>';// ID do item aberto, usado pelo tinymce
V['var'] = '';// init var util
V['superAlerta'] = false;
V['admin_tipo'] = '<?php echo $this->phpsess->get('admin_tipo', 'cms');?>';
<?php
if(isset($json_vars)) echo 'var CMS = '.json_indent($json_vars).';';
?>
</script>
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>libs/jquery/jquery-1.7.1.min.js"></script><!-- 1-->
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>libs/jquery/jquery.delay.js"></script><!-- 2-->
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>ci_itens/js/funcoes-cms.js"></script><!-- 2-->
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>libs/jquery/jquery.qtips.js"></script><!-- 3-->
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>libs/jquery/ui.core.182.js"></script><!-- 4-->
<?php
if(isset($scripts))echo $scripts;
?>
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>ci_itens/js/padrao-cms.js"></script>
<?php
if(isset($estilos))echo $estilos;
?>
<link rel="stylesheet" href="<?php echo $baseurl . app_folder();?>ci_itens/css/groundwork.css" />
<link href="<?php echo $baseurl . app_folder();?>ci_itens/css/ui/jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" media="screen" />


<link href="<?php echo $baseurl . app_folder();?>ci_itens/css/estilo-cms4.css" rel="stylesheet" type="text/css" media="all" />
<link href="<?php echo $baseurl . app_folder();?>ci_itens/css/estilo-cms-cliente.css" rel="stylesheet" type="text/css" media="all" />
<!--padrão CMS fim-->

</head>

<body class="<?php if(isset($body_class)) echo $body_class;?>">
<?php
if(isset($head))echo $head;
?>
<?php
if(isset($menu))echo $menu;
?>

<div id="content">

    <div class="wrap equalz">
    <!--<div class="round-top"><div></div></div>-->
      <div class="margin">
        <!--aqui entra o conteúdo-->
        <?php
        if(isset($corpo))echo $corpo;
        ?>
        <!--aqui entra o conteúdo fim-->
  <div class="clear"></div>
  </div>
  <!--<div class="round-bottom"><div></div></div>-->
</div>

</div>
<div id="footer">
	<ul>
        <li class="bloco1">Sistema de Gerenciamento de Conteúdo<br />
		versão <?php echo $this->config->item('cms_ver');?></li>

      <li class="bloco2">
        
      </li>

      <li class="bloco3">
        Todos os direito reservados &copy; 2009 - <?php echo date('Y');?><br />
      <a href="http://www.brunobarros.com" title="Bruno Barros - Comunicador Visual e Desenvolvedor Web" target="_blank">www.brunobarros.com</a></li>
      <div class="clear"></div>
    </ul>
     <div class="clear"></div>
</div>
</body>
</html>