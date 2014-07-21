<?php
/**
 * Manipula as opções do CMS > Administração > Preferências
 * 
 * 1º) Defina as variáveis em "modules/cms/models/admin_model.php"
 * 2º) Monte o template em "modules/cms/views/prefs/principais.php"
 * 3º) Instancie $this->load->library('cms_options'); no Frontend_Controller ou autopload.php
 * 4º) Use opt('option-key', ['default value', 'parse html']);
 */
class Cms_options {
    
     private $ci;
     
     protected $configs = array();
     
     public function __construct()
     {
         $this->ci = &get_instance();
         $this->ci->load->model('cms/admin_model', 'admin_model');
         $this->fetchAll();
     }
     
     public function get($id = null, $default = '', $parseHtml = false)
     {
         if($id === null)
         {
             return $this->getAll();
         }
         
         if(isset($this->configs[$id]) && strlen($this->configs[$id]) > 0)
         {
             if($parseHtml || (is_bool($default) && $default))
             {
                 return $this->parseHtml($this->configs[$id]);
             }
             else
             {
                 return $this->configs[$id];
             }
         }
         else
         {
             return (string)$default;
         }         
         
     }


     public function getAll()
     {
         return $this->configs;
     }
     
     
     public function fetchAll()
     {
         if(!empty($this->configs))
         {
             return $this->configs;
         }
         $this->configs = $this->ci->admin_model->get_prefs();
         return $this->configs;
     }
     
     
     private function parseHtml($str)
     {
         return nl2br($str);
     }
    
}