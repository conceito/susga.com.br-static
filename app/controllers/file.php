<?php

class File extends Frontend_Controller{
    
    public function __construct() {
        parent::__construct();
        
        $this->load->library(array('cms_arquivo'));
    }
    
    public function index(){
        
        $this->title = "Form upload";
        $this->corpo = $this->load->view('site_add/file', '', TRUE);
        
        $this->templateRender();
        
    }
    
    public function send(){
        
        // envia
//        $retorno = $this->cms_arquivo->send('arquivo');
//        if($retorno['arquivo']['error']){
//            mybug($retorno['arquivo']['error']);
//        }
        // salva
//        $salvou = $this->cms_arquivo->save($retorno['arquivo2'], array(
//            'pasta_id' => 5,
//            'descricao' => 'muito legal'
//        ));
        
        $config['pasta_id'] = 5;
        $retorno = $this->cms_arquivo->send_save('arquivo', $config);
//        $debug = $this->cms_arquivo->debug();
        mybug($retorno);
        
    }
    
    public function cep($cep = '24210445'){
        
        // load
        require_once APPPATH . 'libraries/Phpquery.php';
        // curl
        $this->load->spark('curl/1.2.1');
        
        // Simple call to CI URI
        $html = $this->curl->simple_post('http://m.correios.com.br/movel/buscaCepConfirma.do', array(
                'cepEntrada'=>$cep,
                'tipoCep'=>'',
                'cepTemp'=>'',
                'metodo'=>'buscarCep'
        ));
        
        phpQuery::newDocumentHTML($html, $charset = 'utf-8');

        $dados = array(
                'logradouro'=> trim(pq('.caixacampobranco .resposta:contains("Logradouro: ") + .respostadestaque:eq(0)')->html()),
                'bairro'=> trim(pq('.caixacampobranco .resposta:contains("Bairro: ") + .respostadestaque:eq(0)')->html()),
                'cidade/uf'=> trim(pq('.caixacampobranco .resposta:contains("Localidade / UF: ") + .respostadestaque:eq(0)')->html()),
                'cep'=> trim(pq('.caixacampobranco .resposta:contains("CEP: ") + .respostadestaque:eq(0)')->html())
        );
        
        // remove tudo, menos alfanuméricos e '/'
        $dados['cidade/uf'] = preg_replace("/[^\/[:alnum:]]/", "", $dados['cidade/uf']);
        
        mybug($dados);
        
        
        
    }
    
    
    public function gravatar($email = 'bruno@brunobarros.com'){
        
        $this->load->spark('gravatar/1.1.1');

        $gravatar = gravatar($email, 80, false, 'mm', 'g');

        echo $gravatar;
        
    }
    
}