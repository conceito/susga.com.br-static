<?php

/**
 * die and dump
 * @param $var
 * @param bool $print
 */
function dd($var, $print = true)
{
	echo '<pre>';
	($print) ? print_r($var) : var_dump($var);
	exit;
}

/**
 * set_value
 * fill form fields
 *
 * @param $name
 * @return string
 */
function set_value($name)
{
	if (isset($_SESSION['post'][$name]))
	{
		return $_SESSION['post'][$name];
	}
	else
	{
		return '';
	}
}

/**
 * check if is on index.php
 * @return bool
 */
function is_home()
{
	global $p;
	if ($p == 'index'):
		return true;
	else:
		return false;
	endif;
}

/**
 * check if is on page
 * @param $page
 * @return bool
 */
function is_page($page = null)
{
	global $p;
	if ($p == $page):
		return true;
	else:
		return false;
	endif;
}

/**
 * add classes do body tag
 * @param string $extraClasses
 * @return string
 */
function body_class($extraClasses = '')
{
	global $p;
	$bodyClass = ($p == 'index') ? 'on-home' : 'on-page';
	$bodyClass .= ' ' . 'page-' . $p;
	echo $bodyClass . ' ' . $extraClasses;
}

/**
 * redirects to page
 * @param string $page Page name
 * @param array $params Associative array
 * @param string $hash #form
 */
function redirect($page = '', $params = array(), $hash = '')
{
	header('location: ' . $page . '.php?' . http_build_query($params) . $hash);
	exit;
}

/**
 * load email template
 *
 * @param string $body
 * @return string
 */
function load_email_template($body = '')
{
	global $base_url, $titleSite, $emailSite;
	ob_start();
	include("email.tpl.php");
	$message = ob_get_contents();
	ob_end_clean();

	return $message;
}
