<?php namespace Cms\Adapters;

class JobEvaluationAdapter
{

	protected $evaluationId;

	protected $evaluationData;


	function __construct($evaluationId, array $evaluationData)
	{

		$this->evaluationId   = $evaluationId;
		$this->evaluationData = $evaluationData;
	}

	/**
	 * return the evaluation data to update
	 */
	public function evaluation()
	{

		return array(
			'valor'  => (int)$this->evaluationData['q15'],
			'status' => 1
		);

	}

	/**
	 * return the form data to update
	 */
	public function form()
	{
		return array(
			'txt'    => serialize($this->evaluationData),
			'dt_fim' => date("Y-m-d"),
			'hr_fim' => date("H:i:s")
		);
	}

	public function job()
	{
		$grupoId = 232;

		if ((int)$this->evaluationData['q15'] == 10)
		{
			$grupoId = 230;
		}
		else if ((int)$this->evaluationData['q15'] == 5)
		{
			$grupoId = 259;
		}
		else if ((int)$this->evaluationData['q15'] == 0)
		{
			$grupoId = 231;
		}

		return array(
			'grupo' => $grupoId
		);
	}

}