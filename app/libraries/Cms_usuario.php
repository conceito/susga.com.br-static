<?php
/**
 * @dependencies: $this->load->library('cms_conteudo', 'phpsess').
 * 
 * Biblioteca para manipular usuários.
 * 
 * # inserir novo usuário
 *   $this->cms_usuario->insert(array());
 * 
 * # atualiza dados de um usuário
 *   $this->cms_usuario->update('user_id', array());
 * 
 * # insere o usuário, se já existir atualiza dados. O segundo argumento é o 
 * # campo utilizado para comparação.
 *   $this->cms_usuario->insert_update(array(), 'campo_comparação');
 * 
 * # remove usuário
 *   $this->cms_usuario->delete('user_id', 'campo_comaparação');
 * 
 * # pega os dados de um usuário. o segundo argumento salva o usuário na memória
 *   $this->cms_usuario->get('user_id', $persist);
 * 
 * # pega os dados dos usuários de acordo com os argumentos
 *   $this->cms_usuario->get(array('uf' => 'RJ'));
 * 
 * # pega os dados de um grupo de usuários
 *   $this->cms_usuario->get(array(id, id, id, ...));
 * 
 * # pega os dados dos usuários pelo nome do grupo, ou outro campo no segundo argumento
 *   $this->cms_usuario->get_by_groupname('grupo_id_nick', 'nome');
 * 
 * # inscreve o usuário em um conteúdo, caso o usuário já possua inscrição os 
 * # dados serão atualizados, a não ser que o terceiro argumento seja FALSE
 *   $this->cms_usuario->inscribe(array(
 *      'conteudo_id' => int,
 *      'comentario'  => string,
 *      'user_id'     => int,
 *      'status'      => 0|1|2
 *   ), false, true);
 *  
 *  $this->cms_usuario->inscribe($principal, $dependentes, $atualiza_ou_cria);
 * 
 * # pega os dados de inscrição do usuário. Se entrar com o primeiro argumento
 *   usa o ID da inscrição, se entrar com dois argumentos usa o ID do conteúdo
 *   e do usuário
 *   $this->cms_usuario->get_inscription('inscription_id|conteudo_id', 'user_id');
 * 
 * # outros métodos úteis:
 *   $this->cms_usuario->add_visit($user_array);
 * 
 * # faz o login pelos campos passados como argumento.
 * # retorna FALSE em caso de erro, ou dados do usuário e salva na sessão
 *   $this->cms_usuario->do_login(array(
 *                       'email' => 'brunodanca@gmail.com',
 *                       'senha' => md5('123456')
 *                       ));
 * 
 * # remove a sessão do usuário
 *   $this->cms_usuario->do_logout();
 * 
 * # coloca array da sessão
 *   $this->cms_usuario->save_session($data_array, $space = NULL);
 * 
 * # retorna dados da sessão
 *   $this->cms_usuario->get_session();
 */
class Cms_usuario{
    
    private $ci = NULL;
    private $this_user = FALSE; // armazena dados do usuário
    private $this_inscription = FALSE; // armazena dados de inscrição do usuário principal
    private $modulo_id = 25; // ID do módulo de usuários do CMS
    private $session_space = 'user'; // indice da sessão com dados do usuário
    private $session_columns = 'id, nome, razao, email, status, nasc, sexo, visitas, grupo, cnpj'; // colunas que serão retornadas e ficarão na sessão


    public function __construct() {
        $this->ci = &get_instance();
        // dependencies
        $this->ci->load->library('site_utils');
    }
    
    
    // -------------------------------------------------------------------------
    
