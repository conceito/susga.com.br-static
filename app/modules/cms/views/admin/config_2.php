<div class="erros">É recomendável fazer um backup por mês!<br />
Caso o arquivo de backup seja muito grande o servidor pode não conseguir processa-lo, em caso de erro contacte o suporte.</div>
<br />

<label style="margin-top:-5px;" class="lb-inline">Backup</label><a href="<?php echo cms_url('cms/administracao/fazBackupbd');?>">Fazer backup</a>

<br /><br />


<label class="lb-inline">Restaurar</label><input name="file" type="file" /><input name="" type="submit" value="restaurar" class="bot-restaurar btn" /><?php echo i('Procure o arquivo de backup na extenção <b>SQL</b> ou <b>GZ</b>.<br />Estes são os únicos formatos aceito!');?>

<br />
<br />
