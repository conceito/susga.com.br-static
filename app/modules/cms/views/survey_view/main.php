<?php $baseurl = base_url();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
<head>
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
V['var'] = '';// init var util
V['superAlerta'] = false;

<?php

if(isset($json_vars)) echo 'var CMS = '.json_indent($json_vars).';';
?>
</script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<?php
if(isset($estilos))echo $estilos;
?>
<link rel="stylesheet" href="<?php echo $baseurl . app_folder();?>ci_itens/css/groundwork.css" />
<link href="<?php echo $baseurl . app_folder();?>ci_itens/css/ui/jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" media="screen" />


<link href="<?php echo $baseurl . app_folder();?>ci_itens/css/survey-view.css" rel="stylesheet" type="text/css" media="all" />
<link href="<?php echo $baseurl . app_folder();?>ci_itens/css/estilo-cms-cliente.css" rel="stylesheet" type="text/css" media="all" />
<!--padrão CMS fim-->

</head>

<body class="<?php if(isset($body_class)) echo $body_class;?>">

<div id="filters">

  <div class="container_12">
    
  <div class="row">
    <div class="span12">
  
      <?php if(isset($filters))echo $filters; ?>

    </div><!-- breadcrumb -->
  </div><!-- span -->
  </div>
</div><!-- filters -->


<div id="page" class="container_12">

  <div class="row">
    <div class="span12">
    <div id="breadcrumb">

      <?php if(isset($breadcrumb))echo $breadcrumb; ?>

    </div><!-- breadcrumb -->
    </div><!-- span -->

  </div><!-- row -->

  <div class="row">

    <div class="span12">
    <div id="main">

      <?php if(isset($main))echo $main; ?>

    </div><!-- breadcrumb -->
    </div><!-- span -->

  </div><!-- row -->

</div><!-- main -->

<?php if(isset($footer))echo $footer; ?>

</body>
</html>