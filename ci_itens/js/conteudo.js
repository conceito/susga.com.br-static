$(document).ready(function(){
	
	// na criação de pastas esconde e mostra opções de imagem -----------------------
	$('input[name=tipo]').click(function(){
		var val = parseInt($(this).val());		
		if(val == 0 || val == 1){
			$('.padrao-img').slideDown('slow');
		} else {
			$('.padrao-img').slideUp('slow');
		}
		
	});

        // busca campos dinâmicos de imagem e arquivo
        $(".dyn-arq, .dyn-img").each(function(){
            var t = $(this);
            var tipo = '';// tipo de combo extra
            var name = t.attr('name');
            var title = '';

            if(t.hasClass('dyn-arq')){
                tipo = 'arq';
                title = 'Clique para atualizar com os arquivos disponíveis.';
            } else if(t.hasClass('dyn-img')){
                tipo = 'img';
                title = 'Clique para atualizar com as imagens disponíveis na galeria.';
            }

            // insere o link de atualização após o compo
            t.after(' <a href="#" class="dyn-update '+tipo+'" rel="'+name+'" title="'+title+'">&lt; atualizar</a>');
            //alert(name);
        });

        /*
         * Apaga arquivos associados ao conteúdo
         */
        $('.del-arquiv').click(function(){

            var del = $(this);
            var id = del.attr('rel');

            // envia ajax
            $.ajax({
                type: "POST",
                url: V['site_url']+"cms/cmsutils/removeLigacaoArquivo/"+id,
                //data: "id_uf=" + id_uf,
                dataType: ($.browser.msie) ? "text" : "html",

                beforeSend: function() {
                        // msg de carregando
                        alerta('Aguarde...');
                },
                success: function(message)
                {
                //alert(message);
                    if (message == 1){

                        $('a.arquiv[rel='+id+']').hide();
                        del.hide();
                        

                    } else {
                            alerta('Houve um erro de conexão! conteudo.js 70', 'vermelho');
                    }

                }
            });

            return false;

        });

        // ação ao atualizar um combo extra dinâmico
        $(".dyn-update").live("click", function(){
            
            var name = $(this).attr('rel');
            var combo = $("#"+name);
            var tipo = '';

            if($(this).hasClass('arq')){
                tipo = 'arq';
            } else if($(this).hasClass('img')){
                tipo = 'img';
            }

            // envia ajax
            $.ajax({
                    type: "POST",
                    url: V['site_url']+"cms/cmsutils/extraGetDadosDyn/"+tipo+"/"+V['item_id']+"/"+V['tb'],
                    //data: "id_uf=" + id_uf,
                    dataType: ($.browser.msie) ? "text" : "html",

                    beforeSend: function() {
                            // msg de carregando
                            alerta('Aguarde...');                           
                    },
                    success: function(message)
                    {
                        //alert(message);
                            if (message.length > 5){
                                    
                                    $('#alertas').hide();
                                    // limpa combo
                                    $("#"+name+" option").each(function(){
                                        if($(this).attr("selected") == false){
                                            $(this).remove();
                                        }
                                    });
                                    
                                    $("#"+name+" option:first-child").after(message);
                            } else {
                                    alerta('Houve um erro de conexão! conteudo.js 65', 'vermelho');
                            }

                    }
            });
           
            
            //alert(V['site_url']+"cms/cmsutils/extraGetDadosDyn/"+V['item_id']+"/"+V['tb']);
            return false;
        });


        /**************************************************************************
         * Sistema de tags para conteúdo
        // insere tag na listagem
         */
        $('.tag-add').click(function(){

            var t = $(this);
            var id = t.attr('rel');
            var label = t.text();

            // remove a info 'nenhuma tag'
            $('.nenhuma-tag').remove();

            var html = '<div class="tag-item"> \n\
            <span class="tag-label">'+label+'</span> \n\
            <a href="#" title="remover" class="del">[x]</a> \n\
            <input name="mytags[]" type="hidden" value="'+id+'" />\n\
            </div>';

            $(".campo-tags").append(html);

            return false;

        });

        // remove as datas recem adicionadas  ---------------
		$("a.del", ".tag-item").live("click", function(){
	
			var div = $(this).parent();
			div.remove();
			return false;
	
		});

       
});