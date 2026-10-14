import '../scss/page-company-business-form.scss';
import '../scss/page-company-business-contact-design.scss';
import '../scss/page-company-business-confirm.scss';
import '../scss/page-company-business-thanks.scss';
import '../scss/page-job-entry-design.scss';
import '../scss/page-job-confirm.scss';
import '../scss/page-job-thanks.scss';
import './form-privacy-feedback.js';
import './application-validation.js';
import { validationMessages } from './form-validation-messages.js';

const form = document.querySelector('.p-business-form');
if (form?.closest('.p-business-form-page--entry')) {
    for (const select of form.querySelectorAll('select')) {
        if (select.name === 'contact_time') {
            select.required = false;
            select.setAttribute('aria-required', 'false');
            select.closest('.p-business-form__field')?.querySelector('.p-business-form__label em')?.remove();
            continue;
        }
        select.required = true;
        select.setAttribute('aria-required', 'true');
        const label = select.closest('.p-business-form__field')?.querySelector('.p-business-form__label');
        if (label && !label.querySelector('em')) {
            const required = document.createElement('em');
            required.textContent = '*必須';
            label.append(required);
        }
    }
}

export function businessByteLength(value, name, sending = false) {
    if (sending) value = value.replace(/[ \n\r\t\v\0]+$/, '');
    if (name === 'message') value = value.replace(/\r\n|\r|\n/g, sending ? '<br>' : '\r\n');
    return new TextEncoder().encode(value).length;
}

