<?php $bu = base_url();?>
<!--opções de edição-->
<input name="arquivo_id" type="hidden" value="<?php echo $arq['id'];?>" />
<div class="img-infos">
  <img src="<?php echo $bu . $this->config->item('upl_imgs');?>/<?php echo thumb($arq['nome']);?>" alt="img" />
  <p><span class="nome-arquivo"><?php echo $arq['nome'];?></span> foi postado em <strong><?php echo formaPadrao($arq['dt_ini']);?></strong></p>

<strong>Descrição:</strong><br />
<textarea name="descricao_modal" class="desc-model" cols="" rows=""><?php echo $arq['descricao'];?></textarea>
<br />

<br />
<strong>Relacionar com:</strong><br />

<div class="img-relacao">

<?php echo $this->admin_model->combo_modulos_conteudo($comboRelacionado['modulo_id'], false, array(' -- ninguém -- ' => 0));?> &raquo;
<div class="combo-conteudo-ajax">
<?php echo $this->cmsutils_model->getComboConteudoFromModulo($comboRelacionado['modulo_id'], array('rel' => $comboRelacionado['id']));

//$this->cms_libs->combo_relacionados(array('rel' => $comboRelacionado['modulo_id']), array('rel' => $comboRelacionado['id']));
?>
</div> <!-- .combo-conteudo-ajax -->

</div><!-- .img-relacao -->


<br />

<strong>Tag da imagem:</strong><br />

<div class="btn-group">
<?php if($this->config->item('tag_opt_1')):?>
<label title="Nenhuma" class="btn <?php echo ($tag_opt==0)?'active':'';?>"><?php echo form_radio('tag_opt', '0', ($tag_opt==0)?true:false);?> [X]</label>

<label class="btn <?php echo ($tag_opt==1)?'active':'';?>"><?php echo form_radio('tag_opt', '1', ($tag_opt==1)?true:false);?> <?php echo $this->config->item('tag_opt_1');?></label>
<?php endif;?>

<?php if($this->config->item('tag_opt_2')):?>
<label class="btn <?php echo ($tag_opt==2)?'active':'';?>"><?php echo form_radio('tag_opt', '2', ($tag_opt==2)?true:false);?> <?php echo $this->config->item('tag_opt_2');?></label>
<?php endif;?>

<?php if($this->config->item('tag_opt_3')):?>
<label class="btn <?php echo ($tag_opt==3)?'active':'';?>"><?php echo form_radio('tag_opt', '3', ($tag_opt==3)?true:false);?> <?php echo $this->config->item('tag_opt_3');?></label> 
<?php endif;?>
<?php if($this->config->item('tag_opt_4')):?>
<label class="btn <?php echo ($tag_opt==4)?'active':'';?>"><?php echo form_radio('tag_opt', '4', ($tag_opt==4)?true:false);?> <?php echo $this->config->item('tag_opt_4');?></label>
<?php endif;?>

<?php if($this->config->item('tag_opt_5')):?>
<label class="btn <?php echo ($tag_opt==5)?'active':'';?>"><?php echo form_radio('tag_opt', '5', ($tag_opt==5)?true:false);?> <?php echo $this->config->item('tag_opt_5');?></label>
<?php endif;?>

</div><!-- .btn-group -->

	<div class="clear debug"></div>
</div><!-- .img-infos -->

<fieldset class="modal-dicas"><legend>Dicas</legend>
<ul>

	<li>Após aplicar um dos filtros, feche a janela de edição e pressione <strong>Ctrl + F5</strong> para atualizar as alterações.</li>
	

</ul>
</fieldset>

<span class="cinza14">Opções de edição:</span>
<h4>Girar</h4>
<ul class="lista-opcoes-giro">
	<li><a href="<?php echo cms_url('cms/imagem/girar/op:270/id:'.$id);?>"><img src="<?php echo cms_img();?>giro-90d.jpg" width="36" height="43" alt="90 direita" />90º para<br />direita</a></li>
	<li><a href="<?php echo cms_url('cms/imagem/girar/op:180/id:'.$id);?>"><img src="<?php echo cms_img();?>giro-180.jpg" width="36" height="43" alt="180" />180º</a></li>
	<li style="width:100px;"><a href="<?php echo cms_url('cms/imagem/girar/op:90/id:'.$id);?>"><img src="<?php echo cms_img();?>giro-90e.jpg" width="33" height="43" alt="90 esquerda" />90º para<br />esquerda</a></li>
	<li><a href="<?php echo cms_url('cms/imagem/girar/op:hor/id:'.$id);?>"><img src="<?php echo cms_img();?>giro-h.jpg" width="33" height="43" alt="inverter" />Inverter na<br />horizontal</a></li>
	<li><a href="<?php echo cms_url('cms/imagem/girar/op:vrt/id:'.$id);?>"><img src="<?php echo cms_img();?>giro-v.jpg" width="33" height="43" alt="inverter" />Inverter na<br />vertical</a></li>
</ul>
<h4>Cortar</h4>
<ul class="lista-opcoes-corte">
	<li style="width:90px;"><a href="<?php echo cms_url('cms/imagem/crop/op:livre/id:'.$id);?>"><img src="<?php echo cms_img();?>corte-livre.jpg" width="38" height="42" alt="90 direita" />corte<br />livre</a></li>
	<li><a href="<?php echo cms_url('cms/imagem/crop/op:quadrado/id:'.$id);?>"><img src="<?php echo cms_img();?>corte-quadrado.jpg" width="40" height="42" alt="180" />corte<br />quadrado</a></li>
	<li><a href="<?php echo cms_url('cms/imagem/crop/op:43/id:'.$id);?>"><img src="<?php echo cms_img();?>corte-h.jpg" width="41" height="42" alt="90 esquerda" />retângulo<br />deitado 4:3</a></li>
	<li><a href="<?php echo cms_url('cms/imagem/crop/op:34/id:'.$id);?>"><img src="<?php echo cms_img();?>corte-v.jpg" width="38" height="42" alt="inverter" />retângulo<br />em pé 3:4</a></li>
	
</ul>
<div class="clear"><br />
<br />
</div>