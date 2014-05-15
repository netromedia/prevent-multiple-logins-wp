<?php
/**
 * Login Manager Helper
 * 
 * Essential helper functions for UWPML_Login_Manager
 * Error message formatting, sending notification emails, etc.
 * 
 * @package uwpml
 * @since 1.0.1
 * 
 */
class UWPML_Helper{
    
    public function __construct() {
        
        add_filter('uwpml_already_logged_in_message', 
                array($this, 'authenticate_msg'), 10, 3);
        
        add_action('uwpml_multiple_login_attempt', 
                array($this, 'multiple_login_attempt_email'), 10, 2);
    }
    
    /**
     * Faild Authentication Message
     * 
     * Modify multiple login attempt failed message.
     * 
     * @param type $error
     * @param type $user
     * @return string
     */
    public function authenticate_msg($error, $user, $transient_value){     
        $error  .= '<br /><br />'
        
                . __('Authenticated on : ', 'uwpml')
                . date("Y-m-d H:i:s", $transient_value['auth_on'] )
                . '<br /><br />'
                
                . __('Authenticated for : ', 'uwpml')
                . date("d H:i:s", $transient_value['auth_for'] )
                . '<br /><br />'
                
                . __('Last Visit : ', 'uwpml')
                . date("Y-m-d H:i:s", $transient_value['updated_on'] );
        
        return $error;
    }
    
    /**
     * Send an email notification for failed login attempts.
     * 
     * @param type $user
     * @param type $error
     */
    public function multiple_login_attempt_email($user, $error){
        $admin_email = get_option('admin_email');
        $subject = get_bloginfo('Name') . ' | ' . __('Multiple Login Attempt', 'uwpml');
        $message = print_r($user, true);
        $message .= $error; 
        wp_mail($admin_email, $subject, $message);
    }
}
?>