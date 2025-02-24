<?php



if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../verifier/recaptcha_verify.php';

// register recaptcha script
/**
 * Enqueue admin scripts
 */
function user_registration_login_admin_script_enqueue() {
    wp_register_script("recaptcha", "https://www.google.com/recaptcha/api.js?explicit&hl=" . get_locale());
    wp_enqueue_script("recaptcha");

}
add_action( 'admin_enqueue_scripts', 'user_registration_login_admin_script_enqueue', 0);
// test recaptcha
function recaptcha_test()
{


    ?>

    <div class="wrap">
        <h2>Recaptcha Test</h2>

        <p>
            Use the following form to test it if the recaptcha is working properly.
            The reCaptcha doesn't work in forms until you complete recaptcha test. <br>

            Current status: <?php echo get_option(RECAPTCHA_VERIFIED_OPTION_NAME) ? "<b style='color: green'>Verified</b>" : "<b style='color: red'>Not Verified</b>"; ?>
        </p> Make sure you have added the recaptcha keys in the settings page. <br>
        <a href="<?php echo admin_url('admin.php?page=user-registration-login-settings'); ?>">Settings Page</a>
        <br>
        <br>
        <form method="post"  id="recaptcha-test-form" action="">
            <div class="g-recaptcha" data-sitekey="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"></div>
            <br>
            <input type="submit" name="submit" value="Submit">
        </form>
    </div>

    <?php

    // show recaptcha test submission result
    show_recaptcha_test_submission_result();
    ?>


    <?php

}

/**
 * @return void Show recaptcha test submission result
 */
function show_recaptcha_test_submission_result()
{
    $recaptcha_test_result = recaptcha_test_results()->get_error_messages();
    // get status code and check if it contains test-passed
    $is_test_passed = count($recaptcha_test_result) > 0 && recaptcha_test_results()->get_error_code() === "test-passed";

    require_once plugin_dir_path(__FILE__) . '../plugin_options.php';

    if (count($recaptcha_test_result) > 0 && $is_test_passed) {

        // update the option
        update_option(RECAPTCHA_VERIFIED_OPTION_NAME, true);
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo $recaptcha_test_result[0]; ?></p>
        </div>
        <?php



    } else if (count($recaptcha_test_result) > 0) {

        // update the option
        update_option(RECAPTCHA_VERIFIED_OPTION_NAME, false);

        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo $recaptcha_test_result[0]; ?></p>
        </div>
        <?php
    }
}

/**
 * Handle recaptcha test submission
 */
function recaptcha_test_submission()
{

    if (isset($_POST['g-recaptcha-response'])) {
        $recaptcha_response = $_POST['g-recaptcha-response'];
        $recaptcha_verify = new RecaptchaVerify();
        $is_test_successful = $recaptcha_verify->verifyResponse($recaptcha_response);
        if ($is_test_successful) {
            recaptcha_test_results()->add("test-passed", "Recaptcha test passed");
        } else {
            recaptcha_test_results()->add("test-failed", "Recaptcha test failed");
        }
    }


}

add_action('admin_init', 'recaptcha_test_submission', 10);

// function for recaptcha test result
function recaptcha_test_results()
{
    // define global variable
    global $recaptcha_test_result;

    return $recaptcha_test_result ?? ($recaptcha_test_result = new WP_Error(null, null, null));
}




