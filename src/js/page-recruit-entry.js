import '../scss/page-recruit-entry.scss';

const form = document.querySelector('.p-recruit-entry__form');

form?.addEventListener('submit', (event) => {
  const fields = [...form.querySelectorAll('[required]')];
  const firstInvalid = fields.find((field) => !field.checkValidity());

  form.classList.add('is-validated');

  if (firstInvalid) {
    event.preventDefault();
    firstInvalid.focus();
    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
});
