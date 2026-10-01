import '../scss/page-recruit-work.scss';

document.querySelectorAll('[data-job-modal]').forEach((modal) => {
  const modalName = modal.dataset.jobModal;
  const triggers = document.querySelectorAll(`[data-job-modal-open="${modalName}"]`);
  const dialog = modal.querySelector('[role="dialog"]');
  const closeButtons = modal.querySelectorAll('[data-job-modal-close]');
  const focusableElements = modal.querySelectorAll('button:not([disabled])');
  let previouslyFocused = null;

  const closeModal = () => {
    modal.hidden = true;
    document.body.classList.remove('is-job-modal-open');
    document.removeEventListener('keydown', handleKeydown);
    previouslyFocused?.focus();
  };

  const handleKeydown = (event) => {
    if (event.key === 'Escape') {
      closeModal();
      return;
    }

    if (event.key !== 'Tab' || focusableElements.length === 0) return;

    const first = focusableElements[0];
    const last = focusableElements[focusableElements.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  };

  const openModal = () => {
    previouslyFocused = document.activeElement;
    modal.hidden = false;
    document.body.classList.add('is-job-modal-open');
    document.addEventListener('keydown', handleKeydown);
    dialog?.focus({ preventScroll: true });
  };

  triggers.forEach((trigger) => trigger.addEventListener('click', openModal));
  closeButtons.forEach((button) => button.addEventListener('click', closeModal));
});
