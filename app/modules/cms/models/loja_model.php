<?php


class Loja_model extends CI_Model {
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
        
        // opções de filtro
        $uri_filters = $this->set_products_filters();

        
        // ordenação
        if (isset($modulo['ordenavel']) && $modulo['ordenavel']) {
            $this->db->order_by('cont.ordem');
        }
        else {
            $this->db->order_by('cont.dt_ini desc, cont.titulo');
        }        

        
        $this->db->where('cont.grupo >', 0);
        
        $this->db->from('cms_conteudo as cont');
        $this->db->where('cont.lang', get_lang());
        $this->db->where('cont.tipo', 'conteudo');
        $this->db->where('cont.modulo_id', $v['co']);
        
        $this->db->join('cms_produtos as prod', 'prod.conteudo_id = cont.id');
        
        $this->db->limit($pp, $offset);        
        $this->db->select('SQL_CALC_FOUND_ROWS cont.*, prod.codigo, prod.download, prod.download_limit, prod.estoque, prod.dimensoes, prod.peso, prod.valor_base', false);
        $sql = $this->db->get();
        
        
        // -- pega o Total de registros -- //
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $ttl_rows = $query->row()->Count;

        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/loja/index/co:' . $v['co'].$uri_filters);
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 11; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);

        
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
                // pega grupo com seus parentes, se houver
                $grupoParents = $this->paginas_model->getGrupoParents($row['grupo'], $modulo['id']);
                $row['grupoParents'] = $grupoParents;
                
            }
            
            // insere dados da imagem para thumbnail
            $row['galeria'] = $this->get_thumb($row['galeria']);
            
            // pega o preço vigente
            $row['preco'] = $this->get_preco_final($row);
            
            // pega as opções
            $row['options'] = $this->get_options($row['id']);
            $row['options_estoque'] = $this->get_estoque_from_options($row['options']);
            
            // se existe inscrição pesquisa a quantidade
            if (isset($modulo['inscricao']) && $modulo['inscricao'] == 1) {
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 1);
                $sqlA = $this->db->get('cms_inscritos');
                $row['insc_ttl'] = $sqlA->num_rows();
                // novos
                $this->db->where('conteudo_id', $row['id']);
                $this->db->where('status', 2);
                $sqlN = $this->db->get('cms_inscritos');
                $row['insc_new'] = $sqlN->num_rows();
            }

            $saida[] = $row;
        }

        return $saida;
    }
    
    // ----------------------------------------------------------------------
   
    /**
     * Parseia $_POST e URI para saber se existem filtros ativos
     * Retorna URI para paginação.
     * 
     * @return string
     */
    private function set_products_filters(){
        
        // define os campos que serão usados no filtro
        $campos_usados[] = array('campo' => 'titulo', 'type' => 'like');
        $campos_usados[] = array('campo' => 'grupo', 'type' => 'text');
        $campos_usados[] = array('campo' => 'valor', 'type' => 'money');
        $campos_usados[] = array('campo' => 'estoque', 'type' => 'int');
        $campos_usados[] = array('campo' => 'status', 'type' => 'int');
        $campos_valorados = array();
        
//        mybug($this->input->post());
        
        // uri de filtros para paginação
        $return = '';        
        
        // verifica se veio pelo POST ou URI
        foreach($campos_usados as $row){
            
            $campo = $row['campo'];
            $type  = $row['type'];
            $uri = $this->uri->to_array('filter_'.$campo);
            
            // tem post?
            if(isset($_POST['filter_'.$campo])){
                $valor = $_POST['filter_'.$campo];
            } 
            // tem na URI
            else if($uri['filter_'.$campo] != '') {                
                $valor = $uri['filter_'.$campo];
            } else {
                $valor = FALSE;
            }
            
            // acrescenta o valor
            $row['valor'] = $valor;
            $campos_valorados[] = $row;
            
        }
        
        // faz pesquisa
        foreach($campos_valorados as $row){
            
            if($row['valor']){
                
                $campo = $row['campo'];
                $type  = $row['type'];
                $valor = $row['valor'];
                
                // se for moeda
                if($type == 'money' && $valor != ''){
                    $valor = moneyFormat($valor);                    
                }
                
                    
                // personalização para estoque
                // 999+ ou 999-
                if($campo == 'estoque'){

                    // last caracter
                    $last = substr($valor, -1);
                    $cpl = '';
                    if($last == '-'){
                        $cpl = ' <=';
                    } else if($last == '+'){
                        $cpl = ' >=';
                    }

                    $this->db->where('prod.'.$campo.$cpl, $valor);
                    // elimina digitais
                    $this->db->where('prod.download !=', 1);

                } else {

                    if($type == 'like'){
                        $this->db->like('cont.'.$campo, $valor);
                    } else {
                        $this->db->where('cont.'.$campo, $valor);
                    }

                }


                // incrementa uri
                $return .= '/filter_'.$campo.':'.$valor;
                
                
            }
        }
        
        
//        mybug($return);
        return $return;
    }
    
    // -----------------------------------------------------------------------
    /**
     * Retorna o valor vigente do produto de acordo com a data
     * @param type $row
     * @return string
     */
    function get_preco_final($prod){
//        mybug($row);
        $this->db->where('conteudo_id', $prod['id']);
        $this->db->where('data >=', date("Y-m-d"));
        $this->db->where('tipo', 'preco');
        $this->db->order_by('data');
//        $this->db->limit(1);
        $return = $this->db->get('cms_precos'); 
        
        if($return->num_rows() == 0){
            return $prod['valor_base'];
        }
        
        $precos = $return->result_array();
        
        // percorre preços para encontrar o dia de hoje, ou a menor data
        $preco_f = 0;
        foreach ($precos as $row){
            // se for no dia, retorna e para looping
            if($row['regra'] == 'no-dia' && $row['data'] == date("Y-m-d")){
                $preco_f = $row['valor'];
                break;
            } 
            else if($row['regra'] == 'ate-dia'){
                
                // atribui o valor para no caso de erro do admin                
                // se a data é válida, retorna
                if($row['data'] >= date("Y-m-d")){ 
                    $preco_f = $row['valor'];
                    break;
                }
            }
            
        }
        
        // se não existirem descontos programados... retorna o valor base
        if($preco_f == 0 && isset($prod['valor_base'])){
            $preco_f = $prod['valor_base'];
        } 
        
        
        
        return $preco_f;
        
    }


    // -----------------------------------------------------------------------
    /**
     * Insere/atualiza dados no módulo loja.
     * 
     * @param array $var
     * @return int 
     */
    function conteudo_salva($var)
    {
        // - salva os dados do menu principal Raiz
        $grupo    = $this->input->post('grupos');
        $rel      = $this->input->post('rel');
        $titulo   = $this->input->post('titulo');
        $nick     = $this->input->post('nick');
        $data1    = $this->input->post('dt1');
        $data2    = $this->input->post('dt2');
        $hora1    = $this->input->post('hora1');
        $hora2    = $this->input->post('hora2');
        $status   = $this->input->post('status');
        $resumo   = $this->input->post('resumo');
        $tags     = $this->input->post('tags');
        $txt      = $this->input->post('txt');
        $txtmulti = $this->input->post('txtmulti');
        $mytags   = $this->input->post('mytags');
        
        $dados['tipo']      = 'conteudo';
        $dados['titulo']    = $titulo;
        $dados['resumo']    = $resumo;
        $dados['dt_ini']    = formaSQL($data1);
        $dados['dt_fim']    = formaSQL($data2);
        $dados['hr_ini']    = $hora1;
        $dados['hr_fim']    = $hora2;
        $dados['grupo']     = $grupo;
        $dados['modulo_id'] = $var['co'];
        $dados['tags']      = $tags;
        $dados['status']    = $status;
        $dados['txt']       = campo_texto_utf8($txt);
        $dados['rel']       =  prep_rel_to_sql($rel);
        $dados['atualizado'] = date("Y-m-d H:i:s");
//        mybug($dados['rel']);
        
        
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['lang'] = get_lang();
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => '', 'nick' =>$dados['nick']), $grupo, $var['co']);
            $dados['dt_ini'] = date("Y-m-d");
            
            //mybug($dados);

            $sql = $this->db->insert('cms_conteudo', $dados);
            $esteid = $this->db->insert_id();
            
            // faz atualização da tabela complementar cms_produtos       
            $prod['conteudo_id'] = $esteid;
            $prod['codigo']         = '';
            $prod['download']       = 0;
            $prod['download_limit'] = 5;
            $prod['estoque']        = 5;
            $prod['dimensoes']      = '0x0x0';
            $prod['peso']           = 0;
            $this->db->insert('cms_produtos', $prod);
            
            
            // -- >> LOG << -- //
            $oque = "Novo produto: <a href=\"" . cms_url('cms/loja/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            
            $nick = $this->input->post('nick_edita');
            
            // dados para tabela complementar
            $codigo         = $this->input->post('codigo');
            $download       = $this->input->post('download');
            $download_limit = $this->input->post('download_limit');
            $estoque        = $this->input->post('estoque');
            $dimensoes      = $this->input->post('dimensoes');
            $peso           = $this->input->post('peso');
            $valor_base     = $this->input->post('valor_base');

            $prod['codigo']         = $codigo;
            $prod['download']       = $download;
            $prod['download_limit'] = $download_limit;
            $prod['estoque']        = $estoque;
            $prod['dimensoes']      = $this->prep_dimensoes($dimensoes);
            $prod['peso']           = $this->prep_peso($peso); 
            $prod['valor_base']     = moneyFormat($valor_base); 

            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => $var['id'], 'nick' =>$dados['nick']), $grupo, $var['co']);
            $dados['txtmulti'] = $this->paginas_model->concatenateMultiContents();
            
            // salva dados Se existir sobre o preço e descontos
            $this->calendario_model->save_preco_desconto($var);
            
            // salva opções de produtos
            $this->save_post_options($var);
            
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            
            // faz atualização da tabela complementar cms_produtos
            $this->db->where('conteudo_id', $var['id']);
            $this->db->update('cms_produtos', $prod);
            
            // -- >> LOG << -- //
            $oque = "Atualizou produto: <a href=\"" . cms_url('cms/loja/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }
        
        // faz atualização das tags. precisa do ID, por isso está aqui
        $this->paginas_model->set_tag_conteudo($mytags, $var);
        

        return $esteid;
    }
    
    // -------------------------------------------------------------------------
    
    public function conteudo_salva_copia($var){
        
        $grupo = $this->input->post('grupos');
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $data = trim($this->input->post('dt1'));
        $status = $this->input->post('status');
        
        $conteudo = $this->conteudo_dados($var);
                 
         

        
        $dados['titulo']   = $titulo;
        $dados['ordem']    = $conteudo['ordem'];
        $dados['resumo']   = $conteudo['resumo'];
        $dados['dt_ini']   = formaSQL($data);
        $dados['tags']     = $conteudo['tags'];
        $dados['grupo']    = $grupo;
        $dados['modulo_id'] = $conteudo['modulo_id'];
        $dados['tipo']     = $conteudo['tipo'];
        $dados['status']   = $status;
        $dados['txt']      = $conteudo['txt'];
        $dados['rel']      = $conteudo['rel'];
        $dados['autor']    = $this->phpsess->get('admin_id', 'cms');
        $dados['hr_ini']   = date("H:i:s");
        $dados['lang']     = $conteudo['lang'];
        $dados['txtmulti'] = $conteudo['txtmulti'];
        $dados['show']     = $conteudo['show'];
        $dados['galeria']  = $conteudo['galeria'];
        $dados['destaque'] = $conteudo['destaque'];
        $dados['img']      = $conteudo['img'];
        $dados['semana']   = $conteudo['semana'];
        $dados['extra']    = $conteudo['extra'];
        $dados['nick']     = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
        
        $sql = $this->db->insert('cms_conteudo', $dados);
        $esteid = $this->db->insert_id();
        
        // faz atualização da tabela complementar cms_produtos 
        $prod['conteudo_id'] = $esteid;
        $prod['codigo']         = $conteudo['codigo'];
        $prod['download']       = $conteudo['download'];
        $prod['download_limit'] = $conteudo['download_limit'];
        $prod['estoque']        = $conteudo['estoque'];
        $prod['dimensoes']      = $conteudo['dimensoes'];
        $prod['peso']           = $conteudo['peso'];
        $this->db->insert('cms_produtos', $prod);
        
        // recupera opções do post e salva
        $options = $this->get_options($conteudo['id']);        
        $this->save_post_options($options, $esteid);
        
        // copia preços e descontos
        $this->calendario_model->copy_preco_desconto($conteudo['id'], $esteid);
        
        // -- >> LOG << -- //
        $oque = "Cópia de produto: <a href=\"" . cms_url('cms/loja/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
        $this->cms_libs->faz_log_atividade($oque);
        
        $new_tags = array();
        $t = $this->paginas_model->get_conteudo_tags($var['id']);
        
        
        if(count($t) > 0){
            foreach($t as $tag){
                $new_tags[] = $tag['id'];
            }
            
            // faz atualização das tags. precisa do ID, por isso está aqui
            $this->paginas_model->set_tag_conteudo($new_tags, $esteid);
        }

        return $esteid;        
    }
    
    // ----------------------------------------------------------------------

    /**
     * Recebe o ID do conteúdo e parseia o POST para salvar as opções do 
     * produto. Se a opção $conteudo_id for INT significa que $vars é a cópia
     * das opções de outro conteúdo, não vem do $_POST.
     * 
     * @param       array           $vars
     * @param       boolean|int     $conteudo_id
     */
    public function save_post_options($vars, $conteudo_id = false){
                
        // cópia de outro produto
        if($conteudo_id){
            $id = $conteudo_id;
            $options = $vars;
            $remove = false;
        } 
        // vem do $_POST
        else {
            //$co = $vars['co']; // módulo ID
            $id = $vars['id']; // conteudo ID
            $options = $this->input->post('prod_option');
            $remove = $this->input->post('options_remove');
        }
        
//        mybug($options, true);
//        mybug($_POST['option_1']);
//        mybug($_POST['option_1_1']['prod_opt_value']);
        
        // percorre POST - faz tratamentos
        foreach($options as $key => $opt){
                       
            $nome = ($conteudo_id) ? $opt['titulo'] : $opt['nome'];
            $ordem = $opt['ordem'];
            $diminuir = ($conteudo_id) ? $opt['destaque'] : $opt['diminuir'];
            $prod_opt_value = $opt['prod_opt_value'];
            
            // insere uma nova opção
//            [titulo] Nome da opção 
//            [grupo]  0
//            [ordem]  int
//            [tipo]   'prod_opcao'
            $dados['titulo'] = $nome;
            $dados['ordem']  = $ordem;
            $dados['grupo']  = 0;
            $dados['tipo']   = 'prod_opcao';
            $dados['destaque'] = $diminuir;
            $dados['rel']    = $id;// campo de ligação com conteúdo
            
            if($conteudo_id || $_POST['option_'.$key] == 'novo'){
                $this->db->insert('cms_conteudo', $dados);
                $option_id = $this->db->insert_id();
            } else {
                $option_id = $key;
                $this->db->where('id', $key);
                $this->db->where('tipo', 'prod_opcao');
                $this->db->update('cms_conteudo', $dados);
            }
            
            
            
            // percorre os valores da opção
//            [codigo] => 
//            [titulo] => Valor da opção #1
//            [estoque] => 0
//            [preffix] => +
//            [valor] => 0.00
//            [ordem] => 0
            foreach ($prod_opt_value as $okey => $val){
                
                $codigo  = ($conteudo_id) ? $val['txtmulti'] : $val['codigo']; 
                $titulo  = $val['titulo']; 
                $estoque = ($conteudo_id) ? $val['resumo'] : $val['estoque']; 
                $preffix = ($conteudo_id) ? $val['tags'] : $val['preffix']; 
                $valor   = ($conteudo_id) ? $val['txt'] : moneyFormat($val['valor']); 
                $ordem   = $val['ordem']; 
                
//                [txtmulti]  Código
//                [titulo] Nome do valor
//                [grupo]  opcao_id
//                [ordem]  int
//                [tipo]   'prod_opcao'
//                [resumo] quantidade em estoque
//                [tags]   +|- (prefixo)
//                [txt]    valor que influencia o valor base (valor)
                $dados2['txtmulti'] = $codigo;
                $dados2['titulo']   = $titulo;
                $dados2['grupo']    = $option_id;
                $dados2['ordem']    = $ordem;
                $dados2['tipo']     = 'prod_opcao';
                $dados2['resumo']   = $estoque;
                $dados2['tags']     = $preffix;
                $dados2['txt']      = $valor;
                $dados2['destaque'] = $diminuir;
                
                
                if($conteudo_id || $_POST['option_'.$key.'_'.$okey] == 'novo'){
                    $this->db->insert('cms_conteudo', $dados2);
                } else {
                    $this->db->where('id', $okey);
                    $this->db->where('tipo', 'prod_opcao');
                    $this->db->update('cms_conteudo', $dados2);
                }
                
                
                
            }
            
        }
        
        // para remover
        if(is_array($remove)){
            foreach($remove as $k => $id){
                $this->db->where('id', $id);
                $this->db->delete('cms_conteudo');
                // para não deixar valores
                $this->db->where('grupo', $id);
                $this->db->where('tipo', 'prod_opcao');
                $this->db->delete('cms_conteudo');
            }
        }
        
    }


    // ----------------------------------------------------------------------
    
    /**
    * Pega os dados na Library e parseia os dados
    *
    * @param mixed $var
    * @return
    */
    function conteudo_dados($var)
    {
        
        $this->db->from('cms_conteudo as conteudo');        
        $this->db->join('cms_produtos as prod', 'conteudo.id = prod.conteudo_id');
        $campos = ', prod.codigo, prod.download, prod.download_limit, prod.estoque, prod.dimensoes, prod.peso, prod.valor_base';
        $this->db->join('cms_conteudo as grupo', 'conteudo.grupo = grupo.id');
        $campos .= ', grupo.nick as grupo_nick, grupo.titulo as grupo_titulo, grupo.id as grupo_id';
        $this->db->select('conteudo.*'.$campos);
        $this->db->where('conteudo.id', $var['id']);
        
        $row = $this->db->get();        
        
        if ($row->num_rows() == 0){
            return false;
        }
        
        // percorre array
        $saida = array();
        $post = $row->row_array();
        foreach($post as $chv => $vlr) {
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
            // trata horas
            if ($chv == 'hr_ini')$saida['hora1'] = substr($vlr, 0, 5);
            if ($chv == 'hr_fim')$saida['hora2'] = substr($vlr, 0, 5);
            
            
            
            if($chv == 'id'){
                // pega grupo com seus parentes, se houver
                $grupoParents = $this->paginas_model->getGrupoParents($post['grupo_id'], $post['modulo_id']);
                $saida['grupoParents'] = $grupoParents;
            }
            
            
            
            // coloca no array
            $saida[$chv] = $vlr;
        }
        
        
        return $saida;
    }
    
    // ------------------------------------------------------------------------
    /**
     * Retorna as opções de produto num array multi.
     * 
     * @param       array       $vars
     * @return      boolean
     */
    public function get_options($vars, $select = 'minimum'){
        
        if(is_array($vars)){
            $id = $vars['id'];
        } else {
            $id = $vars;
        }
        
        // define se pegará todos os campos
        if($select == 'all'){
            // para clonar é melhor todos os campos
            $opt_select = '*';
            $val_select = '*';
        } else {
            $opt_select = 'id, titulo, ordem, rel, destaque';
            $val_select = 'id, titulo, grupo, ordem, resumo, txt, txtmulti, tags, destaque';
        }
        
        // pega as opções do conteúdo e retorna um array multi
        $opcoes = array();
        // primeiro as opções
        $result = $this->db->where('rel', $id)
                ->where('grupo', 0)
                ->where('tipo', 'prod_opcao')
                ->order_by('ordem, titulo')
                ->select($opt_select)
                ->get('cms_conteudo');
        
        if($result->num_rows() == 0){
            return false;
        }
        
        // busca os valores das opções
        foreach($result->result_array() as $row){
            
            // faz busca pelos valores
            $result2 = $this->db->where('grupo', $row['id'])
                    ->where('tipo', 'prod_opcao')
                    ->order_by('ordem, id')
                    ->select($val_select)
                    ->get('cms_conteudo');
            
            if($result2->num_rows() == 0){
                //continue;
            }
            
            $row['prod_opt_value'] = $result2->result_array();
            
            $opcoes[] = $row;
        }
        
        return $opcoes;
    }


    // -------------------------------------------------------------------------
 
    /**
     * Recebe os IDs da galeria, retorna um array com os arquivos
     * @param       string      $gallery_ids
     * @param       boolean     $all
     * @return      boolean
     */
    private function get_thumb($gallery_ids, $all = false){
        
        $ids = explode('|', $gallery_ids);
        
        if(empty($ids)){
            return false;
        }
        
        // pega só a primeira
        if($all === false){
            $ids = array($ids[0]);
        }
        
        // se existir o atributo de excluir tags, ele se sobrepõe ao
        // filtro pelas tags de imagens
//        if($this->gallery_no_tag){
//            $this->ci->db->where('tag_opt !=', $this->gallery_no_tag);
//        }
        // filtra imagens tagueadas
//        else if($this->gallery_tag !== FALSE){
//            $this->ci->db->where('tag_opt', $this->gallery_tag); 
//        } 
        
        
        $this->db->where_in('id', $ids);
        $sql = $this->db->get('cms_arquivos');
        
        if($sql->num_rows() == 0){
            return FALSE;
        } else if($sql->num_rows() == 1){
            return $sql->row_array();
        } else {
            return $sql->result_array();
        }
    }
    
    // -------------------------------------------------------------------------
    /**
     * Prepara string com as dimensões eliminado erros de estrutura.
     * A saída deve ser 0.00x0.00x0.00
     * @param string $str
     * @return string
     */
    public function prep_dimensoes($str){
        
        $pieces = explode('x', $str);
        
        if(count($pieces) != 3){
            return '0x0x0';
        }
        
        $return = '';
        
        for($x = 0; $x < 3; $x++){
            
            $uni = trim($pieces[$x]);
            $uni = str_replace(',', '.', $uni);
            $uni = str_replace(' ', '', $uni);
            
            $return .= $uni.'x';
            
        }
        
        return trim($return, 'x');
        
    }
    
    // -----------------------------------------------------------------------
    /**
     * Prepara o peso para salvar
     * @param string $str
     * @return string
     */
    public function prep_peso($str){
        
        $uni = trim($str);
        $uni = str_replace(',', '.', $uni);
        $uni = str_replace(' ', '', $uni);

        return $uni;
    }
    
    // ----------------------------------------------------------------------
    /**
     * Parseia as opções e retorna array com quantidade.
     * 
     * @param type $options_array
     * @return array
     */
    public function get_estoque_from_options($options_array){
        
        if($options_array === FALSE){
            return FALSE;
        }
        
        $return = array();
        
        $quant = 0;
        $soma = '';
        
        foreach($options_array as $row){
            
            if($row['destaque'] == 1 && count($row['prod_opt_value']) > 0){
                
                // percorre as opções para colecionar quantidades
                foreach($row['prod_opt_value'] as $opt){
                    $quant += $opt['resumo'];
                    $soma .= $opt['resumo'].'+';
                }
            }
            
        }
        
        if(strlen($soma) == 0){
            return FALSE;
        }
        
        $return = array(
            'estoque' => $quant,
            'soma'    => trim($soma, '+') // n+n+n
        );
        
        return $return;
        
    }
    
    
    
    /**
     * Faz busca pelos grupos recursivamente
     * Não tem paginação
     */
    function lista_regioes($uriVars) {
        
        $this->db->where('grupo', 0);
        $this->db->where('tipo', 'entrega');
        $this->db->where('lang', get_lang());
        $this->db->where('modulo_id', $uriVars['co']);
        $this->db->order_by('ordem');

        $sql = $this->db->get('cms_conteudo');
        
        $this->load->model('cms/posts_model', 'posts');

        $saida = array();
        foreach ($sql->result_array() as $row) {                

            $saida[] = $this->posts->_parse_grupo($row);
        }

        return $saida;
    }
    
    // -----------------------------------------------------------------------
    
    /**
     * Insere/atualiza dados no módulo calendário.
     * 
     * @param array $var
     * @return int 
     */
    public function entrega_salva($var)
    {
        
        
        // - salva os dados do menu principal Raiz
        $grupo = 0;
        $rel = $this->input->post('rel');
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $data1 = trim($this->input->post('dt1'));
        $data2 = trim($this->input->post('dt2'));
        $hora1 = trim($this->input->post('hora1'));
        $hora2 = trim($this->input->post('hora2'));
        $status = $this->input->post('status');
        $resumo = trim($this->input->post('resumo'));
        $tags = trim($this->input->post('tags'));
        $txt = trim($this->input->post('txt'));
        $mytags = $this->input->post('mytags');
        $semana = $this->calendario_model->montaStringSemana($this->input->post('seg'), $this->input->post('ter'), $this->input->post('qua'), $this->input->post('qui'), $this->input->post('sex'), $this->input->post('sab'), $this->input->post('dom'));

        $dados['tipo'] = 'entrega';
        $dados['titulo'] = $titulo;
        $dados['resumo'] = $resumo;
        $dados['dt_ini'] = formaSQL($data1);
        $dados['dt_fim'] = formaSQL($data2);
        $dados['hr_ini'] = $hora1;
        $dados['hr_fim'] = $hora2;
        $dados['semana'] = $semana;
        $dados['grupo'] = $grupo;
        $dados['modulo_id'] = $var['co'];
        $dados['tags'] = $tags;
        $dados['status'] = $status;
        $dados['txt'] = campo_texto_utf8($txt);
        $dados['rel'] =  prep_rel_to_sql($rel);
        
//        mybug($var);
        
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['lang'] = get_lang();
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => '', 'nick' =>$dados['nick']), $grupo, $var['co']);
            $dados['txt'] = $txt;

            $sql = $this->db->insert('cms_conteudo', $dados);
            $esteid = $this->db->insert_id();
            // -- >> LOG << -- //
            $oque = "Nova região entrega: <a href=\"" . cms_url('cms/loja/entregaEdita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            
            $nick = $this->input->post('nick_edita');

            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['full_uri'] = $this->paginas_model->get_full_uri(array('id' => $var['id'], 'nick' =>$dados['nick']), $grupo, $var['co']);
            $dados['txtmulti'] = $this->set_nonWorkingdates();
                      
            
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            
            // -- >> LOG << -- //
            $oque = "Atualizou região entrega: <a href=\"" . cms_url('cms/loja/entregaEdita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }
        
        

        return $esteid;
    }
    
    // ------------------------------------------------------------------------
    
    /**
    * Pega os dados na Library e parseia os dados
    *
    * @param mixed $var
    * @return
    */
    function entrega_dados($var)
    {
        
        $dd = $this->cms_libs->conteudo_dados($var, 'cms_conteudo', 'entrega');
 
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
            // trata horas
            if ($chv == 'hr_ini')$saida['hora1'] = substr($vlr, 0, 5);
            if ($chv == 'hr_fim')$saida['hora2'] = substr($vlr, 0, 5);
            // checkboxes dias da semana
            if ($chv == 'semana') {
                $saida['cbSemana'] = $this->calendario_model->semanaCheckBox($vlr);
                $saida['nonWorkingDays'] = $this->nonWorkingDays($vlr);
            }
            
            // extrai as datas
            if($chv == 'txtmulti'){
                $saida['nonWorkingDates'] = $this->nonWorkingDates($vlr);
            }
            
            if($chv == 'id'){
                // pega grupo com seus parentes, se houver
                $grupoParents = $this->paginas_model->getGrupoParents($dd['grupo'], $dd['modulo_id']);
                $saida['grupoParents'] = $grupoParents;
            }
            // coloca no array
            $saida[$chv] = $vlr;
        }
//        mybug($saida);
        
        return $saida;
    }
    
    // -------------------------------------------------------------------------
    
    /**
     * Monta string com objetos para função JS de negação dos dias da semana.
     * 
     * @param type $semana
     * @return string
     */
    public function nonWorkingDays($semana = '0000000'){
        
        $saida = '[';
        
        $sem = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        
        for($x = 0; $x < 7; $x++) {
            
            $valor = $semana[$x];
            
            if($valor == 0){
                $saida .= '['.$sem[$x].'],';
            }
            
        }
        
        $saida = trim($saida, ',');
        
        $saida .= ']';
        
        return $saida;
    }
    
    // ---------------------------------------------------------------------
    /**
     * 
     * [[01, 20, 2011],[03, 04, 2011],[03, 07, 2011],[03, 08, 2011],[03, 09, 2011]]
     * @param type $dates
     */
    public function nonWorkingDates($dates){
        
        $dates = explode(',', $dates);
        
        $saida = '[';
        
        
        for($x = 0; $x < count($dates); $x++) {
            
            $data = $dates[$x];
            
            $y = substr($data, 0, 4);
            $m = substr($data, 5, 2);
            $d = substr($data, -2);
            
            
            $saida .= '['.$m.','.$d.','.$y.'],';
            
            
        }
        
        $saida = trim($saida, ',');
        
        $saida .= ']';
        
        return $saida;
        
    }


    // ---------------------------------------------------------------------
    /**
     * Recebe um array de datas. Remove incorretas, duplicadas e combina numa
     * string para BD.
     * 
     * @param       array       $array
     * @return      string
     */
    public function set_nonWorkingdates(){
        
        $nondates      = $this->input->post('nondates');
        $nondates_n    = $this->input->post('nondates_n');
        $dates_for_all = ($this->input->post('dates_for_all')=='1') ? TRUE : FALSE;
        $datas         = array();
        
                
        // percorre as NOVAS datas
        if(!empty($nondates_n)){
            foreach ($nondates_n as $c => $v){

                $sql = magic_valid_data($v);

                if($sql && $sql > date("Y-m-d")){
                    $datas[] = $sql;
                }            

            }   
        }
        
        // se é para atualizar todas as regiões, recupera os IDs
        if($dates_for_all && !empty($nondates_n)){
            
            $uri = $this->uri->to_array(array('co'));
            $regioes_ids = array();
            $regioes = $this->lista_regioes(array(
                'co' => $uri['co']
            ));
            
            // separa os IDs
            foreach ($regioes as $row){
                $regioes_ids[] = $row['id'];
            }
            
            // atualiza todas as regiões
            $this->update_nonWorkingDates($datas, $regioes_ids);
            
        }
        
        // percorre as datas existentes
        if(!empty($nondates)){
            foreach ($nondates as $c => $v){

                $sql = magic_valid_data($v);

                if($sql && $sql > date("Y-m-d")){
                    $datas[] = $sql;
                }            

            }
        }
        
        
        $datas = array_unique($datas);
        
        sort($datas, SORT_STRING);
      
        
        $return = trim(implode(',', $datas), ',');
        
        return $return;
        
    }
    
    // ---------------------------------------------------------------------
    /**
     * Recebe um array de datas no formato SQL e um array com os IDs das
     * regiões que serão atualizadas.
     * 
     * @param       array       $dates_array
     * @param       array       $posts_ids
     * @return      void
     */
    public function update_nonWorkingDates($dates_array, $posts_ids){
        
        
        // se não existir mais de uma região isso não faz sentido
        if(count($posts_ids) < 2){
            return FALSE;
        }
        
        // percorre cada região de entrega
        foreach($posts_ids as $c => $id){
            
            $return = $this->db->where('id', $id)
                    ->select('txtmulti')
                    ->get('cms_conteudo');
            $regiao = $return->row_array();
            
            $datas = explode(',', $regiao['txtmulti']);
            
            // combina os arrays
            $datas = array_merge($datas, $dates_array);
            $datas = array_unique($datas);
            sort($datas, SORT_STRING);
            $datas_str = trim(implode(',', $datas), ',');
//            mybug($datas_str);
            // atualiza as datas desta região
            $this->db->where('id', $id);
            $this->db->update('cms_conteudo', array('txtmulti' => $datas_str));
            
        }        
        
    }
    
    // ----------------------------------------------------------------------
    /**
     * Lista os extratos gerados.
     * 
     * @return type
     */
    public function get_vendas(){
        
        $var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'co', 'pag'));
        
        $pps = $this->config->item('pagination_limits');
        $pp = ($var['pp'] == '') ? $pps[0] : $var['pp']; // por página
        
        if($var['pag'] == ''){
            $offset = 0;
        } else {
            $offset = ($var['pag']-1) * $pp;
        }
        
        // opções de filtro
        $uri_filters = $this->set_vendas_filters();
        
        // pega dados do extrato
        $this->db->where('extrato.modulo_id', $var['co']);
        $this->db->from('cms_extratos as extrato');
        $campos = 'extrato.*';
        $this->db->order_by('extrato.data desc, extrato.hora desc');
        
        // usuário
        $this->db->join('cms_usuarios as user', 'user.id = extrato.usuario_id');
        $campos .= ', user.nome as user_nome';
        
        $this->db->select('SQL_CALC_FOUND_ROWS '.$campos, false);
        
        $this->db->limit($pp, $offset); 
        
        $return = $this->db->get();
        
