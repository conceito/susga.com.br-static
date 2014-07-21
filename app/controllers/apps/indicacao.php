<?php
/**
 * Controller para aplicações genéricas do site
 *
 * @package Controller
 * @author Bruno Barros
 * @copyright Copyright (c) 2009
 * @version 1.0
 * @access public
 */
class Indicacao extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->model(array('apps/indicacao_app_model'));
    }


    /**
     * Executa o voto e retorna STRING. Função requerida via AJAX.
     *
     * @return string
     */
    function indicar()
    {

		// variaveis
        $nomeRem = $this->input->post('nomeind1');
        $emailRem = $this->input->post('emailind1');
        $nomeDes = $this->input->post('nomeind2');
        $emailDes = $this->input->post('emailind2');
        // valida
        if(strlen($nomeRem)==0 || strlen($emailRem)==0 || strlen($nomeDes)==0 || strlen($emailDes)==0){
            $saida = 'Preencha TODOS os campos!';
        } else {

            $ret = $this->indicacao_app_model->envia($nomeRem, $emailRem, $nomeDes, $emailDes);
            if ($ret)$saida = 'Obrigado por indicar nosso site!';
            else     $saida = 'Erro ao enviar indicação!';

        }
        echo $saida;
    }


}

?>