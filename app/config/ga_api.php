<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$config['profile_id'] = '13758707'; // GA profile id
$config['email']      = 'brunodanca@gmail.com'; // GA Account mail
$config['password']   = 'bb020476'; // GA Account password

$config['cache_data'] = false; // request will be cached
$config['cache_folder'] = 'app/cache'; // read/write
$config['clear_cache'] = array('date', '1 day ago'); // keep files 1 day

$config['debug'] = false; // print request url if true