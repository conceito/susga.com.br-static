$(document).ready(function() {
	
	// plugin em: listas.js
	if(jQuery.fn.filter_autocomplete){
		$(".clientes-populate").filter_autocomplete({
			source: CMS.site_url+"cms/usuarios/get_users_by_json"
		});
	}
	
	
	
	/****************************************
	*	Gera código da fatura
	*/
	$('.gerar-fatura').on('click', function(){
		
		// id da fatura
		var self = $(this);
		var id = $(this).data('id');
		
		$.post(CMS.site_url+'/cms/loja/gerar_fatura/'+id, '', function(data){
			console.log(data);
			self.after(data);
			self.fadeOut(1000, function(){
				$(this).remove();	
			});
			
		});	
		
		
	});
	
	
	/***************************************
	*	Atualiza histórico
	*/	
	$('.set-new-historico').on('click', function(e){
		
		e.preventDefault();
		
		var situacao = $('#situacao').val();
		var informar = $('#informar').is(':checked');
		var comentarios = $('#comentarios').val();
		var extrato_id = $('#extrato_id').val();
		
		var data = 'situacao='+situacao+'&informar='+informar+'&comentarios='+comentarios+'&extrato_id='+extrato_id;
		
		var success = '<div class="alert alert-success" id="set_new">'+ 
      '<strong>Venda atualizada com sucesso!</strong>'+
    '</div>';
	
		var wait = '<div class="alert" id="wait">'+ 
      '<strong>Aguarde o processamento...</strong>'+
    '</div>';
	
		$('.new-historico').append(wait);
		
		$.post(CMS.site_url+'/cms/loja/extrato_update/', data, function(data){
			
			//console.log(data);
			$('#wait').remove();
			$('#set_new').remove();
			$('.new-historico').append(data);
			$('.new-historico').append(success);
			
			$('#comentarios').val('');
			
			setTimeout(function(){
				
				$('#set_new').slideUp(3000);
					
			}, 2000);
			
			
		});	
		
		/*console.log(situacao);
		console.log(informar);
		console.log(comentarios);*/
			
	});
});


