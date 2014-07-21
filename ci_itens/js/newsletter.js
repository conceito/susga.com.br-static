$(document).ready(function(){
	// seta se usará editor ou não ---------------------------------------------------------------
	$('.com-editor').click(function(){
		$('input[name=tags]').val('1');
		$('.textarea-longo').addClass('tynimce');
		tinyMCE.execCommand('mceAddControl', false, 'txt');
	});
	$('.sem-editor').click(function(){
		$('input[name=tags]').val('0');	
		$('.textarea-longo').removeClass('tynimce');
		tinyMCE.execCommand('mceRemoveControl', false, 'txt');
	});
	$('input[name^=dt]').datepicker({dateFormat: 'dd/mm/yy'});
	
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
			url: V['site_url']+"cms/cmsutils/apagaItem/"+id+"/cms_news_age",
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
					alerta('Houve um erro ao apagar! newsletter.js 63', 'vermelho');
				}
				
			}
		});
		return false;
	});
	
	// controle das estatísticas ==========================================
	$('a.lnk-stt-showAll').hide();
	// seleciona tabelas com mesmo ID
	$('a.lnk-stt', 'table.stats').click(function(){
		var idTr = $(this).parent().parent().attr('class');
		// desativa todas as TRs que não sejam iguais
		escondeTRs(idTr);
		$('a.lnk-stt-showAll').show();
		return false;
	});
	$('a.lnk-stt-showAll').click(function(){
			mostraTRs();
			return false;
	});
	function escondeTRs(ID){
		$('table.stats tr').each(function(){
			var idThis = $(this).attr('class');
			if(idThis != ID){
				$(this).hide();
			}
		});
	}
	function mostraTRs(){
		$('table.stats tr').each(function(){
			$(this).show();	
			$('a.lnk-stt-showAll').hide();
		});	
	}
	
});