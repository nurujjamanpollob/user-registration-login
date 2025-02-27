<?php

// output the new password set unsuccessful page
function new_password_set_unsuccessful_page_output($reason = null)
{

    echo get_new_password_set_unsuccessful_page_output_html($reason);

    exit;
}

// output the new password set unsuccessful page
function get_new_password_set_unsuccessful_page_output_html($reason = null): string
{

    $new_password_set_unsuccessful_image = plugin_dir_url(__FILE__) . '../assets/img/password_reset_failed.webp';

    $reason = $reason != null ? $reason : 'We\'re sorry, something went wrong when attempting to reset the password.';

    return <<<HTML

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Password reset failed</title>
        

        <style type="text/css">

            .container {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                width: 100vw;
                height: 100vh;
                margin: 0;
                padding: 0;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100vw;
                background-color: #ffffff;
                height: 100%;
                overflow: hidden;
                font-family: 'DM Sans',sans-serif;
            }

            button {
                align-items: center;
                background-color: #ff474c;
                border-radius: 24px;
                border-style: none;
                box-sizing: border-box;
                color: #ffffff;
                cursor: pointer;
                font-family: "Google Sans",Roboto,Arial,sans-serif;
                font-size: 29px !important;
                font-weight: 500;
                height: 60px !important;
                justify-content: center;
                letter-spacing: .25px;
                max-width: 100%;
                padding: 2px 23px;
                position: relative;
                text-align: center;
            }
            .success_message_image {
                max-height: 350px;
                max-width: 350px;
            }
            h1{
                margin:  0;
                justify-content: center;
                text-align: center;
                color: gray;
                padding-top: 15px;
            }
            p{
                font-size: 26px;
                text-align: center;
            }
            span {
                color: #ff474c;
            }
            @media only screen and (min-width: 375px){
                .success_message_image {
                    max-height: 450px;
                    max-width: 450px;
                }
                h1{
                    font-size: 32px;
                }
                p{
                    font-size: 28px;
                    color: grey;
                    margin: 20px 80px 30px 80px;
                }
                button {
                    font-size: 40px;
                    height: 88px;
                }

        </style>

    </head>

    <body>

    <div class="container">

        <img class="success_message_image" style=""
             src="$new_password_set_unsuccessful_image" alt="Password reset failed image"/>

        <h1>Password reset <span> Failed :(</span></h1>
        <p>$reason</p>


        <!-- get current page url, and empty the form, and reload the page -->
        <button onclick="function reload_page() {

            // get current url, and navigate to it
            let current_url = window.location.href;

            // create a hidden button element and click it
            let hidden_button = document.createElement('a');
            hidden_button.href = current_url;
            hidden_button.click();


        }
        reload_page()">Try again</button>


    </div>


    </body>
    </html>
HTML;


}



