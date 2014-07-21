<?php
/**
 * Biblioteca para trabalhar com pagamentos e histórico de pedidos.
 * => cms_extratos
 * => cms_extrat_hist
 */
class Cms_extrato {
    
    private $ci = NULL;
    public  $this_extrato = FALSE;  // dados do extrato na memória
    public  $this_historico = FALSE;// dados do histórico na memória


    public function __construct() {
        $this->ci = &get_instance();
    }
    
    
    
    // -------------------------------------------------------------------------
    /**
     * Após gerar a cobrança, este método atualiza o status do extrato e histórico
     * @param int $ref_id
     * @param array $data
     * @return void
     */
    public function update($ref_id, $data = array()){
        
        if(!is_numeric($ref_id)){
            return FALSE;
        }
        
        // dependento do método o tratamento será muito diferente
        // por enquanto só existe 'pagseguro'
        $metodo = $data['metodo'];       
        
        // passa o parseador dinamicamente
        if($metodo == 'pagseguro'){
            $upd = $this->pagseguro_parser($data);
        } else {
            $upd = $this->pagseguro_parser($data);
        }
        
        
        
        $this->ci->db->update('cms_extratos', $upd, array('id' => $ref_id));
        
        // salva no histórico
        $this->history_update(array(
            'extrato_id' => $ref_id,
            'anotacao'   => $upd['anotacao'],
            'status'     => $upd['status'],
            'notificado' => 1
        ));        
        
    }
    
    // -------------------------------------------------------------------------
    
    private function pagseguro_parser($data){
        
        $upd = array();
        
        // garante que estes estarão no array
        $upd['status'] = false;
        $upd['anotacao'] = false;
//        $upd['notificado'] = 1;
        
        if(isset($data['metodo'])){
            $upd['metodo'] = $data['metodo'];
        }        
        if(isset($data['TransacaoID'])){
            $upd['transacao_id'] = $data['TransacaoID'];
        }
        if(isset($data['TipoFrete'])){
            $upd['tipo_frete'] = $data['TipoFrete'];
        }
        if(isset($data['ValorFrete'])){
            $upd['valor_frete'] = $data['ValorFrete'];
        }        
        if(isset($data['TipoPagamento'])){
            $upd['tipo_pagamento'] = $data['TipoPagamento'];
        }
        if(isset($data['StatusTransacao'])){
            
            $upd['anotacao'] = $data['StatusTransacao'];
            
            if($data['StatusTransacao'] == 'Aprovado'){
                $upd['status'] = 3;
            } else if($data['StatusTransacao'] == 'Em Análise'){
                $upd['status'] = 2;
            } else if($data['StatusTransacao'] == 'Aguardando Pagto'){
                $upd['status'] = 1;
            } else if($data['StatusTransacao'] == 'Completo'){
                $upd['status'] = 3;
            } else if($data['StatusTransacao'] == 'Cancelado'){
                $upd['status'] = 7;
            } else {
                $upd['status'] = 0;
            }         
            
        }
        if(isset($data['Parcelas'])){
            $upd['parcelas'] = $data['Parcelas'];
        }
        if(isset($data['comprovante'])){
            $upd['comprovante'] = $data['comprovante'];
        }
//        if(isset($data['notificado'])){
//            $upd['notificado'] = $data['notificado'];
//        }
        
        return $upd;
        
    }


