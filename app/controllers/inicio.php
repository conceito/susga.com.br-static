<?php
if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

/*
 * Controller principal INDEX
 */

class Inicio extends Frontend_Controller
{

    protected $gravatar;

    function __construct()
    {
        parent::__construct();
        //        $this->output->enable_profiler(true);

        /*
         * Ativar função em caso de site multilingue
         * Ver core/Multilang_Controller.
         */
        //        $this->setLang();

    }

    function index()
    {
        $this->title = '';
        $this->corpo = 'corpo';
	    $this->footer = 'footer';

        $this->templateRender();
    }




}