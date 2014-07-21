<?php

/**
 *
 * @version $Id$
 * @copyright 2010
 */
class Painel_model extends CI_Model {

    // Paineis artificiais
    public $panels = array(
        array(
            'id' => 991,
            'label' => 'Quadro de mensagens',
            'func'  => 'painel_mensagens'
        )
//        ,
//        array(
//            'id' => 992,
//            'label' => 'O que deseja fazer?',
//            'func'  => 'painel_oquefazer'
//        )
    );
    // Módulos que NÃO serão usados no painel
    public $noPanelMod = array(1, 4, 37);
    // quantidade de itens exibidos nos paineis
    public $panelLimit = 3;

    function __construct() {
        parent::__construct();
    }
    /**
     * Verifica os paineis artificiais e módulos ativos
     * Combina e retorna um array na ordem do admin logado
     */
    function getPanels(){

        $col1 = array();
        $col2 = array();
        /*
         * 1º Pega dados de paineis do admin logado
         */
        $admin = $this->getAdminPainelDados();


        
        /*
         * 2º Pega os paineis
         */
        $artificiais = $this->getPaineisArtificiais($admin);
        

        /*
         * 3º Pega os paineis dos módulos ativos
         */
        $modulos = $this->getPaineisModulos($admin);

//        mybug($modulos);

        // coloca tudo em um array para ser dividido e ordenado
        $todos = array();
        foreach($artificiais as $c=>$v){
            $todos[$c] = $v;
        }
        foreach($modulos as $c=>$v){
            $todos[$c] = $v;
        }

        // total de itens
        $quant = count($todos);
        $quantCol1 = ceil($quant / 2);
        
        

        /*
         * Faz a distribuição e ordenação
         */
        // se não existem dados do admin
        if(! $admin){

            // distribui pelas colunas
            $i = 0;
            foreach($todos as $c=>$v){

                if($i < $quantCol1){
                    $col1[$c] = $v;
                } else {
                    $col2[$c] = $v;
                }

                $i++;
            }

        }
        // se existem dados do admin
        else {

            $todosIdsDoAdmin = array();

            // loop pela ordem do admin, se encontrar os itens incrementa no array
            foreach($admin['col1'] as $c => $v){

                // percorre todos os itens da coluna
                foreach($todos as $cc => $vv){

                    // testa caso a caso
                    if($c == $cc){
                        $col1[$cc] = $vv;

                    }                    

                }
                $todosIdsDoAdmin[] = $c;

            }
            foreach($admin['col2'] as $c => $v){

                // percorre todos os itens da coluna
                foreach($todos as $cc => $vv){

                    // testa caso a caso
                    if($c == $cc){
                        $col2[$cc] = $vv;
                    }
                    

                }
                $todosIdsDoAdmin[] = $c;
            }

            

            // se foi acrescentado algum painel que não consta neste admin
            // acrescenta no final
            foreach($todos as $c => $v){

                // exclue os dados dos painel artificiais
                if(array_search($c, $this->getIdsPanelsArtificiais()) === FALSE){

                    // testa caso a caso
                    // se um módulo foi ativado e os dados não constam para o admin
                    // inserimos no final da coluna 2
                    if(array_search($c, $todosIdsDoAdmin) === FALSE){

                        $col2[$c] = $v;
                    }
                }                
                
            }

           

        }

//        mybug(array('col1'=>$col1, 'col2'=>$col2));

        return $saida = array('col1'=>$col1, 'col2'=>$col2);


    }

    /**
     * Retorna array com os ids dos paineis artificiais
     * @return array
     */
    function getIdsPanelsArtificiais(){
        $saida = array();
        foreach($this->panels as $p){
            $saida[] = $p['id'];
        }

        return $saida;
    }

    /**
     * Chama as funções pre-definidas com os dados para a view
     * dos paineis artificiais
     * @return array
     */
    function getPaineisArtificiais($admin){

        $saida = array();

        // looping em cada item e retorna a view montada
        if(count($this->panels) > 0){

            foreach($this->panels as $p){

                // se existir dados do painel neste admin envia para a view
                $p['status'] = 'shown';
                if($admin){
                    $p['status'] = $this->comparaAdminPanelsStatus($admin, $p['id']);
                }

                $ret = $this->$p['func']($p);

                $saida[$p['id']] = $ret;

            }

        }

        

        return $saida;

    }

