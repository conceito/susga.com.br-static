<div id="page" class="outra-classe">
	
    <h1>Questionário</h1>
    <h2><?php echo $survey->titulo ?></h2>

	<?php echo $survey->txt ?>

	<p>
		<a href="<?php echo site_url('questionarios/passo/'.$survey->nick.'/1') ?>" class="btn btn-primary">Começar agora</a>
	</p>


</div>