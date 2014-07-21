<?php

/**
* MY_Session
*
* @package
* @author Bruno
* @copyright Copyright (c) 2009
* @version $Id$
* @access public
*/
class Phpsess {
    var $_flash = array();
    var $sess_time_to_update = 300; // five minutes in unix
    var $now;
    var $sess_expiration = 7200; // 2 horas im unix

    function Phpsess()
    {
        session_start();
        $this->now = time();
        $this->flashinit();

    }

    /**
     * Valida a sessão pelo tempo de vida
     **/
    function _sess_status()
    {

		// mantêm session ativa por 2 horas
        // Is the session current?
        if (! isset($_SESSION['last_activity']) || ($_SESSION['last_activity'] + $this->sess_expiration) < $this->now) {
            $this->destroy();
            return false;
        }
        // We only update the session every five minutes by default
        else if (($_SESSION['last_activity'] + $this->sess_time_to_update) <= $this->now) {
            $_SESSION['last_activity'] = $this->now;
        }
    }

    /* Save a session variable.
     * @paramstringName of variable to save
     * @parammixedValue to save
     * @paramstring  (optional) Namespace to use. Defaults to 'default'. 'flash' is reserved.
    */
    function save($var, $val, $namespace = 'default')
    {
        if ($var == null) {
            $_SESSION[$namespace] = $val;
        } else {
            $_SESSION[$namespace][$var] = $val;
        }
        // atualiza o tempo sa session
        $_SESSION['last_activity'] = $this->now;
    }

    /* Get the value of a session variabe
     * @paramstring  Name of variable to load. null loads all variables in namespace (associative array)
     * @paramstring(optional) Namespace to use, defaults to 'default'
    */
    function get($var = null, $namespace = 'default')
    {
    	// antes de ler, valida a session pelo tempo
		$this->_sess_status();
        if (isset($var))
            return isset($_SESSION[$namespace][$var]) ? $_SESSION[$namespace][$var] : null;
        else
            return isset($_SESSION[$namespace]) ? $_SESSION[$namespace] : null;
    }

    /* Clears all variables in a namespace
     */
    function clear($var = null, $namespace = 'default')
    {

	if (isset($var) && ($var !== null))
            unset($_SESSION[$namespace][$var]);
        else
            unset($_SESSION[$namespace]);
    }

    /**
    * Destroi a seção
    */
    function destroy()
    {
        $this->clear();
        $this->clear(null, 'cms');
		//session_destroy();

    }

    /* Initializes the flash variable routines
     */
    function flashinit()
    {
        $this->_flash = $this->get(null, 'flash');
        $this->clear(null, 'flash');
    }

    /* Saves a flash variable. These are only saved for one page load
     * @paramstringVariable name to save
     * @parammixedValue to save
     */
    function flashsave($var, $val)
    {
        $this->save($var, $val, 'flash');
    }

    /* Gets the value of a flash variable. These are only saved for one page load, so the variable must
     * have either been set or had flashkeep() called on the previous page load
     * @paramstringVariable name to get
     */
    function flashget($var)
    {
        if (isset($this->_flash[$var])) {
            return $this->_flash[$var];
        } else {
            return null;
        }
    }

    /* Keeps the value of a flash variable for another page load.
     * @paramstring(optional) Variable name to keep, or null to keep all. Defaults to keep all (null)
     */
    function flashkeep($var = null)
    {
        if ($var != null) {
            $this->flashsave($var, $this->flashget($var));
        } else {
            $this->save(null, $this->_flash, 'flash');
        }
    }
}

?>