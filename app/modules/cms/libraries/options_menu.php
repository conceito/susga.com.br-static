<?php

/**
 * opções extra dos conteúdos
 */
class Options_menu {

    protected $ci;

    /**
     * order, label, url, icon, co
     * 
     * @var array
     */
    protected $items = array();

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    /**
     * Render the menu
     */
    public function make()
    {       
        if (count($this->items))
        {
//            dd($this->items);
            $this->prepareItems();
            
            $v['items'] = $this->items;
            return $this->ci->load->view('cms/partials/options_menu', $v, true);
        }
        return '';
    }

    /**
     * Interface pública para adicionar itens no menu
     * 
     * @param array $item
     */
    public function add(array $item)
    {
        if (isset($item['url']))
        {
            $this->addItem($item);
        }
        else if (isset($item[0]))
        {
            foreach ($item as $i)
            {
                $this->addItem($i);
            }
        }
    }
    
    /**
     * prebuilt item
     * 
     * link para duplicar conteúdo
     * 
     * @param type $co
     * @param type $id
     * @param type $moduleSlug
     */
    public function addDuplicate($co, $id, $moduleSlug = 'post')
    {
        if(!is_numeric($co) || !is_numeric($id))
        {
            return false;
        }
        $this->addItem(array(
            'order' => 10,
            'label' => 'Duplicar conteúdo',
            'url' => "{$moduleSlug}/duplicar/co:{$co}/id:{$id}",
            'icon' => '',
            'co' => $co
        ));
    }
    
    /**
     * prebuilt item
     * 
     * link para transformar em banner
     * 
     * @param type $co
     * @param type $id
     * @param type $moduleSlug
     */
    public function addBannerize($co, $id)
    {
        if(!is_numeric($co) || !is_numeric($id))
        {
            return false;
        }
        $this->addItem(array(
            'order' => 10,
            'label' => 'Gerar banner deste conteúdo',
            'url' => "banner/bannerize/co:{$co}/id:{$id}",
            'icon' => '',
            'co' => $co
        ));
    }

    /**
     * Adiciona os itens no atributo da classe
     * 
     * @param array $item
     */
    private function addItem($item)
    {
        $tmp = array(
            'order' => $this->getOrder($item),
            'label' => $this->getLabel($item),
            'url' => $this->getUrl($item),
            'icon' => $this->getIcon($item),
            'co' => $this->getCo($item)
        );

        $this->items[] = $tmp;
    }

    private function getOrder($item)
    {
        if (isset($item['order']))
        {
            return $item['order'];
        }
        else
        {
            return 10;
        }
    }

    private function getLabel($item)
    {
        if (isset($item['label']))
        {
            return $item['label'];
        }
        else
        {
            return '(link)';
        }
    }

    private function getUrl($item)
    {
        if (isset($item['url']))
        {
            return cms_url('cms/' . trim($item['url'], '/'));
        }
        else
        {
            return '#';
        }
    }

    private function getIcon($item)
    {
        if (isset($item['icon']))
        {
            return $item['icon'];
        }
        else
        {
            return '';
        }
    }

    private function getCo($item)
    {
        if (isset($item['co']))
        {
            return $item['co'];
        }
        else
        {
            return '';
        }
    }

    
    private function prepareItems()
    {
        $order = array();

        foreach ($this->items as $k => $o)
        {
            $order[$k] = $o['order'];
        }
        array_multisort($order, SORT_ASC, $this->items);
    }

}