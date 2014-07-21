<?php

use Cms\Notifications\EvaluationCompletedNotification;

class Api_avaliacoes extends Api_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->load->model('cms/avaliacao_model', 'avaliacao');
	}

	/**
	 * GET
	 */
	public function all()
	{
		$vars = $this->uri->to_array(array('contentId'));

		$contentId = $vars['contentId'];

		$finished = $this->avaliacao->finished($contentId);
		$awaiting = $this->avaliacao->awaiting($contentId);

		echo $this->responseOk(array(
			'finished' => $finished,
			'awaiting' => $awaiting
		), 'Avaliações retornadas com sucesso.');
	}


	public function remove($id)
	{
		try
		{
			$ret = $this->avaliacao->remove($id);
			echo $this->responseOk(array('id' => $id), 'Avaliação removida.');

		} catch (Exception $e)
		{
			echo $this->responseError($e->getMessage());
		}

	}


	/**
	 * POST
	 * receive evaluation form
	 *
	 * @param $id
	 */
	public function evaluate($id)
	{
		$method = $this->input->server('REQUEST_METHOD');

		if ($method === 'POST')
		{
			$payload = file_get_contents('php://input');
			$data    = json_decode($payload, true);

			try
			{
				$ret = $this->avaliacao->updateEvaluation($id, $data['form'], array());
				echo $this->responseOk($ret, 'Avaliação realizada corretamente .');
			} catch (Exception $e)
			{
				echo $this->responseError($e->getMessage());
			}
		}
		else
		{
			echo $this->responseError('Requisição não autorizada.');
		}
	}

	public function form($id)
	{
		$method = $this->input->server('REQUEST_METHOD');

		if ($method === 'GET')
		{
			$ret = $this->avaliacao->find($id, array('job' => 1));
			$answer = new \Gestalt\Trabalho\EvaluationForm();
			$answer->setData($ret['form_answers']);
			echo $answer->getAnswers('html');
//			echo $this->responseOk($ret, 'Avaliação realizada corretamente .');
		}
		else
		{
			echo $this->responseError('Requisição não autorizada.');
		}
	}


	public function test(){


//		$notify = new EvaluationCompletedNotification();
//
//		var_dump($notify);
//
//		$notify->setEvaluation(array(1,2,3));
//
//		var_dump($notify->send());

	}
}