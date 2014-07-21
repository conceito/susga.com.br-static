<?php include("config.php") ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="pt-BR" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="pt-BR" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="pt-BR" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="pt-BR" class="no-js"> <!--<![endif]-->
<head>
<?php include("head_before.php") ?>
<title><?php echo $titleSite?></title>
<meta name="title" content="--PAGE TITLE HERE-- - <?php echo $titleSite?>">
<meta name="description" content="--DESCRIPTION HERE--">
<?php include("head_after.php") ?>
</head>
<body class="<?php body_class()?>">

<?php include("header.php") ?>

<div id="main" class="clearfix" role="main">

	<div class="container">
		<div class="row">
			<div class="col-xs-12">
                <div class="col-md-6">
                    <p>
                    Precisão nos diagnósticos, alta qualidade dos serviços e tecnologia são
                    os elementos que compõem o SUSGA. Com imagens de alta resolução, é
                    possível obter resultados mais precisos em menos tempo e com menor
                    exposição à radiação.
                    Ligue e marque seus exames diretamente pela central de atendimento.</p>
                </div>
                <div class="col-md-6">
                    <div class="redes">
                    <p> ESTAMOS NAS REDES</p>
                        <img src="<?php echo $base_url ?>../assets/img/facebook.jpg"
                             alt="<?php echo $titleSite ?>"/>
                        <img src="<?php echo $base_url ?>../assets/img/twitter.jpg"
                             alt="<?php echo $titleSite ?>"/>
                        <img src="<?php echo $base_url ?>../assets/img/youtube.jpg"
                             alt="<?php echo $titleSite ?>"/>
                    </div>
			</div>
		</div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="col-md-6">
                    <div class="bt transparent">
                        <img src="<?php echo $base_url ?>../assets/img/header-menu-left.png"
                             alt="<?php echo $titleSite ?>"/>
                        <h3>EXAMES</h3>
                        <img src="<?php echo $base_url ?>../assets/img/header-plus.png"
                             alt="<?php echo $titleSite ?>" class="bt-right"/></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 menu1 transparent">
        <div class="container">
        <div class="col-md-6 menu1-body">
        ULTRASSOM<br/>
            ULTRASSOM<br/>
            ULTRASSOM<br/> ULTRASSOM<br/>

        </div>
        <div class="col-md-6">
            ULTRASSOM
        </div>
    </div>
</div><!-- main -->

<?php include("footer.php") ?>

<script type="text/javascript">
(function($){

})(jQuery);
</script>

</body>
</html>
