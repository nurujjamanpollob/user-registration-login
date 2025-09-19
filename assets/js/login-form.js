document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('ureglogin_login_form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Get the CSRF token via AJAX
            fetch(loginFormAjax.ajax_url + '?action=ureglogin_login_csrf_token', )
                .then(response => response.text())
                .then(token => {
                    const csrfInput = loginForm.querySelector('input[name="_csrf"]');
                    if (csrfInput) {
                        csrfInput.value = token;
                    }
                    loginForm.submit();
                });
        });
    }
});