// Match CF7's telephone rule, including separators and international prefixes.
export function isBusinessTelephone(value) {
    let normalized = value.replace(/[#*].*$/, '').replace(/[()/.*#\s-]+/g, '');
    if (normalized.startsWith('+') || normalized.startsWith('00')) {
        normalized = '+' + normalized.replace(/^[+0]+/, '');
    }
    return /^[+]?[0-9]+$/.test(normalized) && normalized.length > 5 && normalized.length < 16;
}

const siteUrl = document.querySelector('.p-business-form-page--contact [name="site_url"]');
let validateSiteUrl = () => true;
let validateBusinessFields = () => true;
if (siteUrl) {
    const error = document.createElement('p');
    error.id = 'business-site-url-error';
    error.className = 'business-field-error';
    error.setAttribute('aria-live', 'polite');
    error.hidden = true;
    siteUrl.closest('.wpcf7-form-control-wrap')?.append(error);
    if (!error.isConnected) siteUrl.after(error);
    const describedBy = siteUrl.getAttribute('aria-describedby');
    siteUrl.setAttribute('aria-describedby', [describedBy, error.id].filter(Boolean).join(' '));
    validateSiteUrl = () => {
        // Native URL validation works even when CF7 sets novalidate on the form.
        siteUrl.setCustomValidity('');
        const valid = siteUrl.value === '' || !siteUrl.validity.typeMismatch;
        const message = valid ? '' : validationMessages.url;
        siteUrl.setCustomValidity(message);
        siteUrl.setAttribute('aria-invalid', String(!valid));
        error.textContent = message;
        error.hidden = valid;
        return valid;
    };
    siteUrl.addEventListener('input', validateSiteUrl);
    siteUrl.addEventListener('blur', validateSiteUrl);
}

form?.addEventListener('submit', (event) => {
    const urlValid = validateSiteUrl();
    const email = form.querySelector('[name="email"]');
    const confirmation = form.querySelector('[name="email_confirm"]');

    if (email && confirmation && email.value !== confirmation.value) {
        confirmation.setCustomValidity(validationMessages.emailMismatch);
    } else {
        confirmation?.setCustomValidity('');
    }

    const businessValid = validateBusinessFields();
    const fields = form.closest('.p-business-form-page--contact')
        ? [...form.querySelectorAll('input:not([type="hidden"]), select, textarea')]
        : [...form.querySelectorAll('[required]')];
    if (siteUrl && !fields.includes(siteUrl)) fields.push(siteUrl);
    const firstInvalid = fields.find((field) => !field.checkValidity());

    form.classList.add('is-validated');

    if (firstInvalid || !urlValid || !businessValid) {
        event.preventDefault();
        const target = firstInvalid || siteUrl;
        target.focus();
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

form?.querySelector('[name="email_confirm"]')?.addEventListener('input', (event) => {
    event.currentTarget.setCustomValidity('');
});

// Progressive enhancement: CF7 still receives and validates the original select.
const inquirySelect = document.querySelector('.p-business-form-page--contact select[name="inquiry_type"]');
if (inquirySelect) {
    const wrapper = document.createElement('div');
    wrapper.className = 'business-select';
    inquirySelect.before(wrapper);
    wrapper.append(inquirySelect);
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'business-select__trigger';
    trigger.setAttribute('role', 'combobox');
    trigger.setAttribute('aria-label', 'お問い合わせ内容（必須）');
    trigger.setAttribute('aria-required', 'true');
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    const menu = document.createElement('ul');
    menu.id = 'business-inquiry-options';
    menu.className = 'business-select__menu';
    menu.setAttribute('role', 'listbox');
    menu.setAttribute('aria-label', 'お問い合わせ内容');
    menu.hidden = true;
    trigger.setAttribute('aria-controls', menu.id);
    const choices = [...inquirySelect.options].filter(option => option.value !== '');
    let active = 0;
    const items = choices.map((option, index) => {
        const item = document.createElement('li');
        item.id = `${menu.id}-${index}`;
        item.setAttribute('role', 'option');
        item.textContent = option.textContent;
        item.addEventListener('click', () => choose(index));
        menu.append(item);
        return item;
    });
    function highlight(index) {
        active = (index + items.length) % items.length;
        items.forEach((item, i) => item.classList.toggle('is-active', i === active));
        trigger.setAttribute('aria-activedescendant', items[active].id);
    }
    function close() {
        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        trigger.removeAttribute('aria-activedescendant');
    }
    function sync() {
        trigger.textContent = inquirySelect.selectedOptions[0]?.textContent || '';
        items.forEach((item, i) => item.setAttribute('aria-selected', String(choices[i].value === inquirySelect.value)));
        if (inquirySelect.value) trigger.removeAttribute('aria-invalid');
    }
    function choose(index) {
        inquirySelect.value = choices[index].value;
        inquirySelect.dispatchEvent(new Event('input', { bubbles: true }));
        inquirySelect.dispatchEvent(new Event('change', { bubbles: true }));
        sync();
        close();
        trigger.focus();
    }
    function open() {
        if (!items.length) return;
        menu.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        highlight(Math.max(0, choices.findIndex(option => option.value === inquirySelect.value)));
    }
    trigger.addEventListener('click', () => menu.hidden ? open() : close());
    trigger.addEventListener('keydown', event => {
        if (event.key === 'Escape') { close(); return; }
        if (event.key === 'Tab') { close(); return; }
        if (['ArrowDown', 'ArrowUp', 'Home', 'End', 'Enter', ' '].includes(event.key)) {
            event.preventDefault();
            if (menu.hidden) { open(); return; }
            if (event.key === 'Enter' || event.key === ' ') choose(active);
            else if (event.key === 'Home') highlight(0);
            else if (event.key === 'End') highlight(items.length - 1);
            else highlight(active + (event.key === 'ArrowDown' ? 1 : -1));
        }
    });
    document.addEventListener('click', event => { if (!wrapper.contains(event.target)) close(); });
    inquirySelect.addEventListener('change', sync);
    inquirySelect.addEventListener('invalid', event => {
        event.preventDefault();
        trigger.setAttribute('aria-invalid', 'true');
        trigger.focus();
    });
    inquirySelect.addEventListener('focus', () => trigger.focus());
    form?.addEventListener('reset', () => { close(); setTimeout(sync, 0); });
    wrapper.append(trigger, menu);
    inquirySelect.classList.add('business-select__native');
    inquirySelect.tabIndex = -1;
    inquirySelect.setAttribute('aria-hidden', 'true');
    sync();
}

if (form?.closest('.p-business-form-page--contact')) {
    const labels = {
        inquiry_type: 'お問い合わせ内容の種別', company_name: '企業名', department: '部署名',
        postal_code: '郵便番号', address_1: '住所１', address_2: '住所２（建物名）',
        phone: '電話番号', fax: 'FAX番号', contact_name: '担当者名', contact_name_kana: '担当者名（フリガナ）',
        email: 'メールアドレス', email_confirm: '確認用メールアドレス',
        main_products: '主力商品', site_url: 'サイトURL', message: 'お問い合わせ内容', privacy_agree: 'プライバシーポリシーへの同意',
    };
    const controls = [...form.querySelectorAll('input[name], select[name], textarea[name]')]
        .filter(control => control.type !== 'hidden' && labels[control.name] && control !== siteUrl);
    const consent = controls.find(control => control.name === 'privacy_agree');
    if (consent) consent.required = true;
    const validators = new Map();
    const byteLimits = { contact_name: 50, email: 100, message: 1000 };
    for (const control of controls) {
        const error = document.createElement('p');
        error.id = `business-error-${control.name}`;
        error.className = 'business-field-error';
        error.setAttribute('aria-live', 'polite');
        error.hidden = true;
        const privacyRow = control.type === 'checkbox' ? control.closest('.p-business-form__privacy') : null;
        if (privacyRow) {
            const feedback = document.createElement('div');
            feedback.className = 'business-privacy-feedback';
            privacyRow.before(feedback);
            feedback.append(privacyRow, error);
        } else {
            const host = control.closest('.wpcf7-form-control-wrap') || control.parentElement;
            host.append(error);
        }
        const proxy = control === inquirySelect ? control.parentElement.querySelector('.business-select__trigger') : control;
        for (const element of new Set([control, proxy])) {
            if (!element) continue;
            element.setAttribute('aria-describedby', [element.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));
        }
        const validate = (showRequired = false) => {
            control.setCustomValidity('');
            const label = labels[control.name];
            let message = '';
            if (control.required && (control.type === 'checkbox' ? !control.checked : !control.value.trim())) {
                if (showRequired === true) message = control.type === 'checkbox' ? 'プライバシーポリシーへの同意が必要です。' : `${label}を${control.tagName === 'SELECT' ? '選択' : '入力'}してください。`;
            } else if (byteLimits[control.name] && Math.max(businessByteLength(control.value, control.name), businessByteLength(control.value, control.name, true)) > byteLimits[control.name]) {
                const examples = control.name === 'email' ? '半角英数字で100文字まで' : control.name === 'contact_name' ? '漢字・ひらがなだけなら16文字まで' : '漢字・ひらがなだけなら333文字まで。改行も長さに含みます';
                message = `${label}が長すぎます。（${examples}）`;
            } else if (control.value && control.type === 'email' && (control.validity.typeMismatch || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(control.value))) {
                message = validationMessages.email;
            } else if (control.name === 'email_confirm' && control.value && control.value !== form.querySelector('[name="email"]')?.value) {
                message = validationMessages.emailMismatch;
            } else if (control.value && control.type === 'tel' && !isBusinessTelephone(control.value)) {
                message = `${label}の形式を確認してください。半角数字で入力してください。（例：03-1234-5678）`;
            } else if (control.validity.tooLong) {
                message = `${label}は${control.maxLength}文字以内で入力してください。`;
            } else if (control.validity.tooShort) {
                message = `${label}は${control.minLength}文字以上で入力してください。`;
            } else if (control.validity.patternMismatch || control.validity.badInput || control.validity.rangeOverflow || control.validity.rangeUnderflow || control.validity.stepMismatch) {
                message = `${label}の形式・入力範囲を確認してください。`;
            }
            control.setCustomValidity(message);
            control.setAttribute('aria-invalid', String(Boolean(message)));
            proxy?.setAttribute('aria-invalid', String(Boolean(message)));
            error.textContent = message;
            error.hidden = !message;
            return !message;
        };
        validators.set(control, validate);
        control.addEventListener('blur', validate);
        control.addEventListener('input', validate);
        control.addEventListener('change', validate);
        control.addEventListener('invalid', validate);
    }
    form.querySelector('[name="email"]')?.addEventListener('input', () => {
        const confirmation = form.querySelector('[name="email_confirm"]');
        if (confirmation?.value) validators.get(confirmation)?.();
    });
    validateBusinessFields = () => {
        const results = [...validators.values()].map(validate => validate(true));
        return results.every(Boolean);
    };
    form.addEventListener('reset', () => setTimeout(() => {
        for (const control of controls) {
            control.setCustomValidity('');
            control.setAttribute('aria-invalid', 'false');
            const error = document.getElementById(`business-error-${control.name}`);
            if (error) error.hidden = true;
        }
    }, 0));
}
