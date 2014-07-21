<label for="status">Status</label>
<div class="form-opcoes">
<?php echo form_radio('status', 1, ($row['status']==1));?> ativo | 
<?php echo form_radio('status', 0, ($row['status']==0));?> inativo | 
<?php echo form_radio('status', 2, ($row['status']==2));?> editando</div>

<br />

<label for="grupos">Grupo</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>


<br />

<?php if($rel != false){?>
<label for="rel">Relacionado à</label><?php echo (! $rel)? 'Não existem.<br />' : $rel;?>

<br />
<?php }?>

<label for="dt1">Data</label><input name="dt1" id="dt1" type="text" class="input-curto" value="<?php echo set_value('dt1', $row['dt1']);?>" />

<br />

<label for="titulo">Nome</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', form_prep($row['titulo']));?>" />

<br />

<label for="nick">Apelido</label><input name="" id="" type="text" class="input-apelido" value="<?php echo set_value('nick', $row['nick']);?>" readonly="readonly"/>
<?php echo i('Não pode ser alterado.<br />Identificação deste registro.');?>

<br />

<label for="resumo">Profissão/referência</label><textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo', $row['resumo']);?></textarea>

       
<br />

<label for="tags">Tags</label><textarea name="tags" class="textarea-tags" id="tags"><?php echo set_value('tags', $row['tags']);?></textarea>
<?php echo i('TAGS são palavras-chave para que seu conteúdo seja melhor indexado pelos mecanismos de busca.<br />Escreva as palavras ou expressões mais relevantes separadas por vírgula (,).');?>
       
<br />
 
 <label for="txt">Depoimento
 <br />
 <a href="javascript:;" onmousedown="$('#txt').tinymce().show();"><span class="courier">&gt;</span> com editor </a> 
<br />
<a href="javascript:;" onmousedown="$('#txt').tinymce().hide();"><span class="courier">&gt;</span> sem editor </a>
<br /> <br />
<a href="<?php echo cms_url($linkAddImage);?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">imagem</b></span></a>
<br /><br />

<a href="<?php echo cms_url($linkAddArq);?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">arquivo</b></span></a>
 
 </label><textarea name="txt" class="textarea-longo" id="txt"><?php echo set_value('txt', $row['txt']);?></textarea>



        
              
         
        <br />

<?php echo validation_errors(); ?>

