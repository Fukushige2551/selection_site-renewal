export function createFieldError(id, className) {
    const error = document.createElement('p');
    error.id = id;
    error.className = className;
    error.hidden = true;
    error.setAttribute('aria-live', 'polite');
    return error;
}

export function describeFieldError(element, error) {
    element.setAttribute('aria-describedby', [element.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));
}

export function renderFieldError(error, message) {
    error.textContent = message;
    error.hidden = !message;
}
