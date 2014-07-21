$(document).ready(function(){
	// limita caracteres ---------------------------------------------------------------

	$('input[name=nasc]').datepicker({dateFormat: 'dd/mm/yy',
									 changeMonth: true,
			changeYear: true
});
	
	// na cria��o de pastas esconde e mostra op��es de imagem -----------------------
	$('input[name=tipo]').click(function(){
		var val = parseInt($(this).val());		
		if(val == 0 || val == 1){
			$('.padrao-img').slideDown('slow');
		} else {
			$('.padrao-img').slideUp('slow');
		}
		
	});
	
	// formata campos ------------------------------------------------
	$('#tel1').mask("(99)9999-9999");
	$('#tel2').mask("(99)9999-9999");
	$('#cep').mask("99999-999");
	
	// chama cidades via ajax -----------------------------------------
	
	$('#uf').change(function(){
		var uf = $(this).val();
		if(uf == '')return false;
		// envia ajax	
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/cmsutils/comboCidade/"+uf,
			//data: "id_uf=" + id_uf,
			dataType: ($.browser.msie) ? "text" : "html",
			
			beforeSend: function() {
				// msg de carregando				
				alerta('Aguarde...');
				$('#combo_cidade').html('<img src="'+V['base_url']+'ci_itens/img/loading.gif" alt="aguarde..." />');
			},
			success: function(message)
			{						
				if (message.length > 10){	
					$('#alertas').hide();
					// fecha n�
					$('#combo_cidade').html(message);
				} else {
					alerta('Houve um erro de conexão! usuarios.js 48', 'vermelho');
				}
				
			}
		});
									 
	});
});