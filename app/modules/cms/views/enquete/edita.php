
<?php echo validation_errors(); ?>

<label for="status" class="lb-full">Status</label>

<div class="form-opcoes group-buttons">
		<?php echo inputs_status($row['status']);?>
     </div>

<br />


<label for="dt1" class="lb-full">Data</label><input name="dt1" id="dt1" type="text" class="input-curto" value="<?php echo set_value('dt1', $row['dt1']);?>" />

<br />

<label for="titulo" class="lb-full">Pergunta</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo" value="<?php echo set_value('titulo', form_prep($row['titulo']));?>" />

<br />

<?php if($rel != false){?>
<label for="rel" class="lb-full">Relacionado à</label><?php echo (! $rel)? 'Não existem.<br />' : $rel;?>

<br />
<?php }?>





        
              
         
        <br />
