
<h3>Novo campo extra:</h3>

<label for="extraCampo_novo" class="lb-inline">Nome do campo</label><input name="extraCampo_novo" id="extraCampo_novo" type="text" class="input-longo input-titulo" value="" />
    
    <br />

    
    <label for="extraId_novo" class="lb-inline">Identificador</label><input name="extraId_novo" id="extraId_novo" type="text" class="input-longo" value="" />
    
    <input name="extraType_<?php echo $name;?>" type="hidden" value="" />
    
    <label for="tipo" class="lb-inline">Tipo</label><div class="form-opcoes">
<?php echo form_radio('extraType_novo', 'input', true);?> input | 
<?php echo form_radio('extraType_novo', 'text');?> text | 
<?php echo form_radio('extraType_novo', 'radio');?> radio | 
<?php echo form_radio('extraType_novo', 'check');?> check | 
<?php echo form_radio('extraType_novo', 'combo');?> combo | 
<?php echo form_radio('extraType_novo', 'multi');?> multi 

</div>

<br />
    
    <br />
    <br />