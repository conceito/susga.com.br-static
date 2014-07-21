<?php 

echo '<script>$(function(){';	
// exibe alertas
if(isset($tip)){
	if($tip == 'faltaCampos'){?>
		alerta("Você não preencheu o formulário corretamente!", "vermelho");<?php }
	if($tip == 'ok'){?>
		alerta("OK, operação realizada com sucesso!", "verde");<?php }
	if($tip == 'erroGravacao'){?>
		alerta("Atenção! Houve um erro ao tentar gravar dados.", "vermelho");<?php }
	if($tip == 'erroImport'){?>
		alerta("Atenção! Verifique se todos os dados foram preenchidos.", "vermelho");<?php }
}		
	if($tab != ''){
		echo '$("#tabs").tabs(\'select\', '.($tab-1).');';
	}
	
echo '});</script>';

?>
<div id="super-alerta"><!--super alerta-->
	<div class="var"></div>
    <div class="frase">Você precisa confirmar esta alteração!</div>
    <div class="opcoes"><a href="#" class="bot-verm"><span><b class="ico-menos">Cancelar</b></span></a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="#" class="bot-verde"><span><b class="ico-ok">Confirmar</b></span></a> </div>	
</div><!--super alerta fim-->


<div id="head">
<?php ///////////////////////
if($chave !== false): $url = (strlen($chave) < 6) ? 'Manutenção' : $chave;?>
<div class="chave-0">O site está desligado e está sendo redirecionado para: <?php echo $url;?>. <a href="<?php echo cms_url('cms/administracao/config');?>">Para restaurá-lo clique aqui.</a></div>
<?php endif; //////////////////?>

<div class="linha1">

	<div class="left-block"><a href="<?php echo $linkSite;?>" title="ir para o site" class="visitar">&nbsp;← abrir site</a></div>

	<em>Olá <?php echo $nick;?></em> | <?php echo $dataext;?> |  
    
    <?php 
    /*
    |==========================================================================
    |	Se existem idiomas
    |--------------------------------------------------------------------------
    */
   	if($langOpts):
    ?>
    <span class="lang-ico"><a href="#" onclick="return false;"><?php echo $lang;?></a> | 
        <ul id="langs-combo">
         <?php echo $langOpts;?>
        </ul>
    </span>
    <?php 
    endif;
    ?> 
 	<a href="<?php echo cms_url('cms/login/logout');?>">Sair</a>
    
</div><!-- .linha1 -->
 
<div class="linha2">
    	<div class="bloco1"><?php echo $this->painel_model->getLogotipo('header');?></div>
        <div class="bloco2"><div class="logo-cliente"><?php echo $nomeempresa;?></div></div>
    </div>
    <div class="linha3">.</div>
    <div id="alertas"></div>
</div>