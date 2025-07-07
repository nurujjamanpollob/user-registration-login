document.addEventListener('DOMContentLoaded', function () {
    const registrationForm = document.getElementById('urlreglogin_registration_form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetch(registrationFormAjax.ajax_url + '?action=generate_registration_csrf_token')
                .then(response => response.text())
                .then(token => {
                    const csrfInput = registrationForm.querySelector('input[name="_csrf"]');
                    if (csrfInput) {
                        csrfInput.value = token;
                    }
                    registrationForm.submit();
                });
        });
    }
});