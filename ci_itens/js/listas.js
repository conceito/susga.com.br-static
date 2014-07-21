$(document).ready(function(){
	// variaveis
	
	// ação ao pressionar botões de controle do formulario -------------------------------------
    $('.btt-filter').on('click', function() {
        var url = $(this).attr('href');
        // preenche a action do formulário
        $('#formulario').attr('action', url);
        $('#formulario').submit();
        //alert($('#formulario').attr('action'));
        return false;
    });
	// ao teclar ENTER o filtro é submetido --------------------------
	$(document).on('keydown', 'tr.local-search :input', function(e) {
		if (e.which == 13) {
			e.preventDefault();
			$('.btt-filter').click();
		}
	});
	
	// retorna grupos do módulo para filtros
	// plugin em: listas.js
	if(jQuery.fn.filter_autocomplete && CMS.modulo){
		var modulo_id = parseInt(CMS.modulo.id, 10);
		$(".grupo-populate").filter_autocomplete({			
			source: CMS.site_url+"cms/cmsutils/get_groups_by_json/"+modulo_id+'/'
		});
	}
	if(jQuery.fn.filter_autocomplete){
		$(".grupouser-populate").filter_autocomplete({
			source: CMS.site_url+"cms/usuarios/get_usergroups_by_json"
		});
	}
	
	//  sensibilidade das linhas ao passar do mouse -------------------------------------------------
	$('tr:not(.local-search)', '#tbl').mouseover(function(){
		var div = $(this).children().children('div.opcoes').css({'visibility':'visible'});
		$(this).addClass('highlight');
		return false;		
	});
	$('tr', '#tbl').mouseout(function(){
		var div = $(this).children().children('div.opcoes').css({'visibility':'hidden'});
		$(this).removeClass('highlight');
		return false;							   
	});
	
	// controle do 'ler resumo' ------------------------------------------------------
	$('.resumo-item').click(function(e){
        e.preventDefault();
		var div = $(this).parent().parent().next('.resumo-ler');
		div.slideToggle('slow');
	});
	$('.resumo-ler').click(function(e){
        e.preventDefault();
		var div = $(this);
		div.slideToggle('slow');
	});
	
	// ao clicar no link para apagar mostra confirmação ------------------------------------------------------------
	$('.apagar-item').click(function(){
		$(this).parent().hide();
		$(this).parent().next('.confirma').css({display:'inline'}).show();		
		return false;
	});
	
	// atualização da quantidade de amostragens -------------------------------------
	$('#porpag').change(function(){
		var v = $(this).val();
		var uri = V['uri'];
		var parte = uri.split('/');
		var u = '';// init
		u = parte[0]+'/'+ parte[1]+'/'+ parte[2]+'/';
		for(x=3; x<parte.length; x++){
			// coloca se existir a var 'co'
			if(parte[x].substr(0, 2) == 'co'){
				u += parte[x]+'/';
			}			
		}
		//alert(u);return false;
		location.href=V['site_url']+u+'pp:'+v;
		
	});
	// caixa de pesquisa interna ----------------------------------------------------
	$('.input-busca').focus(function(){
		var v = $(this).val();
		if(v == 'busca')$(this).val(''); });
	$('.input-busca').blur(function(){
		var v = jQuery.trim($(this).val());
		if(v == '')$(this).val('busca'); });
	// controles da div de filtros --------------------------------------------------
	$('.bot-maisfiltros').click(function(){
		$('#barra-filtros').toggle(); return false;});
	$('#barra-filtros .fechar-filtros').click(function(){
		$('#barra-filtros').toggle();return false;});	
	// controles da div de exportações --------------------------------------------------
	$('.bot-exportar').click(function(){
		$('#barra-exportacao').toggle(); return false;});
	$('#barra-exportacao .fechar-filtros').click(function(){
		$('#barra-exportacao').toggle();return false;});	
	
	// 1º se cancelar == não, volta ao normal -------------------------------------------------------------
	$('.nao-item').click(function(){
		$(this).parent().hide();
		$(this).parent().prev('.apagar').show();
		return false;
	});
	// 2º se confirmar == apaga este item e remove o nó -----------------------------------------------------
	$('.sim-item').click(function(){
		var tr = $(this).parent().parent().parent().parent();// nó pai
		var id = tr.attr('id');
		//alert(id+"/"+V['tb']);
		//return false;
		
		// chamada AJAX para apagar registro. Preciso da TABELA == cms_conteudo, ID
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/apagaItem/"+id+"/"+V['tb'],
			//data: "id_uf=" + id_uf,
			dataType: ($.browser.msie) ? "text" : "html",
			
			beforeSend: function() {
				// msg de carregando				
				alerta('Aguarde...');
			},
			success: function(message)
			{						
				//alert(message);
				if (message == 1){	
					alerta('OK, item apagado!', 'verde');
					// fecha nó
					tr.animate({opacity:0, height: 0}, 'slow', 'linear', function(){
						tr.empty();						
						
					});
				} else {
					alerta('Houve um erro ao apagar! listas.js 93', 'vermelho');
				}
				
			}
		});
		return false;
	});
	
	// botão para marcar todos os check boxes --------------------------------------------
	$('.check-all').click(function(){
		$('input.cb', '#tbl').each(function(){$(this).attr('checked','checked');});return false;});
	// botão para inverter a seleção dos combos -----------------------------------------------
	$('.check-invert').click(function(){
		$('input.cb', '#tbl').each(function(){
			if($(this).attr('checked')){
				$(this).attr('checked',false);
			}  else {			    
			    $(this).attr('checked','checked');
			}
		});
		
		return false;						  
	});
	
	// verifica se vai apagar em lote ------------------------------------------------------
	$('.apagar-lote').click(function(){
		var num = 0;
		var vars = '';
		// conta quantos chek estão marcados
		$('input.cb', '#tbl').each(function(){
			if($(this).attr('checked')){
				num++;
				vars += $(this).val()+'-';
			}
		});
		// responde caso não seja maior que UM
		if(num < 2) {alerta('Você deve marcar pelo menos 2 itens!', 'verm');return false;
		}
		// abre Super Alerta
		if(V['superAlerta'] === false){
			V['superAlerta'] = true;
			$('#super-alerta').show();
			$('#super-alerta').children('.frase').text('Deseja apagar os itens selecionados?');
			$('#super-alerta').children('.var').text('apagarLote|'+vars);
			$('#super-alerta').css({top:'-90px'});
			$('#super-alerta').animate({top:'0px'}, 'slow', 'swing');
		}
		return false;
	});


	// verifica se vai apagar em lote ------------------------------------------------------
	$('.imprimir-lote').click(function(){
		var num = 0,
		vars = '',
		url = '';
		// conta quantos chek estão marcados
		$('input.cb', '#tbl').each(function(){
			if($(this).attr('checked')){
				num++;
				vars += $(this).val()+'-';
			}
		});
		// responde caso não seja maior que UM
		if(num === 0) {alerta('Você deve marcar pelo menos 1 item!', 'verm');return false;
		}

		url = $(this).attr('href') + '/extrato:' + vars.substr(0, vars.length-1);

		var win = window.open(url, '_blank');
  		win.focus();
		return false;
	});


	//////////////////////////////////////////////////////////////////////////////////////
	//////  ações depois da super alerta
	// opção == 1 : confirma -----------------------------------
	// >>>> por enquanto está preparada apenas para apagar em lote
	$('.bot-verde', '#super-alerta').click(function(){
		// recupera as variaveis dentro do super-alerta
		// normalmente: nomeDaFuncao|id-id-id-id-
		var vars = $('#super-alerta').children('.var').text();
		var divide = vars.split('|');
		var nomeFunction = $.trim(divide[0]);
		var ids = $.trim(divide[1]);

		V['superAlerta'] = false;
		
		$('#super-alerta').animate({top:'-80px'}, 'slow', 'swing', function(){$('#super-alerta').hide();});// retira alerta
		//list = replaceAll(list, '.', ':');
		
		// via AJAX salva submissão
		$.ajax({
				type: "POST",
				url: V['site_url']+"cms/cmsutils/"+nomeFunction+"/"+ids+"/"+V['tb'],
				//data: "id_uf=" + id_uf,
				dataType: ($.browser.msie) ? "text" : "html",
				
				beforeSend: function() {
					// msg de carregando				
					alerta('Aguarde...');
				},
				success: function(message)
				{						
					if (message.length > 1){	
						alerta('OK, operação feita com sucesso!', 'verde');
						// recebe o retorno e processa
						var divide = message.split('|');
						var funcao = divide[0];
						var idstr = jQuery.trim(divide[1]);
						var ids = divide[1].split('-');
						// for reordenação
						if(funcao == 'reordenar'){
							recolheSuperAlerta();
						} else if(funcao == 'apagarLote'){
							// se for apagar em Lote retira os nós							
							if(idstr.length == 0)alerta('As pastas devem estar vazias!', 'vermelho');
							else retiraNosTbl(ids);
						}
						
						
					} else {
						alerta('Houve um erro de conexão: listas.js 178', 'vermelho');
					}	
				}
			});
		return false;
	});
	
	// opção == 2 : cancela fechando o Super Alerta -----------------------------------
	$('.bot-verm', '#super-alerta').click(function(){
		V['superAlerta'] = false;
		recolheSuperAlerta();
		alerta('As alterações não serão salvas.', 'vermelho');
		return false;
	});
	
	// faz a mudança de Ativo para Inativo --------------------------------------
	// 1º se está ativo desativa
	$('.ativo-item', '#tbl').live('click', function(){
		var tr = $(this).parent().parent().parent();// nó pai
		var id = tr.attr('id');	
		var t = $(this);
		// faz alteração via AJAX
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/alteraStatus/"+id+"/"+V['tb']+"/1",
			//data: "id_uf=" + id_uf,
			dataType: ($.browser.msie) ? "text" : "html",			
			beforeSend: function() {
				// msg de carregando				
				alerta('Aguarde...');
			},
			success: function(message)
			{						
				if (message == 1){	
					//alerta('OK, item desativado!', 'verde');
					// altera o atatus do link
					t.removeClass('ativo-item').addClass('inativo-item').text('inativo');
					$('#alertas').hide();
				} else {
					alerta('Houve um erro de conexão: listas.js 128', 'vermelho');
				}				
			}
		});
		
		return false;
	});
	// 2º se está inativo Ativa
	$('.inativo-item', '#tbl').live('click', function(){
		var tr = $(this).parent().parent().parent();// nó pai
		var id = tr.attr('id');	
		var t = $(this);
		// faz alteração via AJAX
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/alteraStatus/"+id+"/"+V['tb']+"/0",
			//data: "id_uf=" + id_uf,
			dataType: ($.browser.msie) ? "text" : "html",			
			beforeSend: function() {
				// msg de carregando				
				alerta('Aguarde...');
			},
			success: function(message)
			{						
				if (message == 1){	
					//alerta('OK, item desativado!', 'verde');
					// altera o atatus do link
					t.removeClass('inativo-item').addClass('editando-item').text('editando');
					$('#alertas').hide();
				} else {
					alerta('Houve um erro de conexão: listas.js 279', 'vermelho');
				}				
			}
		});
		return false;
	});
	// 3º se está inativo Ativa
	$('.editando-item', '#tbl').live('click', function(){
		var tr = $(this).parent().parent().parent();// nó pai
		var id = tr.attr('id');	
		var t = $(this);
		// faz alteração via AJAX
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/alteraStatus/"+id+"/"+V['tb']+"/2",
			//data: "id_uf=" + id_uf,
			dataType: ($.browser.msie) ? "text" : "html",			
			beforeSend: function() {
				// msg de carregando				
				alerta('Aguarde...');
			},
			success: function(message)
			{						
				if (message == 1){	
					//alerta('OK, item desativado!', 'verde');
					// altera o atatus do link
					t.removeClass('editando-item').addClass('ativo-item').text('ativo');
					$('#alertas').hide();
				} else {
					alerta('Houve um erro de conexão: listas.js 308', 'vermelho');
				}				
			}
		});
		return false;
	});
	
	// faz a mudança de Destaque --------------------------------------
	// 1º se está "destacado" desativa
	$('.destaque-sim', '#tbl').live('click', function(){
		var tr = $(this).parent().parent();// nó pai
		var id = tr.attr('id');	
		var t = $(this);
		
		// faz alteração via AJAX
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/alteraDestaque/"+id+"/"+V['tb']+"/1",
			//data: "id_uf=" + id_uf,
			dataType: ($.browser.msie) ? "text" : "html",			
			beforeSend: function() {
				// msg de carregando				
				alerta('Aguarde...');
			},
			success: function(message)
			{						
				if (message == 1){	
					//alerta('OK, item desativado!', 'verde');
					// altera o atatus do link
					t.removeClass('destaque-sim').addClass('destaque-nao').text('não');
					$('#alertas').hide();
				} else {
					alerta('Houve um erro de conexão: listas.js 279', 'vermelho');
				}				
			}
		});
		
		return false;
	});
	// 2º se Não está destacado Ativa
	$('.destaque-nao', '#tbl').live('click', function(){
		var tr = $(this).parent().parent();// nó pai
		var id = tr.attr('id');	
		var t = $(this);
		// faz alteração via AJAX
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/alteraDestaque/"+id+"/"+V['tb']+"/0",
			//data: "id_uf=" + id_uf,
			dataType: ($.browser.msie) ? "text" : "html",			
			beforeSend: function() {
				// msg de carregando				
				alerta('Aguarde...');
			},
			success: function(message)
			{						
				if (message == 1){	
					//alerta('OK, item desativado!', 'verde');
					// altera o atatus do link
					t.removeClass('destaque-nao').addClass('destaque-sim').text('sim');
					$('#alertas').hide();
				} else {
					alerta('Houve um erro de conexão: listas.js 353', 'vermelho');
				}				
			}
		});
		return false;
	});
	
	// capacidade de arrastar TR
	$("#tbl").tableDnD({
		dragHandle: "dragme",
		onDrop: function(table, row) {
            var vars = $.tableDnD.serialize();
		
			// abre Super Alerta
			$('#super-alerta').children('.var').text('reordenar|'+vars);
			if(V['superAlerta'] == false){
				V['superAlerta'] = true;
				$('#super-alerta').show();
				$('#super-alerta').children('.frase').text('Deseja confirmar a reordenação?');				
				$('#super-alerta').css({top:'-90px'});
				$('#super-alerta').animate({top:'0px'}, 'slow', 'swing');
			}
        }

	});

});
///////////////////////  funções de apoio //////////////////////////
// retira os nós da tabela
function retiraNosTbl(ids){
	for(x=0; x<ids.length; x++){
		if($.browser.msie)$('tr[id='+ids[x]+']').empty().css({height: '40px'});
		else $('tr[id='+ids[x]+']').animate({opacity:0}, 'slow', 'linear', function(){$('tr[id='+ids[x]+']').empty().css({height: '40px'});});	
	}	
}
// recolhe a super aleerta
function recolheSuperAlerta(){
	$('#super-alerta').animate({top:'-80px'}, 'slow', 'swing', function(){$('#super-alerta').hide();});
}


