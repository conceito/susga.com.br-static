<?php
/******************************************
*  Template: histórico da venda 
*  Controller: cms/loja/vendaEdita
*/

$baseurl = base_url();

$ex = $extr['historico'];
?>

<?php foreach ($ex as $his): ?>	
    <table class="table table-striped">
    <thead>
    <tr>
      <th style="width:125px">Adicionado em</th>
      <th style="width:150px">Situação</th>
      <th>Comentários</th>
      <th>Cliente notificado</th>
    </tr>
    </thead>
    <tbody>
	<tr>
      <td><?php echo formaPadrao($his['data']); ?> <?php echo $his['hora']; ?></td>
	  <td><?php echo $his['anotacao']; ?></td>
	  <td><?php echo $his['obs']; ?></td>
	  <td><?php echo ($his['notificado'] == '1') ? 'Sim' : 'Não'; ?></td>
	  
      
	</tr>
	</tbody>
	</table>
<?php endforeach; ?>

<div class="new-historico">
	
    
    
</div><!-- .new-historico -->

<div class="well">

    <div class="page-header" style="margin-top:0">
      <h4 class="legend">Atualizar status</h4>
    </div>
    
    <input type="hidden" value="<?php echo $extr['extrato']['id'];?>" id="extrato_id" />

	<div class="control-group ">    
	<label for="situacao" class="lb-full">Situação do pedido</label>
	<div class="form-opcoes">
		<?php  echo form_dropdown('situacao_id', $this->config->item('status_transacao'), '', 'id="situacao"');?>       
     </div>	
	</div><!-- .control-group -->
    
    
    <div class="control-group">
    <div class="controls" style="padding-left: 140px;">    
    	<label class="checkbox">
        <input type="checkbox" name="informar" id="informar" value="1"> Informar ao cliente
      </label>      
    </div>
    </div><!-- .control-group -->
    
   
    
    <div class="control-group ">    
    <label for="comentarios" class="lb-full">Comentários</label>
    <div class="form-opcoes group-buttons">
       <textarea rows="3" class="input-xxlarge" name="comentarios" id="comentarios"></textarea>
     </div>	
	</div><!-- .control-group -->
    
    <div class="form-actions" style="padding-left: 140px; margin:0;">
      <a class="btn set-new-historico">Adicionar histórico de pedido</a>
    </div>
    
</div>

