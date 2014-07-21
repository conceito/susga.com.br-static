<div class="contr-relatorio">

<ul class="nav nav-pills" style="margin-bottom:0">
<li><a href="<?php echo $link_planilha;?>/t:" title="Gerar planilha">Gerar planilha confirmados (ativos)</a></li>
<li><a href="<?php echo $link_planilha;?>/t:todos" title="Gerar planilha">Gerar planilha todos</a></li>
</ul>
  
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl_tab">
      <thead>
      <tr>
       
        <th scope="col">Nome</th>
        <th scope="col">Detalhes</th>

        
		<th scope="col">Opções</th>

        <th scope="col">E-mail</th>
        <th scope="col" class="data">Data</th>
		<th scope="col">Hora</th>
		<th scope="col">IP</th>
      </tr>
      </thead>
      <tbody>
      
      <?php 
	  if(! $inscritos):
	  	echo '<tr><td colspan="3"><strong>Não existem registros.</strong></td></tr>';
	  else:
	  	$i = 0;
		foreach($inscritos as $ins):
			$id = $ins['id'];
			$user_id = $ins['user']['id'];
			$nome = $ins['user']['nome'];
			// linhas pares-impares
			if($ins['rel'] == 0){
			if($i%2 == 0)$zebra = 'even';
			else $zebra = 'odd';
			}
			$linkMessage = cms_url('cms/usuarios/mensagemForm/id:'.$user_id.'/user:'.$user_id.'/tipo:confirmacao');
		 ?>
        <tr class="<?php echo $zebra;?>" id="<?php echo $id;?>">
      	
        <td valign="top">
		<?php // se existe hierarquia de páginas
        if($ins['rel'] != 0):?><?php echo repeater('&#8735;', 1);?><?php endif;?>
		
		<strong class="fs-11">
            <a href="<?php echo cms_url('cms/usuarios/dadosUser/id:'.$user_id);?>" title="Editar este item" class="nyroModal" target="_blank"><?php echo $nome;?></a></strong>
        	<div class="opcoes">
            <span class="editar"><a href="#" title="Editar este item" class="edit-opc">ver comentário</a> |</span>             
            <span class="apagar"><a href="#" title="Apagar este item" class="apagar-item">apagar</a></span>
            <span class="confirma"><a href="#" class="nao-item">não</a> / <a href="#" class="sim-item" rel="cms_inscritos">sim</a></span>
            </div>
        </td>
		<td valign="top">
			<?php if($ins['status']!==FALSE):?>
            <ul class="nav nav-pills" style="margin-bottom:0"><li>
            <a href="<?php echo cms_url($ins['comprovante_url']);?>" class="nyroModal " target="_blank">abrir</a>
            </li></ul>
            <?php endif;?>
        </td>
        
        <td>
        <?php if(isset($ins['subscription_options']) && $ins['subscription_options']): ?>
            <ul class="nav nav-pills" style="margin-bottom:0"><li>
            <a href="<?php echo cms_url('cms/calendario/subscriptions_showoptions/insc:'.$ins['id']);?>" class="nyroModal " title="Ver opções de inscrição" target="_blank">ver opções</a>
            </li></ul>
            
        <?php endif; ?>
        </td>
		
        <td valign="top"><a href="<?php echo $linkMessage;?>" class="enviar-mensagem nyroModal" target="_blank" title="clique para enviar mensagem">enviar</a></td>        
		        
        <td valign="top"><?php echo link_status($ins['data'], $ins['status']);?></td>
		
		<td valign="top"><?php echo $ins['hora'];?></td>
		
		<td valign="top"><?php echo $ins['ip'];?></td>
      </tr>
	  
      <tr class="<?php echo $zebra;?>" id="comment-<?php echo $id;?>">      	
        <td colspan="7" valign="top"><div class="comment-div cd-<?php echo $id;?>"><textarea name="comment-<?php echo $id;?>" cols="" rows=""><?php echo $ins['comentario'];?></textarea></div></td>
      </tr>
         
          <?php 
		  if($ins['rel'] == 0){
	  		$i++;
		  }
	  	endforeach;
	  endif;
	  ?>
      
    
      </tbody>
    </table>