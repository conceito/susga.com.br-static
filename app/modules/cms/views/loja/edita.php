<?php
/******************************************
*  Template: edição de conteúdo de produto
*  Controller: cms/loja/edita
*/
?>
<?php echo validation_errors(); ?>

<div class="panel-left clearfix">
	
    <div class="ai-page">
    	
        <div class="ai">
        <label for="grupos" class="lb-full">Categoria</label>
		<?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
        </div>
        
        <div class="page">
        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Nome do produto</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', form_prep($row['titulo']));?>" />
        </div>
        
    </div><!-- .ai-page -->    
	
	<input name="nick_edita" id="" type="text" class="input-apelido" title="Endereço amigável" placeholder="Endereço amigável" value="<?php echo set_value('nick', $row['nick']);?>" readonly="readonly"/>
	
	
	<br />
    
    
	
    <div class="control-group">
	
	<label for="txt" class="lb-full">Conteúdo principal <?php if($multicontent):?>| <a href="#" onclick="javascript:$('#tabs').tabs('select',1); return false;">Mais conteúdos</a><?php endif;?></label>
	<div class="content-ctrl">
	<a href="<?php echo cms_url($linkAddImage);?>" class="ico-img nyroModal" target="_blank">Subir imagem</a>
	<!--<a href="<?php echo cms_url($linkAddArq);?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">arquivo</b></span></a>-->
	</div>
	<textarea name="txt" class="textarea-longo" id="txt" style="width:100%"><?php echo set_value('txt', $row['txt']);?></textarea>
	
	</div><!-- .control-group -->
    
    <?php if($rel != false){?>
    <div class="control-group">
    
	<label for="rel" class="lb-full">Relacionado à</label><?php echo (! $rel)? 'Não existem.<br />' : $rel;?>
	
	</div><!-- .control-group -->
	<?php }?>
	
	<?php 
	if($swfUplForm){
	
		echo '<div class="control-group box"><div class="attached-box">';
	  	echo $swfUplForm;	
		echo '</div></div>';
	
	}
	?>
    <br />

	
	

</div><!-- .panel-left -->


<div class="panel-right clearfix">


	<div class="control-group box">
    
	<label for="status" class="lb-full">Status</label>
	<div class="form-opcoes group-buttons">
		<?php echo inputs_status($row['status']);?>
     </div>
	
    <div class="help-block">Última atualização em <?php echo datetime_br($row['atualizado']);?></div>
	</div><!-- .control-group -->
    
    
    <div class="control-group box">
    
	<label for="codigo" class="lb-full">Código</label>
    <input name="codigo" id="codigo" type="text" class="input-curto" value="<?php echo set_value('codigo', $row['codigo']);?>" />
		
    </div><!-- .control-group -->
    
    
    <div class="control-group box">
    
    	 
        <label for="download" class="lb-full">Produto digital</label>
        
        <div class="form-opcoes group-buttons">
        <?php echo form_radio(array(
        'name'        => 'download',
        'id'          => 'download0',
        'value'       => '0',
        'checked'     => ($row['download']==0),
        'style'       => '',
        ));?><label for="download0">Não</label> 
        <?php echo form_radio(array(
        'name'        => 'download',
        'id'          => 'download1',
        'value'       => '1',
        'checked'     => ($row['download']==1),
        'style'       => '',
        ));?><label for="download1">Sim</label>
        </div>
        
    	<div class="for-digit-prod">
        <label for="download_limit" class="lb-full">Limite de downloads</label>
        
        <input name="download_limit" id="download_limit" type="text" class="input-curto" value="<?php echo set_value('download_limit', $row['download_limit']);?>" />
        </div><!-- .for-digit-prod -->
        
		
    </div><!-- .control-group -->
    
    
    <div class="control-group box for-fisic-prod">
    
	<label for="estoque" class="lb-full">Quantidade em estoque</label>
    <input name="estoque" id="estoque" type="text" class="input-curto <?php echo ($options_estoque) ? 'estoque-by-opt' : '';?>" value="<?php echo set_value('estoque', ($options_estoque) ? $options_estoque['estoque'] : $row['estoque']);?>" />
    <?php if($options_estoque):?>
    <div class="help-block">O estoque é formado pelas opções do produto (<?php echo $options_estoque['soma'];?>)</div>
    <?php endif;?>
		
    </div><!-- .control-group -->
    
    <div class="control-group box for-fisic-prod">
    
	<label for="dimensoes" class="lb-full">Dimensões</label>
    <input name="dimensoes" id="dimensoes" type="text" class="input-curto" value="<?php echo set_value('dimensoes', $row['dimensoes']);?>" />
    <div class="help-block">(Comprimento)x(Largura)x(Altura)" em centímetros</div>
    
    <label for="peso" class="lb-full">Peso</label>
    <input name="peso" id="peso" type="text" class="input-curto" value="<?php echo set_value('peso', $row['peso']);?>" />
    <div class="help-block">Use o peso em quilograma</div>
		
    </div><!-- .control-group -->
    
	
    <div class="control-group box">
    
	<label for="from" class="lb-full">Data de cadastro</label>
    <input name="dt1" id="from" type="text" class="input-curto" value="<?php echo set_value('dt1', $row['dt1']);?>" />
		
    </div><!-- .control-group -->
	
	<?php viewGetTags();?>
	
	<div class="control-group box">
	
	<label for="resumo" class="lb-full">Resumo</label>
    <textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo', $row['resumo']);?></textarea>
	       
	</div><!-- .control-group -->
    
    <div class="control-group box">
	
	<label for="tags" class="lb-full">Palavras-chave</label>
    <textarea name="tags" class="textarea-tags" id="tags"><?php echo set_value('tags', $row['tags']);?></textarea>
    
	</div><!-- .control-group -->
	
	
	
 


</div><!-- .panel-right -->

        
              