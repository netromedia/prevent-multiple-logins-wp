<?php
/*
Plugin Name: Prevent Multiple Logins
Plugin URI: http://code.google.com/p/prevent-multiple-logins/
Description: Prevents multiple logins to the same user account.
Version: 1.0
Author: Upeksha Wisidagama
Author URI: http://code.google.com/p/prevent-multiple-logins/people/list
License: GPL2 or later.
*/

/*  Copyright 2013  Upeksha Wisidagama  (email : upeksha@php-sri-lanka.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
    MA  02110-1301  USA
*/

// Don't allow this file to be called directly.
if( !defined( 'ABSPATH' ) ){
    header('HTTP/1.0 403 Forbidden');
    die('No Direct Access Allowed!');
}

if ( ! class_exists( 'UWPML_Prevent_Multiple_Logins' ) ){
    
/**
 * Prevent Multiple Logins
 * 
 * This class is instantiated and 
 * plugin_setup() method is attached to
 * the 'plugins_loaded' action hook.
 * 
 * @package uwpml 
 * @since 1.0
 */    
class UWPML_Prevent_Multiple_Logins
{        
        /**
         * Login Manager
         * 
         * Implements the core PML functinality
         * 
         * @var type UWPML_Login_Manager
         */
        protected $login_manager;
        
        /**
         * Login Manager Helper
         *
         * @var type UWPML_Helper
         */
        protected $helper;
        
        /**
	 * Constructor. 
         * 
         * @return void 
	 */
	public function __construct() {            
            $this->plugin_setup();
        }

	/**
	 * Plugin Setup.
	 *
	 * @return  void
	 */
	public function plugin_setup(){
            
            $this->load_language( 'uwpml' );
            
            register_activation_hook( 
                    __FILE__, 
                    array( $this, 'uwpml_activation' ) 
            );
            
            /**
             * Core PML functionality is implemented inside 
             * UWPML_Login_Manager class.
             */
            include 'inc/class-uwpml-login-manager.php';
            $this->login_manager = new UWPML_Login_Manager();    
            
            /**
             * Essential helper functions for UWPML_Login_Manager
             * Error message formatting, sending notification emails, etc.
             */
            include 'inc/class-uwpml-helper.php';
            $this->helper = new UWPML_Helper();            
         
	}

	/**
	 * Loads translation file.
	 *
	 * @param   string $domain
	 * @return  void
	 */
	public function load_language( $domain ){
		load_plugin_textdomain(
			$domain,
			null,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}
        
        /**
         * UWPML Activation Function
         */
        public function uwpml_activation(){
            
            /**
             * Adds 'uwpml_options' option
             * This option will be deleted in 
             * `uninstall.php`
             */
            add_option('uwpml_options');
        }
    }
}

/**
 * UWPML Plugin Object.
 * 
 * This is a global variable.
 */
$uwpml = new UWPML_Prevent_Multiple_Logins();
?>