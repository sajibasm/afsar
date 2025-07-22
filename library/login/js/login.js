document.addEventListener('DOMContentLoaded', function () {
    // Disable submit button initially
    const submitBtn = document.getElementById('form-submit-btn');
    if (submitBtn) {
        submitBtn.disabled = true;
    }

    // reCAPTCHA onSubmit handler
    window.onSubmit = function (token) {
        console.log(token);
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    };

    // reCAPTCHA onload callback
    window.onloadCallback = function () {
        grecaptcha.render('recaptcha-container', {
            sitekey: GOOGLE_CAPTCHA_SITE_KEY,
            callback: onSubmit
        });
    };
});
