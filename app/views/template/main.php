<?php $bs = base_url();?>
<!DOCTYPE html>  
<!--[if lt IE 7 ]> <html lang="pt-BR" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="pt-BR" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="pt-BR" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="pt-BR" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="pt-BR" class="no-js"> <!--<![endif]-->
<head>

  <meta charset="utf-8">
  
  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  
  <title><?php if(isset($title))echo $title;?></title>
  
  <?php if(isset($metatags))echo $metatags;?>

  <!--  Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

  <?php if(isset($estilos)) echo $estilos;	?>
  
  <!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
  <script src="<?php echo $bs . app_folder();?>libs/js/modernizr.basic.js"></script>

<script type="text/javascript">
//variavel global para os JS
<?php if(isset($json_vars)) echo 'var CMS = '.json_indent($json_vars).';'; ?>
</script>	

<?php if(isset($scripts)) echo $scripts; ?>

<?php if(isset($page_scripts)) echo $page_scripts; ?>

</head>



<body class="<?php echo $this->body_class;?>">

<?php if(isset($header)){	echo $header;	}?>

<?php if(isset($body)){ echo $body;} ?>

<?php if(isset($footer)){ echo $footer;} ?>


</body>
</html>