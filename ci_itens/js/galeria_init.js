$(document).ready(function(){
	// variaveis
	//var superAltert = false;// inicia valor 
	$("#galeria").sortable({
		handle: '.drag',
	   update: function(event, ui) {
			
			var list = '';
			var id_mat = $('input[name=conteudo_id]').val();
			
			$("#galeria li").each(function(){
				var ids = $(this).attr('id');
				list += ids + '-';
			});
			$('#super-alerta').children('.var').text('ordenaFotos|'+id_mat+':'+list);
			if(V['superAlerta'] == false){
				V['superAlerta'] = true;
				$('#super-alerta').show();
				$('#super-alerta').children('.frase').text('Após reordenar as fotos clique em Confirmar!');
				$('#super-alerta').css({top:'-90px'});
				$('#super-alerta').animate({top:'0px'}, 'slow', 'swing');
			}
			//$('.debug').text(list);
				
		}
	});
	$("#galeria").disableSelection();
	
	
	
	// repetição do botão para colocar a variavel neste escopo -----------------------------------
	$('.bot-verm', '#super-alerta').click(function(){
		//V['superAlerta'] = false;		
		//$('.debug').text(V['superAlerta']);
		//return false;
	});
	
	// seleciona a imagem da galeria para remoção ------------------------------------------------------------
	$('.remover-img', '#galeria').click(function(){		
		var div_li = $(this).parent().parent().parent();
		marcaImg(div_li);
		var id_mat = $('input[name=conteudo_id]').val();
		// busca os IDs selected e serializa
		var lista = serializaSelecteds();//init
		
		//alert(id);
		$('#super-alerta').children('.var').text('removeFotos|'+id_mat+':'+lista);
		if(V['superAlerta'] == false){
			V['superAlerta'] = true;
			$('#super-alerta').show();
			$('#super-alerta').children('.frase').text('Após selecionar as fotos clique em Confirmar!');
			$('#super-alerta').css({top:'-90px'});
			$('#super-alerta').animate({top:'0px'}, 'slow', 'swing');
		}
		//$('.debug').text(div_li.attr('id'));
		return false
	});
	// remove imagem da seleção -------------------------------------------------
	$('.nao-confirma', '#galeria').click(function(){
		var div_li = $(this).parent().parent();	
		desmarcaImg(div_li);
		var id_mat = $('input[name=conteudo_id]').val();
		var lista = serializaSelecteds();//init	
		if(lista.length == ''){
			//superalertOff($('#super-alerta'));
			//V['superAlerta'] == false;
		}
		//alert(id);
		$('#super-alerta').children('.var').text('removeFotos|'+id_mat+':'+lista);
		//$('.debug').text(div_li.attr('id'));
		return false;
	});
	
	// seleciona o arquivo da galeria para remoção ------------------------------------------------------------
	$('.apagar-arq', '#galeria').click(function(){		
		var div_li = $(this).parent().parent().parent();
		marcaArq(div_li);
		var id_mat = $('input[name=conteudo_id]').val();
		// busca os IDs selected e serializa
		var lista = serializaSelecteds();//init
		
		//alert(id);
		$('#super-alerta').children('.var').text('apagaArquivos|'+id_mat+':'+lista);
		if(V['superAlerta'] == false){
			V['superAlerta'] = true;
			$('#super-alerta').show();
			$('#super-alerta').children('.frase').text('Após selecionar as fotos clique em Confirmar!');
			$('#super-alerta').css({top:'-90px'});
			$('#super-alerta').animate({top:'0px'}, 'slow', 'swing');
		}
		//$('.debug').text(div_li.attr('id'));
		return false
	});
	
	// se for a opção de APAGAR a imagem do sistema ----------------------------------
	$('.apagar-img', '#galeria').click(function(){
		var div_li = $(this).parent().parent().parent();
		marcaImg(div_li);
		var id_mat = $('input[name=conteudo_id]').val();
		// busca os IDs selected e serializa
		var lista = serializaSelecteds();//init
		
		//alert(id);
		$('#super-alerta').children('.var').text('apagaArquivos|'+id_mat+':'+lista);
		if(V['superAlerta'] == false){
			V['superAlerta'] = true;
			$('#super-alerta').show();
			$('#super-alerta').children('.frase').text('Esta opção excluirá definitivamente as imagens. Deseja confirmar?');
			$('#super-alerta').css({top:'-90px'});
			$('#super-alerta').animate({top:'0px'}, 'slow', 'swing');
		}
		//$('.debug').text(div_li.attr('id'));
		return false								
	});
	// remove imagem da seleção para APAGAR -------------------------------------------------
	$('.nao-apaga', '#galeria').click(function(){
		var div_li = $(this).parent().parent();	
		desmarcaImg(div_li);
		var id_mat = $('input[name=conteudo_id]').val();
		var lista = serializaSelecteds();//init	
		//alert(id);
		$('#super-alerta').children('.var').text('apagaFotos|'+id_mat+':'+lista);
		//$('.debug').text(div_li.attr('id'));
		return false;
	});
	
	
});
///////// funções de apoio ////////////////////////////////////
function serializaSelecteds(){
	var lista = '';
	$('#galeria li').each(function(){
			var este = $(this);
			if(este.hasClass('selected'))lista += este.attr('id')+'-';
		});
	return lista;
}
function marcaImg(div_li){
	var id = div_li.attr('id');
	var div_crop = div_li.children('.crop');
	var div_desc= div_li.children('.desc');
	var div_confirma= div_li.children('.confirma');
	div_li.addClass('selected');// sinal
	div_crop.animate({height:'50px'}, 'slow');
	div_desc.animate({height:'0px'}, 'slow', 'linear', function(){div_desc.children().hide();});
	div_confirma.show();	
}
function desmarcaImg(div_li){
	var id = div_li.attr('id');
	var div_crop = div_li.children('.crop');;
	var div_desc= div_li.children('.desc');
	var div_confirma= div_li.children('.confirma');
	div_li.removeClass('selected');
	div_crop.animate({height:'90px'}, 'slow');
	div_desc.animate({height:'30px'}, 'slow', 'linear', function(){div_desc.children().show();});
	div_confirma.hide();	
}
function marcaArq(div_li){
	var id = div_li.attr('id');
	var div_info = div_li.children('.info');
	var div_desc= div_li.children('.desc');
	var div_confirma= div_li.children('.confirma');
	div_li.addClass('selected');// sinal
	//div_info.animate({height:'50px'}, 'slow');
	div_desc.animate({height:'0px'}, 'slow', 'linear', function(){div_desc.children().hide();});
	div_confirma.show();	
}
function superalertOff(obj){
obj.animate({top:'-80px'}, 'slow', 'swing', function(){obj.hide();});
}
// serializa os IDs
function serializa(obj){
	var obj = obj;
	var array = '';
	$(obj).each(function(){
		var cont = $(this).text();
		array += cont + ', ';
	});
	return array;
}