//        mybug($this->db->last_query());
        // -- pega o Total de registros -- //
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $ttl_rows = $query->row()->Count;

        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/loja/vendas/co:' . $var['co'].$uri_filters );
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 11; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);

        
        $saida = array('ttl_rows' => $ttl_rows,
            'rows' => $this->parse_vendas($return->result_array())
        );

        return $saida;
        
    }
    
    // ----------------------------------------------------------------------
   
    /**
     * Parseia $_POST e URI para saber se existem filtros ativos
     * Retorna URI para paginação.
     * 
     * @return string
     */
    private function set_vendas_filters(){
        
        // define os campos que serão usados no filtro
        $campos_usados[] = array('campo' => 'id', 'type' => 'int');
        $campos_usados[] = array('campo' => 'data', 'type' => 'date');
        $campos_usados[] = array('campo' => 'status', 'type' => 'int');
        $campos_usados[] = array('campo' => 'usuario_id', 'type' => 'int');
        $campos_valorados = array();
        
//        mybug($this->input->post());
        
        // uri de filtros para paginação
        $return = '';
  
        // verifica se veio pelo POST ou URI
        foreach($campos_usados as $row){
            
            $campo = $row['campo'];
            $type  = $row['type'];
            $uri = $this->uri->to_array('filter_'.$campo);
            
            // tem post?
            if(isset($_POST['filter_'.$campo])){
                $valor = $_POST['filter_'.$campo];
            } 
            // tem na URI
            else if($uri['filter_'.$campo] != '') {                
                $valor = $uri['filter_'.$campo];
            } else {
                $valor = FALSE;
            }
            
            // acrescenta o valor
            $row['valor'] = $valor;
            $campos_valorados[] = $row;
            
        }
        
        
        // faz pesquisa
        foreach($campos_valorados as $row){
            
            if($row['valor']){
                
                $campo = $row['campo'];
                $type  = $row['type'];
                $valor = $row['valor'];
                
                // se for data
                if($campo == 'data' && strlen($valor) == 10){
                    $valor = formaSQL($valor);
                }
                
                $this->db->where('extrato.'.$campo, $valor);

                // incrementa uri
                $return .= '/filter_'.$campo.':'.$valor;
                 
            }
        }
        
        
//        mybug($return);
        return $return;
    }


    // -----------------------------------------------------------------------
    
    function parse_vendas($array) {
        
        if (count($array) == 0)
            return false;
        
//        $modulo = $this->modulo;
        
        // percorre array
        $saida = array();


        foreach ($array as $row) {
            
            $row['data'] = formaPadrao($row['data']);
            $row['hora'] = substr($row['hora'], 0, 5);
            
            // pega o status númerico e substitui a anotação
            $status_transacao = $this->config->item('status_transacao');
            $row['anotacao'] = $status_transacao[$row['status']];
            
            
            $saida[] = $row;
        }
//        mybug($saida);
        return $saida;
    }
    
    
    // ----------------------------------------------------------------------
    
    // -------------------------------------------------------------------------
    /**
     * Recebe as variáveis:
     * $vars = array(
     *      'co' => int,
     *      'id' => int
     * )
     * e gera três arrays para extrato.
     */
    
    public function get_full_extrato($vars){
        
        $this->load->library('cms_extrato');
        $this->load->library('cms_conteudo');
        $this->load->library('cms_usuario');
        
        
        if(is_numeric($vars)){
            $extrato_id = $vars;
        } else {
//            $modulo_id   = $vars['co'];
            $extrato_id = $vars['id'];
        }
        // pedido detalhes
        $this->db->from('cms_extratos as extrato');                
        $this->db->where('extrato.id', $extrato_id);
        $campos = 'extrato.*';
                
        // usuário
//        $this->db->join('cms_usuarios as user', 'user.id = extrato.usuario_id');
//        $campos .= ', user.id as user_id, user.nome as user_nome, user.email as user_email';
        
        $this->db->select($campos); 
                
        $extrato_result = $this->db->get();
        
        $return['extrato'] = $extrato_result->row_array();
        // comprovante anexo
        $return['extrato']['comprovante'] = $this->calendario_model->get_comprovante($return['extrato']['comprovante']);
        // incrementa com o histórico
        $return['historico'] = $this->get_extrato_historico($extrato_id);
        // dados do usuário
        $return['usuario'] = $this->cms_usuario->get($return['extrato']['usuario_id']);
        // lista de produtos
        $return['produtos'] = $this->get_carrinho_from_extrato($extrato_id);
        // busca os descontos / cupons
        $return['descontos'] = $this->get_carrinho_descontos($extrato_id);

//        mybug($return, true);
        
     
        
        return $return;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna o histórico do extrato.
     * @param type $id
     * @return boolean
     */
    public function get_extrato_historico($id){
        $result = $this->db->where('extrato_id', $id)
                ->order_by('id')
                ->get('cms_extrat_hist');
        
        if($result->num_rows() == 0){
            return FALSE;
        } else {
            return $result->result_array();
        } 
    }
    
    // ------------------------------------------------------------------------
    /**
     * Pesquisa e retorna as opções completas do produto do extrato.
     * 
     * @param type $extrato_id
     * @return boolean
     */
    public function get_carrinho_from_extrato($extrato_id){
        
                  $this->db->where('extrato_id', $extrato_id);
                  $this->db->where('tipo', 'produto');
        $return = $this->db->get('cms_extrat_produtos');
        
        if($return->num_rows() == 0){
            return FALSE;
        }
        
        // percorre produtos para agregar opções
        $ret = array();
        
        foreach($return->result_array() as $row){
            
            // parseia opções
            $row['opcoes'] = $this->convert_str_to_options($row['opcoes']);
            // pega dados completos do conteúdo
            $row['more'] = $this->conteudo_dados(array('id'=>$row['conteudo_id']));
            
            $ret[] = $row;
        }
        
        return $ret;
        
    }
    
    // ------------------------------------------------------------------------
    /**
     * Retorna dados básicos dos descontos e cupons usados no pedido.
     * 
     * @param       int     $extrato_id
     * @return      array
     */
    public function get_carrinho_descontos($extrato_id){
                
                  $this->db->where('extrato_id', $extrato_id);
                  $this->db->where("(tipo = 'desconto' || tipo = 'cupom')");
//                  $this->db->or_where('tipo', 'cupom');
        $return = $this->db->get('cms_extrat_produtos');
        
        if($return->num_rows() == 0){
            return FALSE;
        }
        
        // percorre produtos para agregar opções
        $ret = array();
        
        foreach($return->result_array() as $row){            
            
            $ret[] = array(
                'id'     => $row['id'],
                'titulo' => $row['conteudo_titulo'],
                'valor'  => $row['valor'],
                'opcao'  => $row['opcoes'],
                'regra'  => $row['subtotal']
            );
        }
        
        return $ret;
    }


    // ----------------------------------------------------------------------
    /**
     * Recebe ID:ID,ID:ID,...
     * Retorna:
     * array(
     *      'ID opção' => 'ID valor',
     *      'ID opção' => 'ID valor'
     * )
     * @param type $str
     * @return boolean
     */
    public function convert_str_to_options($str = ''){
        
        if(strlen($str) < 3){
            return FALSE;
        }
        
        $this->load->library('cms_loja');
        
        // quebra string em array
        //Recebe ID:ID,ID:ID,...
        // Retorna:
        // array(
        //      'ID opção' => 'ID valor',
        //      'ID opção' => 'ID valor'
        // )
        $grupos = explode(',', $str);
        // percorre grupos
        $opt_array = array();
        foreach ($grupos as $val){
            
            $ids = explode(':', $val);
            
            $opt_array[$ids[0]] = $ids[1];
            
        }
        
        return $this->cms_loja->get_options_by_array($opt_array);
        
    }
    
    
    // -------------------------------------------------------------------------
    
    /**
     * Envia email notificando cliente sobre o status do pedido.
     * 
     * Array recebido: 
     *  $dados['data']       = date("Y-m-d");
        $dados['hora']       = date("H:i:s");
        $dados['extrato_id'] = $extrato_id;
        $dados['anotacao']   = $anotacao;
        $dados['status']     = $situacao;
        $dados['obs']        = $comentarios;
        $dados['notificado'] = ($informar == 'false') ? 0 : 1;
     * 
     * @param       array       $dados
     * @return      boolean
     */
    public function send_status_notification($dados){
        
        // pega dados do extrato
        $return = $this->db->where('id', $dados['extrato_id'])
                            ->get('cms_extratos');
        $extrato = $return->row_array();
        
        // pega dados do usuário
        $user_ret = $this->db->where('id', $extrato['usuario_id'])
                             ->select('nome, email')
                             ->get('cms_usuarios');
        $usuario = $user_ret->row_array();
        
        $this->load->library('e_mail');
        
        $emailRem = $this->config->item('email1');
        $nomeRem = $this->config->item('title');
        
        $assunto  = $this->config->item('title') . '. Status do pedido #'.$dados['extrato_id'];
        $emailDes = $usuario['email'];
        $nomeDes  = $usuario['nome'];
        
        $mensagem = '<h3 stype="Arial, sans-serif;">Esta é uma notificação 
            sobre o seu pedido de número '.$dados['extrato_id'].'</h3>
        <p stype="Arial, sans-serif; font-size:14px; line-height:22px;">
            <strong>Status: '.$dados['anotacao'].'</strong>
        </p>
        <p stype="Arial, sans-serif; font-size:14px; line-height:22px;">
            '.auto_link(nl2br($dados['obs'])).'
        </p>';
        
        $urlsite = base_url();


        $dados['siteNome'] = $nomeRem;
        $dados['corpo'] = $mensagem;
        $dados['emailSite'] = $emailRem;
        $dados['urlSite'] = $urlsite;

        $menHTML = $this->load->view('cms/email_usuarios', $dados, true);

        $menTXT = 'Esta é uma notificação 
            sobre o seu pedido de número '.$dados['extrato_id'] . PHP_EOL . $mensagem . PHP_EOL . "
		--------------------------------------------------------------------------------------------------------
		" . PHP_EOL . "
		$emailRem | $urlsite";

        // notifica admin
        $ret = $this->e_mail->envia($emailDes, $nomeDes, $assunto, $menHTML, $menTXT, $emailRem, $nomeRem);

        return $ret;
        
    }
    
    
    
    // -----------------------------------------------------------------------
    /**
     * Insere/atualiza dados dos descontos.
     * 
     * @param array $var
     * @return int 
     */
    function desconto_salva($var)
    {
        
        // - salva os dados 
        $tipo        = $this->input->post('tipo');
        $grupo       = $this->input->post('grupo');
        $titulo      = $this->input->post('titulo');
        $data        = $this->input->post('data');
        $termino     = $this->input->post('termino');
        $status      = $this->input->post('status');
        $regra       = $this->input->post('regra');
        $valor       = $this->input->post('valor');
        $verificador = $this->input->post('verificador');
        
        
        $dados['titulo']    = $titulo;
        $dados['data']      = formaSQL($data);
        $dados['termino']   = formaSQL($termino);
        $dados['grupo']     = $grupo;
        $dados['modulo_id'] = $var['co'];
        $dados['regra']     = $regra;
        $dados['status']    = $status;
        $dados['valor']     = ($regra == '%') ? $valor : moneyFormat($valor);
        
        if($regra == 'acima-de'){
            $verificador = moneyFormat($verificador);
        }        
        $dados['verificador'] = ($verificador) ? $verificador : 1;
        
//        mybug($dados['rel']);
//        mybug($dados);
        
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {           
           
            $dados['tipo']      = $tipo;// uma vez definido, não muda
            $dados['data'] = date("Y-m-d");
            
            

            $sql = $this->db->insert('cms_precos', $dados);
            $esteid = $this->db->insert_id();
            
            // -- >> LOG << -- //
            $oque = "Novo desconto: <a href=\"" . cms_url('cms/loja/descontosEdita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {           

            
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_precos', $dados);
            
            // -- >> LOG << -- //
            $oque = "Atualizou desconto: <a href=\"" . cms_url('cms/loja/descontosEdita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }        

        return $esteid;
    }
    
    // ----------------------------------------------------------------------
    /**
     * Retorna os dados parseados do desconto.
     * 
     * @param type $var
     * @return array
     */
    public function desconto_dados($var){
        
        if(is_numeric($var)){
            $id = $var;
        } else {
            $id = $var['id'];
        }
        
        // retorna dados do desconto
        $result = $this->db->where('id', $id)
                            ->get('cms_precos');
        $dados = array($result->row_array());
        $ret   = array();
        // parseia dados
        foreach ($dados as $row){
            
            $row['data'] = formaPadrao($row['data']);
            $row['termino'] = formaPadrao($row['termino']);
            $row['combo_regra'] = $this->combo_regras($row['tipo'], $row['regra']);
            $row['combo_grupo'] = $this->combo_grupos_cliente($row['grupo']);
            
            $ret[] = $row;
        }
        
        return $ret[0];
    }
    
    // ----------------------------------------------------------------------
    /**
     * Lista os descontos e cupons para carrinho de compras.
     * 
     * @return type
     */
    public function descontos_get(){
        
        $var = $this->uri->to_array(array('pp', 'g', 'dt1', 'dt2', 'b', 'stt', 'co', 'pag'));
        
        $pps = $this->config->item('pagination_limits');
        $pp = ($var['pp'] == '') ? $pps[0] : $var['pp']; // por página
        
        if($var['pag'] == ''){
            $offset = 0;
        } else {
            $offset = ($var['pag']-1) * $pp;
        }
        
        // opções de filtro
        $uri_filters = $this->set_descontos_filters();
        
        // pega dados do extrato
        $this->db->where('precos.modulo_id', $var['co']);
        $this->db->from('cms_precos as precos');
        $campos = 'precos.*';
        $this->db->order_by('precos.ordem, precos.data');
        
        // usuário
        $this->db->join('cms_usuarios as user', 'user.id = precos.grupo', 'left');
        $campos .= ', user.nome as grupo_nome';
        
        $this->db->select('SQL_CALC_FOUND_ROWS '.$campos, false);
        
        $this->db->limit($pp, $offset); 
        
        $return = $this->db->get();
        
//        mybug($this->db->last_query());
        // -- pega o Total de registros -- //
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $ttl_rows = $query->row()->Count;

        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/loja/descontos/co:' . $var['co'].$uri_filters );
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 11; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);

        
        $saida = array('ttl_rows' => $ttl_rows,
            'rows' => $this->parse_descontos($return->result_array())
        );

        return $saida;
        
    }
    
    // ----------------------------------------------------------------------
   
    /**
     * Parseia $_POST e URI para saber se existem filtros ativos
     * Retorna URI para paginação.
     * 
     * @return string
     */
    private function set_descontos_filters(){
        
        // define os campos que serão usados no filtro
        $campos_usados[] = array('campo' => 'titulo', 'type' => 'like');
        $campos_usados[] = array('campo' => 'tipo', 'type' => 'text');
        $campos_usados[] = array('campo' => 'regra', 'type' => 'text');
        $campos_usados[] = array('campo' => 'valor', 'type' => 'money');
        $campos_usados[] = array('campo' => 'data', 'type' => 'date');
        $campos_usados[] = array('campo' => 'termino', 'type' => 'date');
        $campos_usados[] = array('campo' => 'status', 'type' => 'int');
        $campos_usados[] = array('campo' => 'grupo', 'type' => 'int');
        $campos_valorados = array();
        
//        mybug($this->input->post());
        
        // uri de filtros para paginação
        $return = '';        
     
        // verifica se veio pelo POST ou URI
        foreach($campos_usados as $row){
            
            $campo = $row['campo'];
            $type  = $row['type'];
            $uri = $this->uri->to_array('filter_'.$campo);
            
            // tem post?
            if(isset($_POST['filter_'.$campo])){
                $valor = $_POST['filter_'.$campo];
            } 
            // tem na URI
            else if($uri['filter_'.$campo] != '') {                
                $valor = $uri['filter_'.$campo];
            } else {
                $valor = FALSE;
            }
            
            // acrescenta o valor
            $row['valor'] = $valor;
            $campos_valorados[] = $row;
            
        }
        
        // faz pesquisa
        foreach($campos_valorados as $row){
            
            if($row['valor']){
                
                $campo = $row['campo'];
                $type  = $row['type'];
                $valor = $row['valor'];
                
                // se for data
                if($type == 'date' && strlen($valor) == 10){
                    $valor = formaSQL($valor);
                }
                
                // se for moeda
                if($type == 'money' && $valor != ''){
                    $valor = moneyFormat($valor);                    
                }
                    
                // datas são feitas juntas
                if($campo == 'data'){
                    $this->db->where("(precos.data = '".$valor."' || precos.termino = '".$valor."')");
                }
                else {
                    
                    if($type == 'like'){
                        $this->db->like('precos.'.$campo, $valor);
                    } else {
                        $this->db->where('precos.'.$campo, $valor);
                    }
                    
                }

                // incrementa uri
                $return .= '/filter_'.$campo.':'.$valor;
                 
            }
        }
        
        
//        mybug($return);
        return $return;
    }
    
    // -----------------------------------------------------------------------
    
    public function parse_descontos($array){
        
        $return = array();
        
        
        foreach($array as $row){
            
            if ($row['status'] == 1)$row['status'] = 'ativo';
            else if ($row['status'] == 0)$row['status'] = 'inativo';
            else if ($row['status'] == 2)$row['status'] = 'editando';
            
            $tipo = $this->config->item('regra_tipo_'.$row['tipo']);            
            
            if($row['regra'] == '%'){
                $row['valor'] = $row['valor'].'%'; 
            } else {
                $row['valor'] = 'R$ '.moneyBR($row['valor']); 
            }                       
            
            if($row['regra'] == 'acima-de'){
                $row['verificador'] = moneyBR($row['verificador']);
            }
            
            $row['data'] = formaPadrao($row['data']);
            if($row['termino'] == '0000-00-00'){
                $row['termino'] = 'nunca';
            }
            else {
                $row['termino'] = formaPadrao($row['termino']);
            }
            
            if($row['grupo'] == 0){
                $row['grupo_nome'] = 'Todos';
            }
            
            
            $row['regra'] = $tipo[$row['regra']];
            
            
            $return[] = $row;
        }
        
        return $return;
        
    }


    // ------------------------------------------------------------------------
    
    /**
     * Monta combobox com os tipos de regra dos tipos de desconto.
     * 
     * @param       string $tipo
     * @param       string $selected
     * @return      string
     */
    public function combo_regras($tipo = 'desconto', $selected = ''){
        
        $array = $this->config->item('regra_tipo_'.$tipo);
        
        $combo = form_dropdown('regra', $array, $selected, 'id="regra"');
        
        return $combo;
        
    }
    
    // ---------------------------------------------------------------------
    /**
     * Monta combobox dos grupos de usuários.
     * @param       int     $selected
     * @return      string
     */
    public function combo_grupos_cliente($selected = ''){
        
        $return = $this->db->where('grupo', 0)
                        ->where('lang', get_lang())
                        ->where('status', 1)
                        ->order_by('nome')
                        ->select('id, nome')
                        ->get('cms_usuarios');
        
        $grupos = $return->result_array();
        $options = array('0' => 'Todos');
        // prepara para combo
        foreach($grupos as $row){
            $options[$row['id']] = $row['nome'];
        }
        
        $combo = form_dropdown('grupo', $options, $selected, 'id="grupo"');
        
        return $combo;
        
    }
    
    // -----------------------------------------------------------------------
    /**
     * Retorna dados de configuração.
     * 
     * @return type
     */
    public function get_configuracoes(){
        
        $campos = $this->set_configs();
        $saida = array();
        
        foreach($campos as $row){
            
            $return = $this->db->where('campo', $row)->get('cms_config');
            $config = $return->row_array();
            $saida[$row] = $config['valor'];
            
        }
        
        return $saida;
        
    }
    
    // ------------------------------------------------------------------------
    
    /**
     * Verifica se existem os campos de configuração da loja
     */
    public function set_configs(){
        
        // campos de identificação
        $campos = array(
            'loja52_min_pf',
            'loja52_min_pj',
            'loja52_dados'
        );
        
        foreach($campos as $row){
            
            // verifica se existe
            $return = $this->db->where('campo', $row)->get('cms_config');
            
            // se não existir cria campo vazio
            if($return->num_rows() == 0){
                $this->db->insert('cms_config', array(
                    'titulo' => 'Configuração da loja',
                    'campo' => $row
                ));
            }
            
        }
        
        return $campos;
        
    }
    
    // ---------------------------------------------------------------------
    
    public function configuracoes_salva($vars){        
        
        $campos = $this->set_configs();
        $saida = array();
        
        foreach($campos as $row){
            
            $this->db->where('campo', $row);
            $this->db->update('cms_config', array(
                'valor' => $this->input->post($row)
            ));            
            
        }
        
    }
    
    
    // -----------------------------------------------------------------------
    /**
     * Pesquisa produtos com o termo passado e retorna um array para autocomplete
     * @param       string  $term
     * @param       type $config
     * @return      array
     */
    public function search_for_autocomplete($term, $config = array()){
        
        $termo = trim($term);
        
        if(strlen($termo) < 3){
            return '';
        }
        
        
//        $this->db->where("(nome LIKE '%$termo%' || email LIKE '%$termo%' || razao LIKE '%$termo%' || fantasia LIKE '%$termo%')");
        $this->db->like('titulo', $termo);
        $this->db->where('status', 1); 
        $this->db->where('lang', get_lang()); 
        $this->db->where('tipo', 'conteudo'); 
        $this->db->where('modulo_id', 52); 
        
        if(isset($config['limit'])){
            $this->db->limit($config['limit']);
        } else {
            $this->db->limit(10);
        }
        
        if(isset($config['grupos']) && $config['grupos'] === true){
            $this->db->where('grupo', 0);
        } else {
            $this->db->where('grupo !=', 0);
        }
        
        $this->db->order_by('titulo');
        $this->db->select('id, titulo');
        $return = $this->db->get('cms_conteudo');
        
//        return $this->db->last_query();
        
        if($return->num_rows() == 0){
            return '';
        }
        
        $retorno = array();
        
        foreach ($return->result_array() as $row){
            
            $retorno[] = array('id' => $row['id'], 'label' => $row['titulo'], 'value' => $row['titulo']);
            
        }
        
        return $retorno;
        
    }
    
    
    // -----------------------------------------------------------------------
    /**
     * Recebe os ID dos produtos, lê e grava as opções para outro produto.
     * 
     * @param       int         $prod_clone_id
     * @param       int         $prod_ref_id
     * @return      int
     */
    public function clone_options_product($prod_clone_id, $prod_ref_id){
        
        $options = $this->get_options($prod_clone_id, 'all');
        
        if(! $options){
            return 0;
        }
        
        foreach($options as $opt){
            
            unset($opt['id']);// remove, pois será criado novo
            $opt['rel'] = $prod_ref_id;// ID novo produto
            
            $prod_opt_value = $opt['prod_opt_value'];
            unset($opt['prod_opt_value']);
            
            $this->db->insert('cms_conteudo', $opt);
            $opt_id = $this->db->insert_id();
            
            // percorre os valores
            if($prod_opt_value){
                foreach ($prod_opt_value as $val){
                    
                    unset($val['id']);
                    $val['grupo'] = $opt_id;
                    
                    $this->db->insert('cms_conteudo', $val);
                    
                } 
            }
            
        }
        
        return 1;
        
    }
}