
WordPress' user registration and login plugin with reCaptcha and anti-spam features! <br>

This plugin is a simple and easy to use plugin that allows you to add a user registration and login form to your website, 
while provides security with Google reCaptcha, and anti-spam features.

The plugin is designed to be lightweight and no unnecessary features,
and can be easily customized to fit your website's design. 
This plugin is perfect if you want a robust user registration and login system without the bloat.

This plugin now offers anti-spam features, such as blacklisting usernames, blocklisting email domains, 
whitelist email domains, and blocklisting signup from known disposable email domains.

This plugin is well tested, and if you find any issues, please let us know.

This plugin now offers override default WordPress login and registration pages, password reset and password change pages.
You can use the same shortcode [register_form], [login_form], [password_recovery_form], and [set_user_password_form]
to override the default WordPress login and registration pages, password reset and password change pages
Check the settings for more details. With version 2.1.3 a new function been added to override the default woocommerce user login form, further raise security and prevent spam registrations.


How to get started:
1. Download the plugin zip file 'user-registration-login.zip'
2. Go to your WordPress admin panel and upload the zip file
3. Set up the plugin by entering your reCaptcha keys in the plugin settings

That's it! You can now start using the plugin on your website.

how to embed forms in your website:
1. Install the plugin
2. Create a new page or post
3. Add the shortcode any of them or together [register_form], [login_form], [password_recovery_form], [set_user_password_form] to the page or post
4. Publish the page or post
5. The form will now be displayed on your website

Customization:
The plugin right now offers a few customizable options. 

The list of customizable options includes:
- reCaptcha keys
- New user default role
- User creation email sent to the site admin
- Load plugin CSS js for input fields styling
- Blacklisting usernames
- Blocklisting email domains
- Blocklisting signup from known disposable email domains according to the list from this file `user-registration-login/assets/file/disposable_email_blocklist.conf`
- Allow signup from specific email domains (Whitelist email domains)
- Override default WordPress login and registration pages
- Override default WordPress password recovery and password change pages
- Override default WooCommerce user login form


Note: if your theme has custom CSS for input fields, you can disable the plugin CSS and js for input fields styling, 
in order to use your theme's CSS for input fields.

If you like to style the form, you can use the plugin CSS classes to style the form.

to customize the form, you can use the following CSS classes:
- .input-container
- input
- .label
- input, .label .text
- input:focus
- input:focus + .label .text
- .label .text
- input:focus + .label .text, :not(input[value=""]) + .label .text
- input:focus + .label .text
- .submit-button
- .error_div
- .minimal-material-dialog-dialog-button
- .error


More customization options will be added in future updates.

Future updates:
- Add more customization options
- Add more form styles
- visual form builder

If you have any questions or need help with the plugin,
please contact us at <a href="https://eazewebit.com">eazewebit.com</a>. 
This plugin is optimized for the best performance and security, leaves minimal footprint on your website.
