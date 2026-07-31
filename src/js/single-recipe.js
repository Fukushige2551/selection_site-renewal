import '../scss/single-recipe.scss';

const setupRelatedRecipesPager = () => {
    const cards = Array.from(document.querySelectorAll('.js-recipe-detail-related-card'));
    const dots = Array.from(document.querySelectorAll('.js-recipe-detail-related-dots button'));
    const pager = document.querySelector('.p-recipe-detail__related-pager');
    const prevButton = document.querySelector('.js-recipe-detail-related-prev');
    const nextButton = document.querySelector('.js-recipe-detail-related-next');
    const relatedSection = document.getElementById('recipe-detail-related-title')?.closest('.p-recipe-archive__list');

    if (!cards.length || !dots.length) {
        return;
    }

    const getPseudoElementHeight = (element, pseudoElement) => {
        const styles = window.getComputedStyle(element, pseudoElement);
        const display = styles.getPropertyValue('display');
        const height = parseFloat(styles.getPropertyValue('height'));

        if (display === 'none' || Number.isNaN(height)) {
            return 0;
        }

        return height;
    };

    const getVisibleHeaderHeight = () => {
        const headers = document.querySelectorAll('.l-header-pc, .l-header');

        for (const header of headers) {
            const rect = header.getBoundingClientRect();

            if (rect.height > 0) {
                return rect.height + getPseudoElementHeight(header, '::after');
            }
        }

        return 0;
    };

    const scrollToRelatedSection = () => {
        if (!relatedSection) {
            return;
        }

        const headerHeight = getVisibleHeaderHeight();
        const breathingRoom = 16;
        const targetTop = relatedSection.getBoundingClientRect().top + window.pageYOffset - headerHeight - breathingRoom;
        const supportsSmooth = 'scrollBehavior' in document.documentElement.style;

        window.scrollTo({
            top: Math.max(0, targetTop),
            behavior: supportsSmooth ? 'smooth' : 'auto',
        });
    };

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

    const render = (page, shouldScroll = false) => {
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

        if (shouldScroll) {
            window.requestAnimationFrame(scrollToRelatedSection);
        }
    };

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => render(index, true));
    });

    if (prevButton) {
        prevButton.addEventListener('click', () => render(currentPage - 1, true));
    }

    if (nextButton) {
        nextButton.addEventListener('click', () => render(currentPage + 1, true));
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