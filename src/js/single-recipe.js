import '../scss/single-recipe.scss';

const setupRelatedRecipesPager = () => {
    const cards = Array.from(document.querySelectorAll('.js-recipe-detail-related-card'));
    const dots = Array.from(document.querySelectorAll('.js-recipe-detail-related-dots button'));
    const prevButton = document.querySelector('.js-recipe-detail-related-prev');
    const nextButton = document.querySelector('.js-recipe-detail-related-next');
    const perPage = 3;

    if (!cards.length || !dots.length) {
        return;
    }

    const pageCount = Math.ceil(cards.length / perPage);
    let currentPage = 0;

    const render = (page) => {
        currentPage = Math.max(0, Math.min(page, pageCount - 1));
        const start = currentPage * perPage;
        const end = start + perPage;

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

    render(0);
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('foodsRecipeDetailItem', window.foodsRecipeDetailItem || null);
    setupRelatedRecipesPager();
});