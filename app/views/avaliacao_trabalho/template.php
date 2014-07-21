<?php $bs = base_url(); ?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html lang="pt-BR" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]><html lang="pt-BR" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]><html lang="pt-BR" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]><html lang="pt-BR" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="pt-BR" class="no-js"> <!--<![endif]-->
<head>

	<meta charset="utf-8">

	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		 Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php if (isset($title)) echo $title; ?></title>

	<?php if (isset($metatags)) echo $metatags; ?>

	<!--  Mobile viewport optimized: j.mp/bplateviewport -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">

	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,700,300' rel='stylesheet' type='text/css'>
	<?php if (isset($estilos)) echo $estilos; ?>

	<!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
	<script src="<?php echo $bs . app_folder(); ?>libs/js/modernizr.basic.js"></script>

	<script type="text/javascript">
		//variavel global para os JS
		<?php if(isset($json_vars)) echo 'var CMS = '.json_indent($json_vars).';'; ?>
	</script>

	<?php if (isset($scripts)) echo $scripts; ?>

	<?php if (isset($page_scripts)) echo $page_scripts; ?>

	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-50606288-1', 'congressogestaltrio.com.br');
		ga('send', 'pageview');

	</script>
</head>


<body class="<?php echo $this->body_class; ?>">


<header id="header-avaliacao">

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-8">
				<a href="<?php echo site_url() ?>" title="página inicial"><?php echo $this->config->item('title')?></a>
			</div>
			<div class="col-xs-4 text-right">
				<a href="<?php echo site_url('avaliacao/logout')?>">SAIR</a>
			</div>
		</div>
	</div>



</header>


<div class="container-fluid site-wrapper">


			<?php
			if(isset($corpo))
			{
				echo $corpo;

			}

			?>



</div>
<!--container-->

<footer id="footer-avaliacao" class="clearfix">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12">
				<p><a href="mailto:contato@congressogestaltrio.com.br">contato@congressogestaltrio.com.br</a> |
					<a href="<?php echo site_url('contato')?>"><strong>CONTATO</strong></a></p>
			</div>
		</div>
	</div>

</footer>



<?php //$this->cms_adminbar->generate(); ?>
</body>
</html>