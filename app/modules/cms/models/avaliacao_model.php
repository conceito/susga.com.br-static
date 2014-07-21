<?php

use Cms\Notifications\EvaluationCompletedNotification;

class Avaliacao_model extends MY_Model
{

	/**
	 * store the allByContent()
	 * @var null
	 */
	protected $evaluations = null;


	public function __construct()
	{
		$this->load->library(array('cms_usuario'));
		$this->load->model('cms/trabalhos_model', 'trabalho');
		$this->load->model('cms/avaliador_model', 'avaliador');
	}

	public function find($id, $options = array())
	{
		$this->db->select("ava.*, form.id as form_id, form.txt as form_answers, form.txtmulti as form_comments,
		form.modulo_id, form.dt_ini as form_dt_ini, form.dt_fim as form_dt_fim, form.hr_ini as form_hr_ini,
		form.hr_fim as form_hr_fim, user.nome as usuario_nome, user.email as usuario_email");
		$this->db->from('cms_conteudo_rel as ava');
		$this->db->join('cms_conteudo as form', 'form.rel = ava.id');
		$this->db->join('cms_usuarios as user', 'user.id = ava.usuario_id');
		$this->db->where('ava.id', $id);
		$this->db->where('form.tipo', 'form-avaliacao');
		$qEvaluations = $this->db->get();

		$decorated = $this->decorateCollection($qEvaluations->result_array(), $options);

		return $decorated[0];
	}

	/**
	 * get all evaluations all statuses
	 *
	 * @see finished()
	 * @see awaiting()
	 * @param $contentId
	 * @return array|null
	 */
	public function allByContent($contentId)
	{
		if ($this->evaluations !== null)
		{
			return $this->evaluations;
		}

		$this->db->select("ava.*, form.id as form_id, form.txt as form_answers, form.txtmulti as form_comments,
		form.modulo_id, form.dt_ini as form_dt_ini, form.dt_fim as form_dt_fim, form.hr_ini as form_hr_ini,
		form.hr_fim as form_hr_fim, user.nome as usuario_nome, user.email as usuario_email");
		$this->db->from('cms_conteudo_rel as ava');
		$this->db->join('cms_conteudo as form', 'form.rel = ava.id');
		$this->db->join('cms_usuarios as user', 'user.id = ava.usuario_id');
		$this->db->where('ava.conteudo_id', $contentId);
		$this->db->where('form.tipo', 'form-avaliacao');
		$qEvaluations = $this->db->get();

		return $this->evaluations = $this->decorateCollection($qEvaluations->result_array());
	}


	/**
	 * filter just finished evaluations == the admin already answered
	 *
	 * @param $contentId
	 * @return array
	 */
	public function finished($contentId)
	{
		$evaluations = $this->allByContent($contentId);

		$finished = array();

		foreach ($evaluations as $eval)
		{
			if ($eval['status'] == 1)
			{
				$finished[] = $eval;
			}
		}

		return $finished;

	}

	/**
	 * filter just awaiting evaluations == the admin still not answered
	 *
	 * @param $contentId
	 * @return array
	 */
	public function awaiting($contentId)
	{
		$evaluations = $this->allByContent($contentId);

		$awaiting = array();

		foreach ($evaluations as $eval)
		{
			if ($eval['status'] == 2)
			{
				$awaiting[] = $eval;
			}
		}

		return $awaiting;

	}

	/**
	 * filter just canceled evaluations == the super admin removed the answer
	 *
	 * @param $contentId
	 * @return array
	 */
	public function canceled($contentId)
	{
		$evaluations = $this->allByContent($contentId);

		$canceled = array();

		foreach ($evaluations as $eval)
		{
			if ($eval['status'] == 0)
			{
				$canceled[] = $eval;
			}
		}

		return $canceled;

	}

	/**
	 * get all evaluations for a user
	 *
	 * @param $userId
	 * @param array $options
	 * @return array|null
	 */
	public function allByUser($userId, $options = array())
	{
		$this->db->select("ava.*, form.id as form_id, form.txt as form_answers, form.txtmulti as form_comments,
		form.modulo_id, form.dt_ini as form_dt_ini, form.dt_fim as form_dt_fim, form.hr_ini as form_hr_ini,
		form.hr_fim as form_hr_fim, user.nome as usuario_nome, user.email as usuario_email");
		$this->db->from('cms_conteudo_rel as ava');
		$this->db->join('cms_conteudo as form', 'form.rel = ava.id');
		$this->db->join('cms_usuarios as user', 'user.id = ava.usuario_id');
		$this->db->where('ava.usuario_id', $userId);
		$this->db->where('form.tipo', 'form-avaliacao');
		$qEvaluations = $this->db->get();

		return $this->decorateCollection($qEvaluations->result_array(), $options);
	}

	private function decorateCollection($array, $options = array())
	{
		if (!is_array($array))
		{
			return null;
		}

		$collection = array();
		foreach ($array as $item)
		{
			$collection[] = $this->decorateEvaluation($item, $options);
		}

		return $collection;
	}

	public function decorateEvaluation($item, $options = array())
	{
		$item['form_dt_ini']  = formaPadrao($item['form_dt_ini']);
		$item['form_dt_fim']  = formaPadrao($item['form_dt_fim']);
		$item['form_hr_ini']  = substr($item['form_hr_ini'], 0, 5);
		$item['form_hr_fim']  = substr($item['form_hr_fim'], 0, 5);
		$item['form_answers'] = unserialize($item['form_answers']);

		// valor = nota da avaliação [10 aprovado, 5 aprovado com correções, 0 reprovado ]
		// status = status da avaliação [1 avaliado, 2 aguardando avaliação, 0 cancelado]

		$item['valor_label'] = $this->getNotaLabel($item['valor'], $item['status']);

		if (isset($options['job']) && $options['job'])
		{
			$item['job'] = $this->trabalho->find($item['conteudo_id'], false);
		}

		return $item;
	}

	public function getNotaLabel($valor, $status)
	{

		$label = 'Não avaliado';
		if ($valor == 10 && $status == 1)
		{
			$label = 'Aprovado';
		}
		else if ($valor == 5 && $status == 1)
		{
			$label = 'Aprovado com correções';
		}
		else if ($valor == 0 && $status == 1)
		{
			$label = 'Reprovado';
		}

		return $label;
	}


	/**
	 * update evaluation
	 * get user form request, adapt, update
	 *
	 * @param $id
	 * @param $form
	 * @param array $extra
	 * @return array
	 */
	public function updateEvaluation($id, $form, $extra = array())
	{
		$adapter = new \Cms\Adapters\JobEvaluationAdapter($id, $form);

		// get evaluation by ID
		$evaluation = $this->find($id);

		if ($evaluationData = $adapter->evaluation())
		{
			$this->db->update('cms_conteudo_rel', $evaluationData, array('id' => $id));
		}

		if ($jobData = $adapter->job())
		{
			$this->db->update('cms_conteudo', $jobData, array('id' => $evaluation['conteudo_id']));
		}

		if ($formData = $adapter->form())
		{
			$this->updateFormByEvaluationId($id, $formData);
		}

		/**
		 * SEND NOTIFICATION
		 */
		$notify = new EvaluationCompletedNotification();
		$notify->setEvaluation($this->find($id, array('job' => 1)));
		//		$notify->debug();
		$notify->send();

		return true;

	}


	/**
	 * set status to canceled
	 *
	 * @param $id
	 * @return object
	 */
	public function remove($id)
	{
		//		$evaluation = $this->find($id);

		$eval['status'] = 0;

		return $this->db->update('cms_conteudo_rel', $eval, array('id' => $id));
	}


	/**
	 * return the form data of a evaluation
	 *
	 * @param $evalId
	 * @return bool
	 */
	public function getFormByEvaluationId($evalId)
	{
		$this->db->where('rel', $evalId);
		$this->db->where('tipo', 'form-avaliacao');

		$qForm = $this->db->get('cms_conteudo');

		if ($qForm->num_rows() == 0)
		{
			return false;
		}

		return $qForm->row_array();
	}


	public function updateFormByEvaluationId($evalId, $data = array())
	{
		$form = $this->getFormByEvaluationId($evalId);

		if ($form)
		{
			$this->db->update('cms_conteudo', $data, array('id' => $form['id']));

			return true;
		}

		throw new Exception("Form da avaliação não foi encontrado.");

	}

}