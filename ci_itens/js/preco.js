$(document).ready(function(e) {
    
	$('.type-data').datepicker({ 		
		beforeShow: function( selectedDate ) {                
			$(this).datepicker( "option", "minDate", new Date() );
		}
    });
	
	
    /************************************************************
    * Adiciona e remove itens no multi conteúdo
    */
    var Multi = {
        
        preco_ttl: 0,
        cupom_ttl: 0,
        contexto: null,
        
        init: function(){
            var self = this;
            this.fetchInputs();
            // add
            $(document).on('click','.add-multi' , function(e){
                e.preventDefault();                
                self.addItem($(this));
				
            });
            // remove
            $(document).on('click', '.remove-multi', function(e){
                e.preventDefault();                
                self.removeItem($(this)); 
            });
			// formato de código			
			$(document).on('keyup', '.type-codigo', function(){
				var v = replaceSpecialChars($(this).val());	
				$(this).val(v.toUpperCase());
				return false;										   
			});
			// formato de moeda
			$(document).on('keyup', '.type-valor', function(){
				var strApelido = money_format($(this).val(), ',');	
				$(this).val(strApelido);
				return false;										   
			});
			// alteração de formato
			$(document).on('change', '.type-regra', function(){
			    self.changeRule($(this));
			});
			// formato percentual
			$(document).on('keyup', '.type-percentual', function(){
				var v = number_percentual($(this).val());	
				$(this).val(v);
				return false;										   
			});
        },
        
        addItem: function($this){
            var self = this,
                contexto = $this.parent().parent().parent(),
                input_group = $this.closest('.group-option').clone(),
                next = 0,
                name = '';
                
                if(contexto.attr('class') == 'preco-group'){
                    next = self.preco_ttl + 1;
                    self.preco_ttl = next;
                    name = 'preco_opt_';
                } else if(contexto.attr('class') == 'cupom-group'){
                    next = self.cupom_ttl + 1;
                    self.cupom_ttl = next;
                    name = 'cupom_opt_';
                }  
                
                    
             // insere
             contexto.append(input_group);
             self.animateIn(input_group);
             // altera o name e value
             $('.group-option', contexto).last()
             .find(':input').attr('name', name+next+'[]').val('');
             
             // troca o ícone para trash
             $this.removeClass('add-multi').addClass('remove-multi')
             .find('i').removeClass('icon-plus').addClass('icon-trash');
             
             // atualiza ID
             $('div.id', input_group).text('#'+next);
			 
			 // ativa datepicker
			$('.type-data', contexto).removeClass('hasDatepicker').removeAttr('id').datepicker({ 		
				beforeShow: function( selectedDate ) {                
					$(this).datepicker( "option", "minDate", new Date() );
				}
			});
			
             
            
               //console.log(newinput);
               //return false;
               
        },
        
        removeItem: function($this){
            var self = this,
                group_option = $this.closest('.group-option'),
                id = group_option.find('input').first().val(),
                contexto = $this.parent().parent().parent();
            
            // acrescenta ID para remover
            var v = $('input.toremove', contexto).val();
            $('input.toremove', contexto).val(v+','+id);
            // remove item
            self.animateOut($this.closest('.group-option'));
        },
        
        fetchInputs: function(){
            var self = this;
            // percorre os inputs para tomar pé da quantidade
            // preços
            $('.group-option', '.preco-group').each(function(){
                self.preco_ttl++;
            });
            // cupons
            $('.group-option', '.cupom-group').each(function(){
                self.cupom_ttl++;
            });           
            
        },
        
        changeRule: function($this){
            var self = this,
                val = $this.val(),
                objeto = $this.parent().parent().find('.regra-rule');
                
           //console.log(val);
           if(val == '%'){
               objeto.removeClass('type-valor').addClass('type-percentual');
           } else if(val == 'R$'){
               objeto.removeClass('type-percentual').addClass('type-valor');
           }
            
            
        },
        
        animateIn: function(obj){
            obj.css({marginTop: -50, opacity:0}).animate({marginTop:0, opacity:1}, 500);
        },
        
        animateOut: function(obj){
            obj.animate({marginTop:-60, opacity:0}, 500, function(){
                $(this).remove();
            });
        }
    };
    Multi.init();
});