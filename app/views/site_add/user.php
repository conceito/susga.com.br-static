<div id="page" class="outra-classe" <?php echo $user['adminbar'];?>>
	
    <?php if(isset($user) || $user):
		
		foreach($user as $c=>$v):
	?>
		<p><strong><?php echo $c;?></strong> <?php echo $v;?></p>
	
	<?php
		endforeach;
	endif;
	?>
	
	
            
            
</div>