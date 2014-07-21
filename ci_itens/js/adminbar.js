$(document).ready(function(e) {
    console.log('adminbar on');
	
	var ABtemplate = {
		
		linkEdicao: '<div class="ab-edit-ctrl"><a href="#" title="clique para editar conteúdo">editar</a></div>'
			
	};
	
	// fas busca pelas classes
	$('[data-admin-url]').each(function(index, element) {
        var el = $(element),
	    url = el.data('admin-url');
	
		// acrescenta classe no container
		el.addClass('ab-editable');
		// injeta controle
		el.prepend(ABtemplate.linkEdicao);
		// troca url do link de edição
		$('.ab-edit-ctrl a', el).attr('href', url).on('mouseenter', function(){
			$(this).parent().parent().addClass('ab-edit-hover');	
		});
		$('.ab-edit-ctrl a', el).on('mouseleave', function(){
			$(this).parent().parent().removeClass('ab-edit-hover');	
		});
		// listenner do container
		//el.on('click', function(){	    
		   // window.location = url;
		//});
		
		//console.log(url);
		
    });
	
	// faz busca para adicionar classes extra de admin
	$('[data-admin-class]').each(function(index, element) {
        var el = $(element),
	    classe = el.data('admin-class');
		
		$('.ab-edit-ctrl', el).addClass(classe);
		
	});
    
	/*
	* CONTROLA EXIBIÇÃO DOS PINS DE EDIÇÃO
	*/
    $('input[name=show_edit_icons]').on('click', function(){
        var v = $(this).is(':checked');
        
        if(v){
			$.cookie('show_edit_icons', '1', {path: '/'});
            $('div.ab-edit-ctrl').show();
        } else {
			$.cookie('show_edit_icons', '0', {path: '/'});
            $('div.ab-edit-ctrl').hide();
        }
        
    });
	// instancia pelo cookie
	if($.cookie('show_edit_icons') == '1'){
		 $('div.ab-edit-ctrl').show();
		 $('input[name=show_edit_icons]').attr('checked', 'checked');
	} else {
		 $('div.ab-edit-ctrl').hide();
		 $('input[name=show_edit_icons]').attr('checked', false);
	}
	
	
});