    function getPaineisModulos($admin){

        foreach($this->noPanelMod as $noId){
            $this->db->where('id !=', $noId);
        }

        // se for admin segmentado exibe os módulos atribuidos
        $admin_tipo = $this->phpsess->get('admin_tipo', 'cms');
        $admin_mod = explode('|', $this->phpsess->get('admin_mod', 'cms'));

        if($admin_tipo == 2){
            
            $this->db->where('tipo >=', 1);
            $this->db->where_in('id', $admin_mod);

        }
        // admins GOD e SUPER
        else {

            $this->db->where('tipo >=', $admin_tipo);
        }

        $this->db->where('status', 1);
        $this->db->where('grupo', 0);
        
        $this->db->order_by('ordem');
        $this->db->select('id, label, uri, tabela');
        $sql = $this->db->get('cms_modulos');

        $saida = array();

        

        foreach($sql->result_array() as $mod){

            // se existir dados do painel neste admin envia para a view
            $mod['status'] = 'shown';
            if($admin){
                $mod['status'] = $this->comparaAdminPanelsStatus($admin, $mod['id']);
            }
            $saida[$mod['id']] = $this->generatePainel($mod);

        }

//        mybug($saida);

        return $saida;

    }



    /**
     * Retorna dados do painel do admin e transforma no array de saída,
     * se não existe retorna FALSE
     * @return array, boolean
     */
    function getAdminPainelDados(){
        $admin = $this->admin_model->dados_administrador(array('id'=>$this->phpsess->get('admin_id', 'cms')));
        $admin_painel = $admin['painel'];

        // não existe preferências
        if(strlen(trim($admin_painel)) < 2){
            $saida = false;
        } else {

            $colunas = explode('},', $admin_painel);
            $col1 = '';
            $col2 = '';

            // parseia encontrando dados das colunas
            foreach($colunas as $col){

                if(substr($col, 0, 4) == 'col1'){
                    $col1 = substr($col, 5);// eliminando o col1{
                }
                else if(substr($col, 0, 4) == 'col2'){
                    $col2 = substr(trim($col, '}'), 5);// eliminando o col2{
                }

            }

            // transforma os dados em array array(id => status)
            $col1Lista = explode(',', $col1);
            $col1Ordem = array();
            foreach($col1Lista as $c){

                $idStatus = explode('-', $c);

                $col1Ordem[$idStatus[0]] = $idStatus[1];

            }

            $col2Lista = explode(',', $col2);
            $col2Ordem = array();
            foreach($col2Lista as $c){

                $idStatus = explode('-', $c);

                $col2Ordem[$idStatus[0]] = $idStatus[1];

            }

            $saida = array('col1'=>$col1Ordem, 'col2'=>$col2Ordem);

            

        }

//mybug($saida);
        return $saida;
    }

    /**
     * Compara o ID do painel testado com o status do admin
     * Retorna os status: shown ou hidden
     * @param array $admin_painel
     * @param int $painel_id
     * @return string
     */
    function comparaAdminPanelsStatus($admin_painel, $painel_id){

        $saida = 'shown';

        // array do admin está separado em duas colunas
        // testa a primeira
        foreach($admin_painel['col1'] as $c => $v){

            if($c == $painel_id){
                $saida = $v;
            }

        }
        // testa a segunda
        foreach($admin_painel['col2'] as $c => $v){

            if($c == $painel_id){
                $saida = $v;
            }

        }

        return $saida;

    }

    /**
     * Recebe dados via AJAX, organiza dados e salva
     * column1	item1-hidden, item2-hidden, item3-hidden,
     * column2	item4-hidden, item5-shown,
     * Saida dos dados:
     * col1{id-status,id-status},col2{id-status,id-status}
     */
    function salvaOrdemPaineis(){
        $col1 = $this->input->post('column1');
        $col2 = $this->input->post('column2');

        $col1 = trim(trim($col1), ',');
        $col2 = trim(trim($col2), ',');

        $lista1 = explode(',', $col1);
        $lista2 = explode(',', $col2);

        $saida = 'col1{';

        // parse dados col1
        if(is_array($lista1) && count($lista1) > 0){

            $l1Lista = '';
            foreach($lista1 as $l1){

                if(strlen($l1) > 0){
                    $l1Lista .= trim($l1).',';
                }

            }

            $saida .= trim($l1Lista, ',');

        }

        $saida .= '},col2{';

        // parse dados col2
        if(is_array($lista2) && count($lista2) > 0){

            $l2Lista = '';
            foreach($lista2 as $l2){

                if(strlen($l2) > 0){
                    $l2Lista .= trim($l2).',';
                }

            }

            $saida .= trim($l2Lista, ',');

        }

        $saida .= '}';

        // salvar dados para admin
        $this->db->where('id', $this->phpsess->get('admin_id', 'cms'));
        $dados['painel'] = $saida;
        $ret = $this->db->update('cms_admin', $dados);

        return $ret;


    }

