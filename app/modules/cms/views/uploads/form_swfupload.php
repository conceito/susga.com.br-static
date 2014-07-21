<?php 
//echo 'id: '.$id.'<br />';
//echo 'pasta: '.$pasta.'<br />';
//echo 'tabela: '.$tabela.'<br />';
?>
<form id="modalform" action="<?=cms_url('cms/upload/processaSwfupload/co:'.$co.'/id:'.$id.'/pasta:'.$pasta['id'].'/onde:'.$onde)?>" method="post" enctype="multipart/form-data" name="modalform">
 

<fieldset class="modal-dicas"><legend>Dicas</legend>
<ul>

	<li>Fique atento aos tipos de arquivos aceitos e ao peso máximo permitido.</li>
	<li>Nomes de arquivos que contenham espaços e/ou caracteres especiais, como: ?, !, #, @, ç etc poderão causar erro.</li>
    <li><strong>Após a mensagem de "Complete" clique em "Salvar" para terminar o processo.</strong></li>

</ul>
</fieldset>

<h3>Pasta de destino:</h3>
<label>&nbsp;</label><strong><?php echo $pasta['titulo'];?></strong>,  Arquivos aceitos: <?php echo $ext;?> com no máximo <strong><?php echo $pesomax;?></strong>

<h3>Procure as imagens:</h3>




<label style="height:20px; float:left;">Arquivos:</label>


					<div style="float:left;">
						<?php echo $this->swf_upload->campo_multi_swfupload(); ?>
					</div>
	
<div class="wrap"></div>
<br /><br />

<textarea name="arquivos" cols="" rows="" class="resumo" style="border:none; visibility:hidden;" readonly="readonly">...</textarea>
			

<?php if($co == 2):?>          
 <h3>Arquivo externo:</h3>
<label>Link do arquivo</label><input name="externo" type="text" size="50" class="input-longo" value="http://" />
<br />
<?php endif;?>
<div id="btnSubmit"></div>
<input name="submit" type="image" src="<?php  echo cms_img(); ?>cbot-salvar.jpg" alt="salvar" id="btnSubmit-depreciado" style="clear:both; margin-top:15px; display:none;" />

	</form>
    <br />
    
    <?php echo validation_errors(); ?>


