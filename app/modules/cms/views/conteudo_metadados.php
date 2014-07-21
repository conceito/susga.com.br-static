
<div class="panel-left clearfix">
	
    <label for="scripts" class="lb-full">Tudo que você adicionar aqui será inserido na tag &lt;head&gt; desta página. Você pode inserir Javascript, CSS e metatags adicionais.</label>
    
    <textarea name="scripts" class="textarea-curto" id="scripts" rows="20" style="width:100%"><?php echo set_value('scripts', $row['scripts']);?></textarea>
  
  
  

<br />
		
</div><!-- .panel-left -->


<div class="panel-right clearfix">

	
	<?php
  if($metadados):
  
  foreach($metadados as $c => $v):
  	
	$type = $v['type'];
	$values = $v['values'];
	$selected = $v['selected'];
  ?>
      <label for="<?php echo $c;?>" class="lb-inline"><?php echo $c;?></label>
      
      <?php if($type == 'input'):?>
      <input type="text" name="<?php echo $c;?>" id="<?php echo $c;?>" value="<?php echo $values;?>" class="input-curto" />
      <?php else:
	  		echo '<div class="form-opcoes">';
			for($x = 0; $x < count($values); $x++):
			
			echo form_radio($c, $values[$x], ($selected == $x)) . $values[$x] . ' &nbsp;&nbsp;&nbsp; ';
	 
	  		endfor;
			echo '</div>';
	  		endif;?>
            
            <br />

      
  <?php
  endforeach;
  
  endif;
  ?>



</div><!-- .panel-right -->       
              
         


