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
<!--[if lt IE 7]>
            <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->

<?php if(isset($topo)){	echo $topo;	}?>




<div class="container container-fluid" >
    <header class="row-fluid clearfix">
    	<div class="span12">
        	<div class=" hero-unit">
            <h1 style="margin:0">Template</h1>
			<h2><?php if(isset($title))echo $title;?></h2>
            </div>
        </div>
    </header>
    
    <div class="cart-bar">
    <div id="cart"><?php echo $this->cms_loja->output_cart();?></div>
    <?php echo $this->breadcrumb->output();?> 
    </div><!-- .cart-bar -->  
    
    <div id="main" class="row-fluid">
    	
        <div class="span4">
        	<?php if(isset($menu)){ echo $menu;} ?>
        </div>
        
        
        <div class="span8">
        
        <?php if(isset($corpo)){ echo $corpo;} ?>			
			
        
        </div>
        
    
    </div>
    
    <footer class="row-fluid">
    	<div class="span12">
			<?php if(isset($rodape)){	echo $rodape; }?>
        </div>
    </footer>
    
    
    
  </div> <!--! end of #container -->



  



  <!-- asynchronous google analytics: mathiasbynens.be/notes/async-analytics-snippet 
       change the UA-XXXXX-X to be your site's ID -->
  <script>
   var _gaq = [['_setAccount', 'UA-XXXXX-X'], ['_trackPageview']];
   (function(d, t) {
    var g = d.createElement(t),
        s = d.getElementsByTagName(t)[0];
    g.async = true;
    g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    s.parentNode.insertBefore(g, s);
   })(document, 'script');
  </script>


<?php $this->cms_adminbar->generate();?>
</body>
</html>