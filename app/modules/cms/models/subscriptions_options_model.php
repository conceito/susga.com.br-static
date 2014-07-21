<?php

class Subscriptions_options_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retorna todos paginados
     * 
     * @param type $param
     */
    public function getAll($params)
    {
        /*
         * Variáveis de paginação
         */
        $pps = $this->config->item('pagination_limits');
        $pp = ($params['pp'] == '') ? $pps[0] : $params['pp']; // por página

        $pag = $this->uri->to_array('pag');
        if ($pag['pag'] == '')
        {
            $offset = 0;
        }
        else
        {
            $offset = ($pag['pag'] - 1) * $pp;
        }


        /*
         * opções de filtro
         */
        $uri_filters = $this->set_posts_filters();

        /*
         * main query
         */
        $this->db->limit($pp, $offset);

        $this->db->order_by('dt_ini desc, titulo');

        $this->db->where('modulo_id', $params['co']);

//        $this->db->where('grupo >', 0);
        $this->db->where('tipo', "subscription_options");

        $this->db->where('lang', get_lang());

        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
        $sql = $this->db->get('cms_conteudo');


        /*
         * pega o Total de registros 
         */
        $query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $ttl_rows = $query->row()->Count;

        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/calendario/subscriptions_options/co:' . $params['co'] . $uri_filters);
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 11; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);

        $saida = array(
            'ttl_rows' => $ttl_rows,
            'rows' => $this->calendario_model->parse_lista_conteudos($sql->result_array())
        );

        return $saida;
    }

    /**
     * Retorna um
     * 
     * @param type $id
     */
    public function find($id)
    {
        $this->db->where('id', $id);
        $this->db->select('id, titulo, resumo, txt, rel');
        $query = $this->db->get('cms_conteudo');

        if ($query->num_rows() == 0)
        {
            return false;
        }

        return $this->prepOption($query->row_array());
    }

    public function prepOption($row)
    {
        return $row;
    }

    /**
     * Insere novo objeti no DB
     * 
     * @param type $var
     */
    public function save_new($var)
    {
        $rel = $this->input->post('rel');
        $titulo = trim($this->input->post('titulo'));


        $dados['tipo'] = 'subscription_options';
        $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
        $dados['lang'] = get_lang();
        $dados['titulo'] = $titulo;
        $dados['dt_ini'] = date("Y-m-d");
        $dados['dt_fim'] = date("Y-m-d");
        $dados['hr_ini'] = date("H:i:s");
        $dados['hr_fim'] = date("H:i:s");
        $dados['grupo'] = 0;
        $dados['modulo_id'] = $var['co'];
        $dados['status'] = 1;
        $dados['rel'] = $rel;
        $dados['atualizado'] = date("Y-m-d H:i:s");

        $sql = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();
        // -- >> LOG << -- //
        $oque = "Novas opções: <a href=\"" . cms_url('cms/calendario/subscriptions_options_edit/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
        $this->cms_libs->faz_log_atividade($oque);

        return $esteid;
    }

    /**
     * Salva um objeto
     * 
     * @param type $id
     * @param type $options
     */
    public function save($id, $options)
    {
        
    }

    /**
     * Serializa e salva opções no campo 'txt'
     * 
     * @param int $optionId
     * @param array $arrayOfOptions
     */
    public function saveOptionsListForOptionId($optionId, $arrayOfOptions)
    {
        $options = serialize($arrayOfOptions);

        $this->db->where('id', $optionId);
        $this->db->update('cms_conteudo', array('txt' => $options));
    }

    /**
     * Retorna a lista de opções como array
     * @param int $optionId
     * @return array|boolean
     */
    public function getOptionsListFromOptionId($optionId)
    {
        $this->db->where('id', $optionId);
        $this->db->where('tipo', 'subscription_options');
        $this->db->where('lang', get_lang());
        $this->db->select('id, titulo, txt');
        $query = $this->db->get('cms_conteudo');

        if ($query->num_rows() == 0)
        {
            return false;
        }

        $row = $query->row_array();

        return $this->unserialize($row['txt']);
    }

    /**
     * Retorna um array com dados das opções.
     * 
     * array(
     *  'id' => int,
     *  'titulo' => string,
     *  'resumo' => string,
     *  'options' => array,
     *  'status' => int,
     * 
     * )
     * 
     * @param int $contentId
     * @return array|bool
     */
    public function getOptionsListFromContentId($contentId)
    {
        $this->db->where('rel', $contentId);
        $this->db->where('tipo', 'subscription_options');
        $this->db->where('lang', get_lang());
        $this->db->select('id, titulo, resumo, txt, status');
        $query = $this->db->get('cms_conteudo');

        if ($query->num_rows() == 0)
        {
            return false;
        }

        $row = $query->row_array();
        $row['options'] = $this->unserialize($row['txt']);
        unset($row['txt']);

        return $row;
    }

    /**
     * Recebe as opções serializadas, desserializa e ordena campos.
     * 
     * @param string $serializedOptions
     * @return array
     */
    public function unserialize($serializedOptions)
    {
        $options = unserialize($serializedOptions);

        if (!empty($options))
        {
            $order = array();

            foreach ($options as $k => $o)
            {
                $order[$k] = $o['ordem'];
            }
            array_multisort($order, SORT_ASC, $options);
        }

        return $options;
    }

    /**
     * Retorna um array com os conteúdos do módulo, que serão decorados com
     * as opções de insrição;
     * 
     * @param int $co
     * @param string $output
     * @return boolean|array
     */
    public function getRelatedContents($co, $selected = null, $output = 'array')
    {
        $this->db->where('modulo_id', $co);
        $this->db->where('tipo', 'conteudo');
        $this->db->where('grupo >', 0);
        $this->db->where('lang', get_lang());
        $this->db->order_by('dt_ini DESC, titulo');
        $this->db->select('id, titulo, status');
        $query = $this->db->get('cms_conteudo');

        if ($query->num_rows() == 0)
        {
            return false;
        }

//        dd($selected);

        $options = $query->result_array();

        if ($output == 'combobox')
        {
            $cb = array();
            foreach ($options as $o)
            {
                $cb[$o['id']] = $o['titulo'];
            }
            return form_dropdown('rel', $cb, $selected, 'class="input-combo " id="rel" style="width:100%"');
        }
        else
        {
            return $options;
        }
    }
    
    
    /**
     * return the options serialized as an array
     * 
     * @param int $subscritionId
     * @return boolean|array
     */
    public function getAnswersFromUserSubscription($subscritionId)
    {
        $inscQuery = $this->db->where('id', $subscritionId)->get('cms_inscritos');
        
        if($inscQuery->num_rows() == 0)
        {
            return false;
        }
        
        $insc = $inscQuery->row_array();

        
        $this->load->library('cms_metadados');
        
        $options = $this->cms_metadados->getByUser($insc['user_id'], '_subscription_options', $insc['id']);
        
        if(! $options)
        {
            return false;
        }
        return $this->unserialize($options);
    }





    /**
     * check to see if the content has options associated
     * if has... returns it
     * 
     * @param int $contentId
     * @param int $moduloId
     * @return boolean|array
     */
    public function checkHasOptions($contentId, $moduloId = null)
    {
        if (!is_numeric($contentId))
        {
            return false;
        }

        if(is_numeric($moduloId))
        {
            $this->db->where('modulo_id', $moduloId);
        }
        $this->db->where('rel', $contentId);
        $this->db->where('tipo', 'subscription_options');
        $this->db->where('status', 1);
        $opts = $this->db->get('cms_conteudo');
        
        if($opts->num_rows() == 0)
        {
            return false;
        }
        
        return $opts->row_array();
    }

    /**
     * Parseia $_POST e URI para saber se existem filtros ativos
     * Retorna URI para paginação.
     * 
     * @return string
     */
    private function set_posts_filters()
    {

        // define os campos que serão usados no filtro
        $campos_usados[] = array('campo' => 'titulo', 'type' => 'like');
//        $campos_usados[] = array('campo' => 'grupo', 'type' => 'text');
//        $campos_usados[] = array('campo' => 'destaque', 'type' => 'int');
        $campos_usados[] = array('campo' => 'dt_ini', 'type' => 'date');
        $campos_usados[] = array('campo' => 'dt_fim', 'type' => 'date');
        $campos_usados[] = array('campo' => 'status', 'type' => 'int');
        $campos_valorados = array();

//        mybug($this->input->post());
        // uri de filtros para paginação
        $return = '';

        // verifica se veio pelo POST ou URI
        foreach ($campos_usados as $row)
        {

            $campo = $row['campo'];
            $type = $row['type'];
            $uri = $this->uri->to_array('filter_' . $campo);

            // tem post?
            if (isset($_POST['filter_' . $campo]))
            {
                $valor = $_POST['filter_' . $campo];
            }
            // tem na URI
            else if ($uri['filter_' . $campo] != '')
            {
                $valor = $uri['filter_' . $campo];
            }
            else
            {
                $valor = '';
            }

            // acrescenta o valor
            $row['valor'] = $valor;
            $campos_valorados[] = $row;
        }

        // faz pesquisa
        foreach ($campos_valorados as $row)
        {

            if ($row['valor'] != '')
            {

                $campo = $row['campo'];
                $type = $row['type'];
                $valor = $row['valor'];

                // se for data
                if ($type == 'date' && strlen($valor) == 10)
                {
                    $valor = formaSQL($valor);
                }

                if ($type == 'like')
                {
                    $this->db->like('' . $campo, $valor);
                }
                else
                {
                    $this->db->where('' . $campo, $valor);
                }

                // incrementa uri
                $return .= '/filter_' . $campo . ':' . $valor;
            }
        }


//        mybug($return);
        return $return;
    }

}