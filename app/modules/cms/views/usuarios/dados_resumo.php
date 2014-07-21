<table width="100%" border="0" cellspacing="5" cellpadding="0" class="table">
  
  <?php 
  foreach($user as $row):?>
  
  <tr>
    <td><strong><?php echo $row['label'];?></strong></td>
    <td><?php echo $row['valor'];?></td>
  </tr>
  
  <?php endforeach;?>
  
</table>
