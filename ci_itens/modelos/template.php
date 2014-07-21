<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-language" content="pt">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CMS3</title>
<!--padrão CMS-->
<script type="text/javascript">
//variavel global para os JS
var V = new Array();
V['base_url'] = 'http://localhost/cms3.com.br/';
V['site_url'] = 'http://localhost/cms3.com.br/';
V['uri'] = 'ci_itens/modelos/template.php';//  sempre quarda a uri para os favoritos
</script>
<link href="../css/mlColorPicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/jquery-1.3.2.min.js"></script><!-- 1-->
<script type="text/javascript" src="../../js/jquery.delay.js"></script>
<script type="text/javascript" src="../js/funcoes-cms.js"></script>
<script type="text/javascript" src="../../js/jquery.qtips.js"></script>
<script type="text/javascript" src="../js/padrao-cms.js"></script>
<script type="text/javascript" src="../../js/mlColorPicker.js"></script>

<script type="text/javascript" src="../js/colorpicker_init.js"></script>


<link href="../css/ui/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../css/estilo-cms3.css" rel="stylesheet" type="text/css" media="all" />
<!--padrão CMS fim-->

</head>

<body>
<div id="super-alerta"><!--super alerta-->
	<div class="var"></div>
	<div class="frase">Você precisa confirmar esta alteração!</div>
    <div class="opcoes"><a href="#" class="bot-verm"><span><b class="ico-menos">Cancelar</b></span></a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="#" class="bot-verde"><span><b class="ico-ok">Confirmar</b></span></a> </div>	
</div><!--super alerta fim-->
<div id="head">
<div class="chave-0">O site está desligado e está sendo redirecionado para: Manutenção. <a href="#">Para restaurá-lo clique aqui.</a></div>
<div class="linha1"><em>Olá apelido</em> | 00 de janeiro de 2009 |  
    
    <span class="lang-ico"><a href="#" onclick="return false;">pt</a> | 
    <ul id="langs-combo">
   	 <li><a href="#" title="português">pt</a></li>
     <li><a href="#" title="inglês">en</a></li>
	</ul>
    </span>  


<a href="#">suporte</a> | <a href="#">ajuda</a> | <a href="#">sair</a></div>
    <div class="linha2">
    	<div class="bloco1">Sistema de<br />Gerenciamento de<br />Conteúdo</div>
        <div class="bloco2">Nome da empresa <a href="#" title="ir para o site" class="visitar">&nbsp;</a></div>
    </div>
    <div class="linha3">Mensagens: <a href="#">3 não lidas</a></div>
    <div id="alertas"></div>
</div>
<div id="menu" class="equalz">
	<div class="round-top"></div>
    <!--aqui inicia o menu-->
    <a href="#" title="Painel" class="bot-painel">Painel</a><div class="div-favoritos"><a href="#" title="Favoritos" class="bot-favoritos">Favoritos</a>
    
   			 <ul class="submenu-favoritos">
              <li id="fav-1"><a href="#" title="remover" class="remover">x</a><a href="index.html">Novo administrador</a></li>
              <li id="fav-2"><a href="#" title="remover" class="remover">x</a><a href="#">Gerenciador de Arquivos</a></li>
              <li id="fav-3"><a href="#" title="remover" class="remover">x</a><a href="#">Sub Ite j joij oij oj oj m 1.3</a></li>
              <li id="fav-4"><a href="#" title="remover" class="remover">x</a><a href="#">Sub Item 1.4</a></li>
             
              </ul>
          </div> 
    
<div class="clear"></div>
    
    <ul class="botoes">

        <li><span>00</span><a href="#">Administração</a>
        	<ul class="submenu">
              <li><a href="#">Sub Item 1.1</a></li>
              <li><a href="#">Sub Item 1.2</a></li>
              <li><a href="#">Sub Item 1.3</a></li>
              <li><a href="#">Sub Item 1.4</a></li>
              <li><a href="#">Sub Item 1.2</a></li>
              <li><a href="#">Sub Item 1.3</a></li>
              <li><a href="#">Sub Item 1.4</a></li>
              </ul>
        </li>
    
    
        <li><span>1</span><a href="#">Usuários</a>
        	<ul class="submenu">
              <li><a href="#">Sub Item 1.1</a></li>
              <li><a href="#">Sub Item 1.2</a></li>
              <li><a href="#">Sub Item 1.3</a></li>
              <li><a href="#">Sub Item 1.4</a></li>
              <li><a href="#">Sub Item 1.2</a></li>
              <li><a href="#">Sub Item 1.3</a></li>
              <li><a href="#">Sub Item 1.4</a></li>
              </ul>
        
        </li>
    
    
        <li><span>2</span><a href="#">Gerenciador de Arquivos</a></li>
    
    </ul>
    
    
    <!--aqui termina o menu-->
    
</div>
<div id="content">
	
    <div class="wrap equalz">
    <div class="round-top"><div></div></div>
      <div class="margin">
      <!--aqui entra o conteúdo-->
      <h1><span class="h1 painel">Painel</span> <span class="favoritos-ico"><a href="#" title="adicionar aos favoritos" class="favoritos-add">&nbsp;</a><div class="fav-form"><div class="lad-esq"></div><div class="form"><span>Nome do link:</span><input name="nome_fav" type="text" />
            <a href="#" class="bot-fav-include"></a></div><div class="lad-dir"></div></div></span></h1>
      
      <p> <label for="dt1">Data</label><input name="cor" id="cor" type="text" />
     <div style="padding:10px;clear:both;border:1px solid black;vertical-align:middle;">
