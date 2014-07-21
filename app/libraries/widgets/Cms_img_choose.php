<?php

/**
 * 
 * O Model deve implementar 'Cms_img_choose_interface'
 * require FCPATH . app_folder() . 'libraries/Cms_img_choose_interface.php';
 */

require APPPATH . 'libraries/widgets/WidgetsInterface.php';

class Cms_img_choose implements WidgetsInterface{
    
     protected $ci;
     
     /**
      * Identificador
      * @var string
      */
     protected $widgetId = 'img-choose';


     /**
      * Relative to de root of project
      * @var string
      */
     protected $imgFolder = '';
     
     /**
      * Extenções permitidas
      * @var array
      */
     protected $imgAlowedTypes = array('jpg', 'jpeg', 'png');
     
     /**
      * Metadados onde será armazenada a seleção
      * @var array
      */
     protected $metas = array('meta_key' => '', 'meta_type' => '', 'meta_value' => '');
     
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
         $this->ci->load->helper('file');
         
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
        if(isset($options['img_folder']))
        {
            $this->registerImgFolder($options['img_folder']);
        }
        if(isset($options['allowed_types']))
        {
            $this->setAllowedTypes($options['allowed_types']);
        }
        if(isset($options['content_id']))
        {
            $this->setContentId($options['content_id']);
        }
        
        /*
         * Render the widget on global widget point
         */
        $scope->widgets[$this->widgetId] = $this->getWidget();
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
     
     /**
      * Registra a pasta onde as imagens estão disponíveis.
      * Todas as imagens serão usadas como opção, a não ser que comecem com '_'.
      * 
      * @param type $relativePath
      */
     public function registerImgFolder($relativePath = 'assets/img/choose')
     {
         $this->imgFolder = $relativePath;
     }
     
     
     /**
      * Seta as extenções retornadas por $this->getImages()
      * @param array $types
      */
     public function setAllowedTypes($types = array())
     {
         if(!is_array($array))
         {
             $types = explode(',', $types);
         }
         
         $this->imgAlowedTypes = $types;
     }
     
     
     public function setContentId($id)
     {
         $this->contentId = $id;
     }


     /**
      * Loop through the folder and return all images (jpeg, png)
      * @param type $someRegexFilter
      */
     public function getImages($someRegexFilter = null)
     {
         if(strlen($this->imgFolder) == 0)
         {
             return false;
         }
         $files = get_filenames($this->imgFolder);

         // empty
         if(!$files )
         {
             return false;
         }
         
         $return = array();
         foreach ($files as $file)
         {
             if(in_array($this->getExt($file), $this->imgAlowedTypes))
             {
                $return[] = array(
                    'file_name' => $file,
                    'full_path' => base_url() . $this->imgFolder . '/' . $file,
                ); 
             }
         }
         
         return $return;
     }
     
     
     /**
      * Template do widget
      * @return string
      */
     public function getWidget()
     {
         // imagens
         $v['images'] = $this->getImages();
         $v['metas'] = $this->metas;
         $v['selected'] = $this->getMetaValueFromContent();
         
         if(! $v['images'])
         {
             return '';
         }
         // carrega assets
         
         // template
         return $this->ci->load->view('cms/widgets/img_choose/img_choose', $v, true);
         
         
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




     /**
      * Retorna a extenção do srquivo
      * @param string $file
      * @return string
      */
     private function getExt($file)
     {
         $f = explode('.', $file);
         return $f[count($f) - 1];
     }

    
}