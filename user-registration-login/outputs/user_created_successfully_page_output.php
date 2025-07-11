<?php

/**
 * Output for user created successful page
 * @return void
 */
function user_created_successfully_page_output($username, $email)
{
    $user_created_successfully_image_url = plugin_dir_url(__FILE__) . '../assets/img/user_created_successfully.webp';

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Created Successfully</title>

        <style>

            header {
                display: none;
            }

            footer {
                display: none;
            }

            p {
                max-width: 800px;
            }

            body {
                height: 100%;
                overflow: hidden;
            }

            .centered-div {
                display: flex;
                height: 100vh;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }

            @media (max-width: 1000px) {
                .success_message_image {
                    height: auto;
                }

                body {
                    margin-left: 30px;
                    margin-right: 30px;
                    height: 100%;
                    overflow: hidden;
                }

                h1 {
                    text-align: center;
                }

                p {
                    text-align: center;
                }
            }

            @media (min-width: 1000px) {
                .success_message_image {
                    height: 100%;
                }

                body {
                    margin-left: 30px;
                    margin-right: 30px;
                    height: 100%;
                    overflow: hidden;
                }

                h1 {
                    text-align: center;
                }

                p {
                    text-align: center;
                }
            }
        </style>
    </head>

    <body style="font-family: 'DM Sans',sans-serif;">

    <div class="centered-div">

        <img class="success_message_image" src="<?php echo $user_created_successfully_image_url; ?>"
             alt="User Created Successfully" style="max-height: 500px; max-width: 1000px" width="100%" height="70%">

        <h1><?php echo $username ?>, your user account created successfully!</h1>

        <p>Thank you for registering with us. We have emailed you to <?php echo $email ?>. Please check your email and
            set a new password to access this website.</p>

        <!-- Button to go to login page -->
        <button style="background-color: #008080; color: white; border: none; border-radius: 5px; padding: 10px 20px; font-size: 16px; cursor: pointer;">
            <a href="<?php echo wp_login_url(); ?>" style="color: white; text-decoration: none;">Go to Login</a>
        </button>

    </div>
    </body>
    </html>
    <?php

}