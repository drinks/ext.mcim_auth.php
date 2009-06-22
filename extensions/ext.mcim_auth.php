<?php
// MoxieCode ImageManager Auth Extension
// Dan Drinkard

if ( ! defined('EXT')) exit('Invalid file request');

class MCIM_Auth
{
    
    var $settings = array();
	var $name = 'MoxieCode ImageManager Auth Extension';
	var $classname = 'MCIM_Auth';
	var $version = '1.0.1';
	var $description = "Sets up & tears down session hash for 
	        enabling MoxieCode's image manager";
	var $settings_exist = 'y';
	var $docs_url = '';
    
    // Construct
    function MCIM_Auth($settings='')
    {
        $this->settings=$settings;
    }

    // Activate Extension
    function activate_extension()
    {
        global $DB;
        // add hooks
        $DB->query($DB->insert_string('exp_extensions',
    			array(
    			'extension_id'	=> '',
    			'class'			=> $this->classname,
    			'method'		=> "_create_mcim_session",
    			'hook'			=> "cp_member_login",
    			'settings'		=> '',
    			'priority'		=> 10,
    			'version'		=> $this->version,
    			'enabled'		=> "y"
    			)
    		)
    	);
    	$DB->query($DB->insert_string('exp_extensions',
    			array(
    			'extension_id'	=> '',
    			'class'			=> $this->classname,
    			'method'		=> "_destroy_mcim_session",
    			'hook'			=> "cp_member_logout",
    			'settings'		=> '',
    			'priority'		=> 10,
    			'version'		=> $this->version,
    			'enabled'		=> "y"
    			)
    		)
    	);
    	// sorta hacky... TODO rewrite settings form
    	$DB->query($DB->insert_string('exp_extensions',
    			array(
    			'extension_id'	=> '',
    			'class'			=> $this->classname,
    			'method'		=> "_update_mcim_session",
    			'hook'			=> "cp_display_page_navigation",
    			'settings'		=> '',
    			'priority'		=> 10,
    			'version'		=> $this->version,
    			'enabled'		=> "y"
    			)
    		)
    	);
        // log me in when I activate
    	$this->_create_mcim_session();
    }
    
    // Update Extension
    function update_extension(){
        //no updates yet.
    }

    // Disable Extension
    function disable_extension()
    {
        global $DB;
        
        $DB->query("DELETE FROM exp_extensions WHERE class = '$this->classname'");
    }
    
    // Settings
    function settings()
    {
        $settings = array();
        $settings['mcim_cookie_name'] = "isLoggedIn";
        return $settings;
    }

        // Create auth hash
        function _create_mcim_session()
        {
            if(!isset($_SESSION)) session_start();
            $_SESSION[$this->settings['mcim_cookie_name']] = true;
        }
    
        // Update auth hash
        function _update_mcim_session()
        {
            if(!isset($_SESSION)) session_start();
            if(!array_key_exists($this->settings['mcim_cookie_name'], $_SESSION))
            {
                $this->_create_mcim_session();
            }
        }
    
        // Remove auth hash
        function _destroy_mcim_session()
        {
            session_start();
            $_SESSION[$this->settings['mcim_cookie_name']] = null;
            session_destroy();
        }
}