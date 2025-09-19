<?php

// function for tracking error messages
function registration_login_errors()
{
    static $user_reg_login_errors; // global variable

    return $user_reg_login_errors ?? ($user_reg_login_errors = new WP_Error(null, null, null));
}

// display error messages
function register_messages()
{

    if ($codes = registration_login_errors()->get_error_codes()) {

        // create a string of error messages
        $error_messages = '<div style="display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: center;" class="error_div">';

        // add image to an error message
        $error_messages .= '<img src="' . plugin_dir_url(__FILE__) . '../assets/img/error_404.webp" style="width: 100%; height: 40%; margin-bottom: 20px;"/>';
        // Loop error codes and display errors
        foreach ($codes as $code) {
            $message = registration_login_errors()->get_error_message($code);
            // echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
            $error_messages .= '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span>';
        }
        echo '</div>';
        $error_messages .= '</div>';

        echo "<script>createAndShowDialog('', '$error_messages', null, [{text: 'Cancel', onClick: () => {closeDialog();}}]);</script>";

    }

}
