<?php
/**
 * E-mail configuration
 */

return array(
	'host' => '',
	'user' => '',
	'pass' => '',
	'port' => 587,
	'encr' => '', // tsc

	'to' => array(
		'name' => '', // leave empty to use $titleSite
		'email' => '' // can be overridden by $toMail
	),

	'from' => array(
		'name' => '', // leave empty to use formName
		'email' => '' // leave empty to use formEmail
	),

	'copies' => array()
);
