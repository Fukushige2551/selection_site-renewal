import '../scss/page-job-search.scss';

const filterToggle = document.querySelector('.js-job-search-filter-toggle');
const filterPanel = document.querySelector('.js-job-search-filter');
const resultsPanel = document.querySelector('.js-job-search-results');

if (filterToggle && filterPanel) {
  filterToggle.addEventListener('click', () => {
    const isOpen = filterToggle.getAttribute('aria-expanded') === 'true';
    filterToggle.setAttribute('aria-expanded', String(!isOpen));
    filterPanel.hidden = isOpen;
    filterPanel.setAttribute('aria-hidden', String(isOpen));
    if (resultsPanel) {
      resultsPanel.hidden = !isOpen;
    }

    if (!isOpen) {
      filterPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
}
