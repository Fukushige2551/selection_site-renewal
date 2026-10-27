import { validationMessages, telephoneIsValid } from './form-validation-messages.js';
import '../scss/application-validation.scss';
import { createFieldError, describeFieldError, renderFieldError } from './form-field-error.js';

function setup() {
    for (const form of document.querySelectorAll('.p-business-form-page--entry form, .p-recruit-entry__form')) {
        form.noValidate = true;
        const validators = new Map();
        for (const field of form.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]), select, textarea')) {
            if (field.name === 'age') {
                field.min = '0';
                field.step = '1';
                field.addEventListener('keydown', event => { if (event.key === '-') event.preventDefault(); });
                field.addEventListener('beforeinput', event => { if (event.data?.includes('-')) event.preventDefault(); });
                field.addEventListener('paste', event => { if (event.clipboardData?.getData('text').includes('-')) event.preventDefault(); });
            }
            const host = field.closest('.wpcf7-form-control-wrap') || field.parentElement;
            const labelNode = host.parentElement.querySelector('.p-business-form__label, .p-recruit-entry__label');
            const label = (labelNode?.textContent || field.name).replace(/\*必須/g, '').trim();
            const error = createFieldError(`application-error-${field.name}`, 'application-field-error');
            host.append(error);
            describeFieldError(field, error);
            const validate = (required = false) => {
                field.setCustomValidity('');
                let message = '';
                if (!field.value.trim()) {
                    if (required && field.required) message = `${label}を${field.tagName === 'SELECT' ? '選択' : '入力'}してください。`;
                } else if (field.name === 'age' && !/^[0-9]+$/.test(field.value)) {
                    message = '年齢は0以上の整数で入力してください。';
                } else if (field.type === 'email' && (field.validity.typeMismatch || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value))) {
                    message = validationMessages.email;
                } else if (field.name === 'email_confirm' && field.value !== form.querySelector('[name="email"]')?.value) {
                    message = validationMessages.emailMismatch;
                } else if (field.type === 'tel' && !telephoneIsValid(field.value)) {
                    message = `${label}の形式を確認してください。半角数字で入力してください。（例：03-1234-5678）`;
                } else if (field.name !== 'birth_date' && !field.validity.valid) {
                    message = `${label}の形式・入力範囲を確認してください。`;
                }
                field.setCustomValidity(message);
                field.setAttribute('aria-invalid', String(Boolean(message)));
                renderFieldError(error, message);
                return !message;
            };
            validators.set(field, validate);
            for (const event of ['input', 'change', 'blur']) field.addEventListener(event, () => validate(false));
        }
        form.querySelector('[name="email"]')?.addEventListener('input', () => {
            const confirmation = form.querySelector('[name="email_confirm"]');
            if (confirmation?.value) validators.get(confirmation)?.(false);
        });
        form.addEventListener('submit', event => {
            const invalid = [...validators].filter(([, validate]) => !validate(true));
            if (invalid.length) { event.preventDefault(); invalid[0][0].focus(); }
        }, true);
    }
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setup);
else setup();
