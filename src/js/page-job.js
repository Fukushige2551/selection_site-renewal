import '../scss/page-job.scss';

const keywordForm = document.querySelector('.p-job-search form');
const keywordInput = keywordForm?.querySelector('input[name="keyword"]');

if (keywordForm && keywordInput) {
  keywordForm.addEventListener('submit', (event) => {
    if (keywordInput.value.trim() !== '') {
      return;
    }

    event.preventDefault();
    window.location.assign(keywordForm.action);
  });
}
