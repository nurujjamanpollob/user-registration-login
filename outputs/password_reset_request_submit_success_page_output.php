<?php

// outputs password reset request submit success page
function password_reset_request_submit_success_page_output($username): void
{

  $password_resend_code_sent_successfully_img = plugin_dir_url(__FILE__) . '../assets/img/password_reset_code_sent.webp';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $username ?>, your password reset verification code sent to your email</title>

        <style type="text/css">

            header {
                display: none;
            }

            footer {
                display: none;
            }

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
                color: #ffffff;
                cursor: pointer;
                font-family: "Raleway", serif;
                font-size: 34px !important;
                font-weight: 400;
                height: 58px !important;
                justify-content: center;
                letter-spacing: .25px;
                max-width: 100%;
                padding: 6px 43px 35px 45px;
                position: relative;
                text-align: center;
            }
            .success_message_image {
                max-height: 350px;
                max-width: 350px;
            }
            h1{
                font-family: "Raleway", serif;
                margin:  0;
                justify-content: center;
                text-align: center;
                color: gray;
                padding-top: 15px;
            }
            p{
                font-size: 25px;
                font-family: "Raleway", serif;
                text-align: center;
            }
            span {
                color: green;
            }
            @media only screen and (min-width: 375px){
                .success_message_image {
                    max-height: 550px;
                    max-width: 550px;
                }
                h1{
                    font-size: 35px;
                }
                p{
                    font-size: 31px;
                    color: grey;
                    margin: 20px 80px 30px 80px;
                }
                button {
                    font-size: 40px;
                    height: 88px;
                    font-weight: ;
                }

        </style>

    </head>

    <body>

    <div class="container">

        <img class="success_message_image" style=""
             src="<?php echo $password_resend_code_sent_successfully_img?>" alt="Password reset code sent successfully">

        <h1>Verification code sent to <span><?php echo $username ?></span></h1>
        <p>Your password reset verification code sent to your email. Using this code, you'll be able to reset your user account password and login to this website.</p>

    </div>


    </body>
    </html>
    <?php
}

