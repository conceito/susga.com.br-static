
<?php
$bu = base_url();
?>
<div class="dragbox <?php echo $vars['status'];?>" id="<?php echo $vars['id'];?>" >  
        <h2><img src="<?php echo cms_img();?>h2-mensagens.gif" width="22" height="22" alt=" " /><?php echo $vars['label'];?> </h2>  
        <div class="dragbox-content" <?php echo ($vars['status']=='hidden')?'style="display:none;"':'';?>>
        
       <?php if(strlen($desc) > 5):?>
       <p style="padding:0; color: #999;"><?php echo $desc;?></p>
       <?php endif;?>

       <table width="96%" border="0" cellspacing="0" cellpadding="5">
          <tr>
              <?php
              foreach($labels as $lbl):
              ?>
            <th align="left" scope="col"><?php echo $lbl;?></th>
            <?php
            endforeach;
            ?>
          </tr>
          <?php
		  $i = 0;
		  if(isset($rows) && $rows):
          foreach($rows as $row):
		  	
			$class = ($i%2 == 0) ? 'even' : 'odd';
			$data = $row['dt_ini'];
			$tit = $row['titulo'];
			$quem = $row['autor'];
			
		  ?>
          
          <tr class="<?php echo $class;?>">
            <td><?php echo $data;?></td>
            <td><?php echo $tit;?></td>
            <td><?php echo $quem;?></td>
          </tr>
          <?php
		  $i++;
          endforeach;
		  
		  else:
		  ?>
          <tr class="">
            <td></td>
            <td>Ainda não possui conteúdo.</td>
            <td></td>
          </tr>
          <?php
		  endif;
		  ?>
     
        </table>
        <br />
		<a href="<?php echo cms_url($vars['uri']);?>" class="ml10"><span class="courier">&gt;</span> ver todas</a>
        <br />
<br />
       
       
       
       
       
        <div class="clear"></div>
        
         </div>
     </div>