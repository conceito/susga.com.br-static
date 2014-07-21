<?php

/**
 * 
 * O Model deve implementar 'Cms_img_choose_interface'
 * require FCPATH . app_folder() . 'libraries/Cms_img_choose_interface.php';
 */

require APPPATH . 'libraries/widgets/WidgetsInterface.php';

class Widget_color_picker implements WidgetsInterface{
    
     protected $ci;
     
     /**
      * Identificador
      * @var string
      */
     protected $widgetId = 'color-picker';


     
     /**
      * Metadados onde será armazenada a seleção
      * @var array
      */
     protected $metas = array('meta_key' => 'color-picker', 'meta_type' => '', 'meta_value' => '');
     
     /**
      * ID do conteúdo sendo editado
      * @var int
      */
     protected $contentId = null;


     /**
      * Model responsável pelo conteúdo
      * @var Model
      */
     protected $model = null;


     public function __construct()
     {
         $this->ci = &get_instance();
//         $this->ci->load->helper('file');
         
     }
     
     /**
      * Recebe o escopo (controller) e insere no array global $this->widgets
      * 
      * @param Controller $scope
      * @throws Exception
      */
     public function registerWidget($scope, $options = null)
    {
        if(isset($scope->widgets[$this->widgetId]))
        {
            throw new Exception("O widget '{$this->widgetId}' já foi registrado.");
        }
        
        $scope->widgets = array_merge($scope->widgets, array($this->widgetId));
        
        // opcionais short-hand
        if(isset($options['model']))
        {
            $this->registerModel($options['model']);
        }
        if(isset($options['meta_key']))
        {
            $this->registerMeta($options['meta_key']);
        }
        if(isset($options['content_id']))
        {
            $this->setContentId($options['content_id']);
        }
        
        /*
         * Render the widget on global widget point
         */
        $scope->widgets[$this->widgetId] = $this->getWidget();

        $scope->setNewPlugin(array('colorpicker'));
    }
     
     
     /**
      * Salva instancia do model para posterior manipulação
      * @param Model $model
      */
     public function registerModel($model)
     {        
         $this->model = $model;
     }
     
     
     /**
      * Registra metadado no conteúdo.
      * 
      * @param type $ketaKey
      * @param type $metaType
      * @param type $defaultValue
      */
     public function registerMeta($metaKey = '', $metaType = '', $defaultValue = '')
     {
         if($this->model === null)
         {
             throw new Exception('O Model não foi passado!');
         }
         $this->metas = array('meta_key' => $metaKey, 'meta_type' => $metaType, 'meta_value' => $defaultValue);
         $this->model->setMetas($this->metas);
     }
     

     

     
     public function setContentId($id)
     {
         $this->contentId = $id;
     }


     
     
     /**
      * Template do widget
      * @return string
      */
     public function getWidget()
     {
         // imagens
         $v['metas'] = $this->metas;
         $v['selected'] = $this->getMetaValueFromContent();

         // carrega assets
         
         // template
         return $this->ci->load->view('cms/widgets/color_picker/color_picker', $v, true);
         
         
     }
     
     public function getMetaValueFromContent()
     {
         if($this->contentId === null)
         {
             return '';
         }
         $allMetas = $this->model->getPostMetas(array('id' => $this->contentId));
 
         if(! $allMetas)
         {
             return '';
         }
         
         $val = '';
         foreach($allMetas as $meta)
         {
             if($meta['meta_key'] === $this->metas['meta_key'] && $meta['meta_type'] === $this->metas['meta_type'] )
             {
                 $val = $meta['meta_value'];
             }
         }
         
         return $val;
     }




    
}