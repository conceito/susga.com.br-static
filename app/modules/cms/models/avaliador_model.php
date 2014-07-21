<?php

use Cms\Notifications\EvaluationInviteNotification;

class Avaliador_model extends MY_Model
{

	/**
	 * store the last user got by find()
	 * @var null
	 */
	protected $userFound = null;

	/**
	 * @var int
	 */
	protected $avalCategoryId = 13;

	public function __construct()
	{

		$this->load->library(array('cms_usuario'));
		$this->load->model('cms/trabalhos_model', 'trabalho');
//		$this->load->model('cms/avaliacao_model', 'avaliacao');
	}


	/**
	 * perform login of appraiser user to perform evaluation of jobs
	 *
	 * @param $login
	 * @param $password
	 * @return array|bool
	 * @throws Exception
	 */
	public function doLogin($login, $password)
	{
		$this->load->helper('checkfix');
		$user = $this->cms_usuario->do_login(array(
			'email' => $login,
			'senha' => cf_password($password, 4, 20),
			'grupo' => $this->avalCategoryId
		));

		if(! $user)
		{
			throw new Exception("Login ou senha estão incorretos, ou usuário não tem permissão.");
		}

		return $user;
	}

	/**
	 * erase session
	 */
	public function doLogout()
	{
		$this->cms_usuario->do_logout();
	}

	/**
	 * check if user is logged in and validate the user group
	 *
	 * @return array|bool
	 */
	public function gerLoggedUser()
	{
		if ($user = $this->cms_usuario->get_session()) {
			return ($user['grupo'] != $this->avalCategoryId) ? false : $user;
		} else {
			return false;
		}
	}


	/**
	 * get user by ID and store on $this->userFound
	 *
	 * @param $id
	 * @return null
	 */
	public function find($id)
	{
		if ($this->userFound !== null)
		{
			return $this->decorateUser($this->userFound);
		}

		$qUser = $this->db->where('id', $id)->get('cms_usuarios');

		if ($qUser->num_rows() == 0)
		{
			return $this->userFound = null;
		}

		$this->userFound = $qUser->row_array();

		return $this->decorateUser($this->userFound);

	}

	public function decorateCollection($users = array())
	{
		if (!is_array($users))
		{
			return null;
		}
		$collection = array();

		foreach ($users as $u)
		{
			$collection[] = $this->decorateUser($u);
		}

		return $collection;
	}

	public function decorateUser($user)
	{
		return $user;
	}

	/**
	 * retrieve all job evaluators
	 *
	 * @return array|null
	 */
	public function all()
	{
		$qUsers = $this->db->where('grupo', $this->avalCategoryId)
			->where('status', 1)
			->get('cms_usuarios');

		if ($qUsers->num_rows() == 0)
		{
			return null;
		}

		return $this->decorateCollection($qUsers->result_array());
	}

	public function update($id, $data = array())
	{

	}


	/**
	 * send invite to user to evaluate a job
	 * - add relationship
	 * - send email
	 *
	 * @param $jobId
	 * @param $userId
	 * @return bool
	 * @throws Exception
	 */
	public function sendInvite($jobId, $userId)
	{
		if (!is_numeric($jobId) || !is_numeric($userId))
		{
			throw new Exception("ID do trabalho ou avalidor não está no formato correto.");
		}

		// get user, save memory
		$user = $this->find($userId);

		// check if the evaluator is already evaluating this job
		$evaluations = $this->getEvaluationsByUser($userId, $jobId);

		if ($evaluations)
		{
			throw new Exception("Usuário já está avaliando.");

			return false;
		}

		// do relationship
		$evaluationId = $this->createJobEvaluation($userId, $jobId);

		// send notification
		$notify = new EvaluationInviteNotification();
		$notify->setUsers(array($user));

//		if(ENVIRONMENT == 'development'){
//			$notify->debug();
//		}

		$notify->send();

		// return
		return $evaluationId;
//		return $this->avaliacao->find($evaluationId);

	}


	/**
	 * create relationship row
	 *
	 * @param $userId
	 * @param $jobId
	 * @return int Evaluation ID
	 */
	public function createJobEvaluation($userId, $jobId)
	{
		// create relationship
		$eval['usuario_id']  = $userId;
		$eval['conteudo_id'] = $jobId;
		$eval['tipo']        = 'avaliacao';
		$eval['valor']       = 0;
		$eval['data']        = date("Y-m-d H:i:s");
		$eval['status']      = 2;

		$this->db->insert('cms_conteudo_rel', $eval);

		$evalId = $this->db->insert_id();

		$eval['id'] = $evalId;

		// create evaluation form row
		$this->createFormEvaluation($eval);

		return $evalId;

	}

	/**
	 * create row for evaluation form answers
	 *
	 * @param array $eval Job evaluation pivot
	 * @return object
	 */
	public function createFormEvaluation($eval)
	{
		// get job details
		$job = $this->trabalho->find($eval['conteudo_id']);

		$form['titulo'] = 'Formulário de avaliação';
		$form['dt_ini'] = date("Y-m-d");
		$form['hr_ini'] = date("H:i:s");
		$form['txt'] = '';
		$form['txtmulti'] = '';
		$form['rel'] = $eval['id'];
		$form['modulo_id'] = $job['modulo_id'];
		$form['tipo'] = 'form-avaliacao';
		$form['status'] = 1;

		return $this->db->insert('cms_conteudo', $form);

	}

	/**
	 * retrieve evaluations by user
	 *
	 * @param null $userId If null get the in memory
	 * @param null $jobId If null return for all jobs
	 * @return bool|array
	 */
	public function getEvaluationsByUser($userId = null, $jobId = null)
	{
		if ($userId === null && $this->userFound)
		{
			$user = $this->userFound;
		}
		else
		{
			$user = $this->find($userId);
		}

		// get all
		$this->db->where('usuario_id', $user['id']);

		if ($jobId)
		{
			$this->db->where('conteudo_id', $jobId);
		}

		$this->db->where('tipo', 'avaliacao');
		$this->db->where('status !=', 0);
		$qEvaluations = $this->db->get('cms_conteudo_rel');

		if ($qEvaluations->num_rows() == 0)
		{
			return false;
		}

		return $qEvaluations->result_array();
	}
}