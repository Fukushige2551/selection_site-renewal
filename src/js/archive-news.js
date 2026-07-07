import '../scss/archive-news.scss';

document.addEventListener('DOMContentLoaded', () => {
    const cards = Array.from(document.querySelectorAll('.js-news-archive-pickup-card'));
    const dots = Array.from(document.querySelectorAll('.js-news-archive-pickup-dots button'));
    const prevButton = document.querySelector('.js-news-archive-pickup-prev');
    const nextButton = document.querySelector('.js-news-archive-pickup-next');
    let activeIndex = 0;

    const normalizeIndex = (index) => {
        if (!cards.length) {
            return 0;
        }

        if (index < 0) {
            return cards.length - 1;
        }

        if (index >= cards.length) {
            return 0;
        }

        return index;
    };

    const getVisibleCount = () => window.matchMedia('(min-width: 1024px)').matches ? 3 : 1;

    const updateCards = (index) => {
        const visibleCount = Math.min(getVisibleCount(), cards.length || 1);
        const maxStartIndex = Math.max(cards.length - visibleCount, 0);
        activeIndex = Math.min(normalizeIndex(index), maxStartIndex);

        cards.forEach((card, cardIndex) => {
            card.hidden = cardIndex < activeIndex || cardIndex >= activeIndex + visibleCount;
        });

        dots.forEach((dot, dotIndex) => {
            const shouldShowDot = dotIndex <= maxStartIndex;
            const isActive = shouldShowDot && dotIndex === activeIndex;
            dot.hidden = !shouldShowDot;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    window.addEventListener('resize', () => {
        updateCards(activeIndex);
    });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            updateCards(Number(dot.dataset.pickupIndex || 0));
        });
    });

    if (prevButton) {
        prevButton.addEventListener('click', () => {
            updateCards(activeIndex - 1);
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', () => {
            updateCards(activeIndex + 1);
        });
    }

    if (cards.length) {
        updateCards(0);
    }
});