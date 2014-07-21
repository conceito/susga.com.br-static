<?php
namespace Cms\Notifications;

interface NotificationInterface {

	/**
	 * @return mixed
	 */
	public function send();

	/**
	 * array of users to receive notification
	 * @param $users
	 */
	public function setUsers($users);

	/**
	 * @return array
	 */
	public function getUsers();

	/**
	 * set sender email and name
	 *
	 * @param $email
	 * @param $name
	 * @return mixed
	 */
	public function setFrom($email, $name);

	public function getFromEmail();
	public function getFromName();

	public function setSubject($subject);
	public function getSubject();

	/**
	 * compose html body with template
	 * @return mixed
	 */
	public function messageBody();

	/**
	 * read notification template view
	 *
	 * @param $body Body string
	 * @return string
	 */
	public function composeTemplate($body);

	/**
	 * turn on debug mode
	 * @return mixed
	 */
	public function debug();


}