    /**
     * Pega últimos registros do módulo e gera view
     * @param array $modulo_dados
     * @return string
     */
    function generatePainel($modulo_dados){

        $dados['vars'] = $modulo_dados;

        $dados['desc'] = '';
        $dados['labels'] = array('data', 'título', 'autor');

        // faz pesquisa pelos dados
         $dados['rows'] = false;
        if($modulo_dados['tabela'] == 'cms_conteudo'){
            $this->db->limit($this->panelLimit);
            $this->db->where('grupo !=', 0);
            $this->db->order_by('dt_ini desc');
            $this->db->where('status', 1);
            $this->db->where('modulo_id', $modulo_dados['id']);
            $this->db->select('id, nick, titulo, dt_ini, autor');
            $this->db->where('lang', get_lang());
            $sql = $this->db->get($modulo_dados['tabela']);

            $rows = $sql->result_array();
            $users = array();
            foreach($rows as $row){

                $autor = $this->admin_model->dados_administrador(array('id'=>$row['autor']));

                $users[] = array(
                    'id' => $row['id'],
                    'titulo' => $row['titulo'],
                    'dt_ini' => formaPadrao($row['dt_ini']),
                    'autor' => $autor['nome']
                );                
            }

            $dados['rows'] = $users;

        }
        else if($modulo_dados['tabela'] == 'cms_usuarios'){

            $this->db->limit($this->panelLimit);
            $this->db->where('grupo !=', 0);
            $this->db->order_by('dt_ini desc');
            $this->db->where('status', 1);
            $this->db->select('id, nome, email, dt_ini');
            $this->db->where('lang', get_lang());
            $sql = $this->db->get('cms_usuarios');

            $rows = $sql->result_array();
            $users = array();
            foreach($rows as $row){

                $users[] = array(
                    'id' => $row['id'],
                    'titulo' => $row['nome'],
                    'dt_ini' => formaPadrao($row['dt_ini']),
                    'autor' => $row['email']
                );

            }

            $dados['desc'] = 'Últimos usuários cadastrados.';
            $dados['labels'] = array('data', 'nome', 'e-mail');
            $dados['rows'] = $users;

        }
        else if($modulo_dados['tabela'] == 'cms_enquete_per'){

            $this->db->limit($this->panelLimit);
            $this->db->order_by('dt_ini desc');
            $this->db->where('status', 1);
            $this->db->select('id, titulo, dt_ini, autor');
            $this->db->where('lang', get_lang());
            $sql = $this->db->get($modulo_dados['tabela']);

            $rows = $sql->result_array();
            $users = array();
            foreach($rows as $row){

                $autor = $this->admin_model->dados_administrador(array('id'=>$row['autor']));

                $users[] = array(
                    'id' => $row['id'],
                    'titulo' => $row['titulo'],
                    'dt_ini' => formaPadrao($row['dt_ini']),
                    'autor' => $autor['nome']
                );

            }

            $dados['labels'] = array('data', 'Pergunta', 'autor');
            $dados['rows'] = $users;
        }

        else if($modulo_dados['tabela'] == 'cms_pastas'){

            $this->db->limit($this->panelLimit);
            $this->db->where('grupo !=', 0);
            $this->db->order_by('dt_ini desc');
            $this->db->where('status', 1);
            $this->db->where('tipo', 1);
            $this->db->select('id, titulo, dt_ini, autor');
            $this->db->where('lang', get_lang());
            $sql = $this->db->get($modulo_dados['tabela']);

            $rows = $sql->result_array();
            $users = array();
            foreach($rows as $row){

                $autor = $this->admin_model->dados_administrador(array('id'=>$row['autor']));

                $users[] = array(
                    'id' => $row['id'],
                    'titulo' => $row['titulo'],
                    'dt_ini' => formaPadrao($row['dt_ini']),
                    'autor' => $autor['nome']
                );

            }

            $dados['labels'] = array('data', 'Álbum', 'autor');
            $dados['rows'] = $users;

        }

        $saida = $this->load->view('cms/painel/modelo_bloco_painel', $dados, true);

        return $saida;

    }

