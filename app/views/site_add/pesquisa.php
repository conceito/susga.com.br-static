<?php echo form_open('pesquisa/r', array('method' => 'get', 'class' => 'form-search'));?>

<div class="input-append">

<?php echo form_label('', 'q') . form_input(array(
              'name'        => 'q',
              'id'          => 'q',
              'value'       => $this->cms_pesquisa->get_terms(),
              'maxlength'   => '',
              'size'        => '50',
			  'class'       => 'span4 search-query',
              'style'       => '',
			  'placeholder' => 'Pesquisar'
            ));?>
<?php echo form_button( array(
    'name' => '',
    'id' => 'submit',
    'value' => '',
    'type' => 'submit',
    'content' => 'OK',
	'class' => 'btn'
));?>

</div>

<?php echo form_close();?>


	
    <?php
	if(isset($total)){
		if($total == 0){
			echo '<p>Não foi encontrado nenhum resultado.</p>';	
		} else if($total == 1){
			echo '<p>Foi encontrado <strong>1</strong> resultado.</p>';
		} else {
			echo '<p>Foram encontrados <strong>'.$total.'</strong> registros. </p>';
		}
	}
	
    if(isset($result) && $result):	
	
	
		foreach($result as $row):
		
		$id = $row['id'];
		$nick = $row['nick'];
		$titulo = $row['titulo'];
		$resumo = $row['resumo'];
		$dt_ini = $row['dt_ini'];
		$modulo_id = $row['modulo_id'];
		$modulo_titulo = $row['modulo_titulo'];
		$grupo_nome = $row['grupo_nome'];
		$grupo_nick = $row['grupo_nick'];
		$grupo_id = $row['grupo_id'];
		$front_uri = $row['front_uri'];
		$uri = $row['uri'];
		
	
		
	?>

	
    <span class="label fs-11"><?php echo $modulo_titulo;?></span>
    <br />
    <p><a href="<?php echo site_url($uri);?>"><?php echo $titulo;?></a><br />
	<?php echo $resumo;?></p>
    	<?php //echo row_tags_html($row, $this->uri->segment(1), 'a');?>
        <?php //echo row_tags_html($row);?>
    
    <?php
		endforeach;
		
    endif;
	?>


	<?php echo (isset($pagination)) ? $pagination : '';?>