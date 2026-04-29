(function () {
    'use strict';

    document.querySelectorAll('.ffl-contact-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var msgBox = form.querySelector('.ffl-contact-message');
            var submitBtn = form.querySelector('.ffl-contact-submit');

            msgBox.textContent = '';
            msgBox.className = 'ffl-contact-message';
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            var formData = new FormData(form);
            formData.append('action', 'flairltd_contact_submit');

            fetch(form.dataset.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Message';

                if (data.success) {
                    msgBox.textContent = data.data.message || 'Thank you! Your message has been sent.';
                    msgBox.classList.add('ffl-contact-success');
                    form.reset();
                } else {
                    msgBox.textContent = data.data.message || 'Something went wrong. Please try again.';
                    msgBox.classList.add('ffl-contact-error');
                }
            })
            .catch(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Message';
                msgBox.textContent = 'Network error. Please check your connection and try again.';
                msgBox.classList.add('ffl-contact-error');
            });
        });
    });
})();
