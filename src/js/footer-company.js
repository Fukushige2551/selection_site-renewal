/**
 * 企業ページ共通フッター
 * details の高さをアニメーションし、閉じる間も本文を表示する。
 * 初期状態は閉じた状態とし、画面幅の変更時も利用者の選択を維持する。
 */

const companyFooterDetails = document.querySelectorAll('.l-company-footer__nav details');
const footerAnimations = new WeakMap();
const footerExpanded = new WeakMap();

function animateFooterDetail(detail, expanded) {
    const startHeight = detail.getBoundingClientRect().height;
    footerAnimations.get(detail)?.cancel();
    footerExpanded.set(detail, expanded);
    detail.classList.toggle('is-expanded', expanded);

    // Measure the natural target height, then keep the content visible until closing ends.
    detail.open = expanded;
    const endHeight = detail.getBoundingClientRect().height;
    detail.open = true;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        detail.open = expanded;
        detail.style.overflow = '';
        footerAnimations.delete(detail);
        return;
    }

    detail.style.overflow = 'hidden';
    const animation = detail.animate([
        { height: `${startHeight}px` },
        { height: `${endHeight}px` },
    ], { duration: 350, easing: 'ease' });
    footerAnimations.set(detail, animation);
    animation.onfinish = () => {
        detail.open = expanded;
        detail.style.overflow = '';
        footerAnimations.delete(detail);
    };
}

companyFooterDetails.forEach((detail) => {
    footerExpanded.set(detail, detail.open);
    detail.classList.toggle('is-expanded', detail.open);
    detail.querySelector('summary').addEventListener('click', (event) => {
        if (event.target.closest('a')) return;
        event.preventDefault();
        const expanded = !footerExpanded.get(detail);

        if (expanded) {
            companyFooterDetails.forEach((otherDetail) => {
                if (otherDetail !== detail && footerExpanded.get(otherDetail)) {
                    animateFooterDetail(otherDetail, false);
                }
            });
        }
        animateFooterDetail(detail, expanded);
    });
});
