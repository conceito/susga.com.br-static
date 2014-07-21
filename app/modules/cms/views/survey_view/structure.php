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
	<div class="step-bar bar">
		<span class="type"><?php echo $stepCount?>)</span> <span class="title"><?php echo $step->titulo ?></span>		
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
//            dd($query);
		?>
		<div class="query-bar bar">
			<span class="type">&gt;</span> <span class="title"><?php echo $query->titulo ?></span>

            <script type="text/javascript">
                $(document).ready(function(){
                    var url = V['base_url'] + 'cms/surveyGraph/index/queryId:' + <?php echo $query->id?>;
                    url = url + '/type:table';
                    var container = '#output-' + <?php echo $query->id?>;
                    console.log(url);

                    $.ajax(url).done(function(req){
                        $(container).html(req);
                    });
                });
            </script>

            <div id="output-<?php echo $query->id?>" class="output">
                <div class="loading"></div>
            </div>
			<?php
            // render the answer graphic
            //echo $this->surveyquery->render($query);
            ?>
			
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
		<div class="group-bar bar">
			<span class="title"><?php echo $group->titulo ?></span>			
		</div>
	</div><!-- group-group -->
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
				<span class="type">&gt;</span> <span class="title"><?php echo $query->titulo ?></span>

                <script type="text/javascript">
                    $(document).ready(function(){
                        var url = V['base_url'] + 'cms/surveyGraph/index/queryId:' + <?php echo $query->id?>;
                        url = url + '/type:table';
                        var container = '#output-' + <?php echo $query->id?>;
                        console.log(url);

                        $.ajax(url).done(function(req){
                            $(container).html(req);
                        });
                    });
                </script>

                <div id="output-<?php echo $query->id?>" class="output">
                    <div class="loading"></div>
                </div>
                <?php
                // render the answer graphic
                //echo $this->surveyquery->render($query);
                ?>
				
			</div><!-- query-bar -->
			<?php 
			endforeach;
			?>
			
		</div><!-- queries-group -->
		<?php 
		endif;//perguntas do grupo
		?>

	
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



<?php endif; ?>