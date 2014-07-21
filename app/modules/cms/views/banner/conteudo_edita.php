


<?php echo validation_errors(); ?>

<div class="panel-stats banner">
	
    <div class="cell">    	
        <h2>Limite</h2>
        <p><?php echo $row['limit'];?></p>
        <span class="help-block">Limite máximo de exibições.</span>
        
    </div>
    
    <div class="cell">    	
        <h2>Views</h2>
        <p><?php echo $row['views'];?></p>
        <span class="help-block">Quantas vezes o banner foi visualizado.</span>
        
    </div>
    
    <div class="cell">    	
        <h2>Clicks</h2>
        <p><?php echo $row['clicks'];?></p>
        <span class="help-block">Quantas vezes o banner foi clicado.</span>
        
    </div>
    
    <div class="cell">    	
        <h2>CTR</h2>
        <p><?php echo ctr($row['clicks'], $row['views']);?>%</p>
        <span class="help-block">Percentual do retorno de cliques sobre as exibições.</span>
        
    </div>
    
</div><!-- .panel-stats -->

<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>

<div class="panel-left clearfix">

	    
    <div class="control-group ">
    
  	<label for="grupos" class="lb-full"><b class="obr">[!]</b> Grupo</label>
	<?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
	
	</div><!-- .control-group -->
    
    <div class="control-group ">
	
	<label for="titulo" class="lb-full"><b class="obr">[!]</b> Título principal</label>
    <input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', $row['titulo']);?>" />
	
	</div><!-- .control-group -->
 
    <div class="control-group ">
    
    <label for="txtmulti" class="lb-full">Descrição secundária</label>
    <input name="txtmulti" id="txtmulti" type="text" class="input-longo input-longo" value="<?php echo set_value('txtmulti', $row['txtmulti']);?>" style="width:98%" />
    
    </div><!-- .control-group -->  
	
    <div class="control-group ">
	 
	 <label for="txt" class="lb-full">Link</label>     
     <input name="txt" id="txt" type="text" class="input-longo" value="<?php echo set_value('txt', $row['txt']);?>" />
     
    </div><!-- .control-group -->
    
    <div class="control-group ">
    
    
    <label for="" class="lb-full">Abrir link...</label>
	<div class="form-opcoes group-buttons">
    
    <?php echo form_radio(array(
    'name'        => 'target',
    'id'          => 'target_blank',
    'value'       => '_blank',
    'checked'     => ($row['target']=='_blank'),
    'style'       => '',
    ));?> <label for="target_blank">em nova janela</label>
    
    <?php echo form_radio(array(
    'name'        => 'target',
    'id'          => 'target_top',
    'value'       => '_top',
    'checked'     => ($row['target']=='_top'),
    'style'       => '',
    ));?> <label for="target_top">na mesma janela</label>    
	
    </div>
    
    </div><!-- .control-group -->
    
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	<input type="hidden" name="arquivo" value="<?php echo $row['resumo'];?>" />
	<?php 
	if($swfUplForm){
		echo '<div class="control-group box"><div class="attached-box">';
	  	echo $swfUplForm;	
		echo '</div></div>';
	}
	?>
	
    



    <div class="control-group box">

	<label for="dt1" class="lb-full">Publicar em</label>
    <input name="dt1" id="from" type="text" class="input-curto" value="<?php echo set_value('dt1', $row['dt1']);?>" />
    
    </div><!-- .control-group -->
    
    <div class="control-group box">
    
    <label for="dt2" class="lb-full">Remover em</label>
    <input name="dt2" id="to" type="text" class="input-curto" value="<?php echo set_value('dt2', $row['dt2']);?>" />
    
    </div><!-- .control-group -->
    
    <div class="control-group box">
    
    <label for="limit" class="lb-full">Limite de impressões</label>
    <input name="limit" id="limit" type="text" class="input-curto" value="<?php echo set_value('limit', $row['limit']);?>" />
    <span class="help-block">Deixe 0 para não haver limite.</span>

	</div><!-- .control-group -->

    <div class="control-group box">
    
        <label for="status" class="lb-full">Status</label>
        <div class="form-opcoes group-buttons">        
            <?php echo inputs_status($row['status']);?>
        </div>
    </div><!-- .control-group --> 

	
	
	
	
	
	
	
	
	
	

</div><!-- .panel-right -->

        
      
