import '../scss/page-shop.scss';

document.addEventListener('DOMContentLoaded', () => {
    const pageShop = document.querySelector('.p-page-shop');

    if (!pageShop) {
        return;
    }

    const anchorLinks = pageShop.querySelectorAll('a[href^="#prefecture-"]');

    function getPseudoElementHeight(element, pseudoElement) {
        const styles = window.getComputedStyle(element, pseudoElement);
        const height = parseFloat(styles.height);

        return Number.isFinite(height) ? height : 0;
    }

    function getVisibleHeaderHeight() {
        const headers = document.querySelectorAll('.l-header-pc, .l-header');

        for (const header of headers) {
            const rect = header.getBoundingClientRect();

            if (rect.height > 0) {
                return rect.height + getPseudoElementHeight(header, '::after');
            }
        }

        return 0;
    }

    anchorLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            const targetId = link.getAttribute('href');
            const target = targetId ? document.getElementById(targetId.slice(1)) : null;

            if (!target) {
                return;
            }

            event.preventDefault();

            const headerHeight = getVisibleHeaderHeight();
            const targetTop = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
            const supportsSmooth = 'scrollBehavior' in document.documentElement.style;

            window.scrollTo({
                top: targetTop,
                behavior: supportsSmooth ? 'smooth' : 'auto',
            });

            history.pushState(null, '', targetId);
        });
    });
});