/*************************************
*	Plugin: filter_autocomplete()
*	Extende o plugin autocomplete para funcionar com filtro de pesquisa tipo LIKE
*/
;(function ( $, window, undefined ) {


  // Create the defaults once
  var pluginName = 'filter_autocomplete',
	  document = window.document,
	  defaults = {
		source: "" // URL retorno AJAX
	  };

  // The actual plugin constructor
  function Plugin( element, options ) {
	this.element = element;

	this.options = $.extend( {}, defaults, options) ;

	this._defaults = defaults;
	this._name = pluginName;

	this.init();
  }

  Plugin.prototype.init = function () {
	// Place initialization logic here
	// You already have access to the DOM element and the options via the instance, 
	// e.g., this.element and this.options
	var rand = Math.round(Math.random() * 99999);
	// clona dados
	var self    = $(this.element);
	var classes = self.attr('class');
	var valor   = self.val();
	
	// cria clone
	self.after('<input id="clone_'+rand+'" class="'+classes+'" value="'+valor+'" type="text" size="100%" />');	
	// torna hidden
	self.get(0).type = 'hidden';
	
	// instancia autocomplete
	var url = this.options.source;
	$('#clone_'+rand).autocomplete({
		source: url,
		minLength: 1,
		select: function( event, ui ) {
			//console.log(ui);			
			self.val(ui.item.id);
			
		}
	});
  };

  // A really lightweight plugin wrapper around the constructor, 
  // preventing against multiple instantiations
  $.fn[pluginName] = function ( options ) {
	return this.each(function () {
	  if (!$.data(this, 'plugin_' + pluginName)) {
		$.data(this, 'plugin_' + pluginName, new Plugin( this, options ));
	  }
	});
  }

}(jQuery, window));