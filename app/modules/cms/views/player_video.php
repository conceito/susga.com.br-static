<?php 
$base = base_url();
?>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" valign="middle">
    
<div id="container"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
<script type="text/javascript" src="<?php echo $base . app_folder();?>js/swfobject.js"></script>
<script type="text/javascript">
	var s1 = new SWFObject("<?php echo $base . app_folder();?>ci_itens/vplay/player.swf","ply","480","320","9","#FFFFFF");
	s1.addParam("allowfullscreen","true");
	s1.addParam("allowscriptaccess","always");
	s1.addParam("flashvars","file=<?php echo $base . app_folder();?>upl/arqs/<?php echo $v;?>&image=preview.jpg");
	s1.write("container");
</script>
    
    &nbsp;</td>
  </tr>
</table>