    /**
     * Retorna dados do usuário. Se passar um array de IDs retorna todos.
     * @param int|array $id_or_array
     * @param bool $persist
     * @return boolean|array
     */
    public function get($id_or_array, $persist = false){
        
        // mais de um ID, pesquisa unicamente pelo ID
        if(is_array($id_or_array) && count($id_or_array) > 1){
            $this->ci->db->where_in('id', $id_or_array);
            
        } else {
            // verifica se já está armazenado
            if($this->this_user && $persist){
                return $this->user_parser($this->this_user);
            }
            // senão continua pesquisa
            // se for array...
            if(is_array($id_or_array)){
                
                // verifica a estrutura do array. se o indice for diferente de 0
                // a pesquisa usará o indice passado            
                $keys = array_keys($id_or_array);
                
                // se for número busca pelo ID
                if(is_numeric($keys[0])){
                    $id_or_array = $id_or_array[0];// o único índice 
                    $this->ci->db->where('id', $id_or_array);
                    
                } else {
                    // se o indice for string a pesquisa será feita pelo campo
                    $this->ci->db->where($keys[0], $id_or_array[$keys[0]]);
                }                
                
            } else {
                // não é um array, aceita apenas o ID
                $this->ci->db->where('id', $id_or_array);
            }            
            
        }
        
        $return = $this->ci->db->get('cms_usuarios');
        
        
        if($return->num_rows() == 0){
            return FALSE;
        } else if($return->num_rows() == 1){            
            if($persist){// salva na memória
                $this->this_user = $return->row_array();
            }
            return $this->user_parser($return->row_array());
        } else {
            return $this->user_parser($return->result_array());
        }        
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna os usuários pelo nome do grupo, ou pelo segundo argumento.
     * @param string $group_name
     * @return boolean|array
     */
    public function get_by_groupname($group_name, $campo = 'nome'){
        
        // identifica o ID do grupo
        $return = $this->ci->db->like($campo, $group_name)
                ->where('grupo', 0)                
                ->select('id')
                ->limit(1)
                ->get('cms_usuarios');
        
        if($return->num_rows() == 0){
            return FALSE;
        } else {
            $group = $return->row_array();
            return $this->get(array('grupo' => $group['id']));
        }
    }

    // -------------------------------------------------------------------------
    /**
     * Parseia os retornos de usuários. Pode ser array simgle ou multi.
     * @param type $array_user
     * @return array
     */
    private function user_parser($array_user){
        
        // verifica se é único, ou lista de usuários
        if(isset($array_user[0]) && is_array($array_user[0])){
            $is_unique = false;            
        } else {
            $is_unique = true;
            $array_user = array($array_user);
        }
        
        $return = array();
        
        // loping ------------------------
        foreach($array_user as $row){
            
            
            // datas
            $row['nasc']   = (isset($row['nasc']))   ? formaPadrao($row['nasc']) : '';
            $row['dt_ini'] = (isset($row['dt_ini'])) ? formaPadrao($row['dt_ini']) : '';
            $row['dt_fim'] = (isset($row['dt_fim'])) ? formaPadrao($row['dt_fim']) : '';
            
            // tipo de usuário
            $row['tipo'] = (strlen($row['cnpj']) < 4) ? 'pf' : 'pj';
            
            // cidade
            $cidade = $this->ci->site_utils->cidade_dados($row['cidade']);
            $row['cidade_id'] = $row['cidade'];
            $row['cidade']    = ($cidade) ? $cidade['nome'] : '';;
            
            // insere a tag adminbar
            $row['adminbar'] = $this->ci->cms_conteudo->adminbar_template('cms/usuarios/edita/co:'.$this->modulo_id.'/id:'.$row['id']);
            
            
            $return[] = $row;
        }
        
        
        if($is_unique){
            $return = $return[0];
        }
        
        return $return;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Insere novo usuário no BD. Em caso de sucesso retorna o ID do usuário.
     * @param array $user
     * @return int|boolean
     */
    public function insert($user){
        
        // ao inserir um usuário, se não existir senha, gera uma automaticamente
        if(! isset($user['senha'])){  
            $this->ci->load->helper('checkfix');
            $user['senha'] = cf_password();        
        }
        
        $data = $this->insert_update_prepare($user);        
                
        $return = $this->ci->db->insert('cms_usuarios', $data);
        
        if($return){
            return $this->ci->db->insert_id();
        } else {
            return FALSE;
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Atualiza os campos passados no segundo argumento. 
     * Retorna o ID em caso de sucesso.
     * @param int $user_id
     * @param array $dados
     * @return int|boolean
     */
    public function update($user_id, $dados){
        
        $data = $this->insert_update_prepare($dados);        
        
        $return = $this->ci->db->update('cms_usuarios', $data, array('id' => $user_id));
        
        if($return){
            return $user_id;
        } else {
            return FALSE;
        }
    }
    
    // -------------------------------------------------------------------------
    /**
     * Faz a pesquisa para ver se o usuário existe baseado no segundo argumento.
     * Se não existe insere, se existe atualiza campos do array.
     * @param array $user
     * @param string $compare
     * @return int|bool
     */
    public function insert_update($user, $compare = 'email'){
        
        // verifica se o usuário existe
        $this->ci->db->where($compare, $user[$compare]);
        $this->ci->db->limit(1);
        $this->ci->db->order_by('id desc');
        $this->ci->db->select('id');
        $return = $this->ci->db->get('cms_usuarios');
                
        if($return->num_rows() == 0){
            // não existe... insere
            return $this->insert($user);
            
        } else {
            // atualiza
            $u = $return->row_array();
            return $this->update($u['id'], $user);
        }
        
    }
    
    // -------------------------------------------------------------------------

    /**
     * Remove o usuário baseado nos argumentos.
     * Faz a limpesa pelas tabelas onde consta o usuário.
     * @param string $indice
     * @param string $compare
     * @return bool
     */
    public function delete($indice, $compare = 'id'){
        
        // se o comparador NÃO for o ID, tem que retornar os dados do usuário.
        if($compare != 'id'){
            $user = $this->get(array($compare => $indice));
        } else {
            $user = array('id' => $indice);
        }
        
        
        // inscrições
        $this->ci->db->where('user_id', $user['id']);
        $this->ci->db->delete('cms_inscritos');
        
        // remove da tabela principal
        $this->ci->db->where('id', $user['id']);
        $return = $this->ci->db->delete('cms_usuarios');
                
        return $return;
        
        
    }

    // -------------------------------------------------------------------------
    /**
     * Prepara os valores antes de inserir no BD.
     * @param array $user
     * @return array
     */
    private function insert_update_prepare($user){
        
        $this->ci->load->helper('checkfix');
        
        if(isset($user['nome'])){
            $data['nome'] = $user['nome'];
        }
        if(isset($user['razao'])){
            $data['razao'] = $user['razao'];
        }
        if(isset($user['fantasia'])){
            $data['fantasia'] = $user['fantasia'];
        }          
        if(isset($user['profissao'])){
            $data['profissao'] = $user['profissao'];
        }
        if(isset($user['atividade'])){
            $data['atividade'] = $user['atividade'];
        }
        if(isset($user['cargo'])){
            $data['cargo'] = $user['cargo'];
        }
        if(isset($user['email'])){
            $data['email'] = $user['email'];
        }
        if(isset($user['email2'])){
            $data['email2'] = $user['email2'];
        }
        if(isset($user['sexo'])){            
            $data['sexo'] = $user['sexo'];// 0 = m, 1 = f
        }
        if(isset($user['nasc'])){
            $data['nasc'] = formaSQL($user['nasc']);
        }
        if(isset($user['cep'])){
            $data['cep'] = cf_cep($user['cep']);
        }
        if(isset($user['logradouro'])){
            $data['logradouro'] = $user['logradouro'];
        }
        if(isset($user['num'])){
            $data['num'] = $user['num'];
        }
        if(isset($user['compl'])){
            $data['compl'] = $user['compl'];
        }
        if(isset($user['cidade'])){
            $data['cidade'] = $user['cidade'];
        }
        if(isset($user['bairro'])){
            $data['bairro'] = $user['bairro'];
        }
        if(isset($user['uf'])){
            $data['uf'] = $user['uf'];
        }
        if(isset($user['bairro'])){
            $data['bairro'] = $user['bairro'];
        }
        if(isset($user['tel1'])){
            $data['tel1'] = tel_to_sql($user['tel1']);
        }
        if(isset($user['tel2'])){
            $data['tel2'] = tel_to_sql($user['tel2']);
        }
        if(isset($user['foto'])){
            $data['foto'] = cf_foto($user);
        }
        if(isset($user['dt_ini'])){
            $data['dt_ini'] = $user['dt_ini'];
        } else {
            $data['dt_ini'] = date("Y-m-d");
        }
        if(isset($user['dt_fim'])){
            $data['dt_fim'] = $user['dt_fim'];
        }
        if(isset($user['rg'])){
            $data['rg'] = cf_rg($user['rg']);
        }
        if(isset($user['cpf'])){
            $data['cpf'] = cf_cpf($user['cpf']);
        }
        if(isset($user['cnpj'])){
            $data['cnpj'] = $user['cnpj'];
        }
        if(isset($user['insc_estadual'])){
            $data['insc_estadual'] = $user['insc_estadual'];
        }
        if(isset($user['insc_municipal'])){
            $data['insc_municipal'] = $user['insc_municipal'];
        }
        if(isset($user['grupo'])){
            $data['grupo'] = $user['grupo'];
        }
        if(isset($user['news'])){
            $data['news'] = $user['news'];
        }
        if(isset($user['senha'])){
            $data['senha'] = cf_password($user['senha']);
        }
        if(isset($user['lang'])){
            $data['lang'] = $user['lang'];
        }
        if(isset($user['obs'])){
            $data['obs'] = $user['obs'];
        }
        if(isset($user['regiao_entrega'])){
            $data['regiao_entrega'] = $user['regiao_entrega'];
        }
        if(isset($user['status'])){
            $data['status'] = $user['status'];
        }
    	
        return $data;       
        
    }
    
    // -------------------------------------------------------------------------


    /**
     * Inscreve usuário no conteúdo. Se houver, insere segundo array como 
     * usuários dependentes.
     * O terceiro argumento verifica se já existe inscrição e atualiza dados,
     * senão cria novo registro.
     * 
     * ** Executar o método abaixo antes para inserir usuários e obter o ID **
     *    $id = $this->cms_usuario->insert_update($dados_user, $campo_comparação);
     * 
     * Dados necessários:
     * $options_array = array(
     *      'conteudo_id' => int,
     *      'comentario'  => string,
     *      'user_id'     => int,
     *      'status'      => 0|1|2
     *   )
     * $dependents_users = array(
     *      'comentario'  => string,
     *      'user_id'     => int
     *   )
     * @param array $options_array
     * @param array|bool $dependents_users
     * @param bool $check_update
     * @return int|bool
     */   
    public function inscribe($options_array, $dependents_users = FALSE, $check_update = TRUE){

        // verifica se os dados básicos estão corretos
        if(!is_numeric($options_array['conteudo_id']) || !is_numeric($options_array['user_id'])){
            log_message('error', 'cms_usuario->inscribe(): os valores conteudo_id ou user_id não estão corretor.');           
            return FALSE;
        }
        
        // dados principais
        $data['conteudo_id'] = $options_array['conteudo_id'];
        $data['user_id']     = $options_array['user_id'];
        $data['comentario']  = (isset($options_array['comentario'])) ? $options_array['comentario'] : '';
        $data['status']      = (isset($options_array['status'])) ? (int)$options_array['status'] : 2;
        $data['ip']          = $this->ci->input->ip_address();
        $data['data']        = date("Y-m-d");
        $data['hora']        = date("H:i:s");
        $data['rel']         = 0; // para usuário principal
        
        $inscription_id = $this->inscribe_ins_upd($data, $check_update);
        
        // se houverem dependentes
        if(is_array($dependents_users)){
            
            // verifica se é UM usuário simples, ou se são vários            
            if(isset($dependents_users['user_id'])){                
                $dependents_users = array($dependents_users);
            }
            
            
            foreach($dependents_users as $user){
                $depend['user_id']     = $user['user_id'];
                $depend['comentario']  = (isset($user['comentario'])) ? $user['comentario'] : '';
                $depend['conteudo_id'] = $data['conteudo_id'];
                $depend['status']      = $data['status'];
                $depend['ip']          = $data['ip'];
                $depend['data']        = $data['data'];
                $depend['hora']        = $data['hora'];
                $depend['rel']         = $inscription_id;
                
                // salva um por um
                $this->inscribe_ins_upd($depend, $check_update);
            }
        }        
        
        return $inscription_id;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Salva dados da inscrição no BD, o segundo argumento verifica se já existe
     * e faz atualização... ou salva um novo registro.
     * Retorna o ID da inscrição.
     * @param array $data
     * @param bool $check_update
     * @return int
     */
    private function inscribe_ins_upd($data, $check_update = TRUE){
        
        // verifica se já existe para atualizar dados
        $insc_exists = FALSE;
        if($check_update){
            
            $return = $this->ci->db->where('conteudo_id', $data['conteudo_id'])
                    ->where('user_id', $data['user_id'])
                    ->order_by('id desc')
                    ->limit(1)
                    ->get('cms_inscritos'); 
            
             $insc_exists = ($return->num_rows() == 1) ? TRUE : FALSE;
            
        }
        
        // Se não for para buscar atualização, OU se a inscrição NÃO existir
        if($check_update === FALSE || $insc_exists === FALSE){
            // insere no BD
            $this->ci->db->insert('cms_inscritos', $data);
            return $this->ci->db->insert_id();
            
        } else {
            
            // se já existe            
            $insc = $return->row_array();
            // acrescenta o comentário, ao invés de removê-lo
            $comment = $insc['comentario'] . PHP_EOL;
            $comment .= ':: atualizado em ' .date("d/m/Y") .' às '.date("h:i").' ::' . PHP_EOL . $data['comentario'];
            $data['comentario'] = $comment;
            // atualiza no BD
            $this->ci->db->update('cms_inscritos', $data, array('id' => $insc['id']));
            return $insc['id'];
            
        }    
    }
    
    // -------------------------------------------------------------------------
    
    /**
     * Pega os dados de inscrição do usuário. Se entrar com o primeiro 
     * argumento usa o ID da inscrição, se entrar com dois argumentos usa o ID 
     * do conteúdo e do usuário.
     * @param int $insc_id_cont_id
     * @param int $user_id
     * @return boolean|array
     */
    public function get_inscription($insc_id_cont_id, $user_id = FALSE){
        
        if(isset($insc_id_cont_id) && $user_id === FALSE){
            $this->ci->db->where('id', $insc_id_cont_id);
        } else {
            $this->ci->db->where('conteudo_id', $insc_id_cont_id);
            $this->ci->db->where('user_id', $user_id);
        }
        
        $this->ci->db->where('rel', 0);// apenas responsável
        $this->ci->db->order_by('id desc');
        $return = $this->ci->db->get('cms_inscritos');
        
        if($return->num_rows() == 0){
            return FALSE;
        } else if($return->num_rows() == 1){
            return $this->inscription_parse($return->row_array());
        } else {        
            return $this->inscription_parse($return->result_array());
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna array com inscrições dependentes.
     * @param int $array_insc
     * @return bool|array
     */
    private function inscription_get_dependents($parent_id){
        
        $return = $this->ci->db->where('rel', $parent_id)
                ->order_by('id')
                ->get('cms_inscritos');
        
        if($return->num_rows() == 0){
            return FALSE;
        } else if($return->num_rows() == 1){
            return $this->inscription_parse($return->row_array());
        } else {        
            return $this->inscription_parse($return->result_array());
        }
    }

    // -------------------------------------------------------------------------
    /**
     * Parseia od dados dos inscritos.
     * @param type $array_insc
     * @return array
     */
    private function inscription_parse($array_insc){
        
        // verifica se é único, ou lista
        if(isset($array_insc[0]) && is_array($array_insc[0])){
            $is_unique = false;            
        } else {
            $is_unique = true;
            $array_insc = array($array_insc);
        }
        
        $return = array();
        
        // loping ------------------------
        foreach($array_insc as $row){
            
            // datas
            $row['data'] = formaPadrao($row['data']);
            
            // hora
            $row['hora'] = substr($row['hora'], 0, 5);
            
            // vrifica se existem dependentes
            $row['dependents'] = $this->inscription_get_dependents($row['id']);
            
            // insere a tag adminbar
//            $row['adminbar'] = $this->ci->cms_conteudo->adminbar_template('cms/usuarios/edita/co:'.$this->modulo_id.'/id:'.$row['id']);            
            
            $return[] = $row;
        }        
        
        if($is_unique){
            $return = $return[0];
        }
        
        return $return;
    }
    
    // -------------------------------------------------------------------------

    /**
     * Faz busca pelos campos passados no argumento.
     * Em caso positivo retorna dados do usuário.
     * @param array $campos_valor_array
     * @return boolean|array
     */
    public function do_login($campos_valor_array){
        
        if(!is_array($campos_valor_array)){
            log_message('error', '$this->cms_usuario->do_login() $campos_valor_array não é array');
            return FALSE;
        }
        
        $return = $this->ci->db->where($campos_valor_array)
                               ->where('status', 1)
                               ->limit(1)
                               ->select($this->session_columns)
                               ->get('cms_usuarios');
        
        if($return->num_rows() == 0){
            return FALSE;
        } else {
            
            $user_data = $this->user_parser($return->row_array());
            // soma visita
            $this->add_visit($user_data);
            // limpa sessão
            $this->do_logout();
            // salva na sessão
            $this->save_session($user_data);
            
            return $user_data;
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Remove seção do usuário.
     */
    public function do_logout(){
        $this->ci->phpsess->clear(null, $this->session_space);
    }
    
    
    // -------------------------------------------------------------------------
    /**
     * Acrescenta uma visita no usuário.
     * @param type $user_array
     */
    public function add_visit($user_array){
        
        if(! isset($user_array['id']) || ! isset($user_array['visitas'])){
            return FALSE;
        }
         
        $data['visitas'] = $user_array['visitas'] + 1;
        $this->ci->db->update('cms_usuarios',$data , array('id' => $user_array['id']));
        
    }


    // -------------------------------------------------------------------------
  
    /**
     * Coloda dados básicos do usuário na sessão.
     * O segundo argumento é para criar uma área diferente na sessão.
     * @param array $data_array
     * @param string $space
     */
    public function save_session($data_array, $space = NULL){
        
        if(!is_array($data_array) || count($data_array) == 0){
            log_message('error', '$this->cms_usuario->save_session() $data_array não é array');
          
        }
        
        $to_session = array();
        
        // delimita os dados que NÃO podem ficar na sessão
        foreach($data_array as $c=>$v){
            if($c == 'senha'){
                continue;
            }
            
            $to_session[$c] = $v;
        }
        
        if($space === NULL){
            $space = $this->session_space;
        }
        
        // insere na sessão
        foreach ($to_session as $c=>$v){
            $this->ci->phpsess->save($c, $v, $space);
        }
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Retorna dados de um espaço da sessão. O padrão é o espaço de usuario.
     * @param string $space
     * @return array
     */
    public function get_session($space = NULL){
        if($space === NULL){
            $space = $this->session_space;
        }
        
        return $this->ci->phpsess->get(NULL, $space);
    }
    
}