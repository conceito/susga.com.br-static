<?php
if($corpo):

    echo '<p>Foram encontrados '. $corpo[0]['total'] .' registros nesta consulta.</p>';

	$i = 1;
	foreach($corpo as $row):
		
              $t = $row['tipo'];
              $porPag = $row['porPag'];
              $offset = $row['offset'];
              $d1 = $row['dt1'];
              $d2 = $row['dt2'];
              $ate = $porPag + $offset;
              $tb = $row['tb'];
              $grupo = $row['grupo'];
              $stt = $row['stt'];
              $campos = $row['campos'];
              $extra = $row['extra'];
              $total = $row['total'];

            

              $uri = '/co:'.$t;
              $uri .= '/p:'.$porPag;
              $uri .= '/o:'.$offset;
              $uri .= '/dt1:'.$d1;
              $uri .= '/dt2:'.$d2;
              $uri .= '/tb:'.$tb;
              $uri .= '/g:'.$grupo;
              $uri .= '/stt:'.$stt;
              $uri .= '/campos:'.$campos;
              $uri .= '/extra:'.$extra;
          ?>
          <a href="<?php echo cms_url('cms/cmsutils/exportaPlanilha'.$uri);?>" target="_blank" title="clique para gerar a planilha">Exportação de dados parte #<?php echo $i;?> (<?php echo $offset?> - <?php echo $ate;?>)</a>
          <br>
			<br>

          <?php
		$i++;
	endforeach;

        
else:
?>
<p>Não foram encontrados registros com estas opções.</p>
<?php
endif;
?>
