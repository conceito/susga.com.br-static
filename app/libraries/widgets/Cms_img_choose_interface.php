<?php

interface Cms_img_choose_interface
{

    public function getMetas();

    public function setMetas($array);
    
    /**
     * Retorna todas as metas de um conteúdo
     * @param array $postArray $post['id']
     */
    public function getPostMetas($postArray);
    
}