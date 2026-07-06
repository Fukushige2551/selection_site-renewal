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

    const updateCards = (index) => {
        activeIndex = normalizeIndex(index);

        cards.forEach((card, cardIndex) => {
            card.hidden = cardIndex !== activeIndex;
        });

        dots.forEach((dot, dotIndex) => {
            const isActive = dotIndex === activeIndex;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

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