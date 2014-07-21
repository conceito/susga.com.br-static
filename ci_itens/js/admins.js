$(document).ready(function(){
	
	// controle do tipo de admin. Se for segmentado abre combobox com módulos
	$('input[name=tipo]').click(function(){
		var val = $(this).val();
		if(val == 2){
			$('.div-modulos').slideDown();
		} else {
			$('.div-modulos').slideUp();	
		}
		
		//$('.debug').text(val);
		//return false;
	});
	
	// controle da chave geral administracao/config ----------------------------------
	var cg = $('input[name=chave]:checked').val();
	if(cg == 0){$('.chave-2passo').show();}// estado inicial das opções de redirecionamento
	else {$('.chave-2passo').hide();}
	
	$('input[name=chave]').click(function(){
		var cg = $(this).val();	
		if(cg == 0){$('.chave-2passo').slideDown();}// estado inicial das opções de redirecionamento
	else {$('.chave-2passo').slideUp();}
	});
	
	// form envio restore BD ----------------------------------------------
	$('.bot-restaurar').click(function(){
		$('#formulario').attr('action', V['site_url']+'cms/administracao/fazRestoreBd');
		$('#formulario').submit();
		//alert($('#formulario').attr('action'));
		return false;								   
	});
	
	
});