// Final submit gate; field-specific capture listeners keep their existing order.
export function setupSubmitValidation(form, {
    validate = () => true,
    getFields = (root) => [...root.querySelectorAll('[required]')],
    getInvalidTarget = (firstInvalid) => firstInvalid,
} = {}) {
    form?.addEventListener('submit', (event) => {
        const valid = validate(form);
        const firstInvalid = getFields(form).find((field) => !field.checkValidity());

        form.classList.add('is-validated');

        if (firstInvalid || !valid) {
            event.preventDefault();
            const target = getInvalidTarget(firstInvalid);
            target.focus();
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
}
