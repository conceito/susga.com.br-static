<?php $baseurl = base_url();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
if(isset($metatags)):
	echo $metatags;
endif;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?></title>

<link rel="stylesheet" href="<?php echo $baseurl . app_folder();?>ci_itens/css/estilo-cms4.css" />
<link rel="stylesheet" href="<?php echo $baseurl . app_folder();?>ci_itens/css/estilo-cms-cliente.css" />
<script type="text/javascript">
//variavel global para os JS
var V = new Array();
V['base_url'] = '<?php echo base_url();?>';
V['site_url'] = '<?php echo trim(cms_url(), '/').'/';?>';
V['uri'] = '<?php echo uri_string();?>';//  sempre quarda a uri para os favoritos
</script>
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>libs/jquery/jquery-1.8.0.min.js"></script><!-- 1-->
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>libs/jquery/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $baseurl . app_folder();?>ci_itens/js/login.js"></script>


<style type="text/css">
<!--
html, body {
	height: 100%;
	width: 100%;
}
body{
	background:#CCC url(<?php echo $baseurl . app_folder();?>ci_itens/img/login-bg.jpg) no-repeat center center;
	background-size:cover;
	
}
.lembra-status {
	display: block;
	clear: both;
}
.lembra-status img {
	float: left;
	margin-right: 10px;
}
.lembra-status span {
	font-weight: bold;
}
-->
</style>
</head>

<body scroll="no"><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" valign="middle">
    
    <div id="login">
    
    <?php if($resposta != ''){?>
    <div id="log" class="resposta"><?php echo $resposta;?></div>
    <?php }?>
    <?php echo validation_errors(); ?>
    
    
    <div class="titulo"><?php echo $this->painel_model->getLogotipo('login');?></div>
    
    <div class="form-login">
    
    
    
    <form action="<?php echo cms_url('cms/login/fazLogin');?>" method="post" name="formlogin" id="formlogin">
    <div class="campos">
    <label class="lb-login" for="adminlogin">seu login</label>
    <input name="adminlogin" id="adminlogin" type="text" class="input-curto" />
    
    <br />
    <label class="lb-login" for="adminsenha">sua senha</label>
    <input name="adminsenha" id="adminsenha" type="password" class="input-curto" />
    <br />
	<label for="salvar_cookie"><input type="checkbox" value="1" name="salvar_cookie" id="salvar_cookie" /> Manter logado</label>
    <br /><br />
    <a href="#" class="bot-lembra">Não lembro minha senha!</a>    
    
    </div>
    <input name="" type="image" src="<?php echo $baseurl. app_folder();?>ci_itens/img/login-bot1.png" alt="OK" class="bot-ok" />
    </form>
    
    <div class="clear"></div>
    <form action="<?php echo cms_url('cms/login/lembraSenha');?>" method="post" name="lembraemail" id="lembraemail">
    <fieldset><legend>Entre com o e-mail cadastrado</legend>
    <input name="adminemail" id="adminemail" type="text" class="input-email" />
    <input name="" type="submit" value="lembrar" class="bot-lembra" disabled="disabled" />
    <div class="lembra-status"><img src="<?php echo $baseurl;?>ci_itens/img/loading.gif" width="16" height="16" alt="aguarde..." /><span></span></div>
    </fieldset>
    </form>
    
    </div>
    
    <!--quando não há suporte a JS-->
    <div class="nao-tem-js">
    <span>Atenção!</span>
	Seu navegador não suporta JavaScript, ou está desabilitado, por isso não será possível acessar o sistema.
    </div>
    <!--quando não há suporte a JS fim-->
   
    
    <div class="creditos">
    Sistema de Gerenciamento de Conteúdo v<?php echo $this->config->item('cms_ver');?>
    <br />
    <span>Todos os direitos reservados 2009 - <?php echo date('Y');?> | <a href="mailto:bruno@brunobarros.com" class="no-lnk-style">bruno@brunobarros.com</a></span>
	</div><!-- .creditos -->

</div><!-- #login -->
    
    
    <br /><br /><br />
    </td>
  </tr>
</table>

</body>
</html>