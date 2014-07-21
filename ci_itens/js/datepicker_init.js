$(document).ready(function(){
	
	$.datepicker.setDefaults({
       dateFormat: 'dd/mm/yy',
       changeMonth: true,
       changeYear: true        
    });

	$('input[name^=dt]', '#barra-filtros').datepicker();
	
	$('input#dt1').datepicker();
	$('input#dt2').datepicker();
		
	// para espaço entre datas
	$( "input#from" ).datepicker({ 		
		onSelect: function( selectedDate ) {                
			$( "input#to" ).datepicker( "option", "minDate", new Date(date_pt_to_us(selectedDate)) );
		}
    });
		
	$( "input#to" ).datepicker({
	    onSelect: function( selectedDate ) {
			$( "input#from" ).datepicker( "option", "maxDate", new Date(date_pt_to_us(selectedDate)) );
	    }
	});
						   
});