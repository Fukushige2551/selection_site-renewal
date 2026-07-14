import '../scss/single-recipe.scss';

const setupRelatedRecipesPager = () => {
    const cards = Array.from(document.querySelectorAll('.js-recipe-detail-related-card'));
    const dots = Array.from(document.querySelectorAll('.js-recipe-detail-related-dots button'));
    const pager = document.querySelector('.p-recipe-detail__related-pager');
    const prevButton = document.querySelector('.js-recipe-detail-related-prev');
    const nextButton = document.querySelector('.js-recipe-detail-related-next');

    if (!cards.length || !dots.length) {
        return;
    }

    const tabletQuery = window.matchMedia('(min-width: 768px) and (max-width: 1023.98px)');
    const laptopQuery = window.matchMedia('(min-width: 1024px) and (max-width: 1279px)');
    const desktopQuery = window.matchMedia('(min-width: 1280px)');
    const getPerPage = () => {
        if (tabletQuery.matches) {
            return 12;
        }

        if (laptopQuery.matches) {
            return 12;
        }

        if (desktopQuery.matches) {
            return 12;
        }

        return 3;
    };
    let perPage = getPerPage();
    let pageCount = Math.ceil(cards.length / perPage);
    let currentPage = 0;

    const updateDotsVisibility = () => {
        dots.forEach((dot, index) => {
            dot.hidden = index >= pageCount;
        });
    };

    const render = (page) => {
        pageCount = Math.ceil(cards.length / perPage);
        currentPage = Math.max(0, Math.min(page, pageCount - 1));
        const start = currentPage * perPage;
        const end = start + perPage;

        if (pager) {
            pager.hidden = pageCount <= 1;
        }

        updateDotsVisibility();

        cards.forEach((card, index) => {
            card.hidden = index < start || index >= end;
        });

        dots.forEach((dot, index) => {
            const isActive = index === currentPage;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        if (prevButton) {
            prevButton.classList.toggle('is-disabled', currentPage === 0);
            prevButton.disabled = currentPage === 0;
        }

        if (nextButton) {
            nextButton.classList.toggle('is-disabled', currentPage === pageCount - 1);
            nextButton.disabled = currentPage === pageCount - 1;
        }
    };

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => render(index));
    });

    if (prevButton) {
        prevButton.addEventListener('click', () => render(currentPage - 1));
    }

    if (nextButton) {
        nextButton.addEventListener('click', () => render(currentPage + 1));
    }

    const handleBreakpointChange = () => {
        const nextPerPage = getPerPage();

        if (nextPerPage === perPage) {
            return;
        }

        perPage = nextPerPage;
        render(0);
    };

    [tabletQuery, laptopQuery, desktopQuery].forEach((mediaQuery) => {
        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', handleBreakpointChange);
        } else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(handleBreakpointChange);
        }
    });

    render(0);
};

document.addEventListener('DOMContentLoaded', () => {
    setupRelatedRecipesPager();
});