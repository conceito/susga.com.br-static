<?php
$bu = base_url();
$imgPath = cms_img();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $siteNome;?></title>
</head>

<body link="#098bd5" vlink="#098bd5" vlink="#990000"><table width="600" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#83cfdd">
  <tr>
    <td>
    <table width="600" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="3"><img src="<?php echo $imgPath;?>/email-topo.jpg" width="600" height="28" alt=" " /></td>
    </tr>
  <tr>
    <td width="34">&nbsp;</td>
    <td width="533">
    <font face="Arial, Helvetica, sans-serif" size="4" color="#3e8795"><strong><?php echo $siteNome;?></strong></font>
    <br />
    <br />
    <font face="Arial, Helvetica, sans-serif" size="2" color="#444444"><?php echo $corpo;?></font>
    
    
    </td>
    <td width="33">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><hr color="#83cfdd" size="1" width="100%" noshade="noshade"/>
    
    <div align="right"><font size="2" color="#098bd5" face="Arial, Helvetica, sans-serif"><a href="mailto:<?php echo $emailSite;?>"><?php echo $emailSite;?></a> | <a href="<?php echo $urlSite;?>" title="visite" target="_blank"><?php echo $urlSite;?></a></font></div>
    
  
    <br /></td>
    <td>&nbsp;</td>
  </tr>
</table>

    
    
    </td>
  </tr>
  
</table>

</body>
</html>
