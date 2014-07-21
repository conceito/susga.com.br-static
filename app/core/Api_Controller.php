<?php

class Api_Controller extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
		/*
		 * CARREGA CONFIGURAÇÕES DO CMS
		 */
		$this->load->config('cmsConfig');

		/*
		 * CARREGA CLASSES GENÉRICAS PARA CMS
		 */
		$this->load->model(array('cms/sessao_model', 'cms/paginas_model'));
		$this->load->helper(array('cms/cmshelper', 'cms/cmstemplate', 'text'));
		$this->load->library(array('cms/cms_libs'));
	}


	public function responseOk($data, $msg = '')
	{
		return $this->response(false, $msg, $data);
	}

	public function responseError($msg = '')
	{
		return $this->response(true, $msg, null);
	}

	public function response($error = false, $msg = '', $data = array())
	{
		return json_encode(array(
			'error' => $error,
			'msg'   => $msg,
			'data'  => $data
		));
	}
}