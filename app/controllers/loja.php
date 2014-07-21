<?php
/**
 * Modelo de utilização para verificação de pedidos e notificações
 */
class Loja extends Frontend_Controller {

    public function __construct() {
        parent::__construct();
        
        //$this->load->library('cms_loja'); # autoload
        //$this->load->helper('moeda');     # autoload
        $this->load->config('loja');
        
    }
    
    function test(){
        $this->load->library(array('pagseguro', 'cms_extrato'));
        
        $p['metodo'] = 'pagseguro';
        $p['TransacaoID'] = '3E9976E948E04B5D8A415CF4A5484A2E';
        $p['TipoFrete'] = 'FR';
        $p['ValorFrete'] = '0,00';
        $p['TipoPagamento'] = 'Boleto';
        $p['StatusTransacao'] = 'Cancelado';
        $p['Parcelas'] = '0';
        
        
        $this->cms_extrato->update(8, $p);
        
    }

    // -------------------------------------------------------------------------
    /**
     * Pode ser a página principal da loja
     * Rota: $route['loja/[tag:|pag:](:any)'] = "loja/index/$1";
     */
    public function index() {
       
//        $this->cart->destroy();
        // breadcrumb
        $this->breadcrumb->add('Loja');
        
        // exibe lista de posts baseado nos argumentos abaixo:
        $view['posts'] = $this->cms_posts->get(array(
            'modulo_id' => 52,
            'per_page' => 10,
            'base_url' => 'loja/index',
            'gallery_tag' => false,
            'gettags' => false
        ));
        // string de navegação para injetar na view
        $view['pagination'] = $this->cms_posts->pagination();
        $view['total'] = $this->cms_posts->get_total();
//        mybug($view['posts'], true);
        
        
        $this->title = 'Loja';
        $this->corpo = $this->load->view('site_add/looping-links', $view, true);

        $this->templateRender();
        
    }
    
    // ------------------------------------------------------------------------
    /**
     * Método padrão para exibir módulo "loja".
     * Tota: $route['loja/p/(:any)'] = "loja/produto/$1";
     */
    public function produto(){
        
        // shortcodes devem ser inicializados primeiro
        $this->cms_conteudo->shortcode_reg(array('slide'));
        
        
        
        // retorna dados da tabela cms_conteudo parseado
        $this->cms_loja->set_page();
        $this->pagina = $this->cms_loja->get_page();
        // retorna galeria
        $this->pagina['galeria'] = $this->cms_loja->get_page_gallery();
        // retorna os arquivos anexos
        $this->pagina['anexos'] = $this->cms_loja->get_page_attachments();
        // retorna dados do módulo
        $this->pagina['modulo'] = $this->cms_loja->set_get_modulo();
        
        // retorna as páginas, ou grupos a que pertencem para breadcrumb
        $this->pagina['hierarchy'] = $this->cms_loja->get_hierarchy();
        //
        $this->pagina['precos'] =  $this->cms_loja->get_precos();
        $this->pagina['cupons'] =  $this->cms_loja->get_cupons();
        $this->pagina['preco_final'] = $this->cms_loja->preco_final();
        //
        $this->pagina['opcoes'] = $this->cms_loja->get_options();
        $this->json_vars('opcoes', $this->pagina['opcoes']);
//        mybug($this->pagina['opcoes'], true);
        
        
        // breadcrumb
        $this->breadcrumb->add('Loja', 'loja');
//        $this->breadcrumb->add($this->pagina['titulo']);
        
        $this->breadcrumb->add($this->pagina['hierarchy']);
        
        $view['post'] = ''; 
        
//        $this->setNewScript(array('cms_loja'));

        $this->title = $this->pagina['titulo'];
        $this->corpo = $this->load->view('site_add/post-content', $view, true);

        $this->templateRender();
    }
    
