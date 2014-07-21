<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-language" content="pt">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CMS3</title>
<!--padrão CMS-->
<script type="text/javascript" src="../../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery.qtips.js"></script>

<script type="text/javascript" src="../../js/ui.core.js"></script>
<script type="text/javascript" src="../../js/ui.tabs.js"></script>
<script type="text/javascript" src="../js/tabs-forms.js"></script>

<script type="text/javascript" src="../js/padrao-cms.js"></script>
<link href="../css/ui/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" media="screen" />
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
    <a href="#" title="Painel" class="bot-painel">Painel</a><div class="div-favoritos"><a href="#" title="Favoritos" class="bot-favoritos">Favoritos</a>
    
    <ul class="submenu-favoritos">
              <li><a href="#">Sub Item 1.1</a></li>
              <li><a href="#">Sub Item 1.2</a></li>
              <li><a href="#">Sub Item 1.3</a></li>
              <li><a href="#">Sub Item 1.4</a></li>
              <li><a href="#">Sub Item 1.2</a></li>
              <li><a href="#">Sub Item 1.3</a></li>
              <li><a href="#">Sub Item 1.4</a></li>
              </ul></div>  
    
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
    
    
        <li><span>1</span><a href="#">Usuários</a></li>
    
    
        <li><span>2</span><a href="#">Gerenciador de Arquivos</a></li>
    
    </ul>
    
    
    <!--aqui termina o menu-->
    
</div>
<div id="content">
	
    <div class="wrap equalz">
    <div class="round-top"><div></div></div>
      <div class="margin">
      <!--aqui entra o conteúdo-->
      <h1><span class="h1 novo">Novo Administrador</span> <span class="favoritos-add"><a href="#" title="adicionar aos favoritos"><img src="../img/favstar-off.png" width="13" height="13" alt="adicionar aos favoritos" /></a></span>
      
      <span class="ico-ajuda"><a href="#" title="Ajuda deste módulo" class="nyroModal" target="_blank">Ajuda do módulo</a></span>
      </h1>
      
      <!--barra de botões abaixo do título-->
      <div id="barra-botoes">
      <div class="floater">
      <ul>
        <li class="esq"><a href="#" class="bot-cinza"><span><b class="ico-marca">Marcar tudo</b></span></a>
      <a href="#" class="bot-cinza"><span><b class="ico-invert">Inverter</b></span></a>
       <a href="#" class="bot-verm"><span><b class="ico-menos">Limpar</b></span></a></li>
      
        <li class="dir"><a href="#" class="bot-verde"><span><b class="ico-mais">Novo</b></span></a>
     
      	<a href="#" class="bot-verde"><span><b class="ico-ok">Salvar</b></span></a> 
        <a href="#" class="bot-verde"><span><b class="ico-ok">Salvar e continuar</b></span></a></li>    
   	  </ul>
      
      
      
      </div>      
      </div>
      <!--barra de botões abaixo do título fim-->
      <form action="" method="post" enctype="multipart/form-data" name="formulario" id="formulario">
      
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Informações</a><img src="../img/ico-salvar.gif" width="8" height="8" alt="atualizado!" title="Não esqueça de salvar!" class="ico-atualizar" /></li>
		<li><a href="#tabs-2">Permissões</a><img src="../img/ico-salvar.gif" width="8" height="8" alt="atualizado!" title="Não esqueça de salvar!" class="ico-atualizar" /></li>
	</ul>
	<div id="tabs-1"><!--ABA == 1-->
		
        <label for="nome">Nome</label><input name="nome" id="nome" type="text" class="input-longo" />
               
        <br />
        <label for="email">E-mail</label><input name="email" id="email" type="text" class="input-longo" />
        <br />
        
        <h3>Novo item </h3>
        
        <label for="apelido">Apelido</label><input name="apelido" id="apelido" type="text" class="input-longo" />
        <img src="../img/ico-i.png" width="21" height="20" alt="informações" class="ico-i" title="Nome como os usuários e o sistema identificarão você. Não confunda com seu login!" />        
        <br />
        <label for="login">Login</label><input name="login" id="login" type="text" class="input-curto" />
        <img src="../img/ico-i.png" width="21" height="20" alt="informações" class="ico-i" title="Será o login para acessar a administração. <br />Deve ter entre 5 e 15 caracteres. <br />Não pode ter espaço e caracteres especiais!" />
        <br />
        <label for="senha">Senha</label><input name="senha" id="senha" type="password" class="input-curto" />
        <img src="../img/ico-i.png" width="21" height="20" alt="informações" class="ico-i" title="Será a senha para acessar a administração. <br />Deve ter entre 5 e 15 caracteres. <br />Não pode ter espaço e caracteres especiais!" />  
        <br />
        <label for="status">Status</label><div class="form-opcoes"><input name="status" type="radio" value="1" checked /> ativo &nbsp;&nbsp;&nbsp;<input name="status" type="radio" value="0"  /> inativo </div>
        
		<!--ABA == 1 fim-->
    <div class="tab-nav-system"><a href="#" class="tabs-go-1">próxima aba <span class="courier">&gt;</span></a></div>
    
    </div>
	<div id="tabs-2"><!--ABA == 2-->
	
    	
        <label for="tipo">Tipo de admin</label><div class="form-opcoes">
        <input name="tipo" type="radio" value="0" /> God &nbsp;&nbsp;&nbsp;
        <input name="tipo" type="radio" value="1"  /> Super admin &nbsp;&nbsp;&nbsp;
        <input name="tipo" type="radio" value="2" checked  /> Admin segmentado <img src="../img/ico-i.png" width="21" height="20" alt="informações" class="ico-i" style="margin-top:-1px;" title="Super admin: Poderá acessar qualquer módulo e poderá adicionar novos administradores.<br />Admin segmentado: Poderá ter sua atuação segmentada de acordo com as permições selecionadas.<br />Não poderá adicionar novos administradores." /></div>
    	<br />
        <label for="mod">Módulos permitidos</label><select name="mod" class="input-combo" multiple="multiple">
        <optgroup label="Selecione os módulos">        
        <option value="1">Usuários</option>
        <option value="2">Notícias</option>
        </optgroup>
      </select>
    	<br />
        <label for="acoes">Ações permitidas</label><div class="form-opcoes">
        <input name="acoes" type="checkbox" value="a" checked="checked" /> Apagar &nbsp;&nbsp;&nbsp;
        <input name="acoes" type="checkbox" value="c" checked="checked" /> Criar conteúdo &nbsp;&nbsp;&nbsp;
        <input name="acoes" type="checkbox" value="r" checked="checked" /> Gerar relatórios <img src="../img/ico-i.png" width="21" height="20" alt="informações" style="margin-top:-1px;" class="ico-i" title="O que o administrador poderá fazer no sistema." /></div>
          
        <br />
    
    <!--ABA == 2 fim-->
       <div class="tab-nav-system">
       <a href="#" class="tabs-go-0"><span class="courier">&lt;</span> aba anterior</a> | 
        <a href="#" class="tabs-go-2">próxima aba <span class="courier">&gt;</span></a>
        </div>
	</div>
	
</div>      


</form>

      
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