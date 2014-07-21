<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login CMS3</title>
<script type="text/javascript">
//variavel global para os JS
var V = new Array();
V['base_url'] = 'http://localhost/cms3.com.br/';
V['site_url'] = 'http://localhost/cms3.com.br/';
V['uri'] = '';//  sempre quarda a uri para os favoritos
</script>
<script type="text/javascript" src="../../js/jquery-1.3.2.min.js"></script><!-- 1-->
<script type="text/javascript" src="../../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/login.js"></script>

<link href="../css/estilo-cms3.css" rel="stylesheet" type="text/css" media="all" />
<style type="text/css">
<!--
html, body {
	height: 100%;
	width: 100%;
}
-->
</style>
</head>

<body scroll="no"><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#bae4ec">
  <tr>
    <td align="center" valign="middle">
    
    <div id="login">
    <div class="top"></div>
    
    <div class="titulo">Faça seu <span>login</span></div>
    
    <div class="form-login">
    <!--<div class="resposta">resposta</div>-->
    <form action="" method="post" name="formlogin" id="formlogin">
    <div class="campos">
    <label>seu login</label>
    <input name="adminlogin" id="adminlogin" type="text" class="input-curto" />
    
    <br />
    <label>sua senha</label>
    <input name="adminsenha" id="adminsenha" type="password" class="input-curto" />
    <br />
    <a href="#" class="bot-lembra">Não lembro minha senha!</a>    
    
    </div>
    <input name="" type="image" src="../img/login-bot1.jpg" alt="OK" class="bot-ok" />
    </form>
    
    <div class="clear"></div>
    <form action="" method="post" name="email" id="lembraemail">
    <fieldset><legend>Entre com o e-mail cadastrado</legend>
    <input name="adminemail" type="text" class="input-email" /><input name="" type="button" value="lembrar" class="bot-lembra" />
    </fieldset>
    </form>
    
    </div>
    
    <!--quando não há suporte a JS-->
    <div class="nao-tem-js">
    <span>Atenção!</span>
	Seu navegador não suporta JavaScript, ou está desabilitado, por isso não será possível acessar o sistema.
    </div>
    <!--quando não há suporte a JS fim-->
    <!--quando fo IE6 -->
    <div class="ie6">
    <span>Atenção!</span>
	Você está navegando com Internet Explorer 6! Este browser está desatualizado e não dá suporte aos recursos deste sistema. Faça a atualização o mais rápido possível.
    </div>
    <!--quando fo IE6 fim-->
    
    <div class="clear"></div>
    <div class="bottom">
    Sistema de Gerenciamento de Conteúdo v3.0
<span>Todos os direitos reservados 2009 - 2010 | <a href="mailto:bruno@brunobarros.com">bruno@brunobarros.com</a></span></div>
    </div>
    
    <br /><br /><br />
    </td>
  </tr>
</table>

</body>
</html>