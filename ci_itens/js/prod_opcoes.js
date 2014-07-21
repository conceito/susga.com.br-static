$(document).ready(function(e) {
    
	
	
	var opt = {
		
		i : 0,
		j : 0,
		preffix : '— ',
		
		init: function(){
			var self = this;
			
			// percorre para estabelecer configurações
			self.fetch();
			
			// add nova opção
			$('a.add-new-option').on('click', function(e){
				e.preventDefault();
				self.addNewOption();	
			});
			// listener para atualizar nome da opção
			$(document).on('keyup', '.nm-opt', function(){
				self.updateOptionName($(this));
			});
			// so selecionar uma opção/valor no select, exibe para edição
			$(document).on('change', '#option', function(){
				self.ativaOpcaoValor();
			});
			// ação para inserir um valor em uma opção
			$(document).on('click', '.add-new-value', function(e){
				e.preventDefault();
				self.addNewValue($(this));
			});
			// ação para remover opção/valor
			$(document).on('click', '.remove-option', function(e){
				e.preventDefault();
				self.removeOptionValue($(this));
			});
			// formato de moeda
			$(document).on('keyup', '.type-valor', function(){
				var strApelido = money_format($(this).val(), ',');	
				$(this).val(strApelido);
				return false;										   
			});
				
		},
		
		// percorre opções
		fetch: function(){
			var self = this;
			
			/*$('.option', '.values-panel').each(function(){
				var optpanel = $(this),
					selected = optpanel.children('.estoque-row').find('select').val();
				//console.log(selected);
				
			});*/
			
		},
		
		// adiciona nova opção no select
		addNewOption: function(){
			var self = this;
			// inscrementa
			self.i++;
			
			// injeta template da opção
			var template = Handlebars.compile($('#tmp-option').html());
			var html     = template({n: self.i});			
			$('.values-panel').append(html);
			
			$('select#option').append('<option value="option_' + self.i + '" class="opt">Opção #' + self.i + '</option>');
			
			// esconde todos, menos o último a entrar
			$('.option:not(#option_'+self.i+')').hide();
			
			// seleciona no select
			$('#option option[value=option_' + self.i + ']').attr('selected', 'selected');			
			
			
		},
		
		// após inserir uma opção ela fica disponível para atualização
		updateOptionName: function(obj){
			var self = this,
				ctx = obj.closest('div.option'),
				id = ctx.attr('id').substr(7),
				nome = obj.val();
				
				// verifica se é valor para acrescentar prefixo
				var arr = id.split('_').length;
				if(arr > 1){
					nome = self.preffix + nome;
				}
				
				// atualiza no select
				$('option[value='+ctx.attr('id')+']', '#option').text(nome);
				
				//console.log(id + ' - ' + nome);
		},
		
		ativaOpcaoValor: function(){
			var self = this,
				str_id = $('select#option').val();
				
			$('.option').hide();
			$('#'+str_id).show();
		},
		
		// adiciona um valor para uma opção existente
		addNewValue: function(obj){
			var self = this,
				ctx = obj.closest('div.option'),
				id = ctx.attr('id').substr(7); // option_?
				
				//console.log(ctx.attr('id'));
			// inscrementa
			self.j++;
				
			
				
			// injeta template da opção
			var template = Handlebars.compile($('#tmp-value').html());
			var html     = template({n: id, v: self.j});			
			$('.values-panel').append(html);
			
			var option = $('#option option[value^=\'option_' + id + '\']:last');
			//console.log('option: ' + option);
			
			option.after('<option value="option_' + id + '_'+self.j+'" class="val">'+self.preffix+'Valor da opção #' + self.j + '</option>');
			
			// esconde todos, menos o último a entrar
			$('.option:not(#option_'+id+'_'+self.j+')').hide();
			
			// seleciona no select
			$('#option option[value=option_' + id + '_'+self.j+']').attr('selected', 'selected');
			
			
				//console.log(id);
		},
		
		// remove uma opção do select e do corpo
		removeOptionValue: function(obj){
			var self = this,
				ctx = obj.closest('div.option'),
				id = ctx.attr('id'),
				subid = id.substr(7),
				arr = subid.split('_');
				
				// console.log(id);
				// console.log(arr);
				
				
				
				if(arr.length == 1){
					$('.option-panel').append('<input type="hidden" name="options_remove[]" value="'+arr[0]+'">');
					$('#option_'+arr[0]).remove();
					$('div[id^=option_'+arr[0]+'_]').remove();
					$('#option option[value=option_'+arr[0]+']').remove();
					$('#option option[value^=option_'+arr[0]+'_]').remove();
				} else {
					$('.option-panel').append('<input type="hidden" name="options_remove[]" value="'+arr[1]+'">');
					$('#option_'+arr[0]+'_'+arr[1]).remove();
					$('#option option[value=option_'+arr[0]+'_'+arr[1]+']').remove();
					
				}
				
				
				
				
				
		}
		
	};
	opt.init();
	
	/*******************************************************
	*	IMPLEMENTA BUSCA DE PRODUTOS PARA CLONAR AS OPÇÕES
	********************************************************
	*/
	$('button.import-opt').hide().on('click', function(e){
		e.preventDefault();
		
		var id = parseInt($('#search-prod-opt').data('id'));
		
		//console.log(CMS.site_url + 'cms/loja/cloneOptions');
		
		$.ajax({
			
			type: 'POST',
			data: 'prod_clone_id=' + id + '&prod_ref_id=' + V['item_id'],
			url: CMS.site_url + 'cms/loja/cloneOptions',
			dataType: ($.browser.msie) ? "text" : "html",
			beforeSend: function() {
				// msg de carregando				
				$('.alert', '.import-box').slideDown();
			},
			success: function( data ){
				
				if(data == 0){
					alert("Este produto não tem opções.");
				} else {
					$('.btt-salva', '#barra-botoes').click();	
				}
				
				
			},
			complete: function(){
				$('.alert', '.import-box').slideUp();
			}
				
		});
		
	});
	
	// abre box de pesquisa
	$('.add-import-option').on('click', function(){
			$('.import-box').slideToggle();
	});
	
	// faz busca pelos produtos
	$('#search-prod-opt').autocomplete({
		source: V['site_url']+"cms/loja/get_products_by_json",
		minLength: 2,
		select: function( event, ui ) {
			//console.log(ui.item.id);			
			//$('#search-prod-opt').val(ui.item.id);
			$('#search-prod-opt').data('id', ui.item.id);
			
			// exibe botão
			$('button.import-opt').show();
			
		}
	});
	
});