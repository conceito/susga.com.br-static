$(document).ready(function(){
	// verifica se apenas UMA mensagem está selecionada ------------------------------------------------------
	$('.bot-agendarnews').click(function(){
		var num = 0;
		var vars = '';
		// conta quantos chek estão marcados
		$('input.cb', '#tbl').each(function(){
			if($(this).attr('checked')){
				num++;
				vars += $(this).val();
			}
		});
		// responde caso não tenha nenhum
		if(num < 1) {alerta('Você deve marcar apenas UMA mensagem!', 'verm');return false;
		}
		// responde caso tenha mais que UM
		if(num > 1) {alerta('Você deve marcar apenas UMA mensagem!', 'verm');return false;
		}
		// faz redirecionamento
		location.href=V['site_url']+'cms/news/agendar/id:'+vars;
		
		return false;
	});						   
});