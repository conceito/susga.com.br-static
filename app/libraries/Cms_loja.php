<?php

/**
 * @dependencies: $this->load->library('site_utils');
 * @dependencies: $this->load->library('Cms_conteudo');
 * @dependencies: $this->load->library('Cms_usuario');
 * @dependencies: $this->load->library('cart'); @tip: colocar no autoload
 * 
 * Biblioteca para manipular conteúdo do módulo "loja".
 */
class Cms_loja {
    
    private $ci = NULL;
    public  $modulo_id = 52; // ID do módulo para ser reaproveitado
    public  $this_options = FALSE; // opções de produto
    public  $this_descontos = FALSE; // descontos da loja toda
    public  $cart_configs = FALSE; // regras para fechar carrinho


    public function __construct() {
        $this->ci = &get_instance();
        // pode ser instanciada no autoload
        $this->ci->load->library('cart');
        $this->ci->load->library('cms_usuario');
    }
    
    // ------------------------------------------------------------------------
    /**
     * Mapeia os métodos da biblioteca "cms_conteudo" para tornar mais intuitivo
     * o uso da biblioteca "cms_loja".
     * 
     * @param       string      $name
     * @param       array       $arguments
     * @return array
     */
    public function __call($name, $arguments) {
        return $this->ci->cms_conteudo->$name();
    }

    public function get_conteudo(){
        return $this->ci->cms_conteudo->this_page;
    }
    
    // ------------------------------------------------------------------------
    /**
     * Retorna as opções de produto
     */
    public function get_options(){
        
        // se já existe na memória, retorna
        if($this->this_options){
            return $this->this_options;
        }
        
        $this_produto = $this->ci->cms_conteudo->this_page;
        
        
        // pega as opções do conteúdo e retorna um array multi
        $opcoes = array();
        // primeiro as opções
        $result = $this->ci->db->where('rel', $this_produto['id'])
                ->where('grupo', 0)
                ->where('tipo', 'prod_opcao')
                ->order_by('ordem, titulo')
                ->select('id, titulo, ordem, rel, destaque as diminui')
                ->get('cms_conteudo');
        
        if($result->num_rows() == 0){
            return false;
        }
        
        // busca os valores das opções
        foreach($result->result_array() as $row){
            
            // faz busca pelos valores
            $result2 = $this->ci->db->where('grupo', $row['id'])
                    ->where('tipo', 'prod_opcao')
                    ->order_by('ordem, id')
                    ->select('id, titulo, grupo, ordem, resumo as estoque, txt as valor, txtmulti as codigo, tags as preffix, destaque as diminui')
                    ->get('cms_conteudo');
            
            // parseia valores
            $vals = array();
            foreach ($result2->result_array() as $val){
                
                // se o valor da opção altera o valor final
                if((int) $val['valor'] > 0){
                    $val['valor_mod'] = $val['preffix'] . ' R$' . formaBR($val['valor']);
                } else {
                    $val['valor_mod'] = '';
                }
                
                $vals[] = $val;
            }
            
            $row['prod_opt_value'] = $vals;
            
            $opcoes[] = $row;
        }
        
        // salva na memória
        $this->this_options = $opcoes;
        
        return $this->this_options;
        
    }
    
    // ------------------------------------------------------------------------
    /**
     * Retorna um array com TODOS os descontos e cupons pertinentes.
     * Verifica se usuário está logado para verificar o grupo.
     * 
     * @return array
     */
    public function set_descontos_all(){
        
        // se já existe na memória, retorna
        if($this->this_descontos){
            return $this->this_descontos;
        }
        
//        $this_produto = $this->ci->cms_conteudo->this_page;
//        $this->modulo_id = $this_produto['modulo_id'];
        
        // pega todos os descontos e cupons válidos
        $this->ci->db->where('modulo_id', $this->modulo_id);
        $this->ci->db->where('data <=', date("Y-m-d"));
        $this->ci->db->where("(termino >= '".date("Y-m-d")."' || termino = '0000-00-00')");
        $this->ci->db->where('status', 1);
        $this->ci->db->order_by('ordem, data');
        
        // verifica se o usuário está logado
        if($this->ci->cms_usuario->get_session()){
            $user = $this->ci->cms_usuario->get_session();
            $this->ci->db->where("( grupo = '".$user['grupo']."' || grupo = '0') ");
        } else {
            $this->ci->db->where('grupo', 0);
        }
        
        $all = $this->ci->db->get('cms_precos');
        
        if($all->num_rows() == 0){
            return FALSE;
        }
        
        $this->this_descontos = $all->result_array();
        return $this->this_descontos;        
        
    }
    
