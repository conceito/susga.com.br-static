<style type="text/css">
	.job-label{
		font-weight: 700;
		color: #999;
		border-bottom: #D8D8D8 solid 1px;
	}

	.job-content{
		margin: 5px 0 3em;
		line-height: 20px;
	}

</style>

<?php if(isset($eixo_tematico)):?>
	<div class="job-label">Eixo temático</div>
	<div class="job-content">
		<?php echo $eixo_tematico ?>
	</div>
<?php endif;?>

<?php if(isset($modalidade)):?>
	<div class="job-label">Modalidade</div>
	<div class="job-content">
		<?php echo $modalidade?>
	</div>
<?php endif;?>

<?php if(isset($titulo)):?>
	<div class="job-label">Título</div>
	<div class="job-content">
		<?php echo $titulo?>
	</div>
<?php endif;?>

<?php if(isset($subtitulo)):?>
	<div class="job-label">Subtítulo</div>
	<div class="job-content">
		<?php echo $subtitulo?>
	</div>
<?php endif;?>

<?php if(isset($resumo)):?>
	<div class="job-label">Resumo</div>
	<div class="job-content">
		<?php echo $resumo?>
	</div>
<?php endif;?>

<?php if(isset($tags)):?>
	<div class="job-label">Palavras-chave</div>
	<div class="job-content">
		<?php echo $tags?>
	</div>
<?php endif;?>

<?php if(isset($txt)):?>
	<div class="job-label">Proposta</div>

	<span class="job-content">
			<?php echo $txt?>
	</span>
<?php endif;?>