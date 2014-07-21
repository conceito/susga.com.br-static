<div id="page" class="outra-classe">
	
    <h1>Questionário</h1>
    <h2><?php echo $survey->titulo ?> (<?php echo $progress->actual ?> de <?php echo $progress->total ?>)</h2>

    <h3><?php echo $step->titulo ?> <small><?php echo $step->resumo ?></small> </h3>

    <?php 
	/** ========================================================================
	 * 	Mensagem de retorno de erro do código
	 * ------------------------------------------------------------------------
	 */
	if($msg_type):
	?>
    <div class="alert alert-<?php echo $msg_type ?>">
    	<p><?php echo $msg; ?></p>
    </div>
    <?php 
    endif;
    ?>

	<?php echo form_open('questionarios/postStep/'.$survey->id.'/'.$progress->actual, 
				array(
				'class' => '-form-horizontal form-step-'.$progress->actual, 
				'id' => 'myform'
				));?>
	
	<?php echo form_hidden('survey_slug', $survey->nick) ?>

	<?php 
	/** ========================================================================
	 * 	Looping pela estrutura de um passo
	 * ------------------------------------------------------------------------
	 */
	foreach($stepStructure->loop() as $o):
	?>

		<?php 
		//
		// se for um grupo --------------------------
		if($o->tipo === 'survey_group'):
		?>
		<div class="survey-group">
	        <h4 class="survey-group-title"><?php echo $o->titulo ?> <small><?php echo $o->resumo ?></small></h4>        
	    </div>
		<?php 
		//
		// é uma questão -----------------------------
		else:
		?>

		<div class="control-group control-<?php echo $o->query_type?>">
	        <label class="control-label" for="<?php echo $o->query_type .'_'. $o->id ?>"><?php echo $o->titulo ?> <small><?php echo $o->resumo ?></small></label>
	        <div class="controls">
	        	<?php echo $stepStructure->form($o, $o->query_type .'_'. $o->id) ?>
	        </div>
	    </div>

		<?php 
		endif;// fim questão
		?>


    <?php 
    endforeach;// looping pela estrutura de um passo
    ?>





	<div class="form-actions">

		<?php if($progress->last): ?>
		<button type="submit" class="btn btn-primary">Finalizar</button>
		<?php else: ?>
		<button type="submit" class="btn btn-primary">Continuar</button>
		<?php endif; ?>
      
    </div>


	<?php echo form_close();?>

</div>