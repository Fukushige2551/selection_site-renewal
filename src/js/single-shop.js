import '../scss/single-shop.scss';

document.addEventListener('DOMContentLoaded', () => {
    const dataElement = document.getElementById('js-single-shop-data');
    const output = document.querySelector('.js-single-shop-debug-output');
    const dealCards = Array.from(document.querySelectorAll('.js-single-shop-deal-card'));
    const dealDots = Array.from(document.querySelectorAll('.js-single-shop-deal-dots button'));
    const dealPrevButton = document.querySelector('.js-single-shop-deal-prev');
    const dealNextButton = document.querySelector('.js-single-shop-deal-next');
    const newsCards = Array.from(document.querySelectorAll('.js-single-shop-news-card'));
    const newsDots = Array.from(document.querySelectorAll('.js-single-shop-news-dots button'));
    const newsPrevButton = document.querySelector('.js-single-shop-news-prev');
    const newsNextButton = document.querySelector('.js-single-shop-news-next');
    const recruitCards = Array.from(document.querySelectorAll('.js-single-shop-recruit-card'));
    const recruitDots = Array.from(document.querySelectorAll('.js-single-shop-recruit-dots button'));
    const nearbyCards = Array.from(document.querySelectorAll('.js-single-shop-nearby-card'));
    const nearbyDots = Array.from(document.querySelectorAll('.js-single-shop-nearby-dots button'));
    let activeDealIndex = 0;
    let activeNewsIndex = 0;

    const getDealVisibleCount = () => {
        return window.matchMedia('(min-width: 1280px)').matches ? 3 : 2;
    };

    const updateDealCards = (activeIndex) => {
        activeDealIndex = normalizeDealIndex(activeIndex);
        const visibleCount = Math.min(getDealVisibleCount(), dealCards.length);
        const visibleIndexes = Array.from({ length: visibleCount }, (_, offset) => normalizeDealIndex(activeDealIndex + offset));

        dealCards.forEach((card, index) => {
            const visibleOrder = visibleIndexes.indexOf(index);
            const isPrimaryVisibleCard = visibleOrder === 0;

            card.hidden = visibleOrder === -1;
            card.style.order = visibleOrder === -1 ? '' : String(visibleOrder);
            card.classList.toggle('is-good-deal', isPrimaryVisibleCard);
        });

        dealDots.forEach((dot, index) => {
            const isActive = index === activeDealIndex;

            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    const normalizeDealIndex = (index) => {
        if (!dealCards.length) {
            return 0;
        }

        if (index < 0) {
            return dealCards.length - 1;
        }

        if (index >= dealCards.length) {
            return 0;
        }

        return index;
    };

    dealDots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const activeIndex = Number(dot.dataset.dealIndex || 0);
            updateDealCards(activeIndex);
        });
    });

    if (dealPrevButton) {
        dealPrevButton.addEventListener('click', () => {
            updateDealCards(activeDealIndex - 1);
        });
    }

    if (dealNextButton) {
        dealNextButton.addEventListener('click', () => {
            updateDealCards(activeDealIndex + 1);
        });
    }

    if (dealCards.length) {
        updateDealCards(0);
        window.addEventListener('resize', () => updateDealCards(activeDealIndex));
    }

    const normalizeNewsIndex = (index) => {
        if (!newsCards.length) {
            return 0;
        }

        if (index < 0) {
            return newsCards.length - 1;
        }

        if (index >= newsCards.length) {
            return 0;
        }

        return index;
    };

    const getNewsVisibleCount = () => {
        if (window.matchMedia('(min-width: 1280px)').matches) {
            return 3;
        }

        return window.matchMedia('(min-width: 1024px) and (max-width: 1279px)').matches ? 2 : 1;
    };

    const updateNewsCards = (activeIndex) => {
        activeNewsIndex = normalizeNewsIndex(activeIndex);
        const visibleCount = Math.min(getNewsVisibleCount(), newsCards.length);
        const visibleIndexes = Array.from({ length: visibleCount }, (_, offset) => normalizeNewsIndex(activeNewsIndex + offset));

        newsCards.forEach((card, index) => {
            const visibleOrder = visibleIndexes.indexOf(index);

            card.hidden = visibleOrder === -1;
            card.style.order = visibleOrder === -1 ? '' : String(visibleOrder);
        });

        newsDots.forEach((dot, index) => {
            const isActive = index === activeNewsIndex;

            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    newsDots.forEach((dot) => {
        dot.addEventListener('click', () => {
            updateNewsCards(Number(dot.dataset.newsIndex || 0));
        });
    });

    if (newsPrevButton) {
        newsPrevButton.addEventListener('click', () => {
            updateNewsCards(activeNewsIndex - 1);
        });
    }

    if (newsNextButton) {
        newsNextButton.addEventListener('click', () => {
            updateNewsCards(activeNewsIndex + 1);
        });
    }

    if (newsCards.length) {
        updateNewsCards(0);
        window.addEventListener('resize', () => updateNewsCards(activeNewsIndex));
    }
    const nearbyCardsPerPage = 3;

    const updateNearbyCards = (activePage) => {
        const startIndex = activePage * nearbyCardsPerPage;
        const endIndex = startIndex + nearbyCardsPerPage;

        nearbyCards.forEach((card, index) => {
            card.hidden = index < startIndex || index >= endIndex;
        });

        nearbyDots.forEach((dot, index) => {
            const isActive = index === activePage;

            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    nearbyDots.forEach((dot) => {
        dot.addEventListener('click', () => {
            updateNearbyCards(Number(dot.dataset.nearbyPage || 0));
        });
    });

    if (nearbyCards.length > nearbyCardsPerPage) {
        updateNearbyCards(0);
    }

    let activeRecruitPage = 0;

    const getRecruitCardsPerPage = () => {
        return window.matchMedia('(min-width: 1280px)').matches ? 3 : 2;
    };

    const updateRecruitCards = (activePage) => {
        const recruitCardsPerPage = getRecruitCardsPerPage();
        const pageCount = Math.ceil(recruitCards.length / recruitCardsPerPage);
        activeRecruitPage = Math.min(Math.max(activePage, 0), Math.max(pageCount - 1, 0));
        const startIndex = activeRecruitPage * recruitCardsPerPage;
        const endIndex = startIndex + recruitCardsPerPage;

        recruitCards.forEach((card, index) => {
            card.hidden = index < startIndex || index >= endIndex;
        });

        recruitDots.forEach((dot, index) => {
            const isActive = index === activeRecruitPage;
            const isAvailable = index < pageCount;

            dot.hidden = !isAvailable;
            dot.classList.toggle('is-active', isActive && isAvailable);
            dot.setAttribute('aria-pressed', isActive && isAvailable ? 'true' : 'false');
        });
    };

    recruitDots.forEach((dot) => {
        dot.addEventListener('click', () => {
            updateRecruitCards(Number(dot.dataset.recruitPage || 0));
        });
    });

    if (recruitCards.length > 0) {
        updateRecruitCards(0);
        window.addEventListener('resize', () => updateRecruitCards(activeRecruitPage));
    }

    if (!dataElement) {
        return;
    }

    let singleShopData = null;

    try {
        singleShopData = JSON.parse(dataElement.textContent || '{}');
    } catch (error) {
        console.error('single-shop data parse error:', error);
        return;
    }

    window.foodsSingleShopData = singleShopData;
    console.log('foodsSingleShopData', singleShopData);

    if (output) {
        output.textContent = JSON.stringify(singleShopData, null, 2);
    }
});
