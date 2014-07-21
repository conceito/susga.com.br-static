$(document).ready(function() {
    // variaveis
    //var superAltert = false;// inicia valor 


    /********************************
     *	Grupo de radio em Botões
     */
    if (jQuery().buttonset) {
        $('.group-buttons').buttonset();
    }

    // controles de navegação entre as TABS ------------------------------------------------------------
    $("#tabs").tabs({
        select: function(event, ui) {

            // tab ativa
            var tab_id = parseInt(ui.index) + 1;
            // pega o botão de salvar
            var btns = $('.btt-salva');
            // remove '/tab:?'
            // altera heref
            btns.each(function() {
                var btn = $(this);
                btn.attr('href', btn.attr('href') + '/tab:' + tab_id);
            });
        }
    });
    $('a[class^=tabs-go]').click(function() {
        var aid = $(this).attr('class').split('-');
        var id = parseInt(aid[2]);
        $("#tabs").tabs('select', id);
        return false;
    });



    // caixa para inserir descrição nas imagens ----------------------------------------------------
    $('.descricao', '#galeria').focus(function() {
        var v = $(this).val();
        $(this).addClass('focus');
        if (v == 'descreva...')
            $(this).val('');
    });
    $('.descricao', '#galeria').blur(function() {
        var v = jQuery.trim($(this).val());
        $(this).removeClass('focus');
        if (v == '') {
            $(this).val('descreva...');
        } else {
            var id = $(this).parent().parent().attr('id'); // id da foto
            // via AJAX salva submissão
            $.ajax({
                type: "POST",
                url: V['base_url'] + "ci_itens/atualizaDescricao.php",
                data: "id=" + id + "&desc=" + v,
                dataType: ($.browser.msie) ? "text" : "html",

                beforeSend: function() {
                    // msg de carregando				
                    alerta('Aguarde...');
                },
                success: function(message) {

                    if (message == 1) {
                        // recebe o retorno e processa	
                        $('#alertas').hide();

                    } else {
                        alerta('Houve um erro de conexão: tabs-forms.js 50', 'vermelho');
                    }
                }
            });
            //alert(id);
            //$(this).val('salvou!');
        }
    });
    // controle do menu de edição das fotos da galeria ----------------------------------------------
    $('#galeria .crop').hover(function() {
        $('.controle', this).animate({
            top: 0
        }, 'fast');
    }, function() {
        $('.controle', this).animate({
            top: 90
        }, 'fast');
    });

    // ação ao pressionar botões de controle do formulario -------------------------------------
    $('a.btt-salva').live('click', function() {
        var url = $(this).attr('href');
        // preenche a action do formulário
        $('#formulario').attr('action', url);
        $('#formulario').submit();
        //alert($('#formulario').attr('action'));
        return false;
    });

    // verifica se vai apagar em lote ou o item em edição!! ------------------------------------------------------
    $('.apagar-lote').click(function() {
        var id = $('input[name=conteudo_id]', '#formulario').val();

        // abre Super Alerta
        if (V['superAlerta'] == false) {
            V['superAlerta'] = true;
            $('#super-alerta').show();
            $('#super-alerta').children('.frase').text('Deseja apagar este item?');
            $('#super-alerta').children('.var').text('apagaUm|' + id);
            $('#super-alerta').css({
                top: '-90px'
            });
            $('#super-alerta').animate({
                top: '0px'
            }, 'slow', 'swing');
        }
        return false;
    });

    // escreve o apelido limpando o título  -------------------------------------------------------
    $(".input-titulo").keyup(function() {
        var strApelido;
        strApelido = replaceSpecialChars($(this).val());
        $("input[name=nick]").val(strApelido);
        return false;
    });


    //////////////////////////////////////////////////////////////////////////////////////
    //////  ações depois da super alerta
    // opção == 1 : confirma -----------------------------------
    // >>>> por enquanto está preparada apenas para apagar em lote
    $('.bot-verde', '#super-alerta').click(function() {
        // recupera as variaveis dentro do super-alerta
        // normalmente: nomeDaFuncao|id-id-id-id-
        var vars = $('#super-alerta').children('.var').text();
        var divide = vars.split('|');
        var nomeFunction = $.trim(divide[0]);
        var ids = $.trim(divide[1]);
        var tb = V['tb'];

        // Verifica se existem modificadores
        //console.log(typeof CMS.attachments);
        if (typeof CMS.attachments == 'object') {
            tb = CMS.attachments.tabela;
        }

        V['superAlerta'] = false;

        $('#super-alerta').animate({
            top: '-80px'
        }, 'slow', 'swing', function() {
            $('#super-alerta').hide();
        }); // retira alerta
        //list = replaceAll(list, '.', ':');

        // via AJAX salva submissão
        $.ajax({
            type: "POST",
            url: V['site_url'] + "cms/cmsutils/" + nomeFunction + "/" + ids + "/" + tb,
            //data: "id_uf=" + id_uf,
            dataType: ($.browser.msie) ? "text" : "html",

            beforeSend: function() {
                // msg de carregando				
                alerta('Aguarde...');
            },
            success: function(message) {
                if (message.length > 1) {
                    alerta('OK, operação feita com sucesso!', 'verde');
                    // recebe o retorno e processa
                    var divide = message.split('|');
                    var funcao = divide[0];
                    var ids = divide[1].split('-');
                    // for reordenação
                    if (funcao == 'apagaUm') {
                        var url = $('a.url-back').attr('href');
                        location.href = url + '/tip:ok';
                        alerta('Clique em Voltar');
                    } else if (funcao == 'removeFotos' || funcao == 'apagaArquivos') {
                        for (x = 0; x < ids.length; x++) {
                            $('li[id=' + ids[x] + ']', '#galeria').slideUp('slow');
                        }
                    }


                } else {
                    alerta('Houve um erro de conexão: tabs-forms.js 113', 'vermelho');
                }
            }
        });
        return false;
    });

    // opção == 2 : cancela fechando o Super Alerta -----------------------------------
    $('.bot-verm', '#super-alerta').click(function() {
        V['superAlerta'] = false;
        recolheSuperAlerta();
        alerta('As alterações não serão salvas.', 'vermelho');

        return false;
    });

    ///---------------

});
///////////////////////  funções de apoio //////////////////////////
// retira os nós da tabela
function retiraNosTbl(ids) {
    for (x = 0; x < ids.length; x++) {
        if ($.browser.msie)
            $('tr[id=' + ids[x] + ']').empty().css({
                height: '40px'
            });
        else
            $('tr[id=' + ids[x] + ']').animate({
                opacity: 0
            }, 'slow', 'linear', function() {
                $('tr[id=' + ids[x] + ']').empty().css({
                    height: '40px'
                });
            });
    }
}
// recolhe a super aleerta
function recolheSuperAlerta() {
    $('#super-alerta').animate({
        top: '-80px'
    }, 'slow', 'swing', function() {
        $('#super-alerta').hide();
    });
}