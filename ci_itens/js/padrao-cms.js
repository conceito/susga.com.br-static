$(document).ready(function(){
	// reações do menu principal ------------------------------------
	/*$('#menu ul.botoes li').hover(
			function() { $('ul', this).css('display', 'block'); },
			function() {
				$('ul', this).css('display', 'none');			 
			});*/
	
	// reações do dropdown de mudança de lingua ---------------------------
	/*$('.lang-ico').hover(
			function() { $('ul', this).css('display', 'inline'); },
			function() { $('ul', this).css('display', 'none'); });*/
	// funcionamento da barra de botões com floater -------------------------------------
	
	/************* script que mantém barra sempre visível **************
	$(window).scroll(function(){
		var s = $(window).scrollTop();		
		if(s > 126){
			$('#barra-botoes div').removeClass('floater').addClass('floating');
		} else {
			$('#barra-botoes div').removeClass('floating').addClass('floater');
		}
		if  ($(window).scrollTop() == $(document).height() - $(window).height()){
		   // chegou no fim da página
		}
	});*/
	
	// tooltips das Infos dos forms ------------------------------------------------	 
	 $('.ico-i').qtip({position:'center'});
	 
	 
	
	
	
	
	

	
});

