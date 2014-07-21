<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Descadastrar</title>
<script type="text/javascript" src="<?=base_url()?>js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	
	$(".lembra").click(function(){
		$(".form_lembra").slideDown('slow');
		$(".form_login").slideUp('slow');
	});
	$(".login").click(function(){
		$(".form_lembra").slideUp('slow');
		$(".form_login").slideDown('slow');
	});
	
});
</script>

<?php 
if(isset($head)):
	echo $head;
endif;

?>


<style type="text/css" media="screen">
<!--
html{

height: 100%;
		overflow: hidden;
}
img{
	margin: 0px;
	padding: 0px;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
}
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #6f7f82;
	margin: 0px;
	padding: 0px;
	height: 100%;
	background-color: #eceaea;
}
#topo{
	background:#3CC;
	background-repeat: no-repeat;
	height: 59px;
	width: 369px;
	padding-top: 30px;
	padding-left: 40px;
}
h1{
	font-size: 28px;
	font-weight: bold;
	color: #FFFFFF;
	margin: 0px;
	padding: 0px;
	display: inline;
	float: left;
}
td {
font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #6f7f82;
}

#div_centro {
	width: 409px;
	margin: 0 auto;
	border:1px none red;
	background-image: url(<?=base_url()?>ci_itens/img/cms_login_fundo.jpg);
	background: #FFF repeat-y;
}
.bot{
	color: #646464;
	background-color: #e0dee7;
	height: 20px;
	
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
}
label{
	font-weight: bold;
	color: #6f7f82;
	display: block;
	float: left;
	height: 27px;
	width: 80px;
	position: relative;
	
	padding-left: 50px;
}
form{
	margin: 0px;
	padding: 0px;
}

textarea{
border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-top-color: #F0F0F0;
	border-right-color: #dcdcdc;
	border-bottom-color: #F0F0F0;
	border-left-color: #dcdcdc;
}

input.texto {
	width: 200px;
	position: relative;

}
.erros{
	color: #990000;
	position: relative;
	left: 50px;
}
a:link, a:visited{
	color: #006699;
	text-decoration: none;
}
a:hover, a:active{
	color: #003399;
	text-decoration: underline;
}
 -->
</style>

</head>

<body>

<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div id="div_centro">
      <div id="topo">
        <h1>Descadastrar</h1>
      </div>
      
     
      
      <br /><br />
      
       <div class="form_login">
      <form action="<?=cms_url('cms/newsopc/descadastra')?>" method="post">
      
      <label title="Seu e-mail">Seu e-mail:</label> <input name="seuemail" class="texto" id="seuemail" type="text" value="" />
      <input name="id_mens" type="hidden" value="<?php echo $id_mens;?>" />
      <input name="id_user" type="hidden" value="<?php echo $id_user;?>" />
      
      	<br /><br />
         <?php 
	  if(isset($motivo)):
	  echo '<span class="erros">' . $motivo . '</span>';
	  endif;
	  ?>
      <?php echo validation_errors(); ?>
      
      <br />
      
      <label> </label><input name="submit" type="image" src="<?=base_url()?>ci_itens/img/bot-form-ok.gif" alt="OK" />
      </form>
      <br />
     
      </div>
      <br /><br />
      
     
    </div><br /><br /><br /></td>
  </tr>
</table>



</body>
</html>
