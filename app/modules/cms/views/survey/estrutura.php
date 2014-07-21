<?php 
/** ========================================================================
 * 	exibição da estrutura do questionário
 * 	cms/survey/edita/co:/id:
 * ------------------------------------------------------------------------
 */
echo validation_errors(); 
?>

<div class="panel-left clearfix" style="width:75%">

	<?php 
	/** ========================================================================
	 * 	Inicia loop pela estrutura da survey
	 * ------------------------------------------------------------------------
	 */
	if($structure):
	$stepCount = 1;
	foreach ($structure->steps() as $step):
	?>
	<div class="step-group">
		<div class="step-bar bar"><span class="type">Passo #<?php echo $stepCount?> -</span> <span class="title"><?php echo $step->titulo ?></span>
			<a href="<?php echo cms_url($step->editUri)?>" class="btn">editar</a>
		</div>

		<?php 
		/** ========================================================================
		 * 	Se existem perguntas sem grupo
		 * ------------------------------------------------------------------------
		 */
		if($structure->queries($step->id)):
		?>
		<div class="queries-group">
			
			<?php 
			foreach ($structure->queries($step->id) as $query):
			?>
			<div class="query-bar bar">
				<span class="type">?</span> <span class="title"><?php echo $query->titulo ?></span>
				<a href="<?php echo cms_url($query->editUri)?>" class="btn">editar</a>
			</div><!-- query-bar -->
			<?php 
			endforeach;
			?>
			
		</div><!-- queries-group -->
		<?php 
		endif;//perguntas sem grupo
		?>

		<?php 
		/** ========================================================================
		 * 	Looping pelos grupos de um passo
		 * ------------------------------------------------------------------------
		 */
		if($structure->groups($step->id)):
		?>
		<?php 
		$groupCount = 1;
		foreach ($structure->groups($step->id) as $group):
		?>
		<div class="group-group">
			<div class="group-bar bar"><span class="type">Grupo #<?php echo $groupCount?> -</span> <span class="title"><?php echo $group->titulo ?></span>
				<a href="<?php echo cms_url($group->editUri)?>" class="btn">editar</a>
			</div>

			<?php 
			/** ========================================================================
			 * 	Loop pelas questões do grupo
			 * ------------------------------------------------------------------------
			 */
			if($structure->queries($group->id)):
			?>
			<div class="queries-group">
				
				<?php 
				foreach ($structure->queries($group->id) as $query):
				?>
				<div class="query-bar bar">
					<span class="type">?</span> <span class="title"><?php echo $query->titulo ?></span>
					<a href="<?php echo cms_url($query->editUri)?>" class="btn">editar</a>
				</div><!-- query-bar -->
				<?php 
				endforeach;
				?>
				
			</div><!-- queries-group -->
			<?php 
			endif;//perguntas do grupo
			?>

		</div><!-- group-group -->
		<?php 
		$groupCount++;
		endforeach;// groups
		?>

		<?php 
		endif;
		?>

	
	</div><!-- step-group -->
	<?php 
	$stepCount++;
	endforeach;// steps

	endif;
	?>



	<?php 
	# modelo
	if(true == false):
	 ?>
	<!-- grupo de passos  -->
	<div class="step-group">
		<div class="step-bar bar"><span class="type">Passo #1 -</span> <span class="title">Quanto aos seus primeiros atendimentos no consultório do cirurgião</span>
			<a href="#" class="btn">editar</a>
		</div>

		<div class="queries-group">

			<div class="query-bar bar">
				<span class="type">?</span> <span class="title">Atendimento ao telefone para marcação da consulta (rapidez, presteza, educação, etc)</span>
				<a href="#" class="btn">editar</a>
			</div><!-- query-bar -->

			<div class="query-bar bar">
				<span class="type">?</span> <span class="title">Atendimento ao telefone para marcação da consulta (rapidez, presteza, educação, etc)</span>
				<a href="#" class="btn">editar</a>
			</div><!-- query-bar -->
			
		</div><!-- queries-group -->
		
		<div class="group-group">
			<div class="group-bar bar"><span class="type">Grupo #1 -</span> <span class="title">Quanto aos seus primeiros atendimentos no consultório do cirurgião (Dr. Alexandre Siciliano)</span>
				<a href="#" class="btn">editar</a>
			</div>
			<div class="queries-group">

				<div class="query-bar bar">
					<span class="type">?</span> <span class="title">Atendimento ao telefone para marcação da consulta (rapidez, presteza, educação, etc)</span>
					<a href="#" class="btn">editar</a>
				</div><!-- query-bar -->

				<div class="query-bar bar">
					<span class="type">?</span> <span class="title">Atendimento ao telefone para marcação da consulta (rapidez, presteza, educação, etc)</span>
					<a href="#" class="btn">editar</a>
				</div><!-- query-bar -->
				
			</div><!-- queries-group -->
			
		</div><!-- group-group -->


	</div><!-- step-group -->
	<?php endif; ?>

</div><!-- .panel-left -->


<div class="panel-right clearfix" style="width:20%">

	<div class="control-group box">
		<br>
		<div class="btn-group btn-group-vertical">
			<a class="btn btn-warning" href="<?php echo cms_url($addStepLink)?>" style="text-align: left;"> <i class="icon-plus icon-white"></i> Adicionar Passo </a>
			<a class="btn btn-warning" href="<?php echo cms_url($addGroupLink)?>" style="text-align: left;"> <i class="icon-plus icon-white"></i> Adicionar Grupo </a>
			<a class="btn btn-warning" href="<?php echo cms_url($addQueryLink)?>" style="text-align: left;"> <i class="icon-plus icon-white"></i> Adicionar Questão </a>
		</div>
		
	</div>

	<div class="control-group box">
		<br>
		<a class="btn btn-primary" href="<?php echo cms_url('cms/surveyView/index/'. $row['id']) ?>" target="_blank"> <i class="icon-eye-open icon-white"></i> Ver relatório </a>
		
	</div>


</div><!-- .panel-right --> 