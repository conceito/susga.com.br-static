<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-language" content="pt">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CMS3</title>
<!--padrão CMS-->
<script type="text/javascript" src="../../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery.qtip.js"></script>

<script type="text/javascript" src="../js/padrao-cms.js"></script>
<script type="text/javascript" src="../js/painel.js"></script>
<link href="../css/estilo-cms3.css" rel="stylesheet" type="text/css" media="all" />

<!--padrão CMS fim-->


</head>

<body>
<div id="head">
	<div class="linha1"><em>Olá apelido</em> | 00 de janeiro de 2009 | pt   | <a href="#">suporte</a> | <a href="#">ajuda</a> | <a href="#">sair</a></div>
    <div class="linha2">
    	<div class="bloco1">Sistema de<br />Gerenciamento de<br />Conteúdo</div>
        <div class="bloco2">Nome da empresa</div>
    </div>
    <div class="linha3">Mensagens: <a href="#">3 não lidas</a></div>
</div>
<div id="menu" class="equalz">
	<div class="round-top"></div>
    <!--aqui inicia o menu-->
    <a href="#" title="Painel" class="bot-painel">Painel</a><a href="#" title="Favoritos" class="bot-favoritos">Favoritos</a> 
    
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
    <div class="clear"></div>
    </ul>
    
    
    <!--aqui termina o menu-->
    
</div>
<div id="content">
	
    <div class="wrap equalz">
    <div class="round-top"><div></div></div>
      <div class="margin">
      <!--aqui entra o conteúdo-->
      <h1><span class="h1 painel">Painel</span> <span class="favoritos-add"><a href="#" title="adicionar aos favoritos"><img src="../img/favstar-off.png" width="13" height="13" alt="adicionar aos favoritos" /></a></span></h1>
     
     <ul id="painel-blocos">

     <!--painel últimas atividades--> 
     <li class="pr15"><div id="painel-ult-atividades">
     	<h2>Últimas atividades <div class="control-painel"><a href="#" class="max" title="maximizar">_</a><a href="#" class="min" title="minimizar">_</a></div></h2>
        
        <div class="painel-content"><!--painel-content-->
        
        <table width="96%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <th align="left" scope="col">data</th>
            <th align="left" scope="col">quem</th>
            <th align="left" scope="col">oquê</th>
          </tr>
          <tr class="even">
            <td>99/99/9999</td>
            <td>fulano</td>
            <td>Atualizou conteúdo: Baile de Natal no
                                         Vera Cruz</td>
          </tr>
          <tr class="odd">
            <td>99/99/9999</td>
            <td>fulano</td>
            <td>Atualizou conteúdo: Baile de Natal no
                                         Vera Cruz</td>
          </tr>
        </table>
        <br />
		<a href="#" class="ml10"><span class="courier">&gt;</span> ver todas</a>
        <br />
