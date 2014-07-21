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
V['img_ativa'] = '';
</script>
<script type="text/javascript" src="../../js/jquery-1.3.2.min.js"></script><!-- 1-->
<script type="text/javascript" src="../js/funcoes-cms.js"></script><!-- 2-->
<script type="text/javascript" src="../../js/jquery.qtips.js"></script><!-- 3-->
<script type="text/javascript" src="../../js/ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.tablednd.js"></script>
<script type="text/javascript" src="../../js/ui.datepicker.js"></script>
<script type="text/javascript" src="../js/padrao-cms.js"></script>
<script type="text/javascript" src="../js/listas.js"></script>

<link href="../css/ui/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../css/estilo-cms3.css" rel="stylesheet" type="text/css" media="all" />
<!--padrão CMS fim-->

</head>

<body>
<div id="super-alerta"><!--super alerta-->
	<div class="frase">Você precisa confirmar esta alteração!</div>
    <div class="opcoes"><a href="#" class="bot-verm"><span><b class="ico-menos">Cancelar</b></span></a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="#" class="bot-verde"><span><b class="ico-ok">Confirmar</b></span></a> </div>	
</div><!--super alerta fim-->

<div id="head">
	<div class="linha1"><em>Olá apelido</em> | 00 de janeiro de 2009 | pt   | <a href="#">suporte</a> | <a href="#">ajuda</a> | <a href="#">sair</a></div>
    <div class="linha2">
    	<div class="bloco1">Sistema de<br />Gerenciamento de<br />Conteúdo</div>
        <div class="bloco2">Nome da empresa</div>
    </div>
    <div class="linha3">Mensagens: <a href="#">3 não lidas</a></div>
     <div id="alertas">iopipoi</div>
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
      <h1><span class="h1 novo">Novo Administrador</span> <span class="favoritos-add"><a href="#" title="adicionar aos favoritos" class="favoritos-add">&nbsp;</a></span></h1>
      
      <!--barra de botões abaixo do título-->
      <div id="barra-botoes">
      <div class="floater">
      <ul>
        <li class="esq"><a href="#" class="bot-cinza check-all"><span><b class="ico-marca">Marcar tudo</b></span></a>
      <a href="#" class="bot-cinza check-invert"><span><b class="ico-invert">Inverter</b></span></a>
       <a href="#" class="bot-verm apagar-lote"><span><b class="ico-menos">Apagar</b></span></a></li>
      
        <li class="dir"><a href="#" class="bot-verde"><span><b class="ico-mais">Novo</b></span></a>
     
      	<a href="#" class="bot-verde"><span><b class="ico-ok">Salvar</b></span></a> 
        <a href="#" class="bot-verde"><span><b class="ico-ok">Salvar e continuar</b></span></a></li>    
   	  </ul>
      
      
      
      </div>      
      </div>
      <!--barra de botões abaixo do título fim-->
      
      <!--barra de navegação básica-->
      <div id="barra-navegacao">
      Páginas: << 1 2 3 4 5 6 7 >> | Total de xxxxxxxx registros | Ver   <select name="porpag" class="porpag" id="porpag">
        <option value="20">20</option>
        <option value="35">35</option>
        <option value="50">50</option>
      </select>        
      por página | 
      <form action="" method="post" name="busca" class="form-busca"><input name="q" value="busca" type="text" class="input-busca" /><input name="" type="image" src="../img/bot-form-ok.gif" alt="ok" class="ok" /></form>                                                       
      <a href="#" style="margin-left:40px;" class="bot-maisfiltros">mais filtros</a></div>
      <!--barra de navegação básica fim-->
      
      <!--barra de filtros avançados-->
      <div id="barra-filtros">
      <div class="perciana">
      <a href="#" class="fechar-filtros"><img src="../img/bot-fecha-div.gif" width="12" height="11" alt="fechar" /></a>
      <form action="" method="post" name="filtros" class="form-filtros">
      <label>DT1<input name="dt1" type="text" value="" class="input-data" /></label>
      <label>DT2<input name="dt2" type="text" value="" class="input-data" /></label>
      <select name="grupos" class="input-combo">
        <option value="0">Grupos</option>
        <option value="1">grupo1</option>
        <option value="2">grupo2</option>
      </select>
      <label><input name="ativo" type="radio" value="1" checked /> ativos</label>
       <label><input name="ativo" type="radio" value="0" /> inativos</label>
       <input name="" type="image" src="../img/bot-form-pesquisar.gif" alt="pesquisar" class="bot-pesquisar" />
      </form>
      </div> 
      </div>
      <!--barra de filtros avançados fim-->
      
      <form action="" method="post" name="formulario" id="formulario">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl">
      <thead>
      <tr>
        <th scope="col" class="sortable">&nbsp;</th>
        <th scope="col" class="cb">&nbsp;</th>
        <th scope="col">Tipo</th>
        <th scope="col">Pasta</th>
        <th scope="col">Quantidade</th>
        <th scope="col">Destaque</th>
        <th scope="col">Padrões</th>
        <th scope="col" class="data">Data</th>
      </tr>
      </thead>
      <tbody>
      <tr class="even" id="id1">
      	<td valign="top" class="dragme"></td>
        <td valign="top" class="td-cb"><input name="cb[]" type="checkbox" value="id1" class="cb" /></td>
        <td valign="top"><a href="#" title="Ver arquivos desta pasta"><img src="../img/pasta-imagem.png" width="32" height="30" alt="imagem" /></a></td>
        <td valign="top"><a href="#" title="Editar este item" class="edit">Nome / Título 11</a>
        	<div class="opcoes">
            <span class="resumo"><a href="#" title="Editar este item" class="resumo-item">ler resumo</a> |</span> 
            <span class="editar"><a href="#" title="Editar este item" class="editar-item">editar</a> |</span> 
            <span class="editar"><a href="#" title="Editar informações principais" class="editar-rapido-item">arquivos</a> |</span> 
            <span class="apagar"><a href="#" title="Apagar este item" class="apagar-item">apagar</a></span>
            <span class="confirma"><a href="#" class="nao-item">não</a> / <a href="#" class="sim-item">sim</a></span>
            </div>
            
            <!--resumo-->
            <div class="resumo-ler">If on the off-chance the image has been previously loaded then it should be in the buffer and changing the dimensions should be a lot quicker rather than having to use the very slow Manipulation library.</div>
            
        </td>
        <td valign="top">001</td>
        <td valign="top"><a href="#" class="destaque-sim" title="clique para trocar status">sim</a></td>
        <td valign="top">Max: 600x600<br />Min: 90x90</td>
        <td valign="top"><span class="data">12/10/2009</span><span class="status"><a href="#" class="ativo-item" title="clique para trocar status">ativo</a></span></td>
      </tr>
      <tr id="id2">
      	<td valign="top" class="dragme"></td>
        <td valign="top" class="td-cb"><input name="cb[]" type="checkbox" value="id2" class="cb" /></td>
        <td valign="top"><a href="#" title="Ver arquivos desta pasta"><img src="../img/pasta-imagem.png" width="32" height="30" alt="imagem" /></a></td>
        <td valign="top"><a href="#" title="Editar este item" class="edit">Nome / Título 22</a>
        	<div class="opcoes">
            <span class="resumo"><a href="#" title="Editar este item" class="resumo-item">ler resumo</a> |</span> 
            <span class="editar"><a href="#" title="Editar este item" class="editar-item">editar</a> |</span> 
            <span class="editar"><a href="#" title="Editar informações principais" class="editar-rapido-item">arquivos</a> |</span> 
            <span class="apagar"><a href="#" title="Apagar este item" class="apagar-item">apagar</a></span>
            <span class="confirma"><a href="#" class="nao-item">não</a> / <a href="#" class="sim-item">sim</a></span>
            </div>
            
             <!--resumo-->
            <div class="resumo-ler">If on the off-chance the image has been previously loaded then it should be in the buffer and changing the dimensions should be a lot quicker rather than having to use the very slow Manipulation library.</div>
        </td>
        <td valign="top">001</td>
        <td valign="top"><a href="#" class="destaque-nao" title="clique para trocar status">não</a></td>
        <td valign="top">Max: 600x600<br />Min: 90x90</td>
        <td valign="top"><span class="data">12/10/2009</span><span class="status"><a href="#" class="inativo-item" title="clique para trocar status">inativo</a></span></td>
      </tr>
      </tbody>
    </table>
    </form>
    
    Páginas: << 1 2 3 4 5 6 7 >>
      
      
      
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