<span id="colorPicker1" style="float:left; border:1px solid black; width:20px; height:20px;margin:5px;background-color:white"></span>
<span id="text1" style="">click on the square</span>
</div>
      Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus non mi  dolor. Vivamus egestas tellus quis nunc vulputate vel dapibus enim  viverra. Phasellus pulvinar sodales pellentesque. Cras vitae pretium  orci. Cras dictum consectetur libero et convallis. Aliquam non nibh vel  urna semper hendrerit luctus at nulla. Cum sociis natoque penatibus et  magnis dis parturient montes, nascetur ridiculus mus. Fusce hendrerit,  velit a placerat congue, turpis nibh luctus sapien, quis ultricies  velit mi sit amet libero. Vestibulum quam ipsum, congue id suscipit et,  viverra non odio. Lorem ipsum dolor sit amet, consectetur adipiscing  elit. Sed dictum auctor ornare. Vestibulum sit amet consectetur purus.  Praesent vel sem neque, placerat malesuada tortor. </p>
  <p>Etiam dapibus, tortor eu porttitor ullamcorper, augue ligula rhoncus  ante, aliquam vehicula tellus dolor a nisl. Nam diam lorem, accumsan et  ultricies ut, euismod sit amet neque. Ut et est quis nulla posuere  egestas. Donec nisi justo, vulputate et aliquet id, lobortis vel nibh.  Nulla placerat congue diam, et euismod quam vehicula vestibulum. In ac  nulla quam. Quisque a mauris dolor, in ornare magna. Maecenas sit amet  magna odio. Duis vulputate sapien magna, ut molestie turpis. Etiam  sollicitudin lectus in mauris consequat scelerisque. Nam egestas nulla  quis ante posuere at laoreet quam lobortis. Curabitur aliquam orci vel  metus feugiat pellentesque. Nullam blandit ante in ante euismod cursus. </p>
  <p>Duis eget nibh in ligula euismod pretium ut eu lorem. Nunc eu neque  id orci facilisis sagittis sed sed urna. Nam a augue ut ante tincidunt  pellentesque id eu est. Morbi eros quam, gravida adipiscing gravida  vitae, tempus vitae enim. Proin ac velit arcu. Aenean vestibulum  venenatis erat a posuere. Cum sociis natoque penatibus et magnis dis  parturient montes, nascetur ridiculus mus. Vivamus vitae nisl quam.  Donec non vulputate magna. Curabitur et orci est. Sed viverra, arcu  quis pretium viverra, ante tortor consequat elit, ut mollis enim libero  eget justo. Sed condimentum tellus ut risus ullamcorper iaculis. </p>
  <p>Cras volutpat, urna at scelerisque volutpat, sapien arcu tempus  nisi, ut porta leo neque ut felis. Nam dictum dolor at nunc dignissim  ut consequat nulla tincidunt. In consectetur ipsum ac massa feugiat  tempus non nec velit. Integer faucibus elit id lectus molestie laoreet.  Praesent tincidunt bibendum mi sit amet tincidunt. Pellentesque  habitant morbi tristique senectus et netus et malesuada fames ac turpis  egestas. Nam vulputate congue ullamcorper. Cras quis velit a ligula  convallis eleifend sit amet ut erat. Morbi interdum mattis rutrum.  Vivamus consequat mattis leo, at aliquet tortor sodales sit amet.  Maecenas et nunc ut mauris tristique sollicitudin eu sed mauris. Fusce  scelerisque, sem eget placerat hendrerit, leo leo fermentum magna, nec  scelerisque dui massa id augue. Morbi lorem eros, aliquet at accumsan  vel, dignissim a urna. Sed auctor pellentesque dui nec hendrerit. </p>
  <p>Nunc ornare tincidunt turpis at suscipit. Praesent sit amet elit sed  metus semper viverra. Duis sit amet nunc orci, non cursus erat.  Phasellus nisl arcu, vulputate et semper quis, tristique at dolor.  Pellentesque semper vestibulum venenatis. Suspendisse potenti. Maecenas  massa nibh, semper et fermentum sit amet, pharetra eu metus. Sed justo  nunc, viverra ac iaculis aliquam, sagittis ac risus. Aliquam sapien  nunc, mollis vitae molestie eu, rhoncus sed nisl. Integer at metus  lorem, sit amet placerat sem. Ut sagittis justo justo, nec molestie  est. Curabitur lobortis vehicula turpis nec dapibus. Etiam nec  vulputate ipsum. Morbi blandit, justo sit amet vehicula fermentum, orci  diam pharetra leo, vel ultrices erat nibh sed felis. Donec consequat  ultricies hendrerit. </p>
  <!--aqui entra o conteúdo fim-->
  <div class="clear"></div>
  </div>
  <div class="round-bottom"><div></div></div>
</div>
    
</div>
<div id="footer">
	<ul>
        <li class="bloco1"><a href="#" title="Painel">Painel</a> | <a href="#" title="Ajuda">Ajuda</a></li>  
    
      <li class="bloco2">
        Sistema de Gerenciamento de Conteúdo<br />
		versão 3.0
      </li>   
    
      <li class="bloco3">
        Todos os direito reservados 2009<br />
      <a href="http://www.brunobarros.com" title="Bruno Barros - Comunicador Visual e Desenvolvedor Web" target="_blank">www.brunobarros.com</a></li>
      <div class="clear"></div>    
    </ul>
     <div class="clear"></div> 
</div>
</body>
</html>