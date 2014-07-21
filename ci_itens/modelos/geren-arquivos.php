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
V['uri'] = 'uri/novo';
V['superAlerta'] = false;
</script>

<script type="text/javascript" src="../../js/jquery-1.3.2.min.js"></script><!-- 1-->
<script type="text/javascript" src="../../js/jquery.delay.js"></script><!-- 2-->
<script type="text/javascript" src="../js/funcoes-cms.js"></script><!-- 3-->
<script type="text/javascript" src="../../js/jquery.qtips.js"></script><!-- 4-->
<script type="text/javascript" src="../../js/ui.core.js"></script>
<script type="text/javascript" src="../../js/ui.datepicker.js"></script>
<script type="text/javascript" src="../js/datepicker_init.js"></script>
<!--dialog-->
<script type="text/javascript" src="../../js/jquery.nyroModal.js"></script>
<script type="text/javascript" src="../js/nyroModal_init.js"></script>

<!--dialog-->
<script type="text/javascript" src="../../js/ui.tabs.js"></script>
<script type="text/javascript" src="../../js/ui.sortable.js"></script>

<script type="text/javascript" src="../js/padrao-cms.js"></script>
<script type="text/javascript" src="../js/tabs-forms.js"></script>

<script type="text/javascript" src="../js/galeria_init.js"></script>
<script type="text/javascript" src="../../js/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript" src="../js/tinymce.js"></script>


<link href="../css/nyroModal.css" rel="stylesheet" type="text/css" />
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
      <input name="conteudo_id" type="hidden" value="<?=rand()?>" />
      
<div id="tabs">
	<ul>
		
		<li><a href="#tabs-1">Arquivos</a><img src="../img/ico-salvar.gif" width="8" height="8" alt="atualizado!" title="Não esqueça de salvar!" class="ico-atualizar" /></li>
        <li><a href="#tabs-2">Informações</a><img src="../img/ico-salvar.gif" width="8" height="8" alt="atualizado!" title="Não esqueça de salvar!" class="ico-atualizar" /></li>
	</ul>
	
	<div id="tabs-1"><!--ABA == 1-->
	
    	<p><img src="../img/ico-addarq.gif" alt="+ foto" width="19" height="18" align="left" style="margin-top:5px; margin-right:5px;" />
        <a href="modal-template.php" target="_blank" class="nyroModal"> Adicionar imagens na galeria</a> (Após enviar suas imagens        <img src="../img/ico-atualizar.png" width="17" height="17" alt="atualizar" style="margin-top:5px; margin-right:2px;" /><a href="conteudo-novo.php#tabs-2"> Atualize esta tela</a>)</p>
        
        
        <!--se não imagens-->
        <div class="como-editar-img" align="center" style="clear:both; "><img src="../img/como_editar_arq.jpg" width="676" height="214" alt="como editar imagens" /></div>
        <!--se não imagens-->
        
        <ul id="galeria">
        
        <!--unidade de manipular arquivo-->
        <li class="unidade-arq" id="id1">
        <div class="drag">1</div>
        <div class="info">
        	<div class="ext">DOCX | 999.999 Kb</div>
            <div class="del"><a href="#" title="apagar arquivo" class="remover-arq"></a><a href="#" title="ver arquivo" class="ver-arq"></a></div>
        </div>
        <div class="desc"><textarea name="descricao" cols="" rows="" class="descricao">descreva...</textarea></div>
        
        <div class="confirma">Confirma?<br /><a href="#" class="nao-confirma">não</a></div>
        </li>
        <!--unidade de manipular arquivo fim-->
        
         <!--unidade de manipular arquivo-->
        <li class="unidade-arq" id="id2">
        <div class="drag">1</div>
        <div class="info">
        	<div class="ext">DOCX | 999.999 Kb</div>
            <div class="del"><a href="#" title="apagar arquivo" class="remover-arq"></a></div>
        </div>
        <div class="desc"><textarea name="descricao" cols="" rows="" class="descricao">descreva...</textarea></div>
        
        <div class="confirma">Confirma?<br /><a href="#" class="nao-confirma">não</a></div>
        </li>
        <!--unidade de manipular arquivo fim-->
        
         <!--unidade de manipular arquivo-->
        <li class="unidade-arq" id="id3">
        <div class="drag">1</div>
        <div class="info">
        	<div class="ext">DOCX | 999.999 Kb</div>
            <div class="del"><a href="#" title="apagar arquivo" class="remover-arq"></a></div>
        </div>
        <div class="desc"><textarea name="descricao" cols="" rows="" class="descricao">descreva...</textarea></div>
        
        <div class="confirma">Confirma?<br /><a href="#" class="nao-confirma">não</a></div>
        </li>
        <!--unidade de manipular arquivo fim-->
        
        
        
        </ul>
    	<div class="clear debug"></div>
    <!--ABA == 1 fim-->
       <div class="tab-nav-system">
       <a href="#" class="tabs-go-0"><span class="courier">&lt;</span> aba anterior</a> | 
        <a href="#" class="tabs-go-2">próxima aba <span class="courier">&gt;</span></a>
        </div>
	</div>
    
    <div id="tabs-2"><!--ABA == 2-->
    	<div class="obrigatorios"><b>[!]</b> campos obrigatórios</div>
       
    	 <label for="status">Status</label><div class="form-opcoes"><input name="status" type="radio" value="1" checked /> ativo &nbsp;&nbsp;&nbsp;<input name="status" type="radio" value="0"  /> inativo </div>
         
         <br />
          <label for="dt1">Opção</label><input name="dt1" id="dt1" type="text" class="input-curto" value="Opção" />
          <div class="enquete-votos"><div class="bar" style="width:100px;">23 %</div>23 votos</div>
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
        <label for="txt"><b class="obr">[!]</b> Conteúdo</label>
        <a href="javascript:;" onmousedown="$('#txt').tinymce().show();"><span class="courier">&gt;</span> com editor </a> &nbsp;&nbsp;&nbsp; 
        <a href="javascript:;" onmousedown="$('#txt').tinymce().hide();"><span class="courier">&gt;</span> sem editor </a> &nbsp;&nbsp;&nbsp; 
        <a href="#" class="bot-verde"><span><b class="ico-mais">Nova imagem</b></span></a>
        <br /><br />
        <textarea name="txt" class="textarea-longo" id="txt"></textarea>
        
              
         
        <br />
       
        
		<!--ABA == 2 fim-->
    <div class="tab-nav-system"><a href="#" class="tabs-go-1">próxima aba <span class="courier">&gt;</span></a></div>
    
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