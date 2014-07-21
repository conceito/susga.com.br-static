<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>


<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

  	<label for="grupos" class="lb-full"><b class="obr">[!]</b> Grupo</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
	
	<br />
	
	<label for="titulo" class="lb-full"><b class="obr">[!]</b> Título principal</label>
    <input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />
    
    <br />
    
    <label for="txtmulti" class="lb-full">Descrição secundária</label>
    <input name="txtmulti" id="txtmulti" type="text" class="input-longo input-longo" value="<?php echo set_value('txtmulti');?>" style="width:98%" />
	
	<br />
	 
    <label for="txt" class="lb-full">Link</label>
    <input name="txt" id="txt" type="text" class="input-longo" value="<?php echo set_value('txt');?>" placeholder="http://www..." />

    <br />
    
    
    <label for="" class="lb-full">Abrir link...</label>
	<div class="form-opcoes group-buttons">
    
    <?php echo form_radio(array(
    'name'        => 'target',
    'id'          => 'target_blank',
    'value'       => '_blank',
    'checked'     => true,
    'style'       => '',
    ));?> <label for="target_blank">em nova janela</label>
    
    <?php echo form_radio(array(
    'name'        => 'target',
    'id'          => 'target_top',
    'value'       => '_top',
    'checked'     => false,
    'style'       => '',
    ));?> <label for="target_top">na mesma janela</label>    
	
    </div>
    
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	
	<?php 
	
	if($swfUplForm){
		echo '<div class="control-group box"><div class="attached-box"><label>Enviar banner</label>';
	  	echo $swfUplForm;	
		echo '</div></div>';
	}
	?>
    
    
    
    <div class="control-group box">

	<label for="dt1" class="lb-full">Publicar em</label>
    <input name="dt1" id="from" type="text" class="input-curto" value="<?php echo set_value('dt1');?>" />
    
    </div><!-- .control-group -->
    
    <div class="control-group box">
    
    <label for="dt2" class="lb-full">Remover em</label>
    <input name="dt2" id="to" type="text" class="input-curto" value="<?php echo set_value('dt2');?>" />
    
    </div><!-- .control-group -->
    
    <div class="control-group box">
    
    <label for="limit" class="lb-full">Limite de impressões</label>
    <input name="limit" id="limit" type="text" class="input-curto" value="<?php echo set_value('limit', 0);?>" />
    <span class="help-block">Deixe 0 para não haver limite.</span>


	
	</div><!-- .control-group -->
	
	
	
	

</div><!-- .panel-right -->

        
      