<br />
		 </div><!--painel-content fim-->
        </div></li>
     <!--painel últimas atividades fim-->
     
     <!--painel mensagens--> 
     <li><div id="painel-mensagens">
     	<h2>Quadro de mensagens <div class="control-painel"><a href="#" class="max" title="maximizar">_</a><a href="#" class="min" title="minimizar">_</a></div></h2>
        
        <div class="painel-content"><!--painel-content-->
        
        <ul class="lista">
        <li><span class="imp">&nbsp;</span><a href="#" class="assunto">Título da mensagem <span class="apelido">- apelido</span></a>
        	<div class="mensagem"> <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus non mi  dolor. Vivamus egestas tellus quis nunc vulputate vel dapibus enim  viverra. Phasellus pulvinar sodales pellentesque. Cras vitae pretium  orci. Cras dictum consectetur libero et convallis. Aliquam non nibh vel  urna semper hendrerit luctus at nulla. Cum sociis natoque penatibus et  magnis dis parturient montes, nascetur ridiculus mus. Fusce hendrerit,  velit a placerat congue, turpis nibh luctus sapien, quis ultricies  velit mi sit amet libero. Vestibulum quam ipsum, congue id suscipit et,  viverra non odio. Lorem ipsum dolor sit amet, consectetur adipiscing  elit. Sed dictum auctor ornare. Vestibulum sit amet consectetur purus.  Praesent vel sem neque, placerat malesuada tortor. </p></div>
        </li>
        <li><span class="imp">!</span><a href="#" class="assunto lido">Título da mensagem <span class="apelido">- apelido</span></a>
        <div class="mensagem"> <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus non mi  dolor. Vivamus egestas tellus quis nunc vulputate vel dapibus enim  viverra. Phasellus pulvinar sodales pellentesque. Cras vitae pretium  orci. Cras dictum consectetur libero et convallis. Aliquam non nibh vel  urna semper hendrerit luctus at nulla. Cum sociis natoque penatibus et  magnis dis parturient montes, nascetur ridiculus mus. Fusce hendrerit,  velit a placerat congue, turpis nibh luctus sapien, quis ultricies  velit mi sit amet libero. Vestibulum quam ipsum, congue id suscipit et,  viverra non odio. Lorem ipsum dolor sit amet, consectetur adipiscing  elit. Sed dictum auctor ornare. Vestibulum sit amet consectetur purus.  Praesent vel sem neque, placerat malesuada tortor. </p></div>
        </li>
        <li><span class="imp">*</span><a href="#" class="assunto lido">Título da mensagem <span class="apelido">- apelido</span></a>
        <div class="mensagem">t amet, consectetur adipiscing  elit. Sed dictum auctor ornare. Vestibulum sit amet consectetur purus.  Praesent vel sem neque, placerat malesuada tortor.</div>
        </li>
        <div class="clear"></div>    
    	</ul>
        
       
		<a href="#" class="ml10 fr mr10"><span class="courier">&gt;</span> ver todas</a>
        
        <br />

		<fieldset><legend>Criar nova mensagem</legend>
        <form action="" method="post">
        
        <label>importante: <input name="imp" type="checkbox" value="1" /></label>
        <br />
        <input name="assunto" type="text" value="Assunto" class="input-text" />
        <br />
        <textarea name="mensagem" cols="" rows="">Mensagem</textarea>
        <br />
        limite de <input name="lim" type="text" value="300" />
        <a href="#" class="bot-verde"><span>Enviar</span></a>
        </form>        
        </fieldset>
        
        </div><!--painel-content fim-->
     </div></li>
     <!--painel mensagens fim-->
     
     <!--painel o que deseja fazer--><li class="pr15"> 
     <div id="painel-oquedeseja">
     	<h2>O que deseja fazer? <div class="control-painel"><a href="#" class="max" title="maximizar">_</a><a href="#" class="min" title="minimizar">_</a></div></h2>
        
        <div class="painel-content"><!--painel-content-->
        
        <ul>        
            <li><a href="#">Backup do BD</a></li>
            <li><a href="#">Editar Usuários</a></li>
            <li><a href="#">Nova Notícia</a></li>
            <li><a href="#">Nova Galeria</a></li>
            <li><a href="#">Backup do BD</a></li>
            <li><a href="#">Editar Usuários</a></li>
            <li><a href="#">Nova Notícia</a></li>
        </ul>
        <div class="clear"></div>
        
         </div><!--painel-content fim-->
     </div></li>
     <!--painel o que deseja fazer fim-->
     
     <!--painel suporte e ajuda--><li>
     <div id="painel-suporte">
     	<h2>Suporte e Ajuda <div class="control-painel"><a href="#" class="max" title="maximizar">_</a><a href="#" class="min" title="minimizar">_</a></div></h2>
        
        <div class="painel-content"><!--painel-content-->
        
        <div class="margin">
        <a href="#">Manual e dúvidas frequentes</a> <br />
        <a href="#">[importar usuários]</a> 
        <a href="#">[inserir fotos]</a> 
        <a href="#">[editar fotos]</a>
        <br /><br />
        <a href="#">Mensagem para suporte técnico</a>
        </div>
		<div class="clear"></div>
        
         </div><!--painel-content fim-->
     </div></li>
     <!--painel suporte e ajuda fim-->
     
     


	

     </ul>
     
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