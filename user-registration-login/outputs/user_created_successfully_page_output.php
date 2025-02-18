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
        .centered-div {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;

        }
    </style>
</head>

    <body style="font-family: 'DM Sans',sans-serif;">

    <div class="centered-div">

        <img src="<?php echo $user_created_successfully_image_url; ?>" alt="User Created Successfully" style="max-height: 400px; max-width: 1000px" width="100%" height="70%">

        <h1><?php echo $username?>, your user account created Successfully!</h1>

        <p>Thank you for registering with us. We have emailed you to <?php echo $email?>. Please check your email and set a new password to access this website.</p>

    </div>

    <script type="text/javascript">document.addEventListener('DOMContentLoaded', () => {const centeredDiv = document.querySelector('.centered-div');const windowHeight = window.innerHeight;centeredDiv.style.height = windowHeight + 'px';});</script>
    </body>
</html>
    <?php

}