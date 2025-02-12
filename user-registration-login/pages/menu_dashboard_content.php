<?php

function menu_dashboard_content()
{
    ?>
    <div class="wrap">
        <h2>User Registration & Login</h2>
        <p>Welcome to the User Registration & Login plugin. Thanks for using this free plugin by Eaze Web IT. If you need any support, visit to our website: <a target="_blank" href="https://eazewebit.com">eazewebit.com</a> </p>

        The Setting page link is <a href="<?php echo admin_url('admin.php?page=user-registration-login-settings'); ?>">here</a>
        <br>
        The Shortcodes page link is <a href="<?php echo admin_url('admin.php?page=user-registration-login-shortcodes'); ?>">here</a>
    </div>
    <?php
}