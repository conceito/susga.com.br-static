<div id="menu" class="equalz">
	<!--<div class="round-top"></div>-->
    <!--aqui inicia o menu-->
    
    
    
    <ul class="botoes">
    
    <li><a href="<?php echo cms_url('cms');?>" title="Painel" class="bot-painel">Painel</a></li>
<?php 
$i = 0;
//-- percorre os botoes
foreach($menus as $raiz)
{
	$item0 = $raiz['raiz']['label'];
	$id = $raiz['raiz']['id'];
	$uri = (strlen($raiz['raiz']['uri'])>4) ? cms_url($raiz['raiz']['uri']) : '#';
	$ativ = $raiz['raiz']['ativo'];
	$clss = ($ativ == 1) ? 'ativo' : '';
	echo '<li class="'.$clss.'"><a href="'.$uri.'">'.$item0.'</a><ul class="submenu">';
	
	// monta os submenus
	foreach($raiz['submenus'] as $sub)
	{
		echo '<li><a href="'.cms_url($sub['uri']).'">'.$sub['label'].'</a></li>';
	}
	
	echo '</ul></li>';
	$i++;
}
?>        
    
    </ul>
    
    
    <!--aqui termina o menu-->
    
</div>