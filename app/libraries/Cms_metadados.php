<?php

class Cms_metadados
{

    /**
     * Metas registradas para usuários e conteúdo
     * @var array
     */
    protected $_metas = array(
        array('meta_key' => 'meta-videos', 'meta_type' => '', 'meta_value' => ''),
    );
    
    public $usuarioMetas = array();
    
    public $conteudoMetas = array();


    private $ci = null;

    public function __construct()
    {
        $this->ci = &get_instance();
    }
    
    public function getByUser($userId = null, $meta_key = '', $meta_type = '', $retValue = true)
    {
        if(!empty($this->usuarioMetas[$userId]))
        {
            $metas = $this->usuarioMetas[$userId];
        }
        else
        {
            $metas = $this->getAllByUser($userId);
        }
        
        return get_meta($metas, $meta_key, $meta_type, $retValue);
    }
    
    public function getByContent($contentId = null, $meta_key = '', $meta_type = '', $retValue = true)
    {
        if(!empty($this->conteudoMetas[$contentId]))
        {
            $metas = $this->conteudoMetas[$contentId];
        }
        else
        {
            $metas = $this->getAllByContent($contentId);
        }
        
        return get_meta($metas, $meta_key, $meta_type, $retValue);
    }

    public function getAllByContent($contentId = NULL)
    {
        return $this->_getMetas($contentId, 'conteudo');
    }

    public function getAllByUser($userId = null)
    {
        return $this->_getMetas($userId, 'usuario');
    }

    public function saveByUser($userId = null, $metas = array())
    {
        return $this->save($userId, $metas, 'usuario');
    }

    public function saveByContent($contentId = null, $metas = array())
    {
        return $this->save($contentId, $metas, 'conteudo');
    }

    /**
     * Retorna todas as metas de um conteúdo o usuário
     * @param type $id
     * @param type $from
     * @return array
     */
    public function _getMetas($id = null, $from = 'conteudo')
    {
        $ret = $this->ci->db->where($from . '_id', $id)
                ->order_by('ordem')
                ->get('cms_' . $from . 'metas');

        if ($ret->num_rows() == 0)
        {
            return FALSE;
        }

        $post = $ret->result_array();
        
        // salva na memória
        if($from == 'conteudo')
        {
            $this->conteudoMetas[$id] = $post;            
        }
        else
        {
            $this->usuarioMetas[$id] = $post;
        }
        
        return $post;
    }

    //------------------------------------------------------------------------

    /**
     * Busca em $_POST e salva os metaconteúdos registrados.
     * @param int $post_id
     * @param array $metas
     * @return void
     */
    public function save($post_id, $metas = array(), $from = 'conteudo')
    {
        $totalSaved = 0;        
        

        // se passar um array de metas, não percorre o $_POST
        if (!empty($metas))
        {
            // verifica se é um array simples, ou multi
            if (isset($metas['meta_key']) && is_string($metas['meta_key']))
            {
                $metas = array($metas);
            }
            // looping pelas metas
            $metaArray = array();
            foreach ($metas as $m)
            {
                $metaArray = array(
                    'meta_key' => $m['meta_key'],
                    'meta_type' => $m['meta_type'],
                    'meta_value' => $m['meta_value']
                );               

            
                // tenta atualizar
                $r = $this->updateMetas($post_id, $metaArray, $from);

                if ($r)
                    $totalSaved++;
            }
            
        }
        else
        {
            // percorre as metas registradas e busca se existe em $_POST
            foreach ($this->_metas as $meta)
            {
                if ($this->ci->input->post($meta['meta_key']))
                {

                    $metaArray = array(
                        'meta_key' => $meta['meta_key'],
                        'meta_type' => $meta['meta_type'],
                        'meta_value' => $this->ci->input->post($meta['meta_key'])
                    );

                    // tenta atualizar
                    $r = $this->updateMetas($post_id, $metaArray, $from);

                    if ($r)
                        $totalSaved++;
                }
            }
            
        }
        


        return $totalSaved;
    }

    //-----------------------------------------------------------------------

    /**
     * Recebe dados e salva metas no banco.
     * @param int $post_id
     * @param array $metas
     * @param string $from
     * @return bool
     */
    public function updateMetas($post_id, $metas, $from = 'conteudo')
    {
        
        
        $exists = $this->ci->db->where($from . '_id', $post_id)
                ->where('meta_key', $metas['meta_key'])
                ->where('meta_type', $metas['meta_type'])
                ->get('cms_' . $from . 'metas');


        if ($exists->num_rows() == 0)
        {

            $metas[$from . '_id'] = $post_id;
            $ret = $this->ci->db->insert('cms_' . $from . 'metas', $metas);
        }
        else
        {
            $old = $exists->row_array();
            $ret = $this->ci->db->where('id', $old['id'])
                    ->update('cms_' . $from . 'metas', $metas);
        }
        
        

        return $ret;
    }

    
    public function addMetaFields($metas = array())
    {
        // verifica se é um array simples, ou multi
        if (isset($metas['meta_key']) && is_string($metas['meta_key']))
        {
            $metas = array($metas);
        }

        foreach ($metas as $m)
        {
            $exists = false;
            // percorre $this->_metas para não sobreescrever
            foreach ($this->_metas as $meta)
            {
                if($meta['meta_key'] == $m['meta_key'] && $meta['meta_type'] == $m['meta_type']){
                    $exists = true;
                }
            }
            
            // se não existe adiciona
            if($exists === false && $m['meta_key'] != '')
            {
                $this->_metas[] = $m;
            }
        }
    }
    
    public function getMetas()
    {
        return $this->_metas;
    }

}