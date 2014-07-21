$(document).ready(function(e) {
    
	var Prod = {
		
		init: function(){
			var self = this;
			
			self.checkProdType();
			
			$('#download0, #download1').on('click', function(e){
				self.updateProdType();
			});
			
		},
		
		checkProdType: function(){
			var self = this,
				is_fisic = $('#download0').is(':checked');
				
			self.updateInterface(is_fisic);
			
			
		},
		
		updateProdType: function(){
			var self = this,
			is_fisic = $('#download0').is(':checked');
			
			self.updateInterface(is_fisic);
		},
		
		updateInterface: function(is_fisic){
			if(is_fisic){
				$('.for-fisic-prod').show();
				$('.for-digit-prod').hide();
			} else {
				$('.for-fisic-prod').hide();
				$('.for-digit-prod').show();
			}
		}
		
	};
	
	Prod.init();
	
});