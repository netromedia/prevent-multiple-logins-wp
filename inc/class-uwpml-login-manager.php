<?php
/**
 * Login Manager
 * 
 * Core PML functionality is implemented inside 
 * UWPML_Login_Manager class.
 * 
 * @package uwpml
 * @since 1.0.1
 * 
 */
class UWPML_Login_Manager{
    
    public function __construct() {        
        add_action( 'set_auth_cookie', 
                array($this, 'set_auth_transient'), 10, 5 );
        add_action( 'clear_auth_cookie', 
                array($this, 'clear_auth_transient'));
        add_action('auth_cookie_valid', 
                array($this, 'uwpml_auth_cookie_valid'), 10, 2);
        add_filter('authenticate', 
                array($this, 'uwpml_authenticate'), 40, 3);
    }
    
    /**
     * Set Auth Transient
     * 
     * Setup 'Auth Transient' with 'Auth Cookie'
     * 
     * Auth cookie is generated upon user login. Create a transient for this 
     * user using 'set_auth_cookie' action hook.
     * 
     * @hook set_auth_cookie
     * 
     * @param type $logged_in_cookie
     * @param type $expire
     * @param type $expiration
     * @param type $user_id
     * @param type $scheme
     * 
     * @return void action hook returns nothing
     */
    public function set_auth_transient( $logged_in_cookie, $expire, 
            $expiration, $user_id, $scheme)
    {
        $auth_for = $expiration - time();
        $transient = 'uwpml_' . $user_id;
        $value = array(
            'auth_on' => time(),
            'auth_for' => $auth_for,
            'updated_on' => time()
        );                        
        set_transient($transient, $value, $auth_for);        
    }
    
    /**
     * Clears Auth Transient
     * 
     * Clears the 'Auth Transient' just before clearing 'Auth Cookies'
     * 
     * @hook clear_auth_cookie
     * 
     * @return void action hook returns nothing
     */
    public function clear_auth_transient(){
	$current_user = wp_get_current_user();
        $transient = 'uwpml_' . $current_user->ID;
        delete_transient($transient);
    }
    
    /**
     * UWPML Auth Cookie Valid
     * 
     * Fires with each visit to the site, if the auth coockie is valid
     * 
     * If the auth cookie is valid, update the transient
     * 
     * @hook `auth_cookie_valid`
     * 
     * @param type $cookie_elements
     * @param type $user
     * 
     * @return void action hook returns nothing
     */
    public function uwpml_auth_cookie_valid($cookie_elements, $user){
        
        $transient = 'uwpml_' . $user->ID;
        $transient_value = get_transient($transient);        
        
        if($transient_value){ // Update Transient                    
            $value = array(
                'auth_on' => $transient_value['auth_on'],
                'auth_for' => $transient_value['auth_for'],
                'updated_on' => time()
            );
            $expiration = $transient_value['auth_on']
                          + $transient_value['auth_for'] 
                          - time() ;
            set_transient($transient, $value, $expiration);            
        }
    }
    
    /**
     * UWPML Authenticate
     * 
     * Throws an error on a multiple login attempt
     * 
     * finally checks `Auth Transient`. i.e. after username,
     * password, auth cookie(priority 40)
     * 
     * @hook authenticate
     * 
     * @param type $user
     * @param type $username
     * @param type $password
     * 
     * @return $user|WP_Error $user if not a multiple login attempt.
     */
    public function uwpml_authenticate($user, $username, $password){
        if ( is_a($user, 'WP_User')) {
            
            $transient = 'uwpml_' . $user->ID; 
            $transient_values = get_transient($transient);
            
            if($transient_values){                
                $error = __('Already Logged In', 'uwpml');                     
                $error = apply_filters(
                        'uwpml_already_logged_in_message', 
                        $error, $user, $transient_values
                );                
                do_action(
                        'uwpml_multiple_login_attempt', 
                        $user, 
                        $error,
                        $transient_values
                );                
                return new WP_Error(
                        'authentication_failed', 
                        $error
                );
            }
        }
        
        return $user;
    }
   
    
}
// Transient Check
// var_dump(get_transient('uwpml_' . get_current_user_id()));
?>

