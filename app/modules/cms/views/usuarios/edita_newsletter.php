<?php echo validation_errors(); ?>
<label for="status" class="lb-full">Recebe news</label>
<div class="form-opcoes">
<?php echo form_radio('news', 1, ($row['news']==1));?> Sim | 
<?php echo form_radio('news', 0, ($row['news']==0));?> Não</div>

<br />

<label for="filtros" class="lb-full">Filtros</label><?php echo $row['combo_filtros'];?>
        
              
         
        <br />

