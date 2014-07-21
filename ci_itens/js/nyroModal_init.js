$(document).ready(function(){

	$.nyroModalSettings({
		height: 600,
		width: 750,
		bgColor: '#fff',
		endFillContent: function(elts, settings) {// se o tamanho do conteúdo mudar a janela se adapta
			if (settings.type == 'iframe') {
				var iframe = $('iframe', elts.content);
				if (iframe.attr('src').indexOf(window.location.hostname) > -1) {
					iframe.load(function() {
						var body = iframe.contents().find('body');
						if(body.height() > 600){
						$.nyroModalSettings({
							width: body.width(),
							height: body.height()
						});
						}
					});
				}
			}
		}
	});
	function preloadImg(image) {
		var img = new Image();
		img.src = image;
	}
	preloadImg(V['base_url']+'ci_itens/img/loader.gif');
	preloadImg(V['base_url']+'ci_itens/img/prev.gif');


        $('.closeModalandRefresh').click(function(e){
            e.preventDefault();

            var url_parent = parent.window.location.href,
				selected = parseInt(parent.$('#tabs').tabs('option', 'selected')),
            	tab = (parent.V['tb'] == 'cms_conteudo') ? '/tab:'+(selected+1) : '';
			
		

            //console.log( );
            parent.$('.nyroModalClose').click();
            parent.window.location = url_parent + tab;
           
        });

});