    function painel_atividades() {
        $dados['ativs'] = $this->atividades(50);
        $saida = $this->load->view('cms/painel/atividades_ult', $dados, true);
        return $saida;
    }

    /**
     * Retorna a view do painel "Quadro de mensagens"
     * @param array $vars
     * @return string
     */
    function painel_mensagens($vars) {

        $dados['vars'] = $vars;
        $dados['mens'] = $this->mensagens_internas_lista(5);
        $saida = $this->load->view('cms/painel/mensagens', $dados, true);
        return $saida;
    }

    /**
     * Retorna a view do painel "O que fazer?"
     * @param array $vars
     * @return string
     */
    function painel_oquefazer($vars) {
        $dados['vars'] = $vars;
        $saida = $this->load->view('cms/painel/oquefazer', $dados, true);
        return $saida;
    }

    function painel_suporte() {
        $saida = $this->load->view('cms/painel/suporte', '', true);
        return $saida;
    }

    function atividades($limit = 0) {
        if ($limit != 0
            )$this->db->limit($limit);
        $this->db->order_by('data desc');
        $this->db->order_by('hora desc');
        $this->db->where('quem >=', $this->phpsess->get('admin_tipo', 'cms'));
        $sql = $this->db->get('cms_log_atividades');
        return $this->parse_atividades($sql->result_array());
    }

    function parse_atividades($array) {
        if (count($array) == 0)
            return false;
// percorre array
        $saida = array();
        foreach ($array as $row) {
            $row['data'] = formaPadrao($row['data']);
// dados do admin
            $this->db->where('id', $row['quem']);
            $sql = $this->db->get('cms_admin');
            $admin = $sql->row_array();
            $row['quem'] = $admin['nick'];

            $saida[] = $row;
        }
// echo '<pre>';
// var_dump($saida);
// exit;
        return $saida;
    }

    function mensagens_internas_lista($limit = 0) {
        if ($limit != 0
            )$this->db->limit($limit);
        $this->db->order_by('data desc');
        $this->db->order_by('hora desc');
        $sql = $this->db->get('cms_sis_mens');
        return $this->parse_mensagens_internas_lista($sql->result_array());
    }

    function parse_mensagens_internas_lista($array = array()) {
        if (count($array) == 0
            )return false;
        $saida = array();
        foreach ($array as $row) {
// verifica se este admin já leu a mensagem
            $this->db->where('admin_id', $this->phpsess->get('admin_id', 'cms'));
            $this->db->where('mens_id', $row['id']);
            $sql = $this->db->get('cms_sis_menslig');
            $row['lido'] = ($sql->num_rows() == 0) ? 0 : 1;
// pega o apelido do admin
            $this->db->where('id', $row['admin_id']);
            $this->db->select('nick');
            $sql = $this->db->get('cms_admin');
            $adm = $sql->row_array();
            $row['nick'] = $adm['nick'];
// saida
            $saida[] = $row;
        }

        return $saida;
    }

    function mensagens_nao_lidas($limit = 100) {

        if ($limit != 0)
            $this->db->limit($limit);
        $sql = $this->db->get('cms_sis_mens');
        $conta = 0;
        foreach ($sql->result_array() as $row) {
// verifica se este admin já leu a mensagem
            $this->db->where('admin_id', $this->phpsess->get('admin_id', 'cms'));
            $this->db->where('mens_id', $row['id']);
            $sql = $this->db->get('cms_sis_menslig');

            if ($sql->num_rows() == 0) {
                $conta++;
            }
        }
        return $conta;
    }
    
    /**
     * Retorna a imagem da instituição, ou a String de acordo com a posição.
     * @param type $posicao
     * @return string 
     */
    function getLogotipo($posicao = 'header'){
        $this->db->where('campo', 'logotipo');
        $sql = $this->db->get('cms_config');
        $configs = $sql->row_array();
        
//        mybug($configs);
        
        if(strlen($configs['valor']) > 4){
            
            $saida = '<div class="logotipo"><img src="'.base_url().$this->config->item('upl_arqs').'/'.$configs['valor'].'" alt="" /></div>';
            
        } else {
            // não tem imagem
            if($posicao == 'header'){
                $saida = '<br>';
            } else if($posicao == 'login'){
                $saida = 'Faça seu <span>login</span>';
            }
            
        }
        return $saida;
    }

}