    // ------------------------------------------------------------------------
    /**
     * Navegação pelas categorias de produtos
     * Rota: $route['loja/c/(:any)'] = "loja/categoria/$1";
     */
    public function categoria(){
        
        // retorna dados da tabela cms_conteudo parseado
        $this->cms_conteudo->set_page();
        $this->pagina = $this->cms_conteudo->get_page();
//        mybug($this->pagina);
        // exibe lista de posts baseado nos argumentos abaixo:
        $view['posts'] = $this->cms_posts->get(array(
            'modulo_id' => 52,
            'per_page' => 10,
            'grupo_id' => $this->pagina['id'],
            'base_url' => 'loja/c/'.$this->pagina['nick'],
            'gallery_tag' => false,
            'gettags' => false
        ));
        // string de navegação para injetar na view
        $view['pagination'] = $this->cms_posts->pagination();
//        mybug($view['posts'], true);
        
        
        // breadcrumb
        $this->breadcrumb->add('Loja', 'loja');
        $this->breadcrumb->add($this->pagina['titulo']);
        
        
        $view['post'] = 'Grupo';        

        $this->title = 'Grupo - ' . $this->pagina['titulo'];
        $this->corpo = $this->load->view('site_add/looping-links', $view, true);

        $this->templateRender();
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Adiciona produto no carrinho.
     * Poder ser via GET ou AJAX
     * -> Redireciona para o carrinho.
     * @param type $prod_id
     */
    public function add($prod_id = NULL){
        
//        echo 'ola: ' . $prod_id;
//        exit;
//        mybug($this->input->post());
        // vindas via POST
        $post = $this->input->post();
        $product_id = $post['product_id'];
        $qty = ( isset($post['qty']) ) ? $post['qty'] : 1;
        // se contém opções, senão é false        
        $options = $this->input->post('option');
        
        
        
        // dados do produto
        $this->cms_loja->set_page($product_id);
        $produto                = $this->cms_loja->get_conteudo();
        $produto['preco_final'] = $this->cms_loja->preco_final();
//        $produto['opcoes']      = $this->cms_loja->get_options();
        
        $valor = $produto['preco_final'];
        $desc = $produto['titulo'];
        
        
        
        $data['id'] = $product_id;
        $data['qty'] = $qty;
        $data['price'] = padraoSQL($valor);
        $data['name'] = $desc;
        if($options){
            $data['options'] = $options;
        }        
        
        
        
        $this->cms_loja->insert($data);
        
        
        if(IS_AJAX){
            echo $this->cms_loja->output_cart();
        } else {
            redirect('loja/carrinho');
        }
    }
    
    // -------------------------------------------------------------------------
    /**
     * 1º)
     * Página com os produtos e opções de atualização do carrinho
     */
    public function carrinho(){
        
        $this->breadcrumb->add('Loja', 'loja');
        $this->breadcrumb->add('Meu carrinho');
        
        $view['cart'] = $this->cms_loja->parse_cart($this->cart->contents());
        $view['cart_off']   = $this->cms_loja->get_descontos();
        $view['cart_cupom'] = $this->cms_loja->get_cupom();
        
        // verifica se existe alguma regra para fechar carrinho
        $view['rules'] = $this->cms_loja->cart_rules();
        
//        mybug($view['cart_off']);
        
        $this->title = 'Carrinho';
        $this->corpo = $this->load->view('site_add/loja-carrinho', $view, true);
        
        $this->templateRender();
    }
    
    // -------------------------------------------------------------------------
    /**
     * 2º)
     * Identifica se está logado. Se está direciona para entrega, se não abre
     * form de login e cadastro
     */
    public function identificacao(){
        
        // 1) verificar se está logado
        
        // 2) verificar se existe alguma regra para fechar o pedido
        
        redirect('loja/pagamento');
    }
    
    // -------------------------------------------------------------------------
    /**
     * 3º)
     * Usuário escolhe opções de entrega.
     */
    public function entrega(){
        
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * 4º)
     * Usuário escolhe opções de pagamento.
     */
    public function pagamento(){
        
        $this->load->library(array('pagseguro', 'cms_extrato'));

        // dados do usuário para gerar botão
        $user_id = rand(8,10);
        $this->pagseguro->set_user($user_id);
        
        // dados produto         
        $carrinho    = $this->cart->contents();
        $valor_total = $this->cms_loja->cart_total();
        $qty_itens   = $this->cart->total_items();
        
        // se não há produtos, volta para o carrinho
        if(empty($carrinho)){
            redirect('loja/carrinho');
        }
        
        
        // insere produtos para botão PagSeguro
        $products = $this->cms_loja->carrinho_para_pagseguro();
        $this->pagseguro->set_products($products);
        
        // insere desconto se aplicável
        $this->pagseguro->set_desconto($this->cms_loja->total_desconto());
//        mybug($this->cms_loja->total_desconto());
        
        // cria um extrato para acompanhar o pedido
        $xtrt['modulo_id'] = 52;
        $xtrt['usuario_id'] = $user_id;
        $xtrt['metodo'] = 'pagseguro';
        $xtrt['valor_total'] = $valor_total;
        $xtrt['descontos'] = $this->cms_loja->total_desconto();
        $xtrt['produtos'] = $carrinho;
        $xtrt['produtos_descontos'] = $this->cms_loja->get_descontos();
        $xtrt['produtos_cupom'] = $this->cms_loja->get_cupom();
        
        $pedido_id = $this->cms_extrato->add($xtrt);
        
        // ID do extrato
        $config['reference'] = $pedido_id;
        
        
        
        
        $this->title = 'Pagamento';
        $this->corpo = $this->pagseguro->get_button($config);// gera botão
        
        $this->templateRender();
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * 5º)
     * Mensagem de confirmação.
     */
    public function confirmacao(){
        
        $this->cart->destroy();
    }
    
    // ------------------------------------------------------------------------
    /**
     * Atualiza dados do carrinho 
     * -> redireciona para loja/carrinho
     */
    public function atualiza_carrinho(){
        
        $this->cart->update($this->input->post());
        // se tiver Cupom insere e deixa na session
        $this->cms_loja->set_cupom('cupom');
        $this->phpsess->flashsave('msg', 'Carrinho atualizado');
        redirect('loja/carrinho');
    }
    
    // ------------------------------------------------------------------------
    /**
     * Remove o produto do carrinho
     * -> redireciona para loja/carrinho
     * @param string $rowid
     */
    public function remover_produto($rowid){
        $this->cart->update(array(
               'rowid' => $rowid,
               'qty'   => 0
            ));
        $this->phpsess->flashsave('msg', 'Carrinho atualizado');
        redirect('loja/carrinho');
    }

    // -------------------------------------------------------------------------
    /**
     * Método que exibe botão de compra de teste.
     */
    public function psdebug() {

        $this->load->library(array('pagseguro', 'cms_extrato'));

        // dados do usuário para gerar botão
        $this->pagseguro->set_user(8);
        
        // dados produto  
        $prod = $this->cms_conteudo->get_page(10);
        $prod['preco'] = $this->cms_conteudo->preco_final('GRUPODE5');
        $prod['cupom'] = $this->cms_conteudo->get_cupom('GRUPODE5');
        
//        mybug($user);
        // insere produtos para botão PagSeguro
        $products[] = array(
            'id' => $prod['id'],
            'descricao' => $prod['titulo'],
            'valor' => $prod['preco'],
            'quantidade' => 1,// quantidade de usuários
            'peso' => 0
        );
        $this->pagseguro->set_products($products);
        
        // cria um extrato para acompanhar o pedido
        $xtrt['modulo_id'] = $prod['modulo_id'];
        $xtrt['conteudo_id'] = $prod['id'];
        $xtrt['user_id'] = 8;
        $xtrt['metodo'] = 'pagseguro';
        $xtrt['valor_total'] = $prod['preco'];
        $xtrt['descontos'] = ($prod['cupom']) ? $prod['cupom']['desconto'] : '';
        
        $pedido_id = $this->cms_extrato->add($xtrt);
        
        // ID do extrato
        $config['reference'] = $pedido_id;

        // gera botão
        echo $this->pagseguro->get_button($config);
    }

    // -------------------------------------------------------------------------
    /**
     * Salva um array no arquivo pagseguro...php em cache/
     * @param type $array
     */
    public function debug($array, $tipo = '') {

        $data = array();
        foreach ($array as $c => $v) {
            $data[] = $c . ': ' . $v;
        }

        $output = implode("\n", $data);

        $this->load->helper('file');
        write_file(APPPATH . "cache/pagseguro_" . $tipo . date("Y-m-d_h-i") . ".php", $output);
    }

    // -------------------------------------------------------------------------
    /**
     * Método de retorno do pag seguro
     * Conteúdo do POST:
     * VendedorEmail: email@pagseguro.com.br
     * TransacaoID: 23A080959E0346F58B8C73D2F032E814 <= 
     * Referencia: 169 <= ID de cms_extrato
     * Extras: 0,00
     * TipoFrete: FR <=
     * ValorFrete: 0,00 <=
     * Anotacao: <=
     * DataTransacao: 31/07/2012 01:03:59 <=
     * TipoPagamento: Pagamento Online <=
     * StatusTransacao: Aguardando Pagto|Aprovado <=
     * CliNome: Nome do usurio
     * CliEmail: bruno@brunobarros.com
     * CliEndereco: rua alguma coisa
     * CliNumero: 0
     * CliComplemento:
     * CliBairro: ing
     * CliCidade: Niteri
     * CliEstado: RJ
     * CliCEP: 24210445
     * CliTelefone: 21 33335555
     * NumItens: 2
     * Parcelas: 1 <=
     * ProdID_1: 129
     * ProdDescricao_1: Descrio obrigatria
     * ProdValor_1: 0,90
     * ProdQuantidade_1: 1
     * ProdFrete_1: 0,00
     * ProdExtras_1: 0,00
     * ProdID_2: 112
     * ProdDescricao_2: 2 Descrio obrigatria
     * ProdValor_2: 0,10
     * ProdQuantidade_2: 1
     * ProdFrete_2: 0,00
     * ProdExtras_2: 0,00
     */
    public function retorno_pagseguro() {

        if (count($_POST) > 0) {

            $informacao = array();

            // POST recebido, indica que é a requisição do NPI,
            // ou notificação de status
            $this->load->library('pagseguro'); //Carrega a library
            $this->load->library('cms_extrato');
            
            // faz conexão com PS para validar o retorno
            $retorno = $this->pagseguro->notificationPost();

            // quando recebe uma notificação que necessita uma requisição GET 
            // para recuperar status da transação
            $notificationType = (isset($_POST['notificationType']) && $_POST['notificationType'] != '') ? $_POST['notificationType'] : FALSE;
            $notificationCode = (isset($_POST['notificationCode']) && $_POST['notificationCode'] != '') ? $_POST['notificationCode'] : FALSE;

            // É uma notificação de status. Passa a ação para o método que vai 
            // atualizar o status do pedido.
            if ($notificationType && $notificationCode) {
                
                $not = $this->pagseguro->get_notification($notificationCode);
                $this->cms_extrato->status_update('pagseguro', $not);           
                
            }

            // informação quando é enviado um POST completo
            $transacaoID = (isset($_POST['TransacaoID'])) ? $_POST['TransacaoID'] : FALSE;

            // Se existe $transacaoID é uma notificação via POST logo após a
            // solicitação de pagamento, neste momento
            if ($transacaoID) {
                
                $_POST['metodo'] = 'pagseguro';
                $this->cms_extrato->update($_POST['Referencia'], $_POST);
            }


            $this->debug($_P0ST, '');

            if ($retorno == "VERIFICADO") {
                //O post foi validado pelo PagSeguro.
                $source = array('.', ',');
                $replace = array('', '.');
                

                if ($_POST['StatusTransacao'] == 'Aprovado') {
                    $informacao['status'] = '1';
                } elseif ($_POST['StatusTransacao'] == 'Em Análise') {
                    $informacao['status'] = '2';
                } elseif ($_POST['StatusTransacao'] == 'Aguardando Pagto') {
                    $informacao['status'] = '3';
                } elseif ($_POST['StatusTransacao'] == 'Completo') {
                    $informacao['status'] = '4';
                } elseif ($_POST['StatusTransacao'] == 'Cancelado') {
                    $informacao['status'] = '5';
                }
            } else if ($retorno == "FALSO") {
                //O post não foi validado pelo PagSeguro.
                $informacao['status'] = '1000';
            } else {
                //Erro na integração com o PagSeguro.
                $informacao['status'] = '6';
            }
        } else {
            // POST não recebido, indica que a requisição é o retorno do Checkout PagSeguro.
            // No término do checkout o usuário é redirecionado para este bloco.
            // redirecionar para página de OBRIGADO e aguarde...
            redirect('loja');
        }
        
    }

    /**
     * Exemplode como consultar status de notificação
     * @param string $code
     */
    public function check($code = NULL) {

        $this->load->library('pagseguro');

        // B180F4-52783D783D4C-A44416DFBFF3-26FBA4
        // E3BC5E-F45B6D5B6D3D-E774AA9F93A7-B863A8
        
        if($code === NULL){
            $code = '45AC39-82659E659E9A-72242E0FAAB7-1EEBBF';
        }

        $string = $this->pagseguro->get_notification($code);

        mybug($string);
    }


}
