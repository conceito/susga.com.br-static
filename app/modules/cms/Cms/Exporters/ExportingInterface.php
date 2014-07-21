<?php namespace Cms\Exporters;


interface ExportingInterface {

	/**
	 * run the exportation process
	 * @return mixed
	 */
	public function make();
} 