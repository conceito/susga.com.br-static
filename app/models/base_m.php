<?php

/**
 * Interface for common data jobs
 *
 * $this->base->mod(1)->base('news')->take(3)->order('cont.ordem')->getAll();
 */
class Base_m extends CI_Model
{
    /**
     * defaults
     */
    protected $moduloId = 7;
    protected $categoryId = null;
    protected $perPage = 10;
    protected $order = 'cont.dt_ini DESC';
    protected $baseUrl = 'noticias';
    protected $activeCategory = null;
    protected $fields = 'id, nick, full_uri, titulo, resumo, dt_ini, galeria, modulo_id';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * alias for setFields()
     */
    public function fields($fields)
    {
        return $this->setFields($fields);
    }

    /**
     * alias for setModuleId()
     */
    public function mod($mId)
    {
        return $this->setModuleId($mId);
    }

    public function setModuleId($mId)
    {
        $this->moduloId = $mId;

        return $this;
    }

    /**
     * alias for setPerPage()
     */
    public function take($int)
    {
        return $this->setPerPage($int);
    }

    /**
     * alias for setOrder()
     */
    public function order($order)
    {
        return $this->setOrder($order);
    }

    /**
     * alias for setBaseUrl()
     */
    public function base($base)
    {
        return $this->setBaseUrl($base);
    }

    /**
     * alias for setCategory()
     */
    public function cat($catSlugId)
    {
        return $this->setCategory($catSlugId);
    }

    public function setCategory($catSlugId)
    {
        if (is_string($catSlugId))
        {
            $this->db->where('modulo_id', $this->getModuleId());
            $this->db->where('nick', $catSlugId);
            $this->db->where('status', 1);
            $this->db->where('grupo', 0);
            $this->db->where('lang', 'pt');
            $this->db->where('tipo', 'conteudo');
            $this->db->select('id, titulo');
            $post  = $this->db->get('cms_conteudo');

            $catId = null;
            if($post->num_rows())
            {
                $cat   = $post->row_array();
                $this->setActiveCategory($cat);
                $catId = $cat['id'];
            }

        }
        else
        {
            if (is_numeric($catSlugId))
            {
                $catId = $catSlugId;
            }
        }

        $this->categoryId = $catId;

        return $this;

    }

    public function getModuleId()
    {
        return $this->moduloId;
    }

    /**
     * get all posts from all categories
     * @return array
     */
    public function getAll()
    {
        $posts = $this->cms_posts->get(array(
            'modulo_id'   => $this->getModuleId(),
            'per_page'    => $this->getPerPage(),
            'base_url'    => $this->getBaseUrl(),
            'ordem'       => $this->getOrder(),
            'gallery_tag' => false,
            'campos'      => $this->getFields()
        ));

        return $posts;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function setPerPage($int)
    {
        $this->perPage = $int;

        return $this;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setBaseUrl($base)
    {
        $this->baseUrl = $base;

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order = 'cont.dt_ini DESC')
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $fields
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * get all categories
     * @return array
     */
    public function getCategories()
    {
        $this->db->where('modulo_id', $this->getModuleId());
        $this->db->where('status', 1);
        $this->db->where('grupo', 0);
        $this->db->where('lang', 'pt');
        $this->db->where('tipo', 'conteudo');
        $this->db->order_by('ordem, titulo');
        $this->db->select($this->getFields());
        $post = $this->db->get('cms_conteudo');

        return $this->parseCategories($post->result_array());

        return $posts;
    }

    public function parseCategories($array)
    {
        if (!is_array($array))
        {
            $categories = array($array);
        }
        else
        {
            $categories = $array;
        }
        $return = array();

        $u2 = $this->uri->segment(2);
        $u3 = $this->uri->segment(3);

        foreach ($categories as $cat)
        {
            $cat['active'] = '';
            if ( ($u2 && $u2 == $cat['nick']) || ($u3 && $u3 == $cat['nick']) )
            {
                $cat['active'] = 'active';
                $this->setActiveCategory($cat);
            }
            $cat['full_uri'] = $this->getBaseUrl() . '/categoria/' . $cat['nick'];
            $return[]        = $cat;
        }

        return $return;
    }

    public function getActiveCategory()
    {
        return $this->activeCategory;
    }


    public function setActiveCategory($catObj)
    {
        $this->activeCategory = $catObj;

        return $this;
    }


    /**
     * get all from category
     * @return array
     */
    public function getFromCategory()
    {
        $posts = $this->cms_posts->get(array(
            'modulo_id' => $this->getModuleId(),
            'per_page'  => $this->getPerPage(),
            'base_url'  => $this->getBaseUrl(),
            'ordem'     => $this->getOrder(),
            'grupo_id'  => $this->getCategory(),
            'campos'    => $this->getFields()
        ));

        return $posts;
    }

    public function getCategory()
    {
        return $this->categoryId;
    }

    /**
     * get the post
     * @param type $postId
     */
    public function find($postId)
    {

    }

    /**
     * back to the default values
     * @return $this
     */
    public function end(){
        $this->setFields('id, nick, full_uri, titulo, resumo, dt_ini, galeria, modulo_id');
        $this->setOrder('cont.dt_ini DESC');
        $this->setActiveCategory(null);
        $this->setPerPage(10);
        return $this;
    }

}