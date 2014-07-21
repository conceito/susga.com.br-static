<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Site_utils: Funções comuns do CMS. Algumas são requisitadas por AJAX, outras podem ser acessadas diretamente.
 *
 * @package Library
 * @author Bruno Barros
 * @copyright Copyright (c) 2009
 * @version 3.0
 * @access public
 */
class Site_utils {

    function Site_utils() {
        $this->ci = &get_instance();
    }
    
    /**
     * Gera o ComboBox dos estados. 
     *
     * @param string $id : sigla do Estado
     * @return string
     */
    function combo_estados($id = '') {
        $selected = array($id);

        $this->ci->db->order_by('nome');
        $sql = $this->ci->db->get('opt_estado');
        
        $options = '<option value=""> - </option>';
        foreach ($sql->result_array() as $row) {
            $i = $row['uf'];
            $lbl = $i;            
            
            $options .= '<option value="'.$i.'">'.$lbl.'</option>';
            
        }
        
        $html = '<select name="uf" id="uf" class="input-xmini required">';
        $html .= $options;
        $html .= '</select>';


        return $html;
    }
    // -------------------------------------------------------------------------
    /**
     * Gera o ComboBox das variáveis do sistema.
     * @param string $combo_name
     * @param int $grupo_id
     * @param string|int $ids
     * @param boolean $multi
     * @return string
     */
    function combo_sist_vars($combo_name = 'variaveis', $grupo_id = '', $ids = '', $class = 'input-xmini', $multi = false) {
        
        if ($grupo_id == ''){
            return false;
        }

        $this->ci->db->where('grupo', $grupo_id); // lingua
        $this->ci->db->order_by('ordem');
        $this->ci->db->where('status', 1);
        $sql = $this->ci->db->get('cms_combobox');

        
        $options = '';
        foreach ($sql->result_array() as $row) {            
            
            $i = ($row['valor'] == '') ? $row['id'] : $row['valor'];
            $lbl = $row['titulo'];
            
            if($ids == $i){
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            
            $options .= '<option value="'.$i.'" '.$sel.'>'.$lbl.'</option>';
            
        }
        
        $html = '<select name="'.$combo_name.'" id="'.$combo_name.'" class="'.$class.'">';
        $html .= $options;
        $html .= '</select>';


        return $html;
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna os valores de um registro
     * @param int $id
     * @return array
     */
    public function get_sist_value($id){
        $this->ci->db->where('valor', $id);
        $this->ci->db->limit(1);
        $sql = $this->ci->db->get('cms_combobox');
        
        if($sql->num_rows() == 0){
            return FALSE;
        } else {
            return $sql->row_array();
        }
    }

    // ///////////////////////////////////////////////////////////////////////
    // /////////        FUNÇÕES EXCLUSIVAS DE MANIPULAÇÃO DO CMS       ///////
    // ///////////////////////////////////////////////////////////////////////
    /**
     * Pode usar o ID ou NICK e
     * retorna o conteúdo ÚNICO como array
     * Ex: $this->site_utils->_get_conteudo('nome-do-post');
     */
    function _get_conteudo($nickID = 0) {
        if (is_numeric($nickID)) {
            if ($nickID == 0)
                return false;
            $eh = 'number';
        } else {
            if (strlen($nickID) == 0)
                return false;
            $eh = 'string';
        }
        // else pesquisa
        if ($eh == 'number')
            $this->ci->db->where('id', $nickID);
        else
            $this->ci->db->where('nick', $nickID);
        
        $sql = $this->ci->db->get('cms_conteudo');
        return $sql->row_array();
    }

    /**
     * Retorna um aray com dados do Módulo
     * @param int $ID
     * @return array
     */
    function _get_modulo($ID = 0) {

        if ($ID == 0)
            return false;


        $this->ci->db->where('id', $ID);
        $sql = $this->ci->db->get('cms_modulos');
        return $sql->row_array();
    }

    /**
     * Recebe as configurações de pesquisa dos conteúdos
     * retorna os conteúdos paginados se o segundo valor for TRUE
     * Ex:
     * $conf['pre_uri'] = 'offset:20/id:3';
     * $conf['pro_uri'] = '';
     * $conf['modulo_id'] = 23; // a que módulo pertence
     * $conf['grupo'] = 2; // a que grupo pertence
     * $conf['offset'] = 20; // a partir do 20º resultado
     * $conf['porPag'] = 8; // 8 resultados por página
     * $conf['status'] = 1;
     * $conf['ordem'] = 'nome desc';
     * $conf['campos'] = 'id, titulo';
     * $conf['dtIni'] = '2010-05-23';
     * $this->site_utils->_conteudo_lista($conf, true);
     */
    function _conteudo_lista($conf = array(), $pagination = false) {
        $preBaseUri = (isset($conf['pre_uri'])) ? $conf['pre_uri'] : '';
        $posBaseUri = (isset($conf['pro_uri'])) ? $conf['pro_uri'] : '';
        $modulo = (isset($conf['modulo_id'])) ? $conf['modulo_id'] : 6;
        $grupo = (isset($conf['grupo'])) ? $conf['grupo'] : false;
//        $nickID = (isset($conf['nickID'])) ? $conf['nickID'] : false;
        $offset = (isset($conf['offset'])) ? $conf['offset'] : 0;
        $porPag = (isset($conf['porPag'])) ? $conf['porPag'] : 10;
        $status = (isset($conf['status'])) ? $conf['status'] : 1;
        $ordem = (isset($conf['ordem'])) ? $conf['ordem'] : 'dt_ini desc';
        $campos = (isset($conf['campos'])) ? $conf['campos'] : 'id, nick, titulo, resumo, dt_ini';
        $dataIni = (isset($conf['dtIni'])) ? $conf['dtIni'] : false;
        $lang = (isset($conf['lang'])) ? $conf['lang'] : 'pt';
        // limpa campos
        $posBaseUri = (strlen($posBaseUri) > 0) ? '/' . $posBaseUri . '/' : '';
        // -
        if ($dataIni){
            $this->ci->db->where('dt_ini >', $dataIni);
        }            
            
        if ($grupo == -1){
            $this->ci->db->where('grupo !=', 0);
        } else if($grupo){
            $this->ci->db->where('grupo', $grupo);
        }
            
        $this->ci->db->where('modulo_id', $modulo);
        $this->ci->db->where('status', $status);
        $this->ci->db->order_by($ordem);
        $this->ci->db->limit($porPag, $offset);
        $this->ci->db->select($campos);
        $this->ci->db->where('lang', $lang);
        $sql = $this->ci->db->get('cms_conteudo');

        if ($pagination) {
            $this->ci->load->library('pagination');
            // pega total de conteudo
            $this->ci->db->where('modulo_id', $modulo);

            if ($grupo == -1){
                $this->ci->db->where('grupo !=', 0);
            } else if($grupo){
                $this->ci->db->where('grupo', $grupo);
            }

            $this->ci->db->where('status', $status);
            $this->ci->db->where('lang', $lang);
            $this->ci->db->select('id');
            $sqlT = $this->ci->db->get('cms_conteudo');
            // configura paginação ----------------------------------
            $config['base_url'] = site_url($preBaseUri);
            $config['total_rows'] = $sqlT->num_rows();
            $config['per_page'] = $porPag;
            $config['uri_segment'] = 3;
            $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
            $config['full_tag_open'] = '<div class="pagination sombra"> Páginas: ';
            $config['full_tag_close'] = '<div class="navpoint"></div></div>';
            $config['num_tag_open'] = '<span class="pagination_number">';
            $config['num_tag_close'] = '</span>';
            $config['cur_tag_open'] = '<span class="pagination_current">';
            $config['cur_tag_close'] = '</span>';
            $config['next_tag_open'] = '<span class="pagination_number">';
            $config['next_tag_close'] = '</span>';
            $config['prev_tag_open'] = '<span class="pagination_number">';
            $config['prev_tag_close'] = '</span>';
            $this->ci->pagination->initialize($config);
        }
        // passa pelo filtro de status user/video. retorna array
        if ($sql->num_rows() == 0)
                return false;
        else
            return $sql->result_array();
    }

    function _albuns_lista($conf = array(), $pagination = false) {
        $preBaseUri = (isset($conf['pre_uri'])) ? $conf['pre_uri'] : '';
        $posBaseUri = (isset($conf['pro_uri'])) ? $conf['pro_uri'] : '';
        $tipo = 1;
        $grupo = (isset($conf['grupo'])) ? $conf['grupo'] : false;
//        $nickID = (isset($conf['nickID'])) ? $conf['nickID'] : false;
        $offset = (isset($conf['offset'])) ? $conf['offset'] : 0;
        $porPag = (isset($conf['porPag'])) ? $conf['porPag'] : 10;
        $status = (isset($conf['status'])) ? $conf['status'] : 1;
        $ordem = (isset($conf['ordem'])) ? $conf['ordem'] : 'dt_ini desc';
        $dataIni = (isset($conf['dtIni'])) ? $conf['dtIni'] : false;
        $lang = (isset($conf['lang'])) ? $conf['lang'] : 'pt';
        // limpa campos
        $posBaseUri = (strlen($posBaseUri) > 0) ? '/' . $posBaseUri . '/' : '';
        // -
        if ($dataIni)
            $this->ci->db->where('dt_ini >', $dataIni);
        $this->ci->db->where('tipo', $tipo);
        if ($grupo == -1){
            $this->ci->db->where('grupo !=', 0);
        } else if($grupo){
            $this->ci->db->where('grupo', $grupo);
        }


        $this->ci->db->where('status', $status);
        $this->ci->db->order_by($ordem);
        $this->ci->db->limit($porPag, $offset);

        $this->ci->db->where('lang', $lang);
        $sql = $this->ci->db->get('cms_pastas');

        if ($pagination) {
            $this->ci->load->library('pagination');
            // pega total de conteudo
            $this->ci->db->where('tipo', $tipo);

            if ($grupo == -1){
                $this->ci->db->where('grupo !=', 0);
            } else if($grupo){
                $this->ci->db->where('grupo', $grupo);
            }

            $this->ci->db->where('status', $status);
            $this->ci->db->where('lang', $lang);
            $this->ci->db->select('id');
            $sqlT = $this->ci->db->get('cms_pastas');
            // configura paginação ----------------------------------
            $config['base_url'] = site_url($preBaseUri);
            $config['total_rows'] = $sqlT->num_rows();
            $config['per_page'] = $porPag;
            $config['uri_segment'] = 3;
            $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
            $config['full_tag_open'] = '<div class="pagination sombra"> Páginas: ';
            $config['full_tag_close'] = '<div class="navpoint"></div></div>';
            $config['num_tag_open'] = '<span class="pagination_number">';
            $config['num_tag_close'] = '</span>';
            $config['cur_tag_open'] = '<span class="pagination_current">';
            $config['cur_tag_close'] = '</span>';
            $config['next_tag_open'] = '<span class="pagination_number">';
            $config['next_tag_close'] = '</span>';
            $config['prev_tag_open'] = '<span class="pagination_number">';
            $config['prev_tag_close'] = '</span>';
            $this->ci->pagination->initialize($config);
        }
        // passa pelo filtro de status user/video. retorna array
        if ($sql->num_rows() == 0)
                return false;
        else
            return $sql->result_array();
    }

    /**
     * Seleciona arquivos de uma determinada pasta
     */
    function _get_arquivos($pasta_id = 0, $ordem = 'ordem') {
        if ($pasta_id == 0)
            return false;
        // else pesquisa
        $this->ci->db->where('pasta', $pasta_id);
        $this->ci->db->order_by($ordem);
        $sql = $this->ci->db->get('cms_arquivos');
        return $sql->result_array();
    }

    /**
     * Retorna dados das imagens de uma galeria.
     * Pode eliminar a primeira imagem.
     * $this->site_utlis->_get_galeria_str('4|5|78|15');
     * */
    function _get_galeria_str($string = '', $firstLess = false) {
        if ($string == '')
            return false;
        $list = explode('|', trim($string, '|'));
        if (count($list) == 0)
            return false;
        if ($firstLess && count($list) == 1)
            return false;
        if ($firstLess)
            array_shift($list);
        // ok, tem galeria
        $saida = array(); // init
        foreach ($list as $id_img) {
            $dados = $this->_get_dado_arq($id_img);
            $saida[] = $dados;
        }
        return $saida;
    }
    
    // ------------------------------------------------------------------------
    /**
     * Retorna todos os arquivos com o ID no array.
     * A pesquisa pode ser feita pelos campos passados no segundo argumento.
     * @param type $array_ids
     * @param type $search_by
     * @return type
     */
    public function get_arquivos_from_array($array_ids, $search_by = 'id'){
        
        
        $this->ci->db->where_in($search_by, $array_ids);       
        
        $sql = $this->ci->db->get('cms_arquivos');
        return $sql->result_array();
    }

    /**
     * Retorna dados do arquivo
     * */
    public function _get_dado_arq($id) {
        $this->ci->db->where('id', $id);
        $sql = $this->ci->db->get('cms_arquivos');
        return $sql->row_array();
    }

    /**
     * Busca por usuário individual
     * Aceita o email ou ID
     */
    function _get_user($emailID = 0) {
        if (is_numeric($emailID)) {
            if ($emailID == 0)
                return false;
            $eh = 'number';
        } else {
            if (strlen($emailID) == 0)
                return false;
            $eh = 'string';
        }
        // else pesquisa
        if ($eh == 'number')
            $this->ci->db->where('cms_usuarios.id', $emailID);
        else
            $this->ci->db->where('cms_usuarios.email', $emailID);
        
        $this->ci->db->from('cms_usuarios');
        $this->ci->db->join('cms_usuarios_extra', 'cms_usuarios_extra.user_id = cms_usuarios.id', 'left');
        $sql = $this->ci->db->get();

        return $sql->row_array();
    }

    /**
     * Framework para gerar comboboxes
     *
     * @param array $ids
     * @param array $populaCombo = ['value', 'Label']
     * @param string $name
     * @param mixed $multi
     * @param string $extra
     * @return
     * */
    function _cb($ids = array(), $populaCombo = array(), $name = 'combo', $multi = false, $extra = 'longo') {
        // -- >> entra com os campos do combobox << -- //
        foreach ($populaCombo as $c => $v) {
            $a_rows[$c] = $v;
        }
        // -- se for multi seleção  -- //
        if ($multi) {
            $tag_multi = ' multiple="multiple"';
            $label = $name . '[]';
        } else {
            $label = $name;
            $tag_multi = '';
        }
        // os IDs devem ser arrays
        if (!is_array($ids))
            $selected = array($ids);
        else
            $selected = $ids;
        // Popula o array
        $mark = array();
        foreach ($selected as $id) {
            if ($id != '')
                $mark[] = $id;
        }
        $drop = form_dropdown($label, $a_rows, $mark, 'class="input combo ' . $extra . '" id="' . $name . '"' . $tag_multi);
        return $drop;
    }

    // ///////////////////////////////////////////////////////////////////////
    // /////////     FIM DAS FUNÇÕES EXCLUSIVAS DE MANIPULAÇÃO DO CMS    /////
    // ///////////////////////////////////////////////////////////////////////

    /**
     * Incrementa o campo 'visitas' na tabela especificada.
     *
     * @param mixed $nick : o apelido deste registro, ou ID
     * @param mixed $tabela : tabela que será feito o incremento
     */
    function soma_visita($nickID, $tabela) {
        // pega o valor atual
        if (is_numeric($nickID)

            )$this->ci->db->where('id', $nickID);
        else
            $this->ci->db->where('nick', $nickID);
        $this->ci->db->select('visitas');
        $sql = $this->ci->db->get($tabela);
        $row = $sql->row_array();
        $soma = $row['visitas'] + 1;
        // atualiza
        if (is_numeric($nickID)

            )$this->ci->db->where('id', $nickID);
        else
            $this->ci->db->where('nick', $nickID);
        $this->ci->db->update($tabela, array('visitas' => $soma));
    }

    /**
     * Retorna dados da cidade
     *
     * @param mixed $id
     * @return
     */
    function cidade_dados($id) {
        if ($id == 0)
            return '0';
        if ($id == '')
            return array('nome' => 'Todos');
        $this->ci->db->where('id', $id);
        $this->ci->db->select('nome, uf');
        $sql = $this->ci->db->get('opt_cidades');
        return $sql->row_array();
    }

    /**
     * Verifica se já existe usuario com este valor
     *
     * @param integer $id
     * @return array
     */
    function existe_user($campo = 'email', $string = '') {
        $this->ci->db->where($campo, $string);
        $sql = $this->ci->db->get('cms_usuarios');

        return $sql->num_rows();
    }

    /**
     * Carrega layout e monta enquete
     *
     * @param integer $id
     * @return array
     */
    function enquete() {
        // pega uma enquete
        $this->ci->db->where('status', 1);
        $this->ci->db->order_by('rand()');
        $this->ci->db->limit(1);
        $sql = $this->ci->db->get('cms_enquete_per');

        if ($sql->num_rows() == 0) {
            $dados = '';
        } else {
            $enquete = $sql->row_array();
            $id_enq = $enquete['id'];
            $pergunta = $enquete['titulo'];
            $dadosPag['id'] = $id_enq;
            $dadosPag['pergunta'] = $pergunta;
            // seleciona opções
            $this->ci->db->where('pergunta', $id_enq);
            $this->ci->db->order_by('rand()');
            $sql = $this->ci->db->get('cms_enquete_opc');
            $opcoes = $sql->result_array();
            $dadosPag['opcoes'] = $opcoes;

            $dados = $this->ci->load->view('apps/enquete_perg', $dadosPag, true);
        }


        return $dados;
    }

    /**
     * Processa o array e monta a string = |str|str|str|
     *
     * @param array $array
     * @return string
     */
    function array_to_str($array, $separador = '|') {
        if ($array != false) {
            $strSaida = $separador;
            $strSaida .= implode($separador, $array);
            $strSaida .= $separador;

            return $strSaida;
        } else {
            return '';
        }
    }

    /**
     * Processa o string (|str|str|str|) e monta array
     *
     * @param string $str
     * @return array
     */
    function str_to_array($str) {
        $this->load->library('cms_libs');
        return $this->cms_libs->str_to_array($str, $separador = '|');
    }

    /**
     * Site_utils::lembrar_senha()
     *
     * @return
     * */
    function lembrar_senha() {
        $this->ci->load->model(array('pagina_model'));
        //
        $email = trim($this->ci->input->post('email'));


        // pesquisa se existe
        $this->ci->db->where('status', 1);
        $this->ci->db->where('email', $email);
        $sql = $this->ci->db->get('cms_usuarios');
        if ($sql->num_rows() == 0) {
            return false;
        } else {
            $resposta = $sql->row_array();

            ///// gera nova senha para usuário e envia por email
            $novaSenha = mt_rand();
            $this->ci->db->where('email', $email);
            $ret = $this->ci->db->update('cms_usuarios', array('senha' => md5($novaSenha)));
            if ($ret) {
                // envia por email
                $dados['emailDestino'] = $email;
                $dados['novaSenha'] = $novaSenha;
                $rete = $this->ci->pagina_model->envia_senha($dados);
                if ($rete) {
                    return 'ok';
                } else {
                    return 'erro2';
                }
            } else {
                return 'erro1';
            }
        }
    }

    /**
     * Retorna o "valor", ou "nome do campo" em caso de array, do campo extra
     * A var $extraModulo deve ser iserida via $this->cms_libs->extraModuloArray($idModulo);
     * @param array $extraModulo
     * @param string $campoID
     * @param array $registroDados
     * @param int $ID
     * @return string
     */
    function getCampoExtra($extraModulo = '', $registroDados = '', $campoID) {
        $saida = '';

        $arrayCompleto = $this->ci->cms_libs->extraMontaArray($extraModulo, $registroDados);


        for ($x = 0; $x < count($arrayCompleto); $x++) {


            if ($arrayCompleto[$x]['id'] == $campoID) {

                $name = $arrayCompleto[$x]['name'];
                $id = $arrayCompleto[$x]['id'];
                $type = $arrayCompleto[$x]['type'];
                $data = $arrayCompleto[$x]['data']; // array
                // percorre as opções
                if ($type == 'input' || $type == 'text' || $type == "arq" || $type == "img") {
                    $saida = $data;
                } else {

                    // percorre os valores e identifica com valor 1
                    foreach ($data as $opc) {
                        $campo = $opc['campo'];
                        $valor = $opc['selected'];

                        if ((string) $valor == "1") {

                            $saida = $campo . ',';
                        }
                    }
                }

                break;
            }
        }
        $saida = trim($saida, ',');
        return $saida;
    }

    /*     * *******************************************************************
     * ********************************************************************
     * As funções abaixo manipulam SESSION de um carrinho de compras
     * Devem ser ajustadas
     * ********************************************************************
     * ******************************************************************** */

    /**
     * Adiciona o produto no carrinho, se já existe incrementa a quantidade.
     *
     * @param mixed $idProd
     * @param mixed $quant
     * @param mixed $valor_uni
     * @return
     * */
    function add_carrinho($idProd, $quant, $valor_uni) {

        $carrinho = $this->CI->session->userdata('carrinho');
        $saida = array(); // init
        // SE NÃO existe produto no carrinho ++
        if (strlen((string) $carrinho) < 6) {
            $prod = $idProd . '|' . $quant . '|' . $valor_uni;
            $this->CI->session->set_userdata('carrinho', $prod);

            return true;
        }
        // se já existe carrinho
        else {

            $carrinhoArr = $this->str_to_array_session($carrinho);

            // pesquisa para ver se este produto existe no carrinho
            $existe = false;
            foreach ($carrinhoArr as $prod) {

                // se o produto já existe incrementa a quantidade
                if ($prod['id'] == $idProd) {
                    $quant_final = $prod['quant'] + $quant; // soma a quantidade
                    $saida[] = array('id' => $prod['id'],
                        'quant' => $quant_final,
                        'valor_uni' => $prod['valor_uni']
                    );
                    $existe = true;
                }
                // não axiste - só coloca no array
                else {
                    $saida[] = $prod;
                }
            }

            // se o produto não existe, acrescenta no array
            if (!$existe) {
                $saida[] = array('id' => $idProd,
                    'quant' => $quant,
                    'valor_uni' => $valor_uni
                );
            }



            // coloca tudo no carrinho
            $this->CI->session->set_userdata('carrinho', $this->array_to_str_session($saida));
        }
    }

    /**
     * Retira um produto do carrinho
     *
     * @param mixed $idProd
     * @return
     * */
    function del_carrinho($idProd) {
        $carrinho = $this->CI->session->userdata('carrinho');
        $carrinhoArr = $this->str_to_array_session($carrinho);
        $saida = array(); // init


        foreach ($carrinhoArr as $prod) {
            if ($prod['id'] != $idProd) {
                $saida[] = array('id' => $prod['id'],
                    'quant' => $prod['quant'],
                    'valor_uni' => $prod['valor_uni']
                );
            }
        }

//		  echo '<pre>';
//		var_dump($this->array_to_str_session($saida));
//		exit;
        $this->CI->session->set_userdata('carrinho', $this->array_to_str_session($saida));
    }

    /**
     * Atualiza a quantidade no carrinho
     *
     * @return
     * */
    function refresh_carrinho() {

        $idProd = $this->CI->input->post('idProd'); // array
        $quant = $this->CI->input->post('quant'); // array

        $carrinho = $this->CI->session->userdata('carrinho');
        $carrinhoArr = $this->str_to_array_session($carrinho);
        $saida = array(); // init

        for ($x = 0; $x < count($idProd); $x++) {
            $_id = $idProd[$x];
            $_quant = $quant[$x];
            //
            foreach ($carrinhoArr as $prod) {
                if ($prod['id'] == $_id) {
                    $saida[] = array('id' => $prod['id'],
                        'quant' => $_quant,
                        'valor_uni' => $prod['valor_uni']
                    );
                }
            }
        }


        $this->CI->session->set_userdata('carrinho', $this->array_to_str_session($saida));
    }

    function clear_carrinho() {
        $this->CI->session->set_userdata('carrinho', '');
    }

    /**
     * Monta dados do carrinho para mostador da coluna esquerda.
     *
     * @return
     * */
    function dados_carrinho() {
        $carrinho = $this->CI->session->userdata('carrinho');
        $saida = array(); // init
        // não tem produto no carrinho
        if (strlen((string) $carrinho) < 6) {
            $saida['quant'] = 0;
            $saida['valor'] = '0,00';
        } else {

            // PROCESSA carrinho
            $carrinhoArr = $this->str_to_array_session($carrinho);
            $_q = 0;
            $_v = array(); // 0.00
            foreach ($carrinhoArr as $prod) {
                $_q += $prod['quant'];

                // acrecenta valores para somatório
                for ($x = 0; $x < $prod['quant']; $x++) {
                    $_v[] = $prod['valor_uni'];
                }
            }

            // soma total
            $ttl = soma_array($_v);

            $saida['quant'] = $_q;
            $saida['valor'] = formaBR($ttl);
        }

        return $saida;
    }

    /**
     * Pega um array multi e transforma em n|n|nn.nn-n|n|nn.nn
     *
     * @param array $array
     * @return
     * */
    function array_to_str_session($array = array()) {
        $saida = ''; // init
        if (count($array) == 0)
            return '';
        foreach ($array as $prod) {
            $saida .= $prod['id'] . '|' . $prod['quant'] . '|' . $prod['valor_uni'] . '-';
        }
        // retira tracinho final;
        return trim($saida, '-');
    }

    /**
     * Pega uma string n|n|nn.nn-n|n|nn.nn e transforma em array multi
     *
     * @param mixed $str
     * @return
     * */
    function str_to_array_session($str) {

        $arr1 = explode('-', $str);
        $saida = array(); // init
        foreach ($arr1 as $item) {
            $itens = explode('|', $item);
            $saida[] = array('id' => $itens[0],
                'quant' => $itens[1],
                'valor_uni' => $itens[2]
            );
        }

        return $saida;
    }

}

?>