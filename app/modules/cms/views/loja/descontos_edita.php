<?php
/******************************************
*  Template: edição de conteúdo de produto
*  Controller: cms/loja/edita
*/
?>
<?php echo validation_errors(); ?>

<div class="panel-left clearfix">
	
    <div class="ai-page">
    	
        <div class="ai">
        <label for="grupos" class="lb-full two-col">Tipo de desconto</label>
		<select name="tipo" id="tipo" class="input-combo " disabled="disabled">
        	<option value="cupom" <?php echo set_select('tipo', 'cupom', ($des['tipo']=='cupom') );?>>Cupom</option>
            <option value="desconto" <?php echo set_select('tipo', 'desconto', ($des['tipo']=='desconto') );?>>Desconto</option>
        </select>
        </div>
        
        <div class="page">
        <label for="titulo" class="lb-full two-col"><b class="obr">[!]</b> Nome para identificar o desconto</label><input name="titulo" id="titulo" type="text" class="input-longo input-titulo full" value="<?php echo set_value('titulo', form_prep($des['titulo']));?>" />
        </div>
        
    </div><!-- .ai-page --> 
    
    <br /> 
    
    
    <table class="table-layout" style="clear:both;">
    	<tr>
        	<td>
            <label for="regra" class="lb-full two-col">Regra para desconto</label>
            <?php echo $des['combo_regra'];?>            
            </td>
            
            <td class="col-verificador">
            <label for="verificador" class="lb-full two-col">Quantidade</label>        
            <div class="input-prepend">        
             <span class="add-on">Qt</span><input type="text" class="input-mini" name="verificador" id="verificador" value="<?php echo set_value('verificador', $des['verificador']);?>" style="float:left" />
         	</div>
            </td>
            
            <td class="col-valor">
            <label for="valor" class="lb-full two-col">Valor descontado</label>
            <div class="input-prepend">        
             <span class="add-on">R$</span><input type="text" class="input-mini type-valor" name="valor" id="valor" value="<?php echo set_value('valor', $des['valor']);?>" style="float:left" />
            </div>
            </td>
        </tr>
    </table>
    
    
    	
    <div class="control-group" style="clear:both">
    
    <label for="grupo" class="lb-full two-col">Grupos de clientes</label>
	<?php echo $des['combo_grupo'];?>
    
    </div><!-- .control-group --> 
    
    
	

	
	

</div><!-- .panel-left -->


<div class="panel-right clearfix">


	<div class="control-group box">
    
	<label for="status" class="lb-full two-col">Status</label>
	<div class="form-opcoes group-buttons">
		<?php echo inputs_status($des['status']);?>
     </div>
	
	</div><!-- .control-group -->
    
    <div class="control-group box">
    
	<label for="ordem" class="lb-full two-col">Prioridade</label>
    <input name="ordem" id="ordem" type="text" class="input-curto" value="<?php echo set_value('ordem', $des['ordem']);?>" />
	
    </div><!-- .control-group -->
    
	
    <div class="control-group box">
    
	<label for="from" class="lb-full two-col">Data de início</label>
    <input name="data" id="from" type="text" class="input-curto" value="<?php echo set_value('data', $des['data']);?>" />
	<label for="to" class="lb-full two-col">Data de término</label>
    <input name="termino" id="to" type="text" class="input-curto" value="<?php echo set_value('termino', $des['termino']);?>" />
    </div><!-- .control-group -->
	
	
	
	
	
 


</div><!-- .panel-right -->

        
              