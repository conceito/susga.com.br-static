<?php
/**
 * Para utilizar Banners:
 * 
 * // caso queira o retorno como um array
 * $this->cms_banner->parseHtml = false;
 *  
 * // obtem o retorno dos banners para o grupo passado como parâmetro
 * $this->cms_banner->getBanners(42); 
 * 
 * // redireciona e soma click
 * $this->cms_banner->redirect($banner_id);
 * 
 */
class Cms_banner{
    
    private $ci = NULL;
    public  $banner_modulo_id = 40;
    public  $parseHtml = true; // ao retornar gera o código HTML
    public  $group = false; // dados do grupo, como dimenções

    public function __construct() {
        $this->ci = &get_instance();
    }
    
    /**
     * Retorna os banners de um grupo.
     * Por padrão retorna com HTML pronto.
     * 
     * @param type $group_id
     * @param type $limit Pode impor um limite na exibição.
     * @return type 
     */
    public function getBanners($group_id, $limit = false){
        
        $this->group = $this->_getGroupById($group_id);
        
        
        $this->ci->db->from('cms_conteudo as conteudo');
        $this->ci->db->select('conteudo.id, conteudo.titulo, conteudo.resumo, conteudo.txt, banner.target, banner.views, banner.clicks, banner.limit');
        $this->ci->db->join('cms_banner as banner', 'conteudo.id = banner.conteudo_id');
        $this->ci->db->where('conteudo.grupo', $group_id);
        $this->ci->db->where('conteudo.status', 1);
        $this->ci->db->where('conteudo.dt_ini <=', date("Y-m-d"));
        $this->ci->db->where("(conteudo.dt_fim >= '".date("Y-m-d")."' OR conteudo.dt_fim = '0000-00-00') ");
        
        if($limit){
            $this->ci->db->limit($limit);
        }
        
        if($this->group['txt'] == 'ordem'){
             $this->ci->db->order_by('conteudo.ordem');
        } else {
            $this->ci->db->order_by('rand()');
        }
        
        
        $result = $this->ci->db->get();
        
//        mybug($this->ci->db->last_query());
        
        $banners = $result->result_array();
        $parseado = $this->parseBanners($banners);
        
        if($this->parseHtml){
            return $this->parseHtmlTemplate($parseado);
        } else {
            return $parseado;
        }     
        
        
    }
    
    /**
     * Parseia os banners do grupo ajustando os indexes de forma mais legível.
     * Remove banners que ultrapassaram o limite de views.
     * Contabiliza view.
     * 
     * Array de retorno:
     * 'id'     => '44' 
       'titulo' => 'Banner do cliente'
       'target' => '_blank' 
       'views'  => '45' 
       'clicks' => '0' 
       'limit'  => '777'
       'uri'    => 'banner/44' 
       'url'    => 'http://www.brunobarros.com'
       'file'   => 'http://localhost/meucms/upl/arqs/homepage.swf'
       'tipo'   => 'swf'
     * 
     * @param type $array
     * @return boolean|string 
     */
    public function parseBanners($array){
        
        if(count($array) == 0) return false;
        
        $return = array();
        
        foreach($array as $banner){
            
            // verifica se existe limite de visualizações
            if($banner['views'] >= $banner['limit'] && $banner['limit'] > 0){
                continue;
            } else {
                // se passou acrescenta uma visualização
                $this->ci->db->where('conteudo_id', $banner['id']);
                $this->ci->db->update('cms_banner', array('views' => $banner['views']+1));
            }
            // classifica tipo de banner: img | swf
            $ext = explode('.', $banner['resumo']);
            $ext = strtolower($ext[count($ext)-1]);
            $banner['tipo'] = ($ext == 'swf') ? 'swf' : 'img';
            
            // acrescenta a url de redirecionamento
            $banner['uri'] = "banner/redirect/" . $banner['id'];
            // corrige index
            $banner['url'] = $banner['txt'];
            unset($banner['txt']);
            // endereço da imagem
            $banner['file'] = base_url().$this->ci->config->item('upl_arqs').'/'.$banner['resumo'];
            unset($banner['resumo']);
            
            
            $return[] = $banner;
            
        }
        
        return $return;
        
    }
    
    /**
     * Monta saida HTML para banners.
     * @param type $array
     * @return string 
     */
    public function parseHtmlTemplate($array){
        
        if(empty($array)) return '';
        
        $html = '<ul class="banners-list unstyled">';
        
        foreach($array as $b){
            
            if($b['tipo'] == 'img'){
                $html .= '<li>';
                $html .= '<a href="'. site_url($b['uri']).'" title="'.$b['titulo'].'" target="'.$b['target'].'">';
                $html .= '<img src="'.$b['file'].'" alt="'.$b['titulo'].'" />';
                $html .= '</a>';
                $html .= '</li>';
            } else {
                $html .= '<li class="flash-banner" rel="'.$b['file'].'|'.$this->group['w'].'|'.$this->group['h'].'|'. site_url($b['uri']).'">'.$b['titulo'].'</li>';
            } 
            
        }
        
        $html .= '</ul>';
        
        return $html;
        
    }

    /**
     * Retorna dados essenciais do grupo de banners.
     * 
     * 'txt'  => 'ordem' 
       'tags' => '728x90' 
       'w'    => '728' 
       'h'    => '90' 
     * 
     * @param type $group_id
     * @return array 
     */
    private function _getGroupById($group_id){
        
        $this->ci->db->where('id', $group_id);
        $this->ci->db->select('txt, tags');
        $row = $this->ci->db->get('cms_conteudo');
        $group = $row->row_array();
        
        // dimensões
        $dim = explode('x', $group['tags']);
        $group['w'] = $dim[0];
        $group['h'] = $dim[1];
        
        if($row->num_rows() == 0){
            return false;
        } else {
            return $group;
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Redireciona usuário e soma um click
     * @param type $banner_id
     */
    public function redirect($banner_id){
        
        
        $this->ci->db->from('cms_conteudo as conteudo');
        $this->ci->db->select('conteudo.id, conteudo.txt, banner.target, banner.views, banner.clicks, banner.limit');
        $this->ci->db->join('cms_banner as banner', 'conteudo.id = banner.conteudo_id');
        $this->ci->db->where('conteudo.id', $banner_id);
        
        $result = $this->ci->db->get();
        
        if($result->num_rows() == 0){
            return FALSE;
        }
        
        $banner = $result->row_array();
        
        $this->add_click($banner_id, $banner['clicks']);

        if(strtolower(substr($banner['txt'], 0, 3)) === 'www')
        {
            $url = prep_url($banner['txt']);
        }
        else
        {
            $url = $banner['txt'];
        }

        redirect($url);
    }

    // -------------------------------------------------------------------------
    /**
     * 
     * @param type $banner_id
     */
    public function add_click($banner_id, $clicks){
        
        $plusone = $clicks + 1;
        $this->ci->db->update('cms_banner', array('clicks'=>$plusone), array('conteudo_id' => $banner_id));
        
    }
    
    
    
}