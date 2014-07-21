<?php echo validation_errors(); ?>


<div class="panel-left clearfix">



    <label for="titulo" class="lb-full">Assunto</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', form_prep($row['titulo']));?>" />
    
    <br />
    
    
    <input name="tags" type="hidden" value="<?php echo set_value('tags', $row['tags']);?>" />
    <label for="txt" class="lb-full">Mensagem HTML
     <br /><br />
    <?php if($row['destaque'] == 1):?>
    <a href="<?php echo cms_url('cms/news/linkNovo/id:'.$row['id']);?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">link</b></span></a>
   
    
    <a href="<?php echo cms_url($linkAddImage);?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">imagem</b></span></a>
    
    
    <a href="<?php echo cms_url($linkAddArq);?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">arquivo</b></span></a>
    
    <?php else:?>
    Campo de edição desabilitado para esta newsletter.
    <?php endif;?>
    
     </label>
     <?php if($row['destaque'] == 1):?>
     [NOME] = Nome do usuário | [EMAIL] = E-mail do usuário
     <?php endif;?>
     <textarea name="txt" class="textarea-longo <?php echo ($row['destaque']==1)?'tynimce':'';?>" id="txt"><?php echo set_value('txt', $row['txt']);?></textarea>
    
        
    <br />
    
    <label for="resumo" class="lb-full">Mensagem em Texto</label><textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo', $row['resumo']);?></textarea>
    
           
    <br />
    
    

</div><!-- .panel-left -->


<div class="panel-right clearfix">
	
    
    
    <a href="<?php echo cms_url('cms/news/agendar/id:'.$row['id']);?>" class="bot-verde bot-agendarnews">
    <span><b class="ico-agendar">Agendar envio</b></span></a>

	<br /><br />




    <label for="status" class="lb-full">Status</label>
    <div class="form-opcoes">
    <?php echo form_radio('status', 1, ($row['status']==1));?> ativo | 
    <?php echo form_radio('status', 0, ($row['status']==0));?> inativo | 
    <?php echo form_radio('status', 2, ($row['status']==2));?> editando</div>
    
    <br />
    
    
    
    <label for="grupos" class="lb-full">Grupo</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>
    
    
    <br />
    
    <label for="tags" class="lb-full">Remetente</label><input name="tags" id="tags" type="text" class="input-longo" value="<?php echo set_value('tags', form_prep($row['tags']));?>" />
    <?php echo i('Nome da pessoa ou instituição que está enviando.');?>
           
    <br />
    
    <label for="extra" class="lb-full">E-mail do Remetente</label><input name="extra" id="extra" type="text" class="input-longo" value="<?php echo set_value('extra', form_prep($row['extra']));?>" />
    <?php echo i('E-mail que receberá as respostas.');?>
           
    <br />
    

</div><!-- .panel-right -->