    // -----------------------------------------------------------------------
   
    /**
     * Retorna apenas os descontos no carrinho.
     * Por padrão retorna o primeiro desconto para não haver acúmulo.
     * Tipos: 
     * 
     * @param       int         $limit
     * @return      boolean
     */
    public function get_descontos(){
        
        // se já existe na memória, retorna
        if($this->this_descontos === FALSE){
            $this->set_descontos_all();
        }
        
        // se ainda dor false não existe descontos
        if($this->this_descontos === FALSE){
            return FALSE;
        }
        
        $descontos = array();
        $valor_cart = $this->ci->cart->total();
        $qty_cart   = $this->ci->cart->total_items();
        
        // verifica se o usuário está logado
        $user_ttl_compras = 0;
        if($this->ci->cms_usuario->get_session()){
            $user = $this->ci->cms_usuario->get_session();
            $user_ttl_compras = $this->get_total_vendas($user['id']);
        }
        
//        mybug(6%3 == 0);
        
        foreach ($this->this_descontos as $row){
            
            if($row['tipo'] == 'desconto'){
                
                // testa o desconto para saber se é válido
                // se for retorna o desconto e PARA
                if($row['regra'] == 'acima-de'){
                    // se o valor do cart for maior
                    if( $valor_cart >= padraoSQL($row['verificador']) ){
                        $descontos = $row;
                        break;
                    }
                } 
                else if($row['regra'] == 'cada-n-pedidos'){
                    // se total de pedidos for maior que '0'
                    // e se for múltiplo do verificador
                    if($user_ttl_compras > 0 && $user_ttl_compras%$row['verificador'] == 0){
                        $descontos = $row;
                        break;
                    }
                }
                else if($row['regra'] == 'quantidade'){
                    // se a quantidade de itens for maior ou igual ao cart
                    if( $qty_cart >= $row['verificador'] ){
                        $descontos = $row;
                        break;
                    }
                }
                
                
            }            
        }
        
        return (empty($descontos)) ? false : $descontos;
        
    }
    
    // -----------------------------------------------------------------------
    /**
     * Verifica o POST para validar cupom.
     * Em caso afirmativo, salva na session: cart_cupom.
     * 
     * @param       string      $input
     * @return      array
     */
    public function set_cupom($input = 'cupom'){
        
        $tem_cupom = FALSE;
        
        $cupom = $this->ci->input->post($input);
        // se foi postado algum cupom
        if(strlen($cupom) > 1){
            
            $cupons = $this->get_cupons();
            
            if($cupons){
                // testa cada um
                foreach ($cupons as $row){
                    if(mb_strtoupper($row['verificador']) == mb_strtoupper($cupom)){
                        $tem_cupom = $row;
                        
                        // coloca na session
                        $this->ci->phpsess->save('cart_cupom', $row);
                        break;
                    }
                }
            }
            
        } else {
            // remove cupom
            $this->ci->phpsess->save('cart_cupom', NULL);
        }
        
        return $tem_cupom;
    }
    
    // -----------------------------------------------------------------------
    /**
     * retorna dados do cupom adicionado pelo usuário... se existir.
     * 
     * @return boolean
     */
    public function get_cupom(){
        
        if($this->ci->phpsess->get('cart_cupom')){
            return $this->ci->phpsess->get('cart_cupom');
        }
        else {
            return FALSE;
        }
        
    }


    // -----------------------------------------------------------------------
    /**
     * Retorna TODOS os cupons válidos da loja.
     * @return array
     */
    public function get_cupons(){
        
        // se já existe na memória, retorna
        if($this->this_descontos === FALSE){
            $this->set_descontos_all();
        }
        
        // se ainda dor false não existe descontos
        if($this->this_descontos === FALSE){
            return FALSE;
        }
        
        $cupons = array();
        
        foreach ($this->this_descontos as $row){
            
            if($row['tipo'] == 'cupom'){
                
                $cupons[] = $row;
                
            }
        }
        
        return $cupons;
        
    }
    
