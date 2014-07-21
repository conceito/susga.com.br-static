<?php namespace Cms\Notifications;

class EvaluationCompletedNotification extends AbstractNotification
{

	protected $subject = 'Avaliação de trabalho concluída';

	private $evaluation;


	/**
	 * @param mixed $evaluation
	 */
	public function setEvaluation($evaluation)
	{
		$this->evaluation = $evaluation;
	}

	/**
	 * @return mixed
	 */
	public function getEvaluation()
	{
		return $this->evaluation;
	}

	/**
	 * @return mixed
	 */
	public function send()
	{

		$this->setUsers(array(
			'nome' => $this->config->item('title'),
			'email' => $this->config->item('email1')
		));
		//		$this->service->adminCopy();
		return $this->service->send();
	}

	/**
	 * compose html body with template
	 * @return mixed
	 */
	public function messageBody()
	{
		$v['eval'] = $this->getEvaluation();
		$body      = $this->load->view('email/evaluation_completed', $v, true);

		return $this->composeTemplate($body);
	}
}