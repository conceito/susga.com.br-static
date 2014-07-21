
$(document).ready(function(){

	$('#datapicker').datepicker({
        beforeShowDay: nonWorkingDates,
		dateFormat: 'dd/mm/yy',
        numberOfMonths: 3,
        minDate: 0, 
        firstDay: 1,
		onSelect: adicionaDtExcecao

    });

	
	var dtEx = Array();
	
	// remove checkbox
	$('label.checkbox', '.new-closed-dates').hide();
	
	function adicionaDtExcecao(dateText, inst){
		
		$('label.checkbox', '.new-closed-dates').show();
		
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
			
			var html = '<div class="tag-item">' +
			'<span class="tag-label">'+dateText+'</span>' +
			'<a href="#" title="remover" class="del">x</a>' + 
			'<input name="nondates_n[]" type="hidden" value="'+dateText+'">' +
			'</div>';			
			
			
			$(".new-closed-dates").append(html);
		
		}
		
	}

    
	
	
});