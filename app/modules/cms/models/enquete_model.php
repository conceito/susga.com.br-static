<?php

/**
*
* @version $Id$
* @copyright 2010
*/

class Enquete_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }

    function lista_conteudos($v, $tipo = 'conteudo')
    {
        // -- trata as variaveis --//
        $pps = $this->config->item('pagination_limits');
        $pp = ($v['pp'] == '') ? $pps[0] : $v['pp']; // por página
                
        $pag = $this->uri->to_array('pag');
        if($pag['pag'] == ''){
            $offset = 0;
        } else {
            $offset = ($pag['pag']-1) * $pp;
        }
        
        $b = $v['b'];
        // se foi feita uma busca
        if (strlen(trim($this->input->post('q'))) > 0) {
            $b = $this->cms_libs->limpa_caracteres(trim($this->input->post('q')));
            $b = ($b == 'busca') ? '' : $b; // prevenir contra falsa busca
            $offset = 0;
        }
        // se foi feita bsca avançada ------------------------
        if (strlen(trim($this->input->post('ativo'))) > 0) {
            $stt = $this->input->post('ativo');
            $offset = 0;
        } else {
            $stt = $v['stt'];
        }
        // se foi feita seleção com grupos
        if (strlen(trim($this->input->post('grupos'))) > 0) {
            $g = $this->input->post('grupos');
        } else {
            $g = ($v['g'] == '') ? 0 : $v['g'];
        }
        // pelas datas
        if (strlen(trim($this->input->post('dt1'))) > 0)$dt1 = formaSQL($this->input->post('dt1'));
        else $dt1 = $v['dt1'];
        if (strlen(trim($this->input->post('dt2'))) > 0)$dt2 = formaSQL($this->input->post('dt2'));
        else $dt2 = $v['dt2'];
        // echo '<pre>';
        // var_dump($v['co']);
        // exit;
        // -- SQL básica com paginação -- //
        if ($dt1 != '' && $dt2 == ''){$this->db->where('dt_ini', $dt1);}
        else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('dt_ini >=', $dt1);
            $this->db->where('dt_ini <=', $dt2);
        }
        if ($stt != '')$this->db->where('status', $stt);
        if ($b != '')$this->db->like('titulo', $b);

        $this->db->limit($pp, $offset);
        $this->db->order_by('dt_ini', 'titulo');

        $this->db->where('lang', get_lang());

        $sql = $this->db->get('cms_enquete_per');
        // -- pega o Total de registros -- //
        if ($dt1 != '' && $dt2 == ''){$this->db->where('dt_ini', $dt1);}
        else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('dt_ini >=', $dt1);
            $this->db->where('dt_ini <=', $dt2);
        }
        if ($stt != '')$this->db->where('status', $stt);
        if ($b != '')$this->db->like('titulo', $b);

        $this->db->where('lang', get_lang());
        $sql_ttl = $this->db->get('cms_enquete_per');
        $ttl_rows = $sql_ttl->num_rows();
        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/enquete/index/co:' . $v['co'] . '/pp:' . $pp . '/g:' . $g . '/dt1:' . $v['dt1'] . '/dt2:' . $v['dt2'] . '/b:' . $b . '/stt:' . $stt . '/');
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
        // var_dump($sql->result_array());
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
            if ($row['status'] == 1)$row['status'] = 'ativo';
            else if ($row['status'] == 0)$row['status'] = 'inativo';
            else if ($row['status'] == 2)$row['status'] = 'editando';
            // pega a quantidade de opções e votos
            $ttlvotos = 0;
            $opcs = '';
            $this->db->where('pergunta', $row['id']);
            $this->db->order_by('id');
            $sql = $this->db->get('cms_enquete_opc');
            $row['ttlopc'] = $sql->num_rows();
            // faz contagem de votos
            $i = 1;
            foreach($sql->result_array() as $opc) {
                $ttlvotos += $opc['votos'];
                $plural = ($opc['votos'] > 1)?'s':'';
                $opcs .= $i . ') ' . $opc['opcao'] . ': ' . $opc['votos'] . ' voto' . $plural . '<br/>';
                $i++;
            }
            $row['ttlvotos'] = $ttlvotos;
            $row['resumo'] = $opcs;
            // coloca no array
            // $saida[] = array('id' => $row['id'],
            // 'titulo' => $row['titulo'],
            // 'tipo' => $tipo,
            // 'status' => $att);
            $saida[] = $row;
        }
        return $saida;
    }

    function conteudo_salva($var)
    {
        // - salva os dados do menu principal Raiz
        $titulo = trim($this->input->post('titulo'));
        $rel = $this->input->post('rel');
        $data = trim($this->input->post('dt1'));
        $status = $this->input->post('status');


        $dados['titulo'] = $titulo;
        $dados['status'] = $status;
        $dados['rel'] = ($rel == '') ? 0 : $rel;
        // echo '<pre>';
        // var_dump($dados);
        // exit;
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['lang'] = get_lang();
            $dados['dt_ini'] = date("Y-m-d");

            $sql = $this->db->insert('cms_enquete_per', $dados);
            $esteid = $this->db->insert_id();
            // -- >> LOG << -- //
            $oque = "Nova Enquete: <a href=\"" . cms_url('cms/enquete/edita/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            $dados['dt_ini'] = formaSQL($data);

            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_enquete_per', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Enquete: <a href=\"" . cms_url('cms/enquete/edita/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }

        /////// ------- salva opções da emquete ---------//////////
        foreach($_POST as $chv => $vlr) {
            $separa = explode("_", $chv);
            $etiqueta = $separa[0]; // label
            if (count($separa) > 1) {
                $identificador = $separa[1]; // number
            } else {
                $identificador = ''; // não deve salvar
            }
            // atualiza cada lista
            if ($etiqueta == 'opc' and is_numeric($identificador)) { // so grava neata variavel
                // echo '<pre>';
                // print_r($separa[0]);
                // exit;
                $sOpcao = $this->input->post('opc_' . $identificador);
				$dados_x['opcao'] = $sOpcao;
				// se for nova enquete
				if($var['id'] == ''){
					if(strlen($sOpcao) > 2){
						$dados_x['pergunta'] =	$esteid;
						$this->db->insert('cms_enquete_opc', $dados_x);
					}

				} else {

					// / faz  update
	                $this->db->where('id', $identificador);
	                $this->db->update('cms_enquete_opc', $dados_x);
				}



            }
        }

        return $esteid;
    }

    /**
    * Pega os dados e parseia
    *
    * @param mixed $var
    * @return
    */
    function conteudo_dados($var)
    {
        $this->db->where('id', $var['id']);
        $sql = $this->db->get('cms_enquete_per');
        if ($sql->num_rows() == 0) return false;
        // percorre array
        $saida = array();
        $ttlvotos = 0;
        foreach($sql->row_array() as $chv => $vlr) {
            // data
            if ($chv == 'dt_ini')$saida['dt1'] = formaPadrao($vlr);
            // pega as opções e parseia também
            if ($chv == 'id') {
                $this->db->where('pergunta', $vlr);
                $this->db->order_by('id');
                $sql = $this->db->get('cms_enquete_opc');
                $rows = $sql->result_array();

                $saida['opcoes'] = $rows;

                // percorre as opções e sova votos
                foreach($rows as $opc){
					$ttlvotos += $opc['votos'];
				}
				$saida['ttlvotos'] = $ttlvotos;

            }
            // coloca no array
            $saida[$chv] = $vlr;
        }

//        echo '<pre>';
//        var_dump($saida);
//        exit;
        return $saida;
    }
}

?>