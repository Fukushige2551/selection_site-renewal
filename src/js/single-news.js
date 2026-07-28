import '../scss/single-news.scss';

document.addEventListener('DOMContentLoaded', () => {
    const relatedCards = Array.from(document.querySelectorAll('.js-news-detail-related-card'));
    const relatedDots = Array.from(document.querySelectorAll('.js-news-detail-related-dots button'));
    const relatedPrev = document.querySelector('.js-news-detail-related-prev');
    const relatedNext = document.querySelector('.js-news-detail-related-next');
    const relatedPager = document.querySelector('.p-news-detail__related-pager');
    const tabletMediaQuery = window.matchMedia('(min-width: 768px) and (max-width: 1023px)');
    const notebookMediaQuery = window.matchMedia('(min-width: 1024px) and (max-width: 1279px)');
    const getVisibleCount = () => {
        if (tabletMediaQuery.matches) {
            return 5;
        }
        if (notebookMediaQuery.matches) {
            return 3;
        }
        return window.innerWidth >= 1280 ? 6 : 2;
    };
    let visibleCount = getVisibleCount();
    let activePage = 0;

    if (!relatedCards.length || !relatedDots.length) {
        return;
    }

    const getMaxPage = () => Math.ceil(relatedCards.length / visibleCount) - 1;

    const normalizePage = (page) => {
        const maxPage = getMaxPage();

        if (page < 0) {
            return maxPage;
        }

        if (page > maxPage) {
            return 0;
        }

        return page;
    };

    const updateRelated = (page) => {
        const maxPage = getMaxPage();
        if (relatedPager) {
            relatedPager.hidden = maxPage < 1;
        }
        activePage = normalizePage(page);
        const startIndex = activePage * visibleCount;
        const endIndex = startIndex + visibleCount;

        relatedCards.forEach((card, cardIndex) => {
            card.hidden = cardIndex < startIndex || cardIndex >= endIndex;
        });

        relatedDots.forEach((dot, dotIndex) => {
            const isAvailable = dotIndex <= maxPage;
            const isActive = dotIndex === activePage;
            dot.hidden = !isAvailable;
            dot.classList.toggle('is-active', isAvailable && isActive);
            dot.setAttribute('aria-pressed', isAvailable && isActive ? 'true' : 'false');
        });
    };

    relatedDots.forEach((dot) => {
        dot.addEventListener('click', () => {
            updateRelated(Number(dot.dataset.relatedPage || 0));
        });
    });

    if (relatedPrev) {
        relatedPrev.addEventListener('click', () => {
            updateRelated(activePage - 1);
        });
    }

    if (relatedNext) {
        relatedNext.addEventListener('click', () => {
            updateRelated(activePage + 1);
        });
    }

    const handleViewportChange = () => {
        visibleCount = getVisibleCount();
        updateRelated(activePage);
    };

    if (typeof tabletMediaQuery.addEventListener === 'function') {
        tabletMediaQuery.addEventListener('change', handleViewportChange);
        notebookMediaQuery.addEventListener('change', handleViewportChange);
    } else if (typeof tabletMediaQuery.addListener === 'function') {
        tabletMediaQuery.addListener(handleViewportChange);
        notebookMediaQuery.addListener(handleViewportChange);
    }

    updateRelated(0);
});