<?php
/**
 *
 *
 * @package CodeIgniter
 * @subpackage MY_Model
 * @link http://github.com/jamierumbelow/codeigniter-base-model
 */

class MY_Model extends CI_Model
{

    /**
     * The database table to use, only
     * set if you want to bypass the magic
     *
     * @var string
     */
    protected $_table;

    /**
     * Meta dados registrados
     * @var type
     */
    protected $_metas = array(
        array('meta_key' => 'priority', 'meta_type' => '', 'meta_value' => ''),
    );

    /**
     * The class constructer, tries to guess
     * the table name.
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function setMetas($metas = array())
    {
        if(!is_array($metas))
        {
            return false;
        }
        if(isset($metas['meta_key']))
        {
            $metas = array($metas);
        }
        $this->_metas = array_merge($this->_metas, $metas);
    }

    public function getMetas()
    {
        return $this->_metas;
    }
    /**
     * Busca pelos POSTs e salva os metaconteúdos registrados.
     * @param int $post_id
     * @param array $metas
     * @return void
     */
    public function saveMetas($post_id, $metas = array())
    {
        // se passar um array de metas, não percorre o POST
        if (!empty($metas)) {

            $metaArray = array(
                'meta_key' => $metas['meta_key'],
                'meta_type' => $metas['meta_type'],
                'meta_value' => clean_html_to_db($metas['meta_value'])
            );

            // tenta atualizar
            $this->_updateMetas($post_id, $metaArray);

            return;
        }

        //        dd($this->getMetas());
        // percorre as metas registradas
        foreach ($this->getMetas() as $meta)
        {
            if ($this->input->post($meta['meta_key']) !== false) {

                $metaArray = array(
                    'meta_key' => $meta['meta_key'],
                    'meta_type' => $meta['meta_type'],
                    'meta_value' => clean_html_to_db($this->input->post($meta['meta_key']))
                );

                // tenta atualizar
                $this->_updateMetas($post_id, $metaArray);
            }
        }
    }

    //-----------------------------------------------------------------------

    /**
     * Recebe dados e salva metas no banco.
     *
     * @param int $post_id
     * @param array $metas
     */
    public function _updateMetas($post_id, $metas)
    {
        $this->db->where('conteudo_id', $post_id);
        $this->db->where('meta_key', $metas['meta_key']);

        if(strlen($metas['meta_type']))
        {
            $this->db->where('meta_type', $metas['meta_type']);
        }

        $exists = $this->db->get('cms_conteudometas');


        if ($exists->num_rows() > 0)
        {
            $oldMeta = $exists->row_array();

            $this->db->where('id', $oldMeta['id'])
                ->update('cms_conteudometas', $metas);
        }
        else
        {

            $metas['conteudo_id'] = $post_id;
            $this->db->insert('cms_conteudometas', $metas);
        }
    }

    //------------------------------------------------------------------------

    /**
     * Recebe um array com o ID do conteúdo e retorna um array com os metadados
     *
     * @param array $vars
     * @return array|boolean
     */
    public function getPostMetas($vars)
    {
        $query = $this->db->where('conteudo_id', $vars['id'])
            ->order_by('ordem')
            ->get('cms_conteudometas');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        else {
            return false;
        }
    }

}
