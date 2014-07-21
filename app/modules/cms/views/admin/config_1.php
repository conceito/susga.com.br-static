<?php echo validation_errors(); ?>

<div class="erros">Atenção ao alterar estas configurações!</div>
<br />

<label for="chave" class="lb-inline">Logo</label>
<div class="logo-container">
	
    <?php if(strlen($con[18]['valor'])):?>
    <div class="logotipo"><img src="<?php echo base_url();?><?php echo $this->config->item('upl_arqs');?>/<?php echo $con[18]['valor'];?>" alt="" /><a href="<?php echo cms_url('cms/administracao/removeLogo');?>" class="remove-img">remover</a></div>
    <?php endif;?>
	
	<?php 
	if($swfUplForm){
		echo '<div class="attached-box">';
	  	echo $swfUplForm;	
		echo '</div>';
	}
	?>
</div>

  

<label for="chave" class="lb-inline">Chave geral</label><div class="form-opcoes">
<?php echo form_radio('chave', 1, ($con[5]['valor'] == 1));?> <span style="color:#7b8f0a;font-weight:bold;">site "no ar"</span> &nbsp;&nbsp;| &nbsp;&nbsp;
<?php echo form_radio('chave', 0, ($con[5]['valor'] == 0));?> <span style="color:#c03312;font-weight:bold;">desativar site</span>
<?php echo i('Este controle serve para retirar o site do ar em função de algum problema ou manutenção.<br/>Ao selecionar &quot;desativar site&quot; você deverá escolher para onde o usuário será redirecionado.');?>
</div>

<br />

<div class="chave-2passo">
<label class="lb-inline">&nbsp;</label><div class="form-opcoes">
<?php echo form_radio('redirecionamento', 0, ($con[6]['valor'] == 0));?> página “manutenção” &nbsp;&nbsp;|&nbsp;&nbsp; 
<?php echo form_radio('redirecionamento', 1, (strlen($con[6]['valor']) > 4));?> redirecionar:<input name="redirecionar" id="redirecionar" type="text" class="input-longo" value="<?php echo set_value('redirecionar', $con[6]['valor']);?>" />
</div>
</div>

<label for="upload" class="lb-inline">Tipo de Upload</label><div class="form-opcoes">
<?php echo form_radio('upload', 0, ($con[7]['valor'] == 0));?> Formulário convencional &nbsp;&nbsp;| &nbsp;&nbsp;
<?php echo form_radio('upload', 1, ($con[7]['valor'] == 1));?> SWF Upload

<?php echo i('<b>Form convensional:</b> Não tem status de progresso e máximo de 3 arquivos por seção.<br/><b>SWF upload:</b> Tem status de progresso e pode selecionar vários arquivos de uma única vêz.<br/>* Dependendo das configurações do servidor o SWF upload pode não funcionar.');?>
</div>

<br />

<label for="comment_message" class="lb-inline">Receber notificação de novos comentários</label><div class="form-opcoes">
<?php echo form_radio('comment_message', 1, ($con[1]['valor'] == 1));?> Sim &nbsp;&nbsp;| &nbsp;&nbsp;
<?php echo form_radio('comment_message', 0, ($con[1]['valor'] == 0));?> Não

</div>

<br />





