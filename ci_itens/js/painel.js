$(document).ready(function(){


    /*
         * Sistema de paineis drag drop
         */
    $('.dragbox')
    .each(function(){
        $(this).hover(function(){
            $(this).find('h2').addClass('collapse');
        }, function(){
            $(this).find('h2').removeClass('collapse');
        })
        .find('h2').hover(function(){
            $(this).find('.configure').css('visibility', 'visible');
        }, function(){
            $(this).find('.configure').css('visibility', 'hidden');
        })
        .click(function(){
            $(this).siblings('.dragbox-content').toggle();

            if ($(this).siblings('.dragbox-content').is(":hidden")) {
                
                $(this).parent().removeClass('shown').addClass('hidden');
            } else {
                
                $(this).parent().removeClass('hidden').addClass('shown');
            }

            salvaOrdemSatusPaineis();


//            console.log(showOrHide);
        });
    });
    $('.column').sortable({
        connectWith: '.column',
        handle: 'h2',
        cursor: 'move',
        placeholder: 'placeholder',
        forcePlaceholderSize: true,
        opacity: 0.4,
        stop: function(event, ui){
//            $(ui.item).find('h2').click();
            salvaOrdemSatusPaineis();

        /*Pass sortorder variable to server using ajax to save state*/
        }
    })
    .disableSelection();

    var ajax_is_working = false;
    function salvaOrdemSatusPaineis(){
        var sortorder = '';
        

//            $('.column').each(function(){
//                var itemorder = $(this).sortable('toArray');
//                var columnId = $(this).attr('id');
//
//                sortorder += columnId+'='+itemorder.toString()+'&';
//            });

            $('.column').each(function(){

                var columnId = $(this).attr('id');

                sortorder += columnId+'=';

                $('.dragbox', $(this)).each(function(){

                   var this_ = $(this);
                   var id = this_.attr('id');
                   var status = (this_.hasClass('hidden')) ? 'hidden' : 'shown';

                   sortorder += id+'-'+status+', ';

                });

                sortorder += '&';

            });

            if(ajax_is_working){

                alerta('Servidor com lentidão. Suas alterações podem não ter sido salvas.');
                return '';
            }

            // via AJAX salva dados
            $.ajax({
                type: "POST",
                url: V['site_url']+"cms/cms/salvaPainelOrdem/",
                data: sortorder,
                dataType: ($.browser.msie) ? "text" : "html",

                beforeSend: function() {
                    // msg de carregando
                    ajax_is_working = true;
                },
                success: function(message)
                {
                    ajax_is_working = false;
                    
                    if (message == 1){
                        // recebe o retorno e processa
                        

//                        console.log(message);

                    } else {
                        alerta('Houve um erro de conexão: painel.js 110', 'vermelho');
                    }
                }
            });

//            console.log('SortOrder: '+sortorder);
    }








    // controle do painel de mensagens
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
                    alerta('Aguarde...');
                },
                success: function(message)
                {
                    if (message == 1){
                        // recebe o retorno e processa
                        $('#alertas').hide();
                    //$('.debug').text(message);
						
                    } else {
                        alerta('Houve um erro de conexão: painel.js 32', 'vermelho');
                    }
                }
            });
        }
		
        return false;
    });
    // limita caracteres ---------------------------------------------------------------
    $("textarea[name=mensagem]").charLimit({
        limit: 700, // number
        speed: "normal", // nummber or string
        descending: true // boolean
    });
	
    // envia a mensagem -----------------------------------------------------
    // envia ajax
    $.AjaxifyDebug = true;
    $('#formmensagem .bot-verde').ajaxify({

        target:'.resposta-form',
        link:V['site_url']+'cms/cmsutils/enviaSisMens',
        forms:'#formmensagem',
        // animação
        animateOut:{
            opacity:'0'
        },
        animateOutSpeed:1000,
        animateIn:{
            opacity:'1'
        },
        animateInSpeed:1000,
        // onde o loading.. aparece
        loading_target:'#painel-mensagens .resposta-form',
        loading_img:V['base_url']+'ci_itens/img/loading.gif',
        method: "POST",
        onStart: function(options){
            $('input[name=imp]', '#formmensagem').attr('checked', '');
            $('input[name=assunto]', '#formmensagem').val('Assunto');
            $('textarea[name=mensagem]', '#formmensagem').val('Mensagem');
        },
        onError: function(options,data){
            alert('erro painel.js 59');
        },
        onSuccess: function(options,data){
            $('#painel-mensagens .resposta-form').slideDown();
            $(this).delay(2000, function(){
                $('#painel-mensagens .resposta-form').slideUp(3000);
            });
        },
        onComplete: function(options){}
	
    });
	
	
    // limpa o form dos valores padrão
    $('input[name=assunto]').focus(function(){
        var val = $(this).val();
        if(val == 'Assunto') $(this).val('');
        return false;
    });
    $('textarea[name=mensagem]').focus(function(){
        var val = $(this).val();
        if(val == 'Mensagem') $(this).val('');
        return false;
    });
	
    // controle de box aberto e fechado ----------------------
    $('.max').hide();
    $('.min').click(function(){
        var box = $(this).parent().parent().parent();
        box.children('.painel-content').slideUp('slow');
        // libera controler
        box.find('.min').hide();
        box.find('.max').show();
        return false;
    });
    $('.max').click(function(){
        var box = $(this).parent().parent().parent();
        box.children('.painel-content').slideDown('slow');
        // libera controler
        box.find('.max').hide();
        box.find('.min').show();
        return false;
    });
});