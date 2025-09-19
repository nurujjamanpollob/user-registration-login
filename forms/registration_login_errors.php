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

	    // Define styles for the container
	    $container_style = 'display: flex; flex-direction: column; text-align: left; box-sizing: border-box;';

	    // Define styles for the error message container with modern design
	    $error_container_style = 'display: flex; align-items: flex-start; margin: 8px 0; padding: 12px 16px; background-color: #ffebee; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);';

	    // Define styles for the vertical bar
	    $vertical_bar_style = 'width: 4px; background-color: #d32f2f; border-radius: 2px; margin-right: 12px; flex-shrink: 0; height: 100%;';

	    // Define styles for the error text
	    $error_text_style = 'color: #c62828; font-size: 1rem; line-height: 1.5; text-align: left; flex-grow: 1; margin: 0;';

	    // create a string of error messages
	    $error_messages = '<div style="' . $container_style . '">';

	    // Loop error codes and display errors
	    foreach ($codes as $code) {
		    $message = registration_login_errors()->get_error_message($code);
		    $error_messages .= '<div style="' . $error_container_style . '">';
		    $error_messages .= '<div style="' . $vertical_bar_style . '"></div>';
		    $error_messages .= '<p class="error" style="' . $error_text_style . '"><strong style="font-weight: 600;">' . __('Error') . '</strong>: ' . esc_html($message) . '</p>';
		    $error_messages .= '</div>';
	    }

	    $error_messages .= '</div>';

	    // Echo the complete error message container
	    echo $error_messages;

    }

}