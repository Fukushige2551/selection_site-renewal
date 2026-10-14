import '../scss/application-validation.scss';
import { formatBirthDate, birthDateBackspaceRange, insertBirthDateDigits } from './birth-date-format.js';

const kanaNames = ['name_kana', 'contact_name_kana', 'your_name_kana'];
const kanaMessage = 'カナはカタカナと空白で入力してください。';
function setup() {
    for (const form of document.querySelectorAll('.foods-cf7 form')) {
        for (const field of form.querySelectorAll('input')) {
            if (!kanaNames.includes(field.name) && field.name !== 'birth_date') continue;
            const isKana = kanaNames.includes(field.name);
            if (!isKana) { field.type = 'text'; field.placeholder = '2000/01/01'; field.inputMode = 'numeric'; }
            const normalizeDate = () => { if (!isKana) field.value = formatBirthDate(field.value); };
            const error = document.createElement('p');
            error.className = 'application-field-error';
            error.id = `format-error-${field.name}`;
            error.hidden = true;
            error.setAttribute('aria-live', 'polite');
            (field.closest('.wpcf7-form-control-wrap') || field.parentElement).append(error);
            field.setAttribute('aria-describedby', [field.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));
            const validate = () => {
                let message = '';
                if (isKana && field.value && !/^[ァ-ヺー・ \u3000ｦ-ﾟ]+$/u.test(field.value)) message = kanaMessage;
                error.textContent = message;
                error.hidden = !message;
                field.setCustomValidity(message);
                field.setAttribute('aria-invalid', String(Boolean(message)));
                return !message;
            };
            field.addEventListener('beforeinput', event => {
                if (isKana) return;
                if (event.inputType === 'deleteContentBackward') {
                    const range = birthDateBackspaceRange(field.value, field.selectionStart, field.selectionEnd);
                    if (range) { event.preventDefault(); field.setRangeText('', range[0], range[1], 'end'); validate(); }
                    return;
                }
                if (event.inputType?.startsWith('insert') && event.data !== null) {
                    event.preventDefault();
                    const next = insertBirthDateDigits(field.value, field.selectionStart ?? field.value.length, event.data);
                    if (next !== null) { field.value = next; field.setSelectionRange(next.length, next.length); validate(); }
                }
            });
            field.addEventListener('paste', event => {
                if (isKana) return;
                event.preventDefault();
                const next = insertBirthDateDigits(field.value, field.selectionStart ?? field.value.length, event.clipboardData?.getData('text') || '');
                if (next !== null) { field.value = next; field.setSelectionRange(next.length, next.length); validate(); }
            });
            field.addEventListener('input', event => { if (!event.isComposing) { if (!isKana) { field.value = field.value.replace(/[^0-9/]/g, ''); if (!event.inputType?.startsWith('delete')) field.value = formatBirthDate(field.value, true); } validate(); } });
            field.addEventListener('compositionend', () => {
                if (!isKana) field.value = formatBirthDate(field.value.replace(/[^0-9/]/g, ''), true);
                else normalizeDate();
                validate();
            });
            field.addEventListener('blur', () => {
                if (isKana && /^[ァ-ヺー・ \u3000ｦ-ﾟ]+$/u.test(field.value)) field.value = field.value.normalize('NFKC');
                normalizeDate();
                validate();
            });
            field.addEventListener('change', () => { normalizeDate(); validate(); });
            form.addEventListener('submit', event => {
                if (isKana && /^[ァ-ヺー・ \u3000ｦ-ﾟ]+$/u.test(field.value)) field.value = field.value.normalize('NFKC');
                normalizeDate();
                if (!validate()) { event.preventDefault(); field.focus(); }
            }, true);
        }
    }
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setup);
else setup();
