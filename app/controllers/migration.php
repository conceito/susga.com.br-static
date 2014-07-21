<?php

/**
 * Classe que implementa um loop para executar um tétodo de migração qualquer.
 * 
 * O método $this->start() executa um script enquanto seu retorno == TRUE
 * Quando retornar == FALSE o looping é interrompido. 
 */
class Migration extends Frontend_Controller {

    public $method = 'start';

    /**
     * Quantidade de iterações.
     * @var integer 
     */
    public $loop = 0;

    /**
     * Quantos registros afetará antes de retornar.
     * @var integer 
     */
    public $step = 10;

    /**
     * Retorno do método de atualização/migração.
     * Em caso 'false' interrompe looping.
     * @var boolean 
     */
    public $continue = true;

    /**
     * Variáveis da URI.
     * @var array 
     */
    public $u = array();
    
    /**
     * Quantos segundos de espera entre cada iteração.
     * @var integer 
     */
    public $sleep = 1;

    // -------------------------------------------------------------------------

    public function __construct() {
        parent::__construct();

        $this->u = $this->uri->to_array(array('loop'));
        // retorno do loop
        $this->loop = ($this->u['loop'] == '') ? $this->loop : $this->u['loop'];
    }

    // -------------------------------------------------------------------------

    /**
     * Exibe mensagem final.
     */
    public function index() {
        
        $loops = $this->phpsess->flashget('loop', 'migration');
        echo 'Migração finalizada. <br>';
        echo 'Total loops: ' . (int)$loops;
        
    }

    // -------------------------------------------------------------------------

    /**
     * Método que será executado a cada interação.
     */
    public function start() {

        /*
         * processa método de migração do usuário
         */
//        $this->load->model('meu_model', 'model');
        if($this->do_something() === FALSE){
            $this->stop();
        } else {
            $this->loop++;
        }
        
        // executado no final do método
        $this->run();
    }

    public function do_something() {
        
        echo '$this->loop = '.$this->loop;
        
        if($this->loop >= 5){
            // avisa que vai parar
            return false;
        } else {            
            return true;
        }
        
    }

    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Método execuado no final do método de atualização do usuário.
     * Dependendo do valor de #this->continue reinicia iteração, ou 
     * redireciona para $this->index() para finalizar.
     */
    private function run() {
        
        sleep($this->sleep);
        
        if ($this->continue) {
            // recebe o $this->loop e reinicializa processo
            redirect('migration/' . $this->method . '/loop:' . $this->loop, 'refresh');
        } else {
            
            $this->phpsess->flashsave('loop', $this->loop, 'migration');
            // finaliza lopping
            redirect('migration/index', 'refresh');
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Faz o fechamento das iterações para interrupção.
     */
    private function stop() {
        $this->continue = false;
    }

}