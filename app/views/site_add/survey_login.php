<div id="page" class="outra-classe">
	
    <h1>Questionário</h1>
    <h2><?php echo $survey->titulo ?></h2>

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

	<?php echo form_open('questionarios/postValidateCode', array('class' => 'form-horizontal', 'id' => 'myform'));?>
	
    <?php echo form_hidden('survey_slug', $survey->nick) ?>
	<?php echo form_hidden('survey_id', $survey->id) ?>
	
	<div class="control-group">
        <label class="control-label" for="code">Código de acesso</label>
        <div class="controls">
          <input type="text" id="code" name="code" placeholder="Código de acesso" value="<?php echo set_value('code');?>">
          <?php echo form_error('code');?>
        </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Entrar</button>
    </div>

    <a href="#">Não lembro meu código</a> | 
    <a href="#">Não está funcionando</a>

	<?php echo form_close();?>


</div>