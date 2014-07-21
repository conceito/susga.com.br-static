$(document).ready(function(){
	//  sensibilidade das linhas ao passar do mouse -------------------------------------------------
	$('tr', '#tbl_tab').mouseover(function(){
		var div = $(this).children().children('div.opcoes').css({'visibility':'visible'});
		$(this).addClass('highlight');
		return false;		
	});
	$('tr', '#tbl_tab').mouseout(function(){
		var div = $(this).children().children('div.opcoes').css({'visibility':'hidden'});
		$(this).removeClass('highlight');
		return false;							   
	});
	
	$('.comment-div').hide();
	// abre e fecha coments --------------------
	$('a.edit', '#tbl_tab').click(function(){
		var tr = $(this).parent().parent();
		var id = tr.attr('id');
		// controle de visibilidade
		$('.cd-'+id).slideToggle();
		//alert(id);
		return false;
	});
	$('a.edit-opc', '#tbl_tab').click(function(){
		var tr = $(this).parent().parent().parent().parent();
		var id = tr.attr('id');
		// controle de visibilidade
		$('.cd-'+id).slideToggle();
		//alert(id);
		return false;
	});
	// ao clicar no link para apagar mostra confirmação ------------------------------------------------------------
	$('.apagar-item').click(function(){
		$(this).parent().hide();
		$(this).parent().next('.confirma').css({display:'inline'}).show();		
		return false;
	});
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
		
		
		// chamada AJAX para apagar registro. Preciso da TABELA == cms_conteudo, ID
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/apagaItem/"+id+"/cms_comentarios",
			//data: "id_uf=" + id_uf,
			dataType: ($.browser.msie) ? "text" : "html",
			
			beforeSend: function() {
				// msg de carregando				
				alerta('Aguarde...');
			},
			success: function(message)
			{						
				if (message == 1){	
					alerta('OK, item apagado!', 'verde');
					// fecha nó
					tr.animate({opacity:0}, 'slow', 'linear', function(){tr.empty().css({height: '40px'});});
				} else {
					alerta('Houve um erro ao apagar! comentarios.js 68', 'vermelho');
				}
				
			}
		});
		return false;
	});
	// faz a mudança de Ativo para Inativo --------------------------------------
	// 1º se está ativo desativa
	$('.ativo-item', '#tbl_tab').live('click', function(){
		var tr = $(this).parent().parent().parent();// nó pai
		var id = tr.attr('id');	
		var t = $(this);
		// faz alteração via AJAX
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/alteraStatus/"+id+"/cms_comentarios/1",
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
					alerta('Houve um erro de conexão: comentarios.js 99', 'vermelho');
				}				
			}
		});
		
		return false;
	});
	// 2º se está inativo Ativa
	$('.inativo-item', '#tbl_tab').live('click', function(){
		var tr = $(this).parent().parent().parent();// nó pai
		var id = tr.attr('id');	
		var t = $(this);
		// faz alteração via AJAX
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/alteraStatus/"+id+"/cms_comentarios/0",
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
					alerta('Houve um erro de conexão: comentarios.js 129', 'vermelho');
				}				
			}
		});
		return false;
	});
	// 3º se está inativo Ativa
	$('.editando-item', '#tbl_tab').live('click', function(){
		var tr = $(this).parent().parent().parent();// nó pai
		var id = tr.attr('id');	
		var t = $(this);
		
		// faz alteração via AJAX
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/alteraStatus/"+id+"/cms_comentarios/2",
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
					alerta('Houve um erro de conexão: comentarios.js 158', 'vermelho');
				}				
			}
		});
		return false;
	});
	// sensibilidade dos comentários --------------
	$('textarea[name^=comment-]').focus(function(){
		//var dado = $(this).attr('name').split('-');
		//var id = dado[1];
		//alert(id);											 
	});
	$('textarea[name^=comment-]').blur(function(){
		var dado = $(this).attr('name').split('-');
		var id = dado[1];
		var texto = $(this).val();
		//alert(texto);	
		
		// via AJAX salva submissão
			$.ajax({
				type: "POST",
				url: V['base_url']+"ci_itens/atualizaDescricao.php",
				data: "id="+id+"&comment="+texto+"&op=comentario",
				dataType: ($.browser.msie) ? "text" : "html",
				
				beforeSend: function() {
					// msg de carregando				
					alerta('Aguarde...');
				},
				success: function(message)
				{						
					if (message == 1){							
						// recebe o retorno e processa	
						$('#alertas').hide();
						//$('.debug').text(message);
						
					} else {
						alerta('Houve um erro de conexão: comentarios.js 196', 'vermelho');
					}	
				}
			});
		
		// depois de salvar fecha
		$(this).parent().slideUp('slow');
		
	});
});