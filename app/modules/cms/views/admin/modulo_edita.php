
<?php echo validation_errors(); ?>


<label for="label" class="lb-inline">Label</label><input name="label" id="label" type="text" class="input-longo input-titulo" value="<?php echo set_value('label', $row['label']);?>" />

<br />

<label for="uri" class="lb-inline">URI</label><input name="uri" id="uri" type="text" class="input-longo" value="<?php echo set_value('uri', $row['uri']);?>" />

<br />

<label for="front_uri" class="lb-inline">Front-end URI</label><input name="front_uri" id="front_uri" type="text" class="input-longo" value="<?php echo set_value('front_uri', $row['front_uri']);?>" />

<br />

<label for="tipo" class="lb-inline">Quem pode ver</label><div class="form-opcoes"><?php echo form_radio('tipo', 0, ($row['tipo']==0));?> God | <?php echo form_radio('tipo', 1, ($row['tipo']==1));?> Admins</div>

<br />

<label for="tabela" class="lb-inline">Tabela</label><input name="tabela" id="tabela" type="text" class="input-curto" value="<?php echo set_value('tabela', $row['tabela']);?>" />

<br />

<label for="ordenavel" class="lb-inline">Ordenável</label><div class="form-opcoes"><?php echo form_radio('ordenavel', 1, ($row['ordenavel']==1));?> sim | <?php echo form_radio('ordenavel', 0, ($row['ordenavel']==0));?> não </div>

<br />

<label for="comments" class="lb-inline">Aceita comentários</label><div class="form-opcoes"><?php echo form_radio('comments', 1, ($row['comments']==1));?> sim | <?php echo form_radio('comments', 0, ($row['comments']==0));?> não </div>

<br />

<label for="destaques" class="lb-inline">Tem destaques</label><div class="form-opcoes"><?php echo form_radio('destaques', 1, ($row['destaques']==1));?> sim | <?php echo form_radio('destaques', 0, ($row['destaques']==0));?> não </div>

<br />

<label for="inscricao" class="lb-inline">Aceita inscrição</label><div class="form-opcoes"><?php echo form_radio('inscricao', 1, ($row['inscricao']==1));?> sim | <?php echo form_radio('inscricao', 0, ($row['inscricao']==0));?> não </div>

<br />

<label for="modulos" class="lb-inline">Se relaciona com</label><?php echo $this->admin_model->combo_modulos($row['rel'], true, array(' -- ninguém -- ' => 0));?> 
<!-- grupo <span class="cb-grupos"><?php //echo $this->cms_libs->combo_grupos($row['id'], $row['rel2'], false, array());?></span>-->

<br />

<label for="pastas_0" class="lb-inline">Pasta de Imagens</label><?php echo $this->pastas_model->combo_pastas(0, $row['pasta_img']);?>


<br />

<label for="pastas_2" class="lb-inline">Pasta de Arquivos</label><?php echo $this->pastas_model->combo_pastas(2, $row['pasta_arq']);?>

<br />

<label for="pastaAjuda" class="lb-inline">Pasta de Ajuda</label><?php echo $this->pastas_model->combo_pastas(2, $row['pasta_ajuda'], 'pastaAjuda', true);?>

<br />

<label for="multicontent" class="lb-inline">Multi conteúdo</label>
<div class="multi-options">
	
    <?php
	
    ///// monta campos multi
	$ttl_multi = count($row['multicontent']);
	
	$html = '';
	for($i=0; $i < $ttl_multi; $i++):
		
		$value  = $row['multicontent'][$i];
		
		$html .= '<div class="multi-option"><input name="multi_'.($i+1).'" type="text" class="input-longo" value="'.$value.'" />';
		
		if($i == ($ttl_multi-1)){// última
			$html .= '<a href="#" class="add-multi"><i class="icon-plus"></i></a>';
		} else {
			$html .= '<a href="#" class="remove-multi"><i class="icon-trash"></i></a>';
		}
		
		$html .= '</div>';
	?>
    
    <?php
    
	endfor;
	
	echo $html;
	?>
    

</div><!--<div class="multi-option"><input name="multi_1" type="text" class="input-longo" value="algua coisa" />
    <a href="#" class="remove-multi"><i class="icon-trash"></i></a></div>
    
    <div class="multi-option"><input name="multi_2" type="text" class="input-longo" value="" />
    <a href="#" class="add-multi"><i class="icon-plus"></i></a></div>-->

<br class="clearfix" style="clear:both;" />

	
<label for="status" class="lb-inline">Status</label><div class="form-opcoes"><?php echo form_radio('status', 1, ($row['status']==1));?> ativo | 
<?php echo form_radio('status', 0, ($row['status']==0));?> inativo | <?php echo form_radio('status', 2, ($row['status']==2));?> editando</div>

<br />
    


