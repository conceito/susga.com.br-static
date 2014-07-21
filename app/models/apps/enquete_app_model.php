<?php

class Enquete_app_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }

    function computa_voto($idEnquete, $idPergunta)
    {
        $dados = array();
        $dados['pergunta'] = $idEnquete;
        $dados['opcao'] = $idPergunta;
        $dados['data'] = date("Y-m-d");
        $dados['ip'] = $this->input->ip_address();
        $ret = $this->db->insert('cms_enquete_res', $dados);
        // incrementa o voto nas opções também
        $this->db->where('id', $idPergunta);
        $this->db->select('votos');
        $sql = $this->db->get('cms_enquete_opc');
        $ttl = $sql->row_array();
        $incrementa = $ttl['votos'] + 1;
        $dadosInc = array();
        $dadosInc['votos'] = $incrementa;
        $this->db->where('id', $idPergunta);
        $this->db->update('cms_enquete_opc', $dadosInc);

        return $ret;
    }

    function valida_user($idEnquete)
    {
        $this->db->where('ip', $this->input->ip_address());
        $this->db->where('data', date("Y-m-d")); // data hoje
        $this->db->where('pergunta', $idEnquete);
        $sql = $this->db->get('cms_enquete_res');
        if ($sql->num_rows() > 0) return true;
        else return false;
    }

    function resultado($idEnquete)
    {
        // pega as opções desta enquete
        $this->db->where('pergunta', $idEnquete);
        $sql = $this->db->get('cms_enquete_opc');
        $opc = $sql->result_array();
        $num_opcs = $sql->num_rows(); // total de opções
        // define total de vosot
        $ttlVotos = 0;
        foreach($opc as $o) {
            $ttlVotos += $o['votos'];
        }
        // percorre cada opção e contabiliza a porcentagem
        foreach($opc as $o) {

			if($ttlVotos > 0)$perc = floor(($o['votos'] * 100) / $ttlVotos);
			else $perc = 0;

            $saida[] = array('id' => $o['id'],
                'quant' => $o['votos'],
                'perc' => $perc,
                'opcao' => $o['opcao']
                );
        }

        return $saida;
    }
}

?>