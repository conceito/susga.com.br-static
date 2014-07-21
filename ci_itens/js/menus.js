$(document).ready(function(){

    // variaveis
    var loop;
    var itemDraging;
    var offsetStep = 30;
    var offsetChildVal = 0; // profundidade dp item arrastado
    var marginLeft;
    var grupo_id = $('input[name=conteudo_id]', '#formulario').val();


    //var superAltert = false;// inicia valor
    $(".menu-sortable").sortable({
        handle: '.drag',
        placeholder: "ui-state-highlight",
        start: function(event, ui){
            itemDraging = ui.item.attr('id');
            marginLeft = parseInt(ui.item.css('margin-left'));
            loop = setInterval(atualizaPosicao, 400);
        //            console.log('item: ' + itemDraging);
        },
        change: function(event, ui){
        //            console.log('offset: ' + ui.item.position().left);
        },
        stop: function(event, ui){
            clearInterval(loop);
            updateItemDepth();
            serializaMenu();
        //          console.log('depth: '+offsetChildVal);

        },
        update: function(event, ui) {
			
            serializaMenu();
            $(".menu-sortable").sortable( "refresh" );

        //$('.debug').text(list);
				
        }
    });
    $(".menu-sortable").disableSelection();

    /*
     * Ao iniciar o evento 'drag' é atualizada o offet.left do item
     */
    function atualizaPosicao(){
        var item = $('#'+itemDraging, '.menu-sortable');
        var positionLeft = parseInt(item.position().left);
        //        var marginLeft = parseInt(item.css('margin-left'));
        //        console.log('ml: '+marginLeft);
        // retorna com o valor da profundidade na atual posição
        offsetChildVal = offsetChild(positionLeft);
        //        console.log('position.left: ' + (positionLeft + marginLeft));
        $('.ui-state-highlight').css('marginLeft', (offsetChildVal * offsetStep) + 5);
    //        console.log(itemDraging);
    }

    /*
     * Dependendo do offset.left é dado um valor para o placeholder
     * Esta é a profundidade; 0, 1, 2, 3 ...
     */
    function offsetChild(offset){

        var v = Math.floor((offset + marginLeft) / offsetStep);
        if(v < 0)v = 0;
        else if(v > 3)v = 3;
        return v;

    }

    /*
     * atualiza a profundidade da classe, após terminar o arraste
     */
    function updateItemDepth(){
        var item = $('#'+itemDraging, '.menu-sortable');
        var depth = itemDepth(item);
        var prev = item.prev('li');
        //        var prevId = parseInt(prev.attr('id'));
        var prevDepth = itemDepth(prev);
        

        // não permite que o item filho tenha profundidade maior que 1 em relação ao seu pai
        if((offsetChildVal - 1) > prevDepth){
            offsetChildVal = prevDepth + 1;
        }

        // remove a classe de profundidade       
        item.removeClass('menu-depth-'+ depth ).addClass('menu-depth-'+ offsetChildVal );

    }

    /*
     * retorna a profunidade do item
     */
    function itemDepth(item){

        var clas = item.attr('class');

        //        console.log('cla: ' + clas);

        if(String(clas) == 'undefined')return -1;
       
        var classes = clas.split(' ');
        // percorre as classes
        for(i=0; i<classes.length; i++){
            
            var c = classes[i];
            if(c.substring(0, 10) == 'menu-depth'){
                return parseInt(c.substring(11));
            }
        }
        return 0;// se não encontrar nada
    }

    /*
     * Serializa ordenação
     */
    function serializaMenu(){
        var list = '';
        var id_mat = $('input[name=conteudo_id]').val();

        $(".menu-sortable li").each(function(){
            var ids = $(this).attr('id');
            var depth = itemDepth($(this));
            var pai_id = 0;
            // pega o item anterior. Se for nível > 0 é o pai
            if(depth > 0){

                //                var pai = $(this).prev('li');
                // pega o LI pai anterior na profundidade
                var pai = $(this).prevAll('.menu-depth-'+(depth-1));
                pai_id = pai.attr('id');
            //                console.log(ids + '=pai='+pai_id);
            }

            list += ids + '.' + depth + '.' + pai_id + '-';
        });
        $('#super-alerta').children('.var').text('ordenaMenus|'+id_mat+':'+list);
        if(V['superAlerta'] == false){
            V['superAlerta'] = true;
            $('#super-alerta').show();
            $('#super-alerta').children('.frase').text('Após reordenar as páginas clique em Confirmar!');
            $('#super-alerta').css({
                top:'-90px'
            });
            $('#super-alerta').animate({
                top:'0px'
            }, 'slow', 'swing');
        }

    //        console.log(list);
    }


    /*
     * abre opções do item de menu
     */
    $('a.options', '.menu-sortable').live('click', function(){

        var ctx = $(this).closest('li');

        $('.menu-dados', ctx).slideToggle('fast');

        return false;

    });


    ////////////////////********************////////////////////////////
    /////////    inicia parte dos box para adicionar páginas    ///////
    $(".box-menu-tabs").tabs();

    /* -------------------------------------------------------------------------
     * Pega seleção no combo de módulos
     */
    $('#modulos', '#add-page').live('change', function(){

        var modulo_id = $(this).val();
        var modulo_label = $(this+':selected', '#tab-modulos').text();
        var modulo_tb = $(this+':selected', '#tab-modulos').attr('title');
        
        // setando na mão
        modulo_tb = 'cms_conteudo';
        

        // atualiza o label da aba e alterna para ela
        $('.tabitem-pagina').text(modulo_label);
        $(".box-menu-tabs", '#add-page').tabs('select', 1);

        // inicia AJAX
        $.ajax({
            type: "POST",
            url: V['base_url']+"cms/menus/getConteudosFromModuloId/"+modulo_id+'/'+modulo_tb,
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

                    $('#conteudo', '#add-page').html(message);
                //                                console.log(message);

                } else {
                    alerta('Não existe conteúdo!', 'vermelho');
                    $('#conteudo', '#add-page').html('<option selected="selected" value=""> -- inexistente -- </option>');
                }
            }
        });

        //        console.log('modulo: ' + modulo_label);

        return false;

    });

    /* -------------------------------------------------------------------------
     * Pega a seleção do conteúdo e adiciona no menu
     * 
     */
    $('.bot-add-page').live('click', function(){

        // encontra o contexto do botão
        var ctx = $(this).parent();
        
        
        var conteudo_id = '';
        // se o contexto for == tab-paginas
        if(ctx.attr('id') == 'tab-paginas'){
            // percorre as opções selecionadas

            $('#conteudo option:selected', '#add-page').each(function(){
            
                conteudo_id += $(this).val() + '-';
            //            var conteudo_nick = $(this).attr('title');
            //            var conteudo_label = $(this).text();
        
            });
        } else if(ctx.attr('id') == 'tab-pesquisar'){

            $('input[name=conteudo]:checked').each(function(){

                conteudo_id += $(this).val() + '-';

            });

        }

//        console.log(conteudo_id);
//
//        return false;

        // verifica se não veio nada selecionado
        if(conteudo_id.length < 1){
            alerta('Nenhum conteúdo selecionado.');
            return false;
        }


        

        // inicia AJAX
        $.ajax({
            type: "POST",
            url: V['base_url']+"cms/menus/setItensMenu/"+conteudo_id+'/'+grupo_id,
            //data: "id="+id+"&desc="+v,
            dataType: ($.browser.msie) ? "text" : "html",

            beforeSend: function() {
                // msg de carregando
                $('.loading', '#add-page').show();
                alerta('Aguarde...');
            },
            success: function(message)
            {
                $('.loading', '#add-page').hide();

                if (message.length > 1){
                    // recebe o retorno e processa

                    // injeta no final
                    $('.menu-sortable').append(message);
                //                    console.log(message);

                } else {
                    alerta('Erro no retorno do controller: menus/setItensMenu', 'vermelho');

                }
            }
        });

        //        console.log('conteudo: ' + conteudo_label);

        return false;

    });

    /* -------------------------------------------------------------------------
     * Adiciona um item vazio no menu
     *
     */
    $('.bot-add-blankpage').live('click', function(){

        // percorre as opções selecionadas
        var conteudo_id = 0;


        // inicia AJAX
        $.ajax({
            type: "POST",
            url: V['base_url']+"cms/menus/setItensMenu/"+conteudo_id+'/'+grupo_id,
            //data: "id="+id+"&desc="+v,
            dataType: ($.browser.msie) ? "text" : "html",

            beforeSend: function() {
                // msg de carregando
                $('.loading', '#add-page').show();
                alerta('Aguarde...');
            },
            success: function(message)
            {
                $('.loading', '#add-page').hide();

                if (message.length > 1){

                    // injeta no final
                    $('.menu-sortable').append(message);
                //                    console.log(message);

                } else {
                    alerta('Erro no retorno do controller: menus/setItensMenu', 'vermelho');

                }
            }
        });

        //        console.log('conteudo: ' + conteudo_label);

        return false;

    });


    /*
     * Remove item do menu
     */
    $('.opt-remover').live('click', function(){

        var this_ = $(this).closest('li');
        var menu_id = this_.attr('id');
       
        // remove do BD via AJAX
        $.ajax({
            type: "POST",
            url: V['base_url']+"cms/menus/removeItemMenu/"+menu_id,
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

                    $(".menu-sortable").sortable( "refresh" );
                //                    console.log(message);

                } else {
                    alerta('Erro no retorno do controller: menus/removeItemMenu', 'vermelho');

                }
            }
        });


        // remove do DOM
        this_.css('backgroundColor', '#EEC8C4');
        this_.slideUp('normal', function(){
            this_.remove();
        });
       
        //       console.log('remover: ' + menu_id);

        return false;

    });


    /* -------------------------------------------------------------------------
     * Atualiza os dados do item de menu via AJAX
     */
    $('.opt-atualizar').click(function(){

        var ctx = $(this).closest('li');
        var id = ctx.attr('id');
        var url = $('input[name=url]', ctx).val();
        var rotulo = $('input[name=rotulo]', ctx).val();
        var title = $('input[name=title]', ctx).val();
        var css = $('input[name=css]', ctx).val();
        var target = $('select[name=target]', ctx).val();
       

        // envia dados via AJAX
        $.ajax({
            type: "POST",
            url: V['base_url']+"cms/menus/atualizaDadosItemMenu/",
            data: "id="+id+"&url="+url+"&rotulo="+rotulo+"&title="+title+"&css="+css+"&target="+target,
            dataType: ($.browser.msie) ? "text" : "html",

            beforeSend: function() {
                // msg de carregando
                $('.loading', ctx).show();
                alerta('Aguarde...');
            },
            success: function(message)
            {
                if (message == 1){
                    // recebe o retorno e processa
                    $('.loading', ctx).hide();
                    alerta('Item atualizado.', 'verde');

                    // atualiza o rótulo deste item de menu para o usuário
                    $('.title', ctx).text(rotulo);
                //                    console.log(message);

                } else {
                    alerta('Erro no retorno do controller: menus/atualizaDadosItemMenu', 'vermelho');

                }
            }
        });

        //       console.log('depth: ' + depth);

        return false;

    });


    /* ------------------------------------------------------------------------
     * Campos de busca -
     */
    $.fn.onTypeFinished = function(func) {
        var T = undefined, S = 0, D = 1500;
        $(this).bind("keyup", onKeyPress);//.bind("focusout", onTimeOut);
        function onKeyPress() {
            clearTimeout(T);
            if (S == 0) {
                S = new Date().getTime();
                D = 1500;
                T = setTimeout(onTimeOut, 1500);
                return;
            }
            var t = new Date().getTime();
            D = (D + (t - S)) / 2;
            S = t;
            T = setTimeout(onTimeOut, D * 2);
        }

        function onTimeOut() {
            func.apply();
            S = 0;
        }
        return this;
    };

    $('.loading').hide();
    var request_in_process = false;
    $('.palavrachave').onTypeFinished(pesquisaItensMenu);

    function pesquisaItensMenu(){

        var q = $('.palavrachave').val();

        // valida o tamanho da string
        if(q.length < 3 && !request_in_process){
            return false;
        }

        request_in_process = true;

        // envia dados via AJAX
        $.ajax({
            type: "POST",
            url: V['base_url']+"cms/menus/pesquisaItensMenu/",
            data: "q="+q+"&grupo_id="+grupo_id,
            dataType: ($.browser.msie) ? "text" : "html",

            beforeSend: function() {
                // msg de carregando
                $('.loading', '#tab-pesquisar').show();
            //                alerta('Aguarde...');
            },
            success: function(message)
            {
                request_in_process = false;
                if (message.length > 1){
                    // recebe o retorno e processa
                    $('.loading', '#tab-pesquisar').hide();

                    // atualiza o rótulo deste item de menu para o usuário
                    $('.pc-resultado', '#tab-pesquisar').empty().html(message);
                //                    console.log(message);

                } else {
                    alerta('Erro no retorno do controller: menus/pesquisaItensMenu', 'vermelho');

                }
            }
        });

        return false;

    }

//    


    
	
	
		
});