
<?php
$bs = base_url();
?>
<div class="dragbox painel-mensagens <?php echo $vars['status'];?>" id="<?php echo $vars['id'];?>" >  
        <h2><img src="<?php echo cms_img();?>h2-mensagens.gif" width="22" height="22" alt=" " />Quadro de mensagens</h2>  
        <div class="dragbox-content" <?php echo ($vars['status']=='hidden')?'style="display:none;"':'';?>>



        <ul class="lista">
<?php 
if(! $mens):
?>
<li><span class="imp">&nbsp;</span>Não existem mensagens!
        	<div class="mensagem"> Não existem mensagens!</div>
        </li>
<?php
else:
	foreach($mens as $row):
	$id = $row['id'];
	$data = formaPadrao($row['data']);
	$hora = substr($row['hora'], 0, 5);
	$titulo = $row['assunto'];
	$txt = $row['txt'];
	$imp = $row['imp'];// importancia 0 ou 1
	$visitas = $row['visitas'];
	$status = $row['status'];
	$lido = $row['lido'];// já leu 0 ou 1
	$nick = $row['nick'];
	// prepara saida
	$mensagem = auto_link(nl2br($txt));
	$class = ($lido == 0) ? '' : 'lido';
	$title = 'em '.$data.' às '.$hora;
	$nivel = ($imp == 0) ? '' : '!';
?>
 <li id="mens-<?php echo $id;?>"><span class="imp"><?php echo $nivel;?> </span> <a href="#" class="assunto <?php echo $class;?>" title="<?php echo $title;?>"><?php echo $titulo;?><span class="apelido"> - <?php echo $nick;?></span></a>
    <div class="mensagem"> <?php echo $mensagem;?>
    </div>
</li>
<?php
	endforeach;
endif;
?>
     
        <div class="clear"></div>    
    	</ul>
        
       
		<a href="<?php echo cms_url('cms/cms/mensagens');?>" class="ml10 fr mr10 nyroModal" target="_blank"><span class="courier">&gt;</span> ver todas</a>
        
        <br />

		<fieldset><legend>Criar nova mensagem</legend>
        <div class="resposta-form"></div>
        <form action="" method="post" id="formmensagem">
        
        <label>importante: <input name="imp" type="checkbox" value="1" /></label>
        <br />
        <input name="assunto" type="text" value="Assunto" class="input-text" />
        <br />
        <textarea name="mensagem" cols="" rows="">Mensagem</textarea>
        <br />
       
        <a href="#" class="bot-verde" style=""><span>Enviar</span></a>
        </form>        
        </fieldset>
        
        </div>
     </div>