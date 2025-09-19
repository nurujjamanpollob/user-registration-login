document.addEventListener('DOMContentLoaded', function () {

    const passwordRecoveryForm = document.getElementById('ureglogin_password_recovery_form');

    if (passwordRecoveryForm) {

        passwordRecoveryForm.addEventListener('submit', function (e) {
            e.preventDefault();

            fetch(pwdRecovery.ajax_url + '?action=generate_password_recovery_csrf_token',)
                .then(response => response.text())
                .then(token => {
                    const csrfInput = passwordRecoveryForm.querySelector('input[name="_csrf"]');
                    if (csrfInput) {
                        csrfInput.value = token;
                    }

                    passwordRecoveryForm.submit();
                });
        });
    }
});