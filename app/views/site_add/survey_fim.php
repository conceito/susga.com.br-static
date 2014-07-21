<div id="page" class="outra-classe">
	
    <h1>Questionário</h1>
    <h2><?php echo $survey->titulo ?></h2>

    <?php 
    /** ========================================================================
     *  Mensagem de retorno de erro do código
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

	<h1>Obrigado</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis, odio, odit numquam quo natus nemo tempore sed quam suscipit mollitia architecto voluptate maiores repellat fugit dolores blanditiis at officiis aperiam.</p>


</div>