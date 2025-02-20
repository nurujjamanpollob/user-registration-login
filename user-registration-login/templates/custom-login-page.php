<?php

function output()
{

    $user_created_successfully_image_url = plugin_dir_url(__FILE__) . '../assets/img/user_created_successfully.webp';

    ob_start();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Created Successfully</title>

        <style>

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
                    height: 400px;
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
    <body>
        <div class="centered-div">
            <h1>This is user account login form</h1>
            <img src="<?php echo $user_created_successfully_image_url; ?>" alt="User Created Successfully" class="success_message_image">
            <p>User account login form!</p>
        </div>
    </body>
    </html>

    <?php
    return ob_get_clean();

}

echo output();

