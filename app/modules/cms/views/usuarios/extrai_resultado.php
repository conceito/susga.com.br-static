<?php
if($resultado){
	
	?>
	<p>Total de <?php echo count($resultado);?> emails.<hr /></p>
	<?php
	
	
	foreach($resultado as $email){
		
		echo $email . "
		<br>";
		
		
		
	}
	?>
	<p><hr />Total de <?php echo count($resultado);?> emails.</p>
	<?php
	
} else {
?>
<p>Nenhum e-mail encontrado.</p>
<?php

}
?>