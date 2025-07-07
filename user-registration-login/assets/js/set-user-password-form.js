document.addEventListener('DOMContentLoaded', function () {
    const setUserPasswordForm = document.getElementById('set_user_password_form');
    if (setUserPasswordForm) {
        setUserPasswordForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetch(setUserPasswordFormAjax.ajax_url + '?action=generate_set_password_csrf_token')
                .then(response => response.text())
                .then(token => {
                    const csrfInput = setUserPasswordForm.querySelector('input[name="_csrf"]');
                    if (csrfInput) {
                        csrfInput.value = token;
                    }
                    setUserPasswordForm.submit();
                });
        });
    }
});