    // -------------------------------------------------------------------------
    /**
     *  parcelas, valor_total, descontos, tipo_frete, valor_frete, data, hora, anotacao, status
     * @param type $xtrt
     */
    public function add($xtrt){
        
        // se existem produtos, remove do array para salvar em tabela própria
        $produtos = FALSE;
        if(isset($xtrt['produtos'])){
            $produtos = $xtrt['produtos'];
            $produtos_descontos = $xtrt['produtos_descontos'];
            $produtos_cupom = $xtrt['produtos_cupom'];
            unset($xtrt['produtos']);
            unset($xtrt['produtos_descontos']);
            unset($xtrt['produtos_cupom']);
        }
        
        // cria novo registro
        $xtrt['data'] = date("Y-m-d");
        $xtrt['hora'] = date("H:i:s");
        
        $ret = $this->ci->db->insert('cms_extratos', $xtrt);
        $pedido_id = $this->ci->db->insert_id();
        
        // cria o histórico
        $hist['extrato_id'] = $pedido_id;
        $hist['data'] = $xtrt['data'];
        $hist['hora'] = $xtrt['hora'];
        $this->ci->db->insert('cms_extrat_hist', $hist);
        
        // salva os produtos que estão no carrinho
        if($produtos){
            $this->insert_cart_to_extrato($pedido_id, $produtos);
            $this->insert_cart_descontos($pedido_id, $produtos_descontos);
            $this->insert_cart_descontos($pedido_id, $produtos_cupom);
        }
        
        
        return $pedido_id;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Insere os desconto na tabela de produtos para manter o histórico.
     * 
     * @param int $pedido_id
     * @param array $produtos_descontos
     */
    public function insert_cart_descontos($pedido_id, $produtos_descontos){
        
        if($produtos_descontos){
            $prod['extrato_id']      = $pedido_id;
            $prod['conteudo_id']     = $produtos_descontos['id'];
            $prod['conteudo_titulo'] = $produtos_descontos['titulo'];
            $prod['quantidade']      = 1;
            $prod['valor']           = $produtos_descontos['valor'];
            $prod['subtotal']        = $produtos_descontos['regra'];
            $prod['tipo']            = $produtos_descontos['tipo'];            
            $prod['opcoes']          = $produtos_descontos['verificador'];            

            $this->ci->db->insert('cms_extrat_produtos', $prod);
        }
        
    }


    // -----------------------------------------------------------------------
    /**
     * Insere os produtos do carrinho relacionado ao extrato gerado.
     * 
     * Carrinho:
     * array
      'rowid' => string '9262d1fbe39660100e7f6b9be0521aec' (length=32)
      'id' => string '124' (length=3)
      'qty' => string '1' (length=1)
      'price' => string '153.00' (length=6)
      'name' => string 'Cópia de Placa mãe' (length=20)
      'options' => 
        array
          128 => string '130' (length=3)
          125 => string '126' (length=3)
      'subtotal' => float 153
     * 
     * @param type $pedido_id
     * @param type $produtos
     */
    public function insert_cart_to_extrato($pedido_id, $produtos){
        
        // percorre produtos
        
        foreach($produtos as $row){
            
            $prod['extrato_id']      = $pedido_id;
            $prod['conteudo_id']     = $row['id'];
            $prod['conteudo_titulo'] = $row['name'];
            $prod['quantidade']      = $row['qty'];
            $prod['valor']           = padraoSQL($row['price']);
            $prod['subtotal']        = padraoSQL($row['subtotal']);
            $prod['tipo']            = 'produto';
            
            if(isset($row['options'])){
                $prod['opcoes'] = $this->options_to_string($row['options']);
            }
            
            $this->ci->db->insert('cms_extrat_produtos', $prod);
            
        }       
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Recebe o array de opções e transforma em string, no padrão:
     * id_opt:id_valor,id_opt:id_valor,id_opt:id_valor,...
     * 
     * @param       array       $options_array
     * @return      string
     */
    public function options_to_string($options_array){
        
        if(empty($options_array)){
            return '';
        }
        
        $str = '';
        
        foreach($options_array as $c => $v){
            
            $str .= $c.':'.$v.',';
            
        }
        
        $str = trim($str, ',');
        
        return $str;
        
    }

    // -------------------------------------------------------------------------
    /**
     * Faz a pesquisa pelo extrato e pelo histórico. Armazena ambos na memória
     * e retorna o extrato.
     * @param int $extrato_id
     * @return array|boolean
     */
    public function get($extrato_id = NULL){
        
        if($extrato_id === NULL){
            return FALSE;
        }
        // se está na memória retorna
        if($this->this_extrato){
            return $this->this_extrato;
        }
        // senão, pesquisa
        $return = $this->ci->db->where('id', $extrato_id)
                ->get('cms_extratos');
        
        if($return->num_rows() == 0){
            return FALSE;
        }
        
        // faz a pesquisa do histórico
        $hist = $this->ci->db->where('extrato_id', $extrato_id)
                ->order_by('id')
                ->get('cms_extrat_hist');
        
        // se existir coloca na memória
        if($hist->num_rows() > 0){
            $this->this_historico = $hist->result_array();
        }
        // coloca na memória
        $this->this_extrato = $return->row_array();
        return $this->this_extrato;
        
    }


    // -------------------------------------------------------------------------
    /**
     * Atualiza o status do pedido em cms_extrato e guarda o histórico em cms_extrat_hist
     * Recebe:
     * $dados = array(
     *      'code' => int,
     *      'status' => string,
     *      'reference' => int ID do pedido
     * )
     * @param string $metodo
     * @param array $ret
     * @internal param array $dados
     */
    public function status_update($metodo = 'pagseguro', $ret = array()){
        
        // dependento do método o tratamento será muito diferente
        // por enquanto só existe 'pagseguro'
        $metodo = $metodo;
        
        // recupera dados do extrato. histórico fica na memória.
        $extrato = $this->get($ret['reference']);
        
        // atualiza status ... apenas
        $dados['anotacao'] = $ret['status'];
        $dados['status'] = $ret['code'];
        
        $ret = $this->ci->db->update('cms_extratos', $dados, array('id' => $ret['reference']));
        
        // salva no histórico
        $this->history_update(array(
            'extrato_id' => $extrato['id'],
            'anotacao'   => $ret['status'],
            'status'   => $ret['code']
        ));
        
        //@todo: notificar cliente e admin
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Acrescenta registro no histórico do pedido.
     * @param array $dados
     * @return boolean
     */
    private function history_update($dados){        
        
        if(!isset($dados['extrato_id']) || !isset($dados['status']) || ! is_numeric($dados['status'])){
            return FALSE;
        }
        
        // pega o último satus
        $last = $this->ci->db->where('extrato_id', $dados['extrato_id'])
                ->order_by('id desc')
                ->limit(1)
                ->get('cms_extrat_hist');
        
        // verfica se houve alguma alteração no status
        if($last->num_rows() > 0){
            $hist = $last->row_array();
            if($hist['status'] == $dados['status']){
                // não houve! retorna.
                return FALSE;
            }
        }
        
        $dados['data'] = date("Y-m-d");
        $dados['hora'] = date("H:i:s");
        
        $ret = $this->ci->db->insert('cms_extrat_hist', $dados);
        
    }
    
    
    
}