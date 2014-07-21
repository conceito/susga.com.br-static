<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>
<label for="status">Status</label>
<div class="form-opcoes">
<?php echo form_radio('status', 1, true);?> ativo | 
<?php echo form_radio('status', 0);?> inativo | 
<?php echo form_radio('status', 2);?> editando</div>

<br />

<label for="grupos"><b class="obr">[!]</b> Grupo</label><?php echo (! $grupos)? 'Não existem.<br />' : $grupos;?>

<br />

<?php if($rel != false){?>
<label for="rel">Relacionado à</label><?php echo (! $rel)? 'Não existem.<br />' : $rel;?>

<br />
<?php }?>

<label for="titulo"><b class="obr">[!]</b> Nome</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo');?>" />

<br />

<label for="nick">Apelido</label><input name="nick" id="nick" type="text" class="input-apelido" value="<?php echo set_value('nick');?>" />
<?php echo i('Identificação deste registro. <br />Deve ser único e NÃO pode conter espaços ou caracteres especiais.');?>

<br />

<label for="resumo"><b class="obr">[!]</b> Profissão/referência</label><textarea name="resumo" class="textarea-curto" id="resumo"><?php echo set_value('resumo');?></textarea>

       
<br />

<label for="tags">Tags</label><textarea name="tags" class="textarea-tags" id="tags"><?php echo set_value('tags');?></textarea>
<?php echo i('TAGS são palavras-chave para que seu conteúdo seja melhor indexado pelos mecanismos de busca.<br />Escreva as palavras ou expressões mais relevantes separadas por vírgula (,).');?>
       
<br />
 
 <label for="txt"><b class="obr">[!]</b> Depoimento
 <br />
 <a href="javascript:;" onmousedown="$('#txt').tinymce().show();"><span class="courier">&gt;</span> com editor </a> 
<br />
<a href="javascript:;" onmousedown="$('#txt').tinymce().hide();"><span class="courier">&gt;</span> sem editor </a>
<br /> <br />
Você só poderá adicionar novas imagens após salvar este conteúdo.
 
 </label><textarea name="txt" class="textarea-longo" id="txt"><?php echo set_value('txt');?></textarea>



        
              
         
        <br />

<?php echo validation_errors(); ?>