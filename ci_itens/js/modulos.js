$(document).ready(function(){
	
    /************************************************************
    * Adiciona e remove itens no multi conteúdo
    */
    var Multi = {
        
        ttl: 0,
        
        init: function(){
            var self = this;
            this.fetchInputs();
            // add
            $(document).on('click', '.add-multi', function(e){
                e.preventDefault();                
                self.addItem($(this)); 
            });
            // remove
            $(document).on('click', '.remove-multi', function(e){
                e.preventDefault();                
                self.removeItem($(this)); 
            });
        },
        
        addItem: function($this){
            var self = this,
                input_group = $this.closest('.multi-option').clone(),
                next = self.ttl + 1;
                
                self.ttl = next;
                    
             // insere
             $('.multi-options').append(input_group);
             // altera o name e value
             $('.multi-option', '.multi-options').last()
             .find('input').attr('name', 'multi_'+next).val('');
             
             // troca o ícone para trash
             $this.removeClass('add-multi').addClass('remove-multi')
             .find('i').removeClass('icon-plus').addClass('icon-trash');
             
            
               //console.log(newinput);
               //return false;
               
        },
        
        removeItem: function($this){
            var self = this;
            
            $this.closest('.multi-option').remove();
        },
        
        fetchInputs: function(){
            var self = this;
            // percorre os inputs para tomar pé da quantidade
            $('input', '.multi-options').each(function(){
                self.ttl++;
            });           
            
        }
    };
    Multi.init();
    
    
	// atualiza combo de grupos de um determinado móduilo
	// * depreciado: usa apenas o módulo
	/*
	$('#modulos').change(function(){
		
		var id = $(this).val();
		//alert(id);
		if(id == 0){
			$('.cb-grupos').html('<input type="hidden" value="" name="grupos" />');	
		} else {
			// chamada AJAX para carregar combo de grupos. 
			$.ajax({
				type: "POST",
				url: V['site_url']+"cms/cmsutils/comboGrupos/"+id+"/",
				//data: "id_uf=" + id_uf,
				dataType: ($.browser.msie) ? "text" : "html",
				
				beforeSend: function() {
					// msg de carregando				
					alerta('Aguarde...');
				},
				success: function(message)
				{						
					
					//alert(message);
					if (message.length > 10){	
						
						$('.cb-grupos').html(message);
					} else {
						$('.cb-grupos').html('<input type="hidden" value="" name="grupos" />');						
					}
					
				}
			});
		}
		
		
	});
	*/
	
});