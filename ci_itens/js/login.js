$(document).ready(function(){
	
	// detecta JS habilitado ----------------------------
	$('.nao-tem-js').hide();
	$('.form-login').show();
	
	// detecta browser ----------------------------------
	/*
	if($.browser.msie){
		if($.browser.version.substr(0,1) < 7){
			$('.nao-tem-js').hide();
			$('.form-login').hide();
			$('.ie6').show();
		}		
	}
	*/
	
	// detecta inputs para acender o botão ----------------
	var img1 = $('.bot-ok').attr('src');
	var tam = img1.length;
	var img2 = img1.substr(0, tam-5) + '2.png';
	var qtl = 0;// init
	var qts = 0;
	// 
	$('#adminlogin').keyup(function(){
		var quant = $(this).val().length;
		qtl = quant;
		if(qtl > 4){
			if(qts > 4) $('.bot-ok').attr('src', img2);
		} else {
			$('.bot-ok').attr('src', img1);
		}
	});
	$('#adminsenha').keyup(function(){
		var quant = $(this).val().length;
		qts = quant;
		if(qts > 4){
			if(qtl > 4) $('.bot-ok').attr('src', img2);
		} else {
			$('.bot-ok').attr('src', img1);
		}
	});
	
	// esconde campo de lembrar senha -----------------
	$('#lembraemail').hide();
	$('.bot-lembra').click(function(){
		$('#lembraemail').slideDown();			   
	});
	
	// validação ----------------------------------------
	$("#formlogin").validate({
		rules: {
			adminlogin: {
				required: true,
				minlength: 5
			},
			adminsenha: {
				required: true,
				minlength: 5	
			}
		},
		messages: {
			adminlogin: {
				required: " Preenchimento obrigatório!",
				minlength: " Mínimo de 5 caracteres."
			},
			adminsenha: {
				required: " Preenchimento obrigatório!",
				minlength: " Mínimo de 5 caracteres."
			}
		}
	});	
	// valida lembra login ------------------------------
	$('.lembra-status img').hide();
	$("#lembraemail").validate({
		rules: {
			adminemail: {
				required: true,
				email: true
			}
		},
		messages: {
			adminemail: " Preenchimento obrigatório!"
			
		}
	});
	
	$('input[name=adminemail]').keyup(function(){
		var val = $.trim($(this).val());
		// verifica email se existe on the fly
		$.ajax({
			type: "POST",
			url: V['site_url']+"cms/login/validaEmailAdmin",
			data: "email="+val,
			dataType: ($.browser.msie) ? "text" : "html",
			
			beforeSend: function() {
				// msg de carregando				
				$('.lembra-status img').show();
			},
			success: function(message)
			{						
				if (message.length > 1){							
					// recebe o retorno e processa	
					if(message == 'nao'){
						$('.lembra-status span').text('E-mail não existe!');
						$('.lembra-status img').hide();
						$('.bot-lembra').attr('disabled', 'disabled');
					} else if(message == 'sim'){
						$('.lembra-status span').text('E-mail válido!');
						$('.lembra-status img').hide();
						$('.bot-lembra').attr('disabled', '');
					}
					
					
				} else {
					$('.lembra-status span').text('Preencha corretamente.');
					$('.bot-lembra').attr('disabled', 'disabled');
				}	
			}
		});
		//  --
		
		return false;
	});
	
});