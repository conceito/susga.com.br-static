$(document).ready(function(){
	/** ========================================================================
	 * 	relacionamento combobox steps e groups
	 * ------------------------------------------------------------------------
	 */
	$comboSteps = $('select[name=survey_steps]');
	$comboGroups = $('select[name=survey_groups]');

	var comboSteps = {

		selected:0,

		init: function(){
			$comboSteps.on('change', this.setSelected);
		},

		setSelected: function(){
			var s = $comboSteps.find(":selected");
			this.selected = $(s).val();
			comboGroups.update(this.selected);
		},
	};
	

	var comboGroups = {

		options: null,

		init: function(){
			this.options = $comboGroups.find('option[data-step-id]');
			this.clear();
		},

		clear: function(){
			var selected = this.getSelected(),
			val = parseInt(selected.val(), 10),
			sStepId = selected.data('step-id');			

			this.options.each(function(index, ele){
				var opt = $(ele);
				var stepId = opt.data('step-id');
				if(sStepId !== stepId){
					opt.hide();					
				}
			});
			
		},

		update: function(stepId){

			$comboGroups.find('option').first().prop('selected', true);
			this.options.each(function(index, ele){
				var opt = $(ele);
				var optId = opt.data('step-id');

				if(optId == stepId){
					opt.show();
				} else {
					opt.hide();
				}
				
			});

			this.shake();
		},

		getSelected: function(){
			return $($comboGroups.find(":selected"));
		},

		shake: function(){
			$comboGroups.stop().animate({marginLeft: 10}, 300, function(){
				$comboGroups.animate({marginLeft:0}, 300);
			});
		},
	};

	comboGroups.init();
	comboSteps.init();


	/** ========================================================================
	 * 	Comment
	 * ------------------------------------------------------------------------
	 */
	var OptionsAnswer = {
        
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
             //$this.removeClass('add-multi').addClass('remove-multi').find('i').removeClass('icon-plus').addClass('icon-trash');
             // remove o ícone
             $this.remove();
            
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
    OptionsAnswer.init();


});