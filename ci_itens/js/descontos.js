$(document).ready(function(e) {
    
	// plugin em: listas.js
	if(jQuery.fn.filter_autocomplete){
		$(".grupoclientes-populate").filter_autocomplete({
			source: CMS.site_url+"cms/usuarios/get_usergroups_by_json"
		});
	}
	
	// formato de moeda
	$(document).on('keyup', '.type-valor', function(){
		var strApelido = money_format($(this).val(), ',');	
		$(this).val(strApelido);
		return false;										   
	});
	
	// formato percentual
	$(document).on('keyup', '.type-percentual', function(){
		var v = number_percentual($(this).val());	
		$(this).val(v);
		return false;										   
	});
	
	// formato de código			
	$(document).on('keyup', '.type-codigo', function(){
		var v = replaceSpecialChars($(this).val());	
		$(this).val(v.toUpperCase());
		return false;										   
	});
	
	
	/***********************************************
	*	manipula os labels da quantidade/valor
	*	A opção 'acima-de' trata de moeda, 
	*	o restante de quantidade
	*/
	var DES = {
		
		init: function(){
			var self = this;
			self.set_labels();
			// ao mudar a regra
			$('#regra').on('change', function(){
				self.set_labels();	
			});	
			// formato de moeda
			/*$(document).on('keyup', '.type-valor', function(){
				var strApelido = money_format($(this).val(), ',');	
				$(this).val(strApelido);
				return false;										   
			});*/
		},
		
		get_regra: function(){
			var val = $('#regra').val();
			return val;
		},
		
		set_labels: function(){
			var self = this,
			regra = this.get_regra();
			
			if(regra == 'acima-de'){
				$('.col-verificador').children('label').text('Valor carrinho');
				$('.col-verificador').find('span.add-on').text('R$');
				$('input#verificador').removeClass('type-codigo').addClass('type-valor');
				$('.col-valor').find('.add-on').text('R$');
				$('input#valor').removeClass('type-percentual').addClass('type-valor');
				//console.log($('.col-verificador'));
			}
			else if(regra == 'R$'){
				$('.col-verificador').children('label').text('Cupom');
				$('.col-verificador').find('.add-on').text('Cod.').hide();
				$('input#verificador').removeClass('type-valor').addClass('type-codigo');
				$('.col-valor').find('.add-on').text('R$');
				$('input#valor').removeClass('type-percentual').addClass('type-valor');
			}
			else if(regra == '%'){
				$('.col-verificador').children('label').text('Cupom');
				$('.col-verificador').find('.add-on').text('Cod.').hide();
				$('input#verificador').removeClass('type-valor').addClass('type-codigo');
				$('.col-valor').find('.add-on').text('%');
				$('input#valor').removeClass('type-valor').addClass('type-percentual');
			}
			else {				
				$('.col-verificador').children('label').text('Quantidade');
				$('.col-verificador').find('.add-on').text('Qt');
				$('input#verificador').removeClass('type-valor');
				$('.col-valor').find('.add-on').text('R$');
				$('input#valor').removeClass('type-percentual').addClass('type-valor');
			}
			
			
		}
	
	};
	
	DES.init();
	
});