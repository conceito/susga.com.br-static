<?php

class Banner extends Frontend_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Recebe requisição AJAX para retornar os banners.
     * 
     * @param       int     $group_id
     * @param       int     $limit
     */
    public function load($group_id = NULL, $limit = false) {
        
        $this->load->model('banner_model', 'banner');
        
        $bannersHTML = $this->banner->getBanners($group_id, $limit);
        
        echo $bannersHTML;        
        
    }

    public function redirect($id) {

        $this->load->model('banner_model', 'banner');

        $this->banner->redirect($id);
    }
    
}