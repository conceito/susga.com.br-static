<?php include("config.php") ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="pt-BR" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="pt-BR" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="pt-BR" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="pt-BR" class="no-js"> <!--<![endif]-->
<head>
	<?php include("head_before.php") ?>
	<title><?php echo $titleSite?></title>
	<meta name="title" content="Contato - <?php echo $titleSite?>">
	<meta name="description" content="--DESCRIPTION HERE--">
	<?php include("head_after.php") ?>
</head>
<body class="<?php body_class()?>">

<?php include("header.php") ?>

<div id="main" class="clearfix" role="main">

	<div class="container">
		<div class="row">
			<div class="col-xs-12">

				<h1>Contato</h1>

				<?php
				/*
				|===================================================================================
				|        Alertas
				|-----------------------------------------------------------------------------------
				*/
				if (!empty($error)):
					?>
					<div class="alert alert-<?php echo ($error['id'] == 0) ? 'success' : 'danger'; ?>">
						<p><?php echo $error['msg']; ?></p>
					</div>
				<?php
				endif;
				?>

				<form action="mail.php?t=contato" method="post" role="form" class="form-horizontal">

					<div class="form-group">
						<label for="nome_field" class="col-sm-2 control-label">Nome</label>
						<div class="col-sm-8">
							<input type="text" name="nome" class="form-control" id="nome_field" value="<?php echo
							set_value
							('nome')?>" required>
						</div>
					</div>

					<div class="form-group">
						<label for="email_field" class="col-sm-2 control-label">E-mail</label>
						<div class="col-sm-8">
							<input type="email" name="email" class="form-control" id="email_field" value="<?php
							echo set_value('email')?>"
							       required>
						</div>
					</div>


					<div class="form-group">
						<label for="mensagem_field" class="col-sm-2 control-label">Mensagem</label>
						<div class="col-sm-8">
							<textarea name="mensagem" id="mensagem_field" class="form-control" cols="30" rows="10" required><?php echo set_value('mensagem')?></textarea>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-8">
							<div class="checkbox">
								<label>
									<input type="checkbox"> Remember me
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-8">
							<button type="submit" class="btn btn-default">ENVIAR MENSAGEM</button>
						</div>
					</div>
				</form>

			</div>
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
