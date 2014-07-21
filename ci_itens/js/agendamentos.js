$(document).ready(function(){
						   
	var dtEx = Array();
	//var closedDates = Array();
   // var closedDays = Array();
	
	/*$('input[name^=dt]').datepicker({
									dateFormat: 'dd/mm/yy',
									minDate: 0, 
									maxDate: "+3M"
									});*/
	
	
	$('#datapicker').datepicker({
        beforeShowDay: nonWorkingDates,
		dateFormat: 'dd/mm/yy',
        numberOfMonths: 1,
        minDate: 0, 
		maxDate: "+3M",
        firstDay: 1,
		onSelect: adicionaDtExcecao

    });
	// remove as datas recem adicionadas pelo datepicker ---------------
	$("a.del", "#datas-excecao").live("click", function(){
		
		var div = $(this).parent();
		div.remove();
		return false;
		
	});
	// remove as datas já salvas no BD ---------------
	$("a.del", "#datas-bd").live("click", function(){
		
		var div = $(this).parent();
		div.css('backgroundColor', '#ccc');
		var dt = div.children('.dt').text();
		
		div.children('.imput').val(dt);
		$(this).remove();
		return false;
		
	});
	
	function adicionaDtExcecao(dateText, inst){
		
		// verifica se existe
		var jaExiste = false;
		for(var x = 0; x < dtEx.length; x++){
			if(dateText == dtEx[x]){
				jaExiste = true;
			}
		}
		
		if(jaExiste == false){
			// adiciona no array
			dtEx.push(dateText);
			
			var html = '<div class="dtex">    	<span class="dt">'+dateText+'</span>        <a href="#" title="remover" class="del">[x]</a>        <input name="dt1[]" type="hidden" value="'+dateText+'" />    </div>';
			$("#datas-excecao").append(html);
		
		}
		
	}

    
	
	// acrescenta campos de data de exceção -----------------------
	$(".mais-data").live("click", function(){
		
		var html = '<br /><label> Data de exceção</label><input name="dt[]" type="text" class="input-curto dp" value="" /><a href="#" class="mais-data">+ Mais</a>';
		
		$(this).after(html);
		$(this).hide();
		
		return false;
		
	});
	
});