    // ------------------------------------------------------------------------
    /**
     * Retorna a quantidade de pedidos do usuário passado com status == 3
     * 
     * @param       int         $user_id
     * @return      int
     */
    public function get_total_vendas($user_id){
        
        $return = $this->ci->db->where('usuario_id', $user_id)
                ->where('status', 3)
                ->select('id')
                ->get('cms_extratos');
        
        return $return->num_rows();
        
    }
    
    // ------------------------------------------------------------------------
    /**
     * faz conta para descontar no total do carrinho.
     * 
     * @return type
     */
    public function cart_total(){
        
        $desconto = $this->get_descontos();
        
        if($desconto){
            $subtrai = $desconto['valor'];
        } else {
            $subtrai = 0;
        }
        
        $subtotal = $this->ci->cart->total() - $subtrai;
        
        // verifica cupom
        $cupom = $this->get_cupom();
        if($cupom){
            if($cupom['regra'] == '%'){
                $perc = ($subtotal * $cupom['valor']) / 100;
                $subtotal -= $perc;
            } else if($cupom['regra'] == 'R$'){
                $subtotal -= $cupom['valor'];
            }
            
        }
        
        return $subtotal;
            
    }
    
    // -------------------------------------------------------------------------
    /**
     * Se o total do carrinho for menor que o valor dos produtos é porque existe
     * desconto. Retorna o valor da diferença.
     * 
     * @return int|boolean
     */
    public function total_desconto(){
        
        // total dos produtos do carrinho
        $subtotal = $this->ci->cart->total();
        
        // total do carrinho menos descontos
        // total real
        $total = $this->cart_total();
        
        if($total < $subtotal){
            return padraoSQL($subtotal - $total);
        }
        else {
            return false;
        }
        
    }

    // ------------------------------------------------------------------------
    /**
     * Recebe um array sendo o índice o ID da opção e o valor o ID do valor selecionado.
     * array(
     *      'ID opção' => 'ID valor',
     *      'ID opção' => 'ID valor'
     * )
     * @param type $options_array
     * @return boolean|array
     */
    public function get_options_by_array($options_array){
        
        if(!is_array($options_array)){
            return FALSE;
        }
        
        $return = array();
        
        foreach($options_array as $c => $v){
            
            // primeiro a opção
            $result = $this->ci->db->where('id', $c)
                    ->select('id, titulo, ordem, rel, destaque as diminui')
                    ->get('cms_conteudo');
            
            // faz busca pelo valor
            $result2 = $this->ci->db->where('id', $v)
                    ->select('id, titulo, grupo, ordem, resumo as estoque, txt as valor, txtmulti as codigo, tags as preffix, destaque as diminui')
                    ->get('cms_conteudo');
            
            $return[] = array(
                'opcao' => $result->row_array(),
                'valor' => $result2->row_array()
            );
            
        }
        
        return $return;
        
    }


    // ------------------------------------------------------------------------
    /**
     * Faz as verificações sobre as opções que podem alterar o valor do produto
     * @param type $data
     */
    public function insert($data){
        
        // recupera os dados das opções
        if(isset($data['options'])){
            $opcoes = $this->get_options_by_array($data['options']);
            
            foreach ($opcoes as $opt){
                
                $valor   = $opt['valor']['valor'];
                $preffix = $opt['valor']['preffix'];
                
                // se o valor for maior que zero, faz a alteração antes de 
                // colocar no carrinho
                if((double)$valor > 0){
                    if($preffix == '+'){
                        $data['price'] += $valor;
                    } else {
                        $data['price'] -= $valor;
                    }
                }
            }
            
           
        }
        
//         mybug($data);
        
        $this->ci->cart->insert($data);
    }


    // -----------------------------------------------------------------------
    /**
     * Retorna o HTML do carrinho.
     */
    public function output_cart(){
        
        $carrinho = $this->ci->cart->contents();
        
        if(empty($carrinho)){
            $view['cart'] = FALSE;
            $view['total'] = 0;
            $view['itens'] = 0;
        } 
        else {
            $view['cart'] = $this->parse_cart($carrinho);
            $view['total'] = $this->cart_total();
            $view['itens'] = $this->ci->cart->total_items();
        }
        
        
//        mybug($view['cart'], true);
        
        return $this->ci->load->view('site_add/loja-small-cart', $view, true);
    }
    
