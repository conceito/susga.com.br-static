<?php
/**
 * Paginas_model
 *
 * @package
 * @author Bruno
 * @copyright Copyright (c) 2010
 * @version $Id$
 * @access public
 **/
class News_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }

    /**
    * Lista os conteudos dos grupos e conteudos
    *
    * @param mixed $v array com dados via URI
    * @param string $tipo grupo ou conteudo
    * @param array $modulo array dados do módulo
    * @return
    */
    function lista_conteudos($v, $tipo = 'conteudo', $modulo = array())
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
        if ($b != ''){
			$this->db->like('titulo', $b);$this->db->or_like('resumo', $b);
		}
        $this->db->limit($pp, $offset);
        // ordenação
        if (isset($modulo['ordenavel']) && $modulo['ordenavel'])$this->db->order_by('ordem');
        else $this->db->order_by('dt_ini', 'titulo');
        $this->db->where('modulo_id', $v['co']);
        if ($tipo == 'grupo') {
            $this->db->where('grupo', 0); // busca grupos
        } else {
            if ($g == 0)$this->db->where('grupo !=', 0); // todos conteudos
            else $this->db->where('grupo', $g); // conteudos do grupo
        }
        $this->db->where('lang', get_lang());

        $sql = $this->db->get('cms_conteudo');
        // -- pega o Total de registros --------------------------------------------------- //
        // -- pega o Total de registros -- //
        if ($dt1 != '' && $dt2 == ''){$this->db->where('dt_ini', $dt1);}
        else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('dt_ini >=', $dt1);
            $this->db->where('dt_ini <=', $dt2);
        }
        if ($stt != '')$this->db->where('status', $stt);
        if ($b != ''){
			$this->db->like('titulo', $b);$this->db->or_like('resumo', $b);
		}
        $this->db->where('modulo_id', $v['co']);
        if ($tipo == 'grupo') {
            $this->db->where('grupo', 0); // busca grupos
        } else {
            if ($g == 0)$this->db->where('grupo !=', 0); // todos conteudos
            else $this->db->where('grupo', $g); // conteudos do grupo
        }
        $this->db->where('lang', get_lang());
        $sql_ttl = $this->db->get('cms_conteudo');
        $ttl_rows = $sql_ttl->num_rows();
        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/news/index/co:' . $v['co'] . '/pp:' . $pp . '/g:' . $g . '/dt1:' . $v['dt1'] . '/dt2:' . $v['dt2'] . '/b:' . $b . '/stt:' . $stt . '/');
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
            'rows' => $this->parse_lista_conteudos($sql->result_array(), $modulo));

        return $saida;
    }

    /**
    * Prepara pesquisa de item de menu
    *
    * @param mixed $array
    * @return
    */
    function parse_lista_conteudos($array, $modulo = array())
    {
        if (count($array) == 0) return false;
        // percorre array
        $saida = array();
        foreach($array as $row) {
            if ($row['status'] == 1)$row['status'] = 'ativo';
            else if ($row['status'] == 0)$row['status'] = 'inativo';
            else if ($row['status'] == 2)$row['status'] = 'editando';

            if ($row['grupo'] == 0) {
                $row['grupo'] = 'Grupo';
            } else {
                $this->db->where('id', $row['grupo']);
                $this->db->select('titulo, tags');
                $sql = $this->db->get('cms_conteudo');
                $item = $sql->row_array();
                $row['grupo'] = $item['titulo'];
                $cores = (strlen($item['tags']) > 6) ? explode('|', $item['tags']) : array("", "");
                $row['grupoCor1'] = $cores[0];
                $row['grupoCor2'] = $cores[1];
            }
            // se existe comentários pesquisa a quantidade
            if (isset($modulo['comments']) && $modulo['comments'] == 1) {
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 1);
                $sqlA = $this->db->get('cms_comentarios');
                $row['comm_ttl'] = $sqlA->num_rows();
                // novos
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 2);
                $sqlN = $this->db->get('cms_comentarios');
                $row['comm_new'] = $sqlN->num_rows();
            }
            // coloca no array
            // $saida[] = array('id' => $row['id'],
            // 'titulo' => $row['titulo'],
            // 'tipo' => $tipo,
            // 'status' => $att);
            $saida[] = $row;
        }
        // echo '<pre>';
        // var_dump($saida);
        // exit;
        return $saida;
    }

    /**
     * Dados de UM omentário de um conteúdo
     **/
    function comentario_dados($id_cont)
    {
        $this->db->where('id', $id_cont);
        $sql = $this->db->get('cms_comentarios');
        return $sql->row_array();
    }

    /**
     * Todos os comentários de um conteúdo
     **/
    function comentarios_dados($id_cont)
    {
        $this->db->where('conteudo_id', $id_cont);
        $this->db->order_by('data desc');
        $this->db->order_by('hora desc');
        $sql = $this->db->get('cms_comentarios');
        return $this->parse_comentarios_dados($sql->result_array());
    }
    /**
    * Prepara pesquisa de item de menu
    *
    * @param mixed $array
    * @return
    */
    function parse_comentarios_dados($array)
    {
        if (count($array) == 0) return false;
        // percorre array
        $saida = array();
        foreach($array as $row) {
            if ($row['status'] == 1)$row['status'] = 'ativo';
            else if ($row['status'] == 0)$row['status'] = 'inativo';
            else if ($row['status'] == 2)$row['status'] = 'editando';

            $saida[] = $row;
        }
        return $saida;
    }



    function conteudo_salva($var)
    {
        // - salva os dados do menu principal Raiz
        $grupo = $this->input->post('grupos');
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $status = $this->input->post('status');
        $resumo = trim($this->input->post('resumo'));
        $tags = trim($this->input->post('tags'));
        $txt = trim($this->input->post('txt'));
        $extra = trim($this->input->post('extra'));
        $destaque = $this->input->post('destaque');


        $dados['titulo'] = $titulo;
        $dados['resumo'] = $resumo;
        if($destaque !== FALSE)$dados['destaque'] = $destaque;
        $dados['grupo'] = $grupo;
        $dados['modulo_id'] = $var['co'];
        $dados['tags'] = $tags;
        $dados['extra'] = $extra;
        $dados['status'] = $status;
        $dados['txt'] = $txt; // encode ISO
//         echo '<pre>';
//         var_dump($dados);
//         exit;
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['hr_ini'] = date("H:i:s");
            $dados['lang'] = get_lang();
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['dt_ini'] = date("Y-m-d");

            $sql = $this->db->insert('cms_conteudo', $dados);
            $esteid = $this->db->insert_id();
            // -- >> LOG << -- //
            $oque = "Nova Newsletter: <a href=\"" . cms_url('cms/news/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {


            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Newsletter: <a href=\"" . cms_url('cms/news/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }

        return $esteid;
    }

    function links_salva()
    {
		$desc1 = trim($this->input->post('desc1'));
		$link1 = trim($this->input->post('link1'));
		$desc2 = trim($this->input->post('desc2'));
		$link2 = trim($this->input->post('link2'));
		$desc3 = trim($this->input->post('desc3'));
		$link3 = trim($this->input->post('link3'));
		$id = $this->input->post('mens_id');


		if(strlen($link1) > 10){
			$dados['titulo'] = (strlen($desc1)==0) ? $link1 : $desc1;
			$dados['url'] = $link1;
			$dados['mens_id'] = $id;
			$sql1 = $this->db->insert('cms_news_links', $dados);
		} else if(strlen($link2) > 10){
			$dados2['titulo'] = (strlen($desc2)==0) ? $link2 : $desc2;
			$dados2['url'] = $link2;
			$dados2['mens_id'] = $id;
			$sql2 = $this->db->insert('cms_news_links', $dados2);
		} else if(strlen($link3) > 10){
			$dados3['titulo'] = (strlen($desc3)==0) ? $link3 : $desc3;
			$dados3['url'] = $link3;
			$dados3['mens_id'] = $id;
			$sql3 = $this->db->insert('cms_news_links', $dados3);
		}

		return $sql1;
	}

    /**
    * Pega os dados na Library e parseia os dados
    *
    * @param mixed $var
    * @return
    */
    function conteudo_dados($var)
    {
        $dd = $this->cms_libs->conteudo_dados($var);
        if (! $dd) return false;
        // percorre array
        $saida = array();
        foreach($dd as $chv => $vlr) {
            // data
            if ($chv == 'dt_ini')$saida['dt1'] = formaPadrao($vlr);
            if ($chv == 'dt_fim')$saida['dt2'] = formaPadrao($vlr);
            // quantidade de imagens na galeria
            if ($chv == 'galeria') {
                if (strlen($vlr) == 0) {
                    $saida['quantGal'] = 0;
                } else {
                    $array = explode('|', $vlr);
                    $saida['quantGal'] = count($array);
                }
            }
            // coloca no array
            $saida[$chv] = $vlr;
        }
        // echo '<pre>';
        // var_dump($saida);
        // exit;
        return $saida;
    }

    /**
    * Pega os dados na Library e parseia os dados
    *
    * @param mixed $var
    * @return
    */
    function links_dados($var)
    {

		$this->db->where('mens_id', $var['id']);
        $this->db->order_by('titulo');
        $sql = $this->db->get('cms_news_links');
		$ttl = $sql->num_rows();
		$dd = $sql->result_array();

        //if ($ttl==0) return false;
        // percorre array

        // echo '<pre>';
        // var_dump($saida);
        // exit;
        return array('ttl_links' => $ttl, 'links' => $dd);
    }
    /**
    * Retorna os dados de um link
    */
    function link_dado($id)
    {

		$this->db->where('id', $id);
        $sql = $this->db->get('cms_news_links');
		return $sql->row_array();
    }

    function agendar_envios($var)
    {
		$mens_id = $var['id'];
		$modulo = $var['co'];
		$data = trim($this->input->post('dt1'));
		$titulo = trim($this->input->post('titulo'));
		$grupos = $this->input->post('grupos');
		$filtros = $this->input->post('filtros');


		//
		// 1º Reserva tabela do agendamento e retorna o ID
		$grupos_ser = implode('|', $grupos);
		$filtros_ser = ($filtros) ? implode('|', $filtros) : '';

		$dados['data'] = formaSQL($data);
		$dados['mens_id'] = $mens_id;
		$dados['titulo'] = $titulo;
		$dados['grupos'] = $grupos_ser;
		$dados['filtros'] = $filtros_ser;

		$this->db->insert('cms_news_age', $dados);
		$esteid = $this->db->insert_id();



		// 2º Seleciona os usuários e conta total de registros
		$QUERY = 'SELECT id, nome FROM cms_usuarios WHERE news=1 AND status=1 ';

		// percorre os grupos
		if(count($grupos) == 1){
			$QUERY .= 'AND grupo='.$grupos[0].' ';
		} else {// AND ( grupo=1 OR grupo=7 )
			$QUERY .= 'AND (';
			$subQUERY = '';
			foreach($grupos as $i => $g){
				$subQUERY .= ' grupo='.$g.' OR';
			}
			$QUERY .= trim($subQUERY, 'OR');
			$QUERY .= ') ';
		}
		// percorre os filtros
		if($filtros){ // AND ( filtro LIKE '%|21|%' OR filtro LIKE '%|22|%' )
			$QUERY .= 'AND (';
			$subQUERY = '';
			foreach($filtros as $f){
				$subQUERY .= ' filtro LIKE \'%|'.$f.'|%\' OR';
			}
			$QUERY .= trim($subQUERY, 'OR');
			$QUERY .= ') ';
		}

		// pega valores
		$sql = $this->db->query($QUERY);
		$selects = $sql->result_array();
		$quant = $sql->num_rows();
//echo '<pre>';
//         var_dump($selects);
//         exit;
		// 3º  Grava os usuarios para envio
		foreach($selects as $u){
			$dados2['age_id'] = $esteid;
			$dados2['user_id'] = $u['id'];
			$this->db->insert('cms_news_age_users', $dados2);
		}
		// 4º Atualiza a quantidade de envios no agendamento
		$this->db->where('id', $esteid);
		$sql = $this->db->update('cms_news_age', array('quant' => $quant));

		return $esteid;
	}


        /**
         * Retorna dados da news a ser disparada
         *
         * @param int $idAgendamento
         * @return array
         */
	function agendamentos_dados($idAgendamento)
	{
		$this->db->where('id', $idAgendamento);	
		$sql = $this->db->get('cms_news_age');
		return $this->parse_agendamentos_dados($sql->result_array());
	}

        /**
         * Parseia dados do agendamento adicionando dados da newsletter
         * 
         * @param array $array
         * @return array
         */
	function parse_agendamentos_dados($array = array()){
		if (count($array) == 0) return false;
        // percorre array
        $saida = array();
		foreach($array as $row){

			// pega dados da mensagem
			$this->db->where('id', $row['mens_id']);
			$this->db->select('id, titulo');
			$sql = $this->db->get('cms_conteudo');
			$mensagem = $sql->row_array();
			$row['mensagem'] = $mensagem;
			//--------------
			$saida[] = $row;
		}
		return $saida;
	}

	function mensagem_envia($var)
    {
        $this->load->library('e_mail');
        $news = $this->conteudo_dados($var['id']);
        $emailRem = $this->config->item('email1');
        $nomeRem = $this->config->item('title');
        $assunto = $news['titulo'];
        $emailDes = $this->input->post('email');
        $nomeDes = $emailDes;


		$menHTML = $news['txt'];
		$menTXT = $news['resumo'];

        // notifica admin
        $ret = $this->e_mail->envia($emailDes, $nomeDes, $assunto, $menHTML, $menTXT, $emailRem, $nomeRem);

        return $ret;
    }

    function stats_dados($var)
    {
		$saida = array();
		$saida['ok'] = array();
		$saida['erros'] = array();
		$saida['views'] = array();
		$saida['clicked'] = array();
                $saida['removed'] = array();
		// pega envios sucesso ----------------------
		$this->db->where('mens_id', $var['id']);
		$this->db->where('acao', 3);
		$sql = $this->db->get('cms_news_stats');
		if($sql->num_rows() > 0){
			// parseia
			foreach($sql->result_array() as $s){
				$dadosUser = $this->cms_libs->conteudo_dados($s['user_id'], 'cms_usuarios');
				if(! $dadosUser){
					$idUser = '0';
					$nomeUser = 'desconhecido';
					$emailUser = 'desconhecido';
				} else {
					$idUser = $dadosUser['id'];
					$nomeUser = $dadosUser['nome'];
					$emailUser = $dadosUser['email'];
				}

				$saida['ok'][] = array(
					'idUser' => $idUser,
					'nome' => $nomeUser,
					'email' => $emailUser,
					'data' => formaPadrao($s['data']),
					'hora' => substr($s['hora'], 0, 5)
				);
			}
		}


		// pega erros envios ------------------------
		$this->db->where('mens_id', $var['id']);
		$this->db->where('acao', 4);
		$sql = $this->db->get('cms_news_stats');
		if($sql->num_rows() > 0){
			// parseia
			foreach($sql->result_array() as $s){
				$dadosUser = $this->cms_libs->conteudo_dados($s['user_id'], 'cms_usuarios');
				if(! $dadosUser){
					$idUser = '0';
					$nomeUser = 'desconhecido';
					$emailUser = 'desconhecido';
				} else {
					$idUser = $dadosUser['id'];
					$nomeUser = $dadosUser['nome'];
					$emailUser = $dadosUser['email'];
				}

				$saida['erros'][] = array(
					'idUser' => $idUser,
					'nome' => $nomeUser,
					'email' => $emailUser,
					'data' => formaPadrao($s['data']),
					'hora' => substr($s['hora'], 0, 5)
				);
			}
		}

		// aberturas de emails ---------------------
		$this->db->where('mens_id', $var['id']);
		$this->db->where('acao', 2);
		$sql = $this->db->get('cms_news_stats');
		if($sql->num_rows() > 0){
			// parseia
			foreach($sql->result_array() as $s){
				$dadosUser = $this->cms_libs->conteudo_dados($s['user_id'], 'cms_usuarios');
				if(! $dadosUser){
					$idUser = '0';
					$nomeUser = 'desconhecido';
					$emailUser = 'desconhecido';
				} else {
					$idUser = $dadosUser['id'];
					$nomeUser = $dadosUser['nome'];
					$emailUser = $dadosUser['email'];
				}

				$saida['views'][] = array(
					'idUser' => $idUser,
					'nome' => $nomeUser,
					'email' => $emailUser,
					'data' => formaPadrao($s['data']),
					'hora' => substr($s['hora'], 0, 5)
				);
			}
		}

		// clicks em links internos ----------------
		$this->db->where('mens_id', $var['id']);
		$this->db->where('acao', 1);
		$sql = $this->db->get('cms_news_stats');
		if($sql->num_rows() > 0){
			// parseia
			foreach($sql->result_array() as $s){
				$dadosUser = $this->cms_libs->conteudo_dados($s['user_id'], 'cms_usuarios');
				if(! $dadosUser){
					$idUser = '0';
					$nomeUser = 'desconhecido';
					$emailUser = 'desconhecido';
				} else {
					$idUser = $dadosUser['id'];
					$nomeUser = $dadosUser['nome'];
					$emailUser = $dadosUser['email'];
				}

				$link = $this->link_dado($s['link']);
				if(! $link){
					$linkId = '0';
					$linkNome = 'desconhecido';
					$linkUrl = 'desconhecido';
				} else {
					$linkId = $link['id'];
					$linkNome = $link['titulo'];
					$linkUrl = $link['url'];
				}

				$saida['clicked'][] = array(
					'idUser' => $idUser,
					'nome' => $nomeUser,
					'email' => $emailUser,
					'linkNome' => $linkNome,
					'linkUrl' => $linkUrl,
					'data' => formaPadrao($s['data']),
					'hora' => substr($s['hora'], 0, 5)
				);
			}
		}

                // pega os descadastros ----------------------------
                $this->db->where('mens_id', $var['id']);
		$this->db->where('acao', 5);
		$sql = $this->db->get('cms_news_stats');
                if($sql->num_rows() > 0){
			// parseia
			foreach($sql->result_array() as $s){
				$dadosUser = $this->cms_libs->conteudo_dados($s['user_id'], 'cms_usuarios');
				if(! $dadosUser){
					$idUser = '0';
					$nomeUser = 'desconhecido';
					$emailUser = 'desconhecido';
				} else {
					$idUser = $dadosUser['id'];
					$nomeUser = $dadosUser['nome'];
					$emailUser = $dadosUser['email'];
				}



				$saida['removed'][] = array(
					'idUser' => $idUser,
					'nome' => $nomeUser,
					'email' => $emailUser,
					'data' => formaPadrao($s['data']),
					'hora' => substr($s['hora'], 0, 5)
				);
			}
		}

//		echo '<pre>';
//         var_dump($saida);
//         exit;

		return $saida;
	}
}

?>