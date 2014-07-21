<?php
namespace Cms\Notifications;

/**
 * Class EvaluationInviteNotification
 *
 * send email for users about new evaluation
 *
 * <code>
 * $notify = new EvaluationInviteNotification();
 * $notify->setUsers(array($user));
 * $notify->debug(); // optional
 * $notify->send();
 * </code>
 *
 * @package Cms\Notifications
 */
class EvaluationInviteNotification extends AbstractNotification
{
	protected $subject = 'Trabalho para avaliação disponível';

	protected $job;
	/**
	 * @return mixed
	 */
	public function send()
	{
//		$this->service->adminCopy();
		return $this->service->send();
	}

	/**
	 * compose html body with template
	 * @return mixed
	 */
	public function messageBody()
	{
		$body = $this->load->view('email/new_evaluation_invitation', '', true);

		return $this->composeTemplate($body);
	}






}