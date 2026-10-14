import '../scss/form-privacy-feedback.scss';

function setupPrivacyFeedback() {
    document.querySelectorAll('.p-contact-privacy, .p-business-form-page--entry .p-business-form__privacy, .p-recruit-entry__privacy').forEach(row => {
        const checkbox = row.querySelector('input[type="checkbox"]');
        const text = row.querySelector('a');
        if (!checkbox || !text || row.closest('.form-privacy-feedback')) return;
        const wrapper = document.createElement('div');
        wrapper.className = 'form-privacy-feedback';
        row.before(wrapper);
        wrapper.append(row);
        const align = () => {
            wrapper.style.setProperty('--privacy-text-offset', `${Math.max(0, text.getBoundingClientRect().left - wrapper.getBoundingClientRect().left)}px`);
        };
        new ResizeObserver(align).observe(row);
        align();
        window.addEventListener('resize', align);
        if (row.classList.contains('p-contact-privacy')) return; // Existing field validator owns its error.
        const error = document.createElement('p');
        error.className = 'form-privacy-feedback__error';
        error.id = `${checkbox.name}-consent-error`;
        error.setAttribute('aria-live', 'polite');
        error.hidden = true;
        error.textContent = 'プライバシーポリシーへの同意が必要です。';
        wrapper.append(error);
        checkbox.setAttribute('aria-describedby', [checkbox.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));
        const validate = () => {
            error.hidden = checkbox.checked;
            checkbox.setAttribute('aria-invalid', String(!checkbox.checked));
            align();
            return checkbox.checked;
        };
        checkbox.addEventListener('change', () => {
            error.hidden = true;
            checkbox.setAttribute('aria-invalid', 'false');
        });
        checkbox.addEventListener('invalid', validate);
        const form = checkbox.closest('form');
        form?.addEventListener('submit', event => {
            if (!validate()) { event.preventDefault(); checkbox.focus(); }
        });
        form?.addEventListener('reset', () => { error.hidden = true; checkbox.removeAttribute('aria-invalid'); });
    });
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setupPrivacyFeedback);
else setupPrivacyFeedback();
