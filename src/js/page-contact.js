import '../scss/page-contact.scss';
import './form-privacy-feedback.js';
import { validationMessages } from './form-validation-messages.js';

const ERROR_CLASS = 'p-contact-error';
const INVALID_CLASS = 'is-invalid';

const validationRules = [
    {
        name: 'contact_store',
        type: 'radio',
        message: 'お問い合わせ先を選択してください。',
        getTarget: (root) => root.querySelector('.p-contact-store__options'),
    },
    {
        name: 'your_name',
        message: 'お名前を入力してください。',
    },
    {
        name: 'your_email',
        message: 'メールアドレスを入力してください。',
        formatMessage: validationMessages.email,
        validate: (field) => field.validity.valid && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value),
    },
    {
        name: 'your_email_confirm',
        message: 'メールアドレス（確認）を入力してください。',
        formatMessage: validationMessages.email,
        matchMessage: validationMessages.emailMismatch,
        validate: (field, root) => {
            const email = root.querySelector('[name="your_email"]');

            if (!field.validity.valid || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                return 'format';
            }

            if (email && email.value.trim() !== field.value.trim()) {
                return 'match';
            }

            return true;
        },
    },
    {
        name: 'message',
        message: 'お問い合わせ内容を入力してください。',
    },
    {
        name: 'privacy_agree',
        type: 'checkbox',
        message: validationMessages.consent,
        getTarget: (root) => root.querySelector('.p-contact-privacy__label'),
    },
];

const getField = (root, rule) => {
    if (rule.type === 'radio') {
        return [...root.querySelectorAll(`[name="${rule.name}"]`)];
    }

    return root.querySelector(`[name="${rule.name}"]`);
};

const getErrorId = (rule) => `contact-error-${rule.name.replace(/_/g, '-')}`;

const getErrorTarget = (root, rule, field) => {
    if (rule.getTarget) {
        return rule.getTarget(root);
    }

    return field?.closest('.p-contact-fields__item') || field;
};

const clearError = (root, rule) => {
    const error = root.querySelector(`#${getErrorId(rule)}`);
    const field = getField(root, rule);

    if (error) {
        error.remove();
    }

    if (Array.isArray(field)) {
        field.forEach((input) => {
            input.removeAttribute('aria-invalid');
            input.removeAttribute('aria-describedby');
            input.closest('.p-contact-store__option')?.classList.remove(INVALID_CLASS);
        });
        return;
    }

    field?.removeAttribute('aria-invalid');
    field?.removeAttribute('aria-describedby');
    field?.classList.remove(INVALID_CLASS);
};

const showError = (root, rule, message) => {
    const field = getField(root, rule);
    const target = getErrorTarget(root, rule, Array.isArray(field) ? field[0] : field);

    if (!target) {
        return;
    }

    clearError(root, rule);

    const error = document.createElement('p');
    error.id = getErrorId(rule);
    error.className = ERROR_CLASS;
    error.setAttribute('role', 'alert');
    error.textContent = message;
    if (target.classList.contains('p-contact-fields__item')) {
        target.append(error);
    } else {
        target.insertAdjacentElement('afterend', error);
    }

    if (Array.isArray(field)) {
        field.forEach((input) => {
            input.setAttribute('aria-invalid', 'true');
            input.setAttribute('aria-describedby', error.id);
            input.closest('.p-contact-store__option')?.classList.add(INVALID_CLASS);
        });
        return;
    }

    field?.setAttribute('aria-invalid', 'true');
    field?.setAttribute('aria-describedby', error.id);
    field?.classList.add(INVALID_CLASS);
};

const validateRule = (root, rule, showRequired = true) => {
    const field = getField(root, rule);

    if (Array.isArray(field)) {
        const isChecked = field.some((input) => input.checked);

        if (!isChecked && showRequired) {
            showError(root, rule, rule.message);
            return false;
        }

        clearError(root, rule);
        return true;
    }

    if (!field) {
        return true;
    }

    if (rule.type === 'checkbox') {
        if (!field.checked && showRequired) {
            showError(root, rule, rule.message);
            return false;
        }

        clearError(root, rule);
        return true;
    }

    if (!field.value.trim()) {
        if (!showRequired) { clearError(root, rule); return true; }
        showError(root, rule, rule.message);
        return false;
    }

    const byteLimit = { your_name: 50, your_email: 100, message: 2000 }[rule.name];
    if (byteLimit) {
        const raw = rule.name === 'message' ? field.value.replace(/\r\n|\r|\n/g, '\r\n') : field.value;
        let sending = field.value.replace(/[ \n\r\t\v\0]+$/, '');
        if (rule.name === 'message') sending = sending.replace(/\r\n|\r|\n/g, '<br>');
        const encoder = new TextEncoder();
        if (Math.max(encoder.encode(raw).length, encoder.encode(sending).length) > byteLimit) {
            showError(root, rule, `入力内容が長すぎます。UTF-8で${byteLimit}バイト以内にしてください。`);
            return false;
        }
    }

    if (rule.validate) {
        const result = rule.validate(field, root);

        if (result === 'format') {
            showError(root, rule, rule.formatMessage);
            return false;
        }

        if (result === 'match') {
            showError(root, rule, rule.matchMessage);
            return false;
        }

        if (result === false) {
            showError(root, rule, rule.formatMessage || rule.message);
            return false;
        }
    }

    clearError(root, rule);
    return true;
};

const validateContact = (root) => {
    const results = validationRules.map((rule) => validateRule(root, rule));
    const firstError = root.querySelector(`.${ERROR_CLASS}`);

    if (firstError) {
        firstError.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }

    return results.every(Boolean);
};

const setupContactValidation = () => {
    const root = document.querySelector('.p-contact');
    const inputForm = root?.querySelector('.p-contact-form');

    if (!root || !inputForm) {
        return;
    }

    validationRules.forEach((rule) => {
        const field = getField(root, rule);
        const fields = Array.isArray(field) ? field : [field];

        fields.filter(Boolean).forEach((input) => {
            input.addEventListener('input', () => validateRule(root, rule, false));
            input.addEventListener('change', () => validateRule(root, rule, false));
        });
    });

    inputForm.addEventListener('submit', (event) => {
        if (!validateContact(root)) {
            event.preventDefault();
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupContactValidation);
} else {
    setupContactValidation();
}