    // -----------------------------------------------------------------------
    /**
     * [0] => Array
        (
            [rowid] => 10266a996cd2490487c618ab320da91e
            [id] => 124
            [qty] => 1
            [price] => 153.00
            [name] => Cópia de Placa mãe
            [options] => Array
                (
                    [125] => 126
                    [128] => 129
                )

            [subtotal] => 153
        )

     * Prepara dados do carrinho para serem exibidos.
     * 
     * @param       array       $carrinho
     * @return      array
     */
    public function parse_cart($carrinho){
        
        $return = array();
//        mybug($carrinho, true);
        foreach($carrinho as $row){
            
            // imagem de capa
            $this->ci->cms_conteudo->set_page($row['id']);
            $produto = $this->ci->cms_conteudo->get_page();
            $galeria = $this->ci->cms_conteudo->get_page_gallery($row['id']);
            
            // resgata as informações das opções com os IDs passados
            if(isset($row['options']) && $row['options']){
                $produto_options = $this->get_options_by_array($row['options']);
                $row['options'] = $produto_options;
            }
            
//            mybug($galeria);
            // não possui imagem
            if(empty($galeria) || !$galeria){
                $row['thumb'] = '';
            } else {
                $row['thumb'] = base_url().$this->ci->config->item('upl_imgs').'/'.thumb($galeria[0]['nome']);
            }
            
            $row['uri'] = $produto['full_uri'];
            
            
            
            $return[] = $row;
        }
        
        
        return $return;
        
    }
    
    // ---------------------------------------------------------------------
    /**
     * Adapta produtos do carrinho para o modelo do pagseguro.
     * 
     * @return int
     */
    public function carrinho_para_pagseguro(){
        
        $carrinho = $this->ci->cart->contents();
        $products = array();
        
        if(empty($carrinho)){
            return FALSE;
        }
        
        // insere produtos para botão PagSeguro
        
        foreach ($carrinho as $row){
            
            $products[] = array(
                'id' => $row['id'],
                'descricao' => $row['name'],
                'valor' => padraoSQL($row['price']),
                'quantidade' => $row['qty'],
                'peso' => 0
            );
            
        }
        
        return $products;
        
    }
    
    // ------------------------------------------------------------------------
    /**
     * Retorna o campo e valor das configurações para a loja... e salva na memória
     * 
     * @return array
     */
    public function get_cart_configs(){
        
        // verifica se está na memória
        if($this->cart_configs){
            return $this->cart_configs;
        }
        
        // campos de identificação
        $campos = array(
            'loja52_min_pf', // itens mínimos para PF
            'loja52_min_pj', // itens mínimos para PJ
            'loja52_dados'   // dados de endereço e contato
        );
        
        $this->ci->db->where_in('campo', $campos);
        $this->ci->db->select('campo, valor');
        $return = $this->ci->db->get('cms_config');
        
        if($return->num_rows() == 0){
            return FALSE;
        }
               
        // salva dados na memória
        foreach($return->result_array() as $row){
            
            $this->cart_configs[$row['campo']] = $row['valor'];
            
        } 
        
        return $this->cart_configs;
        
    }


    // ------------------------------------------------------------------------
    /**
     * Faz teste para verificar se a regra passou.
     * Se não passar regra no argumento retornam todas as regras.
     * 
     * RULES:
     *      continuar|fechar = pode fechar o pedido
     * 
     * @param type $rule
     * @return boolean
     */
    public function cart_rules($rule = NULL){
        
        // instancia regras
        $this->get_cart_configs();
        
        // se não há regra para checar retorna todas as regras
        if($rule === NULL){
            return $this->cart_configs;
        }
        
        $return = TRUE;
        
        /**
         * verifica se existe algum impedimento para fechar o pedido
         */
        if($rule == 'continuar' || $rule == 'fechar'){
            
            $user = $this->ci->cms_usuario->get_session();
            $total_items = $this->ci->cart->total_items();
            
            if($total_items == 0){
                $return = FALSE;
            }
            
            if( strtolower($user['tipo']) == 'pf' ){
                if(isset($this->cart_configs['loja52_min_pf'])){
                    if( $total_items < $this->cart_configs['loja52_min_pf'] ){
                        $return = FALSE;
                    }
                }
            } else if( strtolower($user['tipo']) == 'pj' ){
                if(isset($this->cart_configs['loja52_min_pj'])){
                    if( $total_items < $this->cart_configs['loja52_min_pj'] ){
                        $return = FALSE;
                    }
                }
            }
        }
        
        return $return;
        
    }
    
}