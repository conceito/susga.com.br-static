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
class Enquete extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->model(array('apps/enquete_app_model'));
    }

    /**
     * Executa o voto e retorna STRING. Função requerida via AJAX.
     *
     * @return string
     */
    function votar()
    {
        // variaveis
        $idEnquete = $this->input->post('idEnquete');
        $idPergunta = $this->input->post('opc');
//        echo $idPergunta;
//        exit;
        // valida voto
        if (strlen($idPergunta) == 0) {
            $saida = '';
        } else {
            // perquisa se este user já votou nesta enquete HOJE
            $javotou = $this->enquete_app_model->valida_user($idEnquete);
            // senão vota
            if ($javotou) {
                $saida = 'Seu voto não foi computado, pois você já votou nesta enquete hoje.<br />Vote amanhã novamente!<br /><br />';
            } else {
                $ret = $this->enquete_app_model->computa_voto($idEnquete, $idPergunta);
                if ($ret)$saida = 'Obrigado por votar! <br /><br />';
                else $saida = 'Erro ao votar! <br /><br />';
            }
        }
        echo $saida;
    }

    function resultados()
    {
        // variaveis
        $idEnquete = $this->input->post('idEnquete');
        //
        $dadosPag['opcoes'] = $this->enquete_app_model->resultado($idEnquete);
        // saida
        $result = $this->load->view('apps/enquete_result', $dadosPag, true);
        echo $result;
    }
}

?>