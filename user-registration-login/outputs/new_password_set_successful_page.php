<?php


/**
 * Output for new password set successful page
 */
function new_password_set_successful_page_output($user) {

 $new_password_set_successful_image_url = plugin_dir_url(__FILE__) . '../assets/img/password_reset_done.webp';


    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $user ?>, your user account password reset is successful</title>

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
                background-color: #008080;
                border-radius: 24px;
                border-style: none;
                box-sizing: border-box;
                color: #FFF;
                cursor: pointer;
                font-family: "Google Sans",Roboto,Arial,sans-serif;
                font-size: 29px !important;
                font-weight: 400;
                height: 60px !important;
                justify-content: center;
                letter-spacing: .25px;
                max-width: 100%;
                padding: 1px 24px 2px 24px;
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
                font-size: 25px;
                text-align: center;
            }
            span {
                color: #008080;
            }
            @media only screen and (min-width: 375px){
                .success_message_image {
                    max-height: 450px;
                    max-width: 450px;
                }
                h1{
                    font-size: 34px;
                }
                p{
                    font-size: 30px;
                    color: grey;
                    margin: 20px 80px 30px 80px;
                }
                button {
                    font-size: 44px;
                    height: 88px;
                }

        </style>

    </head>

    <body>

    <div class="container">

        <img class="success_message_image" style=""
             src="<?php echo $new_password_set_successful_image_url ?>" alt="New password set successful image"/>

        <h1>Hey <span> <?php echo $user ?></span>, your user account password has been reset successfully!</h1>
        <p>Now, you can use your new password to log in.</p>

        <!-- embed login button -->
        <button onclick="window.location.href='<?php echo wp_login_url('/') ?>'">Login</button>

    </div>


    </body>
    </html>
    <?php

}