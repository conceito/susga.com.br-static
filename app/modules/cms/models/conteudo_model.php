<?php

/**
*
* @version $Id$
* @copyright 2010
*/

class Conteudo_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }

    function lista_conteudos($v)
    {
        // -- trata as variaveis --//
        $pps = $this->config->item('pagination_limits');
        $offset = ($v['offset'] == '') ? 0 : $v['offset'];
        $pp = ($v['pp'] == '') ? $pps[0] : $v['pp']; // por página
        $b = $v['b'];
        $g = ($v['g'] == '') ? 0 : $v['g'];// subgrupo
        // se foi feita uma busca
        if (strlen(trim($this->input->post('q'))) > 0) {
            $b = $this->cms_libs->limpa_caracteres(trim($this->input->post('q')));
            $b = ($b == 'busca') ? '' : $b; // prevenir contra falsa busca
            $offset = 0;
        }
        // se foi feita bsca avançada
        if (strlen(trim($this->input->post('ativo'))) > 0) {
            $stt = $this->input->post('ativo');
            $offset = 0;
        } else {
            $stt = $v['stt'];
        }
        // echo '<pre>';
        // var_dump($b);
        // exit;
        if ($stt != '')$this->db->where('status', $stt);
        if ($b != '')$this->db->like('titulo', $b);
        $this->db->limit($pp, $offset);
        $this->db->order_by('dt_ini', 'titulo');
        $this->db->where('modulo_id', $v['co']);
        $this->db->where('grupo', $g);
        $sql = $this->db->get('cms_conteudo');
        // pega o Total de registros
        if ($stt != '')$this->db->where('status', $stt);
        if ($b != '')$this->db->like('titulo', $b);
        $this->db->where('modulo_id', $v['co']);
        $this->db->where('grupo', $g);
        $sql_ttl = $this->db->get('cms_conteudo');
        $ttl_rows = $sql_ttl->num_rows();
        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/conteudo/index/co:'.$v['co'].'/pp:' . $pp . '/g:' . $g . '/dt1:' . $v['dt1'] . '/dt2:' . $v['dt2'] . '/b:' . $b . '/stt:' . $stt . '/');
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 11; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
        // echo '<pre>';
        // var_dump($this->parse_menus_raiz($sql->result_array()));
        // exit;
        $saida = array('ttl_rows' => $ttl_rows,
            'rows' => $this->parse_lista_conteudos($sql->result_array()));

        return $saida;
    }

    /**
    * Prepara pesquisa de item de menu
    *
    * @param mixed $array
    * @return
    */
    function parse_lista_conteudos($array)
    {
        if (count($array) == 0) return false;
        // percorre array
        $saida = array();
        foreach($array as $row) {
            if ($row['status'] == 1)$att = 'ativo';
            else if ($row['status'] == 0)$att = 'inativo';

            if ($row['tipo'] == 0)$tipo = 'Grupo';
            else $tipo = 'Conteudo';
            // coloca no array
            $saida[] = array('id' => $row['id'],
                'titulo' => $row['titulo'],
                'tipo' => $tipo,
                'status' => $att);
        }
        return $saida;
    }
}

?>