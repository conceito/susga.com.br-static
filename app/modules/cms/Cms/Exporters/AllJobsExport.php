<?php namespace Cms\Exporters;

class AllJobsExport extends Pdf implements ExportingInterface
{
	/**
	 * @var string
	 */
	private $jobs;

	function __construct($jobs = null)
	{
		parent::__construct();
		$this->jobs = $jobs;
	}


	/**
	 * run the exportation process
	 * @return mixed
	 */
	public function make()
	{
//		$this->debug();


		$content = array();
		foreach($this->getJobs() as $job)
		{
			$content[] = $this->ci->load->view('cms/trabalhos/export_page_pdf', $job, true);
		}

		$this->setArrayContent($content);
//		$this->setOutputMode('F');
//		$this->render('pdf-name-jobs');

		// show on screen
		$this->setSavePath('');
		$this->setOutputMode('I');
		$this->render(url_title($this->ci->config->item('title')));

	}

	/**
	 * @return string
	 */
	public function getJobs()
	{
		if($this->jobs === null)
		{
			$this->ci->load->model('cms/trabalhos_model', 'trabalho');
			$jobsAll = $this->ci->trabalho->lista_conteudos(
				array('pp' => 999, 'co' => 66),
				'conteudo',
				array('id' => 66));

			$content = array();
//			dd($jobsAll['rows']);
			foreach($jobsAll['rows'] as $job)
			{
				$content[] = $this->ci->trabalho->find($job['id'], false);
			}


			return $content;
		}
		return $this->jobs;
	}
}