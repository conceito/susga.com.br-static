$(document).ready(function(){

	
	
	// percorre as barrinhas para animar -----------------------
	$('.enquete-votos').each(function(){
		var divbar = $(this).children('.bar');
		var txt = divbar.text();
		var txt2 = txt.split(' ');
		var perc = txt2[0];
		var perc2 = parseInt(perc) * 2;
		// zera as barras
		divbar.css('width', '0px');
		divbar.animate({width:perc2+'px'}, 3000, 'swing');
		
										  
	});
});