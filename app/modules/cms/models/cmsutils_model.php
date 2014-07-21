<?php

class Cmsutils_model extends CI_Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * gera o combo para páginas de um determinado módulo.
     * 
     * @param type $modulo_id
     * @param type $tabela
     * @return type 
     */
    function getComboConteudoFromModulo($modulo_id, $dadosConteudo = '', $tabela = 'cms_conteudo'){
        
        $this->db->where('lang', get_lang());
        $this->db->where('modulo_id', $modulo_id);
        $this->db->where('grupo !=', 0);
        $this->db->where('tipo', 'conteudo');
        $this->db->where('status', 1);
        $this->db->order_by('titulo');
        $sql = $this->db->get('cms_conteudo');


        $menus['0'] = 'escolha';// init

        foreach ($sql->result_array() as $i) {
            if ($tabela == 'cms_usuarios') {
                $label = $i['nome'];
            } else {
                $label = $i['titulo'];
            }
            $menus[$i['id']] = $label;
        }
        
        // gera os ids já relaionados neste conteúdo
        if ($dadosConteudo != '' && $tabela == 'cms_conteudo'){
            $ids = explode(',', $dadosConteudo['rel']);
        } else {
            $ids = '';
        }
        
        $cb = $this->cms_libs->cb($ids, $menus, 'rel', false);
        return $cb;

    }
    
}