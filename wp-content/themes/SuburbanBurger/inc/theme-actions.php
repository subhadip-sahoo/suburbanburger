<?php 
    function login_with_email_address($user, $username, $password) {
        $user = get_user_by('email',$username);
        if(!empty($user->user_login))
            $username = $user->user_login;
        return wp_authenticate_username_password( null, $username, $password );
    }
    add_filter('authenticate','login_with_email_address', 20, 3);
    
    add_filter('show_admin_bar', '__return_false');
    
    function theme_new_user_reg($user_id){
        global $wpdb;
        $key = wp_generate_password( 20, false );
        do_action( 'retrieve_password_key', $user_id, $key );
        $wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'ID' => $user_id ) );
        update_user_meta($new_user_id,'account_status', 1);
    }
    add_action('user_register', 'theme_new_user_reg', 10, 1);
?>