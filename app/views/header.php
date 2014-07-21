<header id="header">

	<div class="container">

		<div class="row">
			<div class="col-xs-12">
				<div class="col-md-4"></div>
				<div class="col-md-8">
					<p>LIGUE E MARQUE SUA CONSULTA     <span class="telefone">21 3799-8999</span></p>
				</div>
			</div>
		</div>


		<div class="row">
			<div class="col-xs-12">
				<div class="col-md-4">
					<div class="logo">
						<a href="./" title="<?php echo $titleSite ?>">
							<img src="<?php echo img()?>susga-logo.png" class="img-responsive"
							     alt="<?php echo $titleSite ?>"/></a>
					</div>
				</div>
				<div class="col-md-8">
					<img src="<?php echo img() ?>32anos.jpg" class="img-responsive cert"
					     alt="<?php echo $titleSite ?>"/>
					<div class="menu_container">
						<div class="col-md-4">
							<nav>
								<ul class="unstyled header-menu">
									<li class="<?php echo (is_home()) ? 'active' : '' ?>"><a href="./">HOME</a></li>
									<li class="<?php echo (is_page('exames')) ? 'active' : '' ?>"><a href="exames.php">EXAMES</a></li>
									<li class="<?php echo (is_page('convenios')) ? 'active' : '' ?>"><a href="convenios.php">CONVÊNIOS</a></li>
									<li class="<?php echo (is_page('faq')) ? 'active' : '' ?>"><a href="faq
									.php">FAQ</a></li>

								</ul>
							</nav>
						</div>
						<div class="col-md-6">
							<nav>
								<ul class="unstyled header-menu" >
									<li class="<?php echo (is_home()) ? 'active' : '' ?>"><a href="./">PRÉ AGENDAMENTO DE EXAMES</a></li>
									<li class="<?php echo (is_home()) ? 'active' : '' ?>"><a href="./">RESULTADO DOS EXAMES</a></li>
									<li class="<?php echo (is_home()) ? 'active' : '' ?>"><a href="./">FALE CONOSCO</a></li>
								</ul>
							</nav>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>



</header>