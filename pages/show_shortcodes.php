<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


function showShortCodes()
{

    ?>
    <div class="wrap">
        <h2>User Registration & Login Shortcodes</h2>
        <p>Use the following shortcodes to display the registration and login forms on your website.</p>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Registration Form</th>
                <td><input type="text" value="[register_form]" readonly /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Login Form</th>
                <td><input type="text" value="[login_form]" readonly /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Password Reset Form</th>
                <td><input type="text" value="[password_recovery_form]" readonly /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Set User Password Form</th>
                <td><input type="text" value="[set_user_password_form]" readonly /></td>
            </tr>
        </table>
    </div>
    <?php

}