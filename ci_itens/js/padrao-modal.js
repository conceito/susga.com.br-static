$(document).ready(function(){
	
	
    // função que liga e desliga alertas ---------------------------------------------------
    // função que liga e desliga alertas ---------------------------------------------------
    $('.resposta-ok').animate({
        opacity:90
    }, 3000, 'linear', function(){
        $('.resposta-ok').animate({
            opacity:0
        }, 'slow', 'linear', function(){
            $('.resposta-ok').slideUp('slow');
        });
    });
    $('.resposta-erro').animate({
        opacity:90
    }, 3000, 'linear', function(){
        $('.resposta-erro').animate({
            opacity:0
        }, 'slow', 'linear', function(){
            $('.resposta-erro').slideUp('slow');
        });
    });
	
    // caixa para inserir descrição nas imagens ----------------------------------------------------
    $('textarea[name=descricao_modal]').focus(function(){
        var v = $(this).val();
        $(this).addClass('focus');
        if(v == 'descreva...')$(this).val('');
    });
    $('textarea[name=descricao_modal]').blur(function(){
        var v = jQuery.trim($(this).val());
        $(this).removeClass('focus');
        $('.debug').text(v);
	
        if(v == ''){
            $(this).val('descreva...');
        }
		
        else {
            var id = $('input[name=arquivo_id]').val();// id da foto
            // via AJAX salva submissão
            $.ajax({
                type: "POST",
                url: V['base_url']+"ci_itens/atualizaDescricao.php",
                data: "id="+id+"&desc="+v,
                dataType: ($.browser.msie) ? "text" : "html",
				
                beforeSend: function() {
                    // msg de carregando
                    $('.debug').slideDown();
                    $('.debug').text('aguarde...');
                },
                success: function(message)
                {
                    if (message == 1){
                        // recebe o retorno e processa
                        $('.debug').slideDown();
                        $('.debug').text('descrição salva!');
                        $('.debug').delay(2000, function(){
                            $('.debug').slideUp();
                        });
						
                    } else {
                        $('.debug').slideDown();
                        $('.debug').text('Houve um erro de conexão: padrao-model.js 44');
                    }
                }
            });
        //alert(id);
        //$(this).val('salvou!');
        }
    });
	
// link para apagar um arquivo assim que foi uploaded ------------------------
$('.apagar-arq').click(function(){
    var div_li = $(this).parent().parent();
    var id = div_li.attr('id');
		
    // via AJAX salva submissão
    $.ajax({
        type: "POST",
        url: V['site_url']+"cms/cmsutils/apagaArquivos/id:"+id,
        //data: "id="+id+"&desc="+v,
        dataType: ($.browser.msie) ? "text" : "html",
			
        beforeSend: function() {
            // msg de carregando
            $('.clear').text('aguarde...');
        },
        success: function(message)
        {
            if (message.length > 1){
                // recebe o retorno e processa
                $('.clear').text('');
                div_li.slideUp('slow');
					
            } else {
                $('.clear').text('Houve um erro de conexão: padrao-modal.js 81');
            }
        }
    });
		
    return false;
});
	
	
// manilular imagens ------------------------------------------
$('.lista-imagens a').live('click', function(){
    //$('.lista-imagens a').click(function(){
    var div_li = $(this).parent();
    var id = div_li.attr('id');
    if(div_li.hasClass('selected')){
			
        // via AJAX remove imagem
        $.ajax({
            type: "POST",
            url: V['site_url']+"cms/cmsutils/removeFotos/"+V['item_id']+":"+id,
            //data: "id="+id+"&desc="+v,
            dataType: ($.browser.msie) ? "text" : "html",
				
            beforeSend: function() {
            // msg de carregando
            //$('.clear').text('aguarde...');
            },
            success: function(message)
            {
                if (message.length > 1){
                    // recebe o retorno e processa
                    //$('.clear').text('');
                    div_li.removeClass('selected');
                    // sinaliza clique
                    $('.click-del', div_li).css('marginTop', '0px').show();
                    $('.click-del', div_li).animate({
                        'marginTop':'-20px'
                    }, 2000, 'swing', function(){
                        $(this).hide().removeClass('click-del').addClass('click-add').text('inserido');
                    });
						
                } else {
                    $('.clear').text('Houve um erro de conexão: padrao-modal.js 125');
                }
            }
        });
			
    } else {
			
        // via AJAX add imagem
        $.ajax({
            type: "POST",
            url: V['site_url']+"cms/cmsutils/atualizaGaleria/"+V['item_id']+"/"+id,
            //data: "id="+id+"&desc="+v,
            dataType: ($.browser.msie) ? "text" : "html",
				
            beforeSend: function() {
            // msg de carregando
            //$('.clear').text('aguarde...');
            },
            success: function(message)
            {
                $('.clear').text(message);
                if (message == 1){
                    // recebe o retorno e processa
                    //$('.clear').text('');
                    div_li.addClass('selected');
                    // sinaliza clique
                    $('.click-add', div_li).css('marginTop', '0px').show();
                    $('.click-add', div_li).animate({
                        'marginTop':'-20px'
                    }, 2000, 'swing', function(){
                        $(this).hide().removeClass('click-add').addClass('click-del').text('removido');
                    });
						 
						
                } else {
                    $('.clear').text('Houve um erro de conexão: padrao-modal.js 158');
                }
            }
        });
			
    }
		
		
		
		
    //alert(id);
    return false;
});
	
// controla as abas das mensagens --------------------------------------------------

$('a.assunto').click(function(){
    var men = $(this).next('.mensagem');
    var mens_id = $(this).parent().attr('id').split('-');
    var id = mens_id[1];
		
    men.slideToggle();
    if($(this).hasClass('lido')){
    // ja fo lido
    } else {
        $(this).addClass('lido');
        // via AJAX marca com lido
        $.ajax({
            type: "POST",
            url: V['site_url']+"cms/cmsutils/mensLida/"+id,
            //data: "id="+id+"&desc="+v,
            dataType: ($.browser.msie) ? "text" : "html",
				
            beforeSend: function() {
                // msg de carregando
                $('#alertas').show().text('Aguarde...');
            },
            success: function(message)
            {
                if (message == 1){
                    // recebe o retorno e processa
                    $('#alertas').hide();
                //$('.debug').text(message);
						
                } else {
                    $('#alertas').show().text('Houve um erro de conexão: padrao-model.js 197');
                }
            }
        });
    }
		
    return false;
});


/*************************************************************************
 * Relacionamentos de imagens com conteúdos
 */
 $('#modulos', '.img-relacao').change(function(){

     var modulo_id = $(this).val();

     if(modulo_id > 0){

         // inicia AJAX
        $.ajax({
            type: "POST",
            url: V['base_url']+"cms/cmsutils/comboConteudoFromModulo/"+modulo_id+'/cms_conteudo',
            //data: "id="+id+"&desc="+v,
            dataType: ($.browser.msie) ? "text" : "html",

            beforeSend: function() {
                // msg de carregando
                alerta('Aguarde...');
            },
            success: function(message)
            {
                $('.loading', '#add-page').hide();

                if (message.length > 1){
                    // recebe o retorno e processa

                    $('.combo-conteudo-ajax', '.img-relacao').html(message);
                //                                console.log(message);

                } else {
                    alerta('Não existe conteúdo!', 'vermelho');
                    $('.combo-conteudo-ajax', '.img-relacao').html('<option selected="selected" value=""> -- inexistente -- </option>');
                }
            }
        });


     }

//     console.log(modulo_id);

 });

 $('#rel', '.img-relacao').live("change", function(){

     var conteudo_id = $(this).val();
     
     var id = $('input[name=arquivo_id]').val();// id da foto

     // via AJAX salva submissão
        $.ajax({
            type: "POST",
            url: V['base_url']+"ci_itens/atualizaDescricao.php",
            data: "id="+id+"&op=rel&rel="+conteudo_id,
            dataType: ($.browser.msie) ? "text" : "html",

            beforeSend: function() {
                // msg de carregando
                $('.debug').slideDown();
                $('.debug').text('aguarde...');
            },
            success: function(message)
            {
                if (message == 1){
                    // recebe o retorno e processa
                    $('.debug').slideDown();
                    $('.debug').text('Relacionamento salvo!');
                    $('.debug').delay(2000, function(){
                        $('.debug').slideUp();
                    });

                } else {
                    $('.debug').slideDown();
                    $('.debug').text('Houve um erro de conexão: padrao-model.js 303');
                }
            }
        });

     console.log(conteudo_id);

 });

	/******************************************
	* CONTROLE DE TAGS DE IMAGENS
	*/
	$('input[name=tag_opt]').click(function(){
	
	 var self = $(this),
	 	val = self.val(),
		id = $('input[name=arquivo_id]').val();// id da foto
	
	 // via AJAX salva submissão
		$.ajax({
			type: "POST",
			url: V['base_url']+"ci_itens/atualizaDescricao.php",
			data: "id="+id+"&op=tag_opt&var="+val,
			dataType: ($.browser.msie) ? "text" : "html",
	
			beforeSend: function() {
				// msg de carregando
				$('.debug').slideDown();
				$('.debug').text('aguarde...');
			},
			success: function(message)
			{
				if (message == 1){
					// recebe o retorno e processa
					$('.debug').slideDown();
					$('.debug').text('Tag salva!');
					$('.debug').delay(2000, function(){
						$('.debug').slideUp();
					});
					// altera o status dos botões
					$('input[name=tag_opt]').parent('label').removeClass('active');
					self.parent('label').addClass('active')
	
				} else {
					$('.debug').slideDown();
					$('.debug').text('Houve um erro de conexão: padrao-model.js 343');
				}
			}
		});
	
	//     console.log(val);
	
	});
	
	
});