<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-language" content="pt" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Menu - CMS4</title>
<!--padrão CMS-->
<script type="text/javascript">
//variavel global para os JS
var V = new Array();
V['base_url'] = 'http://localhost/cms4.com.br/';
V['site_url'] = 'http://localhost/cms4.com.br/';
V['uri'] = 'uri/novo';
</script>

<script type="text/javascript" src="../../libs/jquery/jquery-1.4.4.min.js"></script><!-- 1-->
<script type="text/javascript" src="../../libs/jquery/jquery.delay.js"></script><!-- 2-->
<script type="text/javascript" src="../js/funcoes-cms.js"></script><!-- 3-->
<script type="text/javascript" src="../../libs/jquery/jquery.qtips.js"></script><!-- 4-->
<script type="text/javascript" src="../../libs/jquery/ui.core.js"></script>
<script type="text/javascript" src="../../libs/jquery/ui.datepicker.js"></script>
<script type="text/javascript" src="../js/datepicker_init.js"></script>

<!--dialog-->
<script type="text/javascript" src="../../libs/jquery/jquery.nyroModal.js"></script>
<script type="text/javascript" src="../js/nyroModal_init.js"></script>
<!--dialog-->
<script type="text/javascript" src="../../libs/jquery/ui.tabs.js"></script>
<script type="text/javascript" src="../../libs/jquery/ui.sortable.js"></script>

<script type="text/javascript" src="../js/menus.js"></script>

<script type="text/javascript" src="../../libs/jquery/mlColorPicker.js"></script>

<script type="text/javascript" src="../js/colorpicker_init.js"></script>

<script type="text/javascript" src="../js/padrao-cms.js"></script>
<script type="text/javascript" src="../js/tabs-forms.js"></script>



<link href="../css/mlColorPicker.css" rel="stylesheet" type="text/css" />
<link href="../css/nyroModal.css" rel="stylesheet" type="text/css" />
<link href="../css/ui/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" media="screen" />

<link href="../css/estilo-cms3.css" rel="stylesheet" type="text/css" media="all" />
<link href="../css/menus.css" rel="stylesheet" type="text/css" />

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

        <li><span>00</span><a href="#" class="ativo">Administração</a>
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
      <h1><span class="h1 novo">Novo Conteúdo</span> <span class="favoritos-add"><a href="#" title="adicionar aos favoritos" class="favoritos-add">&nbsp;</a></span></h1>
      
      <!--barra de botões abaixo do título-->
      <div id="barra-botoes">
      <div class="floater">
      <ul>
        <li class="esq"><!--<a href="#" class="bot-cinza"><span><b class="ico-marca">Marcar tudo</b></span></a>
      <a href="#" class="bot-cinza"><span><b class="ico-invert">Inverter</b></span></a>
       <a href="#" class="bot-verm"><span><b class="ico-menos">Limpar</b></span></a>-->&nbsp;</li>
      
        <li class="dir">
         <a href="#" class="bot-cinza"><span><b class="ico-voltar">Voltar</b></span></a>
        <a href="#" class="bot-verm"><span><b class="ico-menos">Limpar</b></span></a>
     <a href="#" class="bot-verde"><span><b class="ico-mais">Novo</b></span></a>
      	<a href="#" class="bot-verde"><span><b class="ico-ok">Salvar</b></span></a> 
        <a href="#" class="bot-verde"><span><b class="ico-ok">Salvar e continuar</b></span></a></li>    
   	  </ul>
      
      
      
      </div>      
      </div>
      <!--barra de botões abaixo do título fim-->
      <form action="" method="post" enctype="multipart/form-data" name="formulario" id="formulario">
      
      <input name="conteudo_id" type="hidden" value="13" />
      
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Men principal</a><img src="../img/ico-salvar.gif" width="8" height="8" alt="atualizado!" title="Não esqueça de salvar!" class="ico-atualizar" /></li>
		<li><a href="#tabs-2">Gerenciar itens</a><img src="../img/ico-salvar.gif" width="8" height="8" alt="atualizado!" title="Não esqueça de salvar!" class="ico-atualizar" /></li>
	</ul>
	<div id="tabs-1"><!--ABA == 1-->
    	<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>
       
    	 <label for="status">Status</label><div class="form-opcoes"><input name="status" type="radio" value="1" checked /> ativo &nbsp;&nbsp;&nbsp;<input name="status" type="radio" value="0"  /> inativo </div>
         
         <br />
          <label for="opc">Opçõs</label><input name="opc" id="opc" type="text" class="input-curto" value="" />
          <div class="enquete-votos"><div class="bar" style="width:100px;">23 %</div>23 votos</div>
        <br />
        
        <label for="cor">Cor do texto</label><span id="colorPicker1" class="color-picker" style="background-color:white"></span><input name="cor1" id="cor1" type="text" class="input-cor" value="" />

		<br />
        
        <label for="cor">Cor do fundo</label><span id="colorPicker2" class="color-picker" style="background-color:white"></span><input name="cor2" id="cor2" type="text" class="input-cor" value="" />

		<br />
		
        <label for="dt1">Data</label><input name="dt1" id="dt1" type="text" class="input-curto" />
        <br />
        <label for="nome"><b class="obr">[!]</b> Nome</label><input name="nome" id="nome" type="text" class="input-longo" />
               
        <br />
        <label for="email">E-mail</label><input name="email" id="email" type="text" class="input-longo" />
        <br />
        <label for="apelido">Apelido</label><input name="apelido" id="apelido" type="text" class="input-longo" />
        <img src="../img/ico-i.png" width="21" height="20" alt="informações" class="ico-i" title="Nome como os usuários e o sistema identificarão você. Não confunda com seu login!" />  
        
        <br />
        <label for="resumo">Resumo</label><textarea name="resumo" class="textarea-curto" id="resumo"></textarea>
        <img src="../img/ico-i.png" width="21" height="20" alt="informações" class="ico-i" title="Nome como os usuários e o sistema identificarão você. Não confunda com seu login!" /> 
        
        
        
              
         
        <br />
       
        
		<!--ABA == 1 fim-->
    <div class="tab-nav-system"><a href="#" class="tabs-go-1">próxima aba <span class="courier">&gt;</span></a></div>
    
    </div>
	<div id="tabs-2"><!--ABA == 2-->
	
    	
        <!-- coluna esquerda do gerenciamento de itens de Menu -->
