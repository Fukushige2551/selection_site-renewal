import '../scss/page-company-business-form.scss';

const form = document.querySelector('.p-business-form');

form?.addEventListener('submit', (event) => {
    const email = form.querySelector('[name="email"]');
    const confirmation = form.querySelector('[name="email_confirm"]');

    if (email && confirmation && email.value !== confirmation.value) {
        confirmation.setCustomValidity('メールアドレスが一致していません。');
    } else {
        confirmation?.setCustomValidity('');
    }

    const fields = [...form.querySelectorAll('[required]')];
    const firstInvalid = fields.find((field) => !field.checkValidity());

    form.classList.add('is-validated');

    if (firstInvalid) {
        event.preventDefault();
        firstInvalid.focus();
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

form?.querySelector('[name="email_confirm"]')?.addEventListener('input', (event) => {
    event.currentTarget.setCustomValidity('');
});
