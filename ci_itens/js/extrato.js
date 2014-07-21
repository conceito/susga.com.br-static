$(document).ready(function(e) {
 
		var ttl = $('.content', "#accordion" ).length;
		
		
		$( "#accordion" ).accordion({		
			collapsible: true,
                        autoHeight: false		
		});
		
		if(ttl > 1){
			$( "#accordion" ).accordion( "activate" , false );
		}
		
		
		//$('.content', '#accordion').hide();
	
	
});