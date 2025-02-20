<?php


echo output2();

function output2()
{
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
    <body>
        <div class="centered-div">
            <h1>User Created Successfully</h1>
            <img class="success_message_image" src="<?php echo plugin_dir_url( __DIR__ ) . 'assets/images/success.png'; ?>" alt="Success">
            <p>Your account has been created successfully. You can now login to your account.</p>
        </div>

    </body>
    </html>



    <?php

    return ob_get_clean();

}


