<?php 
//echo '<pre>';
echo validation_errors(); 
if($camposExtra):
	foreach($camposExtra as $extra):
		
            $class = '';
            if($extra['type'] == 'input'){
			$class = 'input-longo';
			
		} else if($extra['type'] == 'text'){
			$class = 'textarea-curto';
		}
		
		echo $this->cms_libs->extraArrayToForm($extra['name'], $extra['id'], $extra['type'], $extra['data'], $class);
		echo '<br />';
		
	endforeach;
endif;


//var_dump( $this->cms_libs->extraStringToArray($row['extra']) );


?>