<div class="col-esq">
        
        <div id="add-page" class="box-menu-ajax">
        	
            <h3>Módulos</h3>
            
            <div class="box-menu-tabs">
            	<ul>
                	<li><a href="#tab-modulos">Selecionar</a></li>
                    <li><a href="#tab-paginas" class="tabitem-pagina">Páginas</a></li>
                    <li><a href="#tab-pesquisar">Pesquisar</a></li>
                </ul>
                
                <div id="tab-modulos">                	
                    
                    <select id="modulos" class="input-combo " name="modulos">
                    <option selected="selected" value="0"> -- ninguém -- </option>
                    <option value="1" title="cms_admin">Administração</option>
                    <option value="34">Agendamentos</option>
                    <option value="25">Usuários</option>
                    <option value="29">Newsletter</option>
                    <option value="4">Gerenciador de Arquivos</option>
                    <option value="37">Menus</option>
                    <option value="6" title="cms_conteudo">Páginas</option>
                    <option value="7">Notícias</option>
                    <option value="21">Calendário</option>
                    <option value="18" title="cms_enquete_per">Enquetes</option>
                    <option value="13" title="cms_pastas">Álbuns de Fotos</option>
                    </select>
                    
                </div><!-- .tab-recentes -->
                
                <div id="tab-paginas">
                
                  <img src="../img/loader.gif" width="43" height="43" alt="loading" class="loading" style="float:left;" />
                  
                  <select id="conteudo" class="input-combo " name="conteudo" multiple="multiple">
                    
                   </select>
                  
                  
                  </div><!-- .tab-paginas -->
                
                <div id="tab-pesquisar">pesquisar</div>
                
                
            </div><!-- .box-menu-tabs -->
            
            <a href="#" class="bot-add-page">Adicionar ao menu</a>
            
        </div><!-- .box-menu-ajax -->
        
</div><!-- .col-esq -->
        
        <!-- coluna direita do gerenciamento de itens de Menu -->
        <div class="col-dir">
        
        <div class="field-title">Arraste para reordenar o menu</div>
        
        
        <ul class="menu-sortable">
        	
            <li id="92" class="menu-depth-0">
            <div class="title drag">Página 2</div>
            <a href="#" class="options" title="detalhes">+</a>
            
            	<!-- dados do item de menu -->
                <div class="menu-dados menu-item-2">
                	
                    <label class="lb-menu">URL:</label>
                    <input name="url" type="text" value="http://" class="input-menu" />
                    
                    <div class="item-info-metade">                    
                    <label class="lb-menu">Rótulo:</label>
                    <input name="rotulo" type="text" value="Nome da página" class="input-menu" />
                    </div>
                    
                    <div class="item-info-metade">
                    <label class="lb-menu">Title:</label>
                    <input name="title" type="text" value="Nome da página" class="input-menu" />
                    </div>
                    
                    <div class="item-info-metade">                    
                    <label class="lb-menu">CSS:</label>
                    <input name="css" type="text" value="" class="input-menu" />
                    </div>
                    
                    <div class="item-info-metade">
                    <label class="lb-menu">Target:</label>
                    
                    <select name="target" class="input-menu">
                    	<option value="">nenhum</option>
                      <option value="_blank">_blank</option>
                      <option value="_parent">_parent</option>
                      <option value="_self">_self</option>
                      <option value="_top">_top</option>
                    </select>
                    </div>
                    
                    
                    
                    <div class="md-options"><a href="#" class="opt-remover">&raquo; remover do menu</a> | <a href="#" class="opt-atualizar">&raquo; atualizar dados</a></div>
                    
                </div><!-- .menu-dados -->
            
            </li>
            
            <li id="94" class="menu-depth-1">
            <div class="title drag">Página 4</div>
            <a href="#" class="options" title="detalhes">+</a>
            
            	<!-- dados do item de menu -->
                <div class="menu-dados menu-item-4">
                	
                    <a href="#">iojoçijoij</a>
                    
                    <div class="md-options"><a href="#" class="opt-remover">remover</a> | <a href="#">outra coisa</a> <img src="../img/Jcrop.gif" width="8" height="8" alt="loading" class="loading" /></div>
                    
                </div><!-- .menu-dados -->
                
            </li>
            
            <li id="95" class="menu-depth-0">
            <div class="title drag">Página 5</div>
            <a href="#" class="options" title="detalhes">+</a>
            	
                <!-- dados do item de menu -->
                <div class="menu-dados menu-item-5">
                	
                    <p>ajsioajsoij</p>
                    
                </div><!-- .menu-dados -->
                            
            </li>
            
            <li id="96" class="menu-depth-0">
            <div class="title drag">Página 6</div>
            <a href="#" class="options" title="detalhes">+</a>
            	
                <!-- dados do item de menu -->
                <div class="menu-dados menu-item-6">
                	
                    <p>ajsioajsoij</p>
                    
                </div><!-- .menu-dados -->
                
            </li>
            
        </ul>
        
        
        </div><!-- .col-dir -->
        
        
    	<div class="clear debug"></div>
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