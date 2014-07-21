$(document).ready(function(){
    
    /*********************************************
    * Controle da criação de um grupo
    */
    var Bannergrupo = {
        
        estadoWraper: 'closed',
        box: $('.box-t-a'),
        wraper: $('.box-inner', this.box),
        
        init: function(){
            var self = this;
            // fecha box
            this.wraper.hide();
            // botão de criar grupo
            $('.btn-success', '#barra-botoes').on('click', $.proxy(this.openCloseWraper, this));
            $('.title', this.box).on('click', $.proxy(this.openCloseWraper, this));
            $('.panel-right', this.box).css({width:'39%'});
            // tamanhos listener
            $('#banners_type', this.box).on('change', $.proxy(this.setTamanhos, this));
            // cancelar listener
            $('.btn-cancel-new-group', this.box).on('click', $.proxy(this.cancel, this));
        },
        
        openCloseWraper: function(e){
            e.preventDefault();
            
            if(this.estadoWraper == 'closed'){
                this.wraper.show();
                this.estadoWraper = 'opend';
            } else {
                this.wraper.hide();
                this.estadoWraper = 'closed';
            }
            
        },
        
        setTamanhos: function(){
            
            var self = this
                val = $('#banners_type', this.box).val();
                
            // quebra o valor
            if(val == 0){
                $('#banner_width', self.box).val('').focus();
                $('#banner_height', self.box).val('');
            } else {
                var v = val.split('x');
                $('#banner_width', self.box).val(v[0]);
                $('#banner_height', self.box).val(v[1]);
            }
            //console.log(val);
            
        },
        
        cancel: function(){
            // limpa form
            $('input, textarea', this.box).val('');
            this.wraper.hide();
            this.estadoWraper = 'closed';
        }
    };
    
    Bannergrupo.init();
    
    $('#frm').validate({
        
        rules:{
            titulo:{
                required:true
            },
            banner_width:{
                required:true,
                number: true,
                maxlength:4
            },
            banner_height:{
                required:true,
                number: true,
                maxlength:3
            }
        },
        
        messages:{
            titulo:{
                required:"Obrigatório"
            },
            banner_width:{
                required:"Obrigatório",
                number:"Apenas números",
                maxlength:"Número grande demais"
            },
            banner_height:{
                required:"",
                number:"Apenas números",
                maxlength:"Número grande demais"
            }
            
        }
        
    });
    
    
    /*******************************************************
    *
    */
    var Bannernovo = {
        
        init: function(){
            var self = this;
            this.prepForm();
            
            // listene
            $('a[class^=open-]').on('click', function(){
                self.setPropertySourse($(this))
            });
        },
        
        prepForm: function(){
            $('.url-via-conteudo').hide();
        },
        
        setPropertySourse: function(obj){
            var self = this,
                classId = obj.attr('class').substr(5);
            
            // fecha as opções
            $('div[class^=url-]:not(.url-'+classId+')').slideUp();
            // abre
            $('.url-'+classId).slideDown();
            
            $('input[name=flag]').val(classId);
        }
        
    };
    //Bannernovo.init();
    
});