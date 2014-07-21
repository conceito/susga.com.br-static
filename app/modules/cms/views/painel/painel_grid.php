<?php
$bs = base_url();
?>
<script type="text/javascript" >
$(function(){
	
});
</script>


<div class="column" id="column1">  
    
            
            <?php //echo $blocos[0];?>
            <?php //echo $blocos[1];?>
     <?php
     if(isset($paineis['col1']) && $paineis['col1']):
	 	
		foreach($paineis['col1'] as $c):
	 		
			echo $c;
	 
	 	endforeach;
     endif;
	 ?>
             
        
    
</div> 

 
<div class="column" id="column2" >  


    <?php
     if(isset($paineis['col2']) && $paineis['col2']):
	 	
		foreach($paineis['col2'] as $c):
	 		
			echo $c;
	 
	 	endforeach;
     endif;
	 ?>
    
    
     
</div>






