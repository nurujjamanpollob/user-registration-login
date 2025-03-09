<?php

/**
 * Output for user created successful page
 * @return void
 */
function user_creation_failed_page_output($username, $email)
{
    $user_created_successfully_image_url = plugin_dir_url(__FILE__) . '../assets/img/error_404.webp';

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
                text-align: center;
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

        <h1><?php echo $username ?>, your user account creation failed :(</h1>

        <p>Unfortunately, the username: <?php echo $username ?> and email: <?php echo $email ?> cannot be used to create an account at this time. Please try again later.</p>

    </div>
    </body>
    </html>
    <?php

}