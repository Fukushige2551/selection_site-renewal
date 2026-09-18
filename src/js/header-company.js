/**
 * 企業ページ共通ヘッダーのメニュー開閉
 */
(() => {
    const header = document.querySelector('[data-company-header]');
    if (!header) return;
    const toggle = header.querySelector('.l-company-header__toggle');
    const nav = header.querySelector('.l-company-header__nav');
    const desktop = window.matchMedia('(min-width: 1024px)');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let menuAnimation = null;

    // 表示状態とアクセシビリティ属性を同期し、必要に応じて開閉をアニメーションする。
    const setOpen = (open, immediate = false) => {
        if (!immediate && toggle.getAttribute('aria-expanded') === String(open)) return;
        const currentStyle = getComputedStyle(nav);
        const start = {
            height: `${nav.getBoundingClientRect().height}px`,
            paddingTop: nav.hidden ? '0px' : currentStyle.paddingTop,
            paddingBottom: nav.hidden ? '0px' : currentStyle.paddingBottom,
            opacity: nav.hidden ? 0 : currentStyle.opacity,
        };
        menuAnimation?.cancel();
        menuAnimation = null;
        nav.style.removeProperty('overflow');
        header.classList.toggle('is-open', open && !desktop.matches);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'メニューを閉じる' : 'メニューを開く');
        nav.inert = !desktop.matches && !open;

        if (immediate || desktop.matches || reducedMotion.matches) {
            nav.hidden = !desktop.matches && !open;
            return;
        }

        nav.hidden = false;
        const expandedStyle = getComputedStyle(nav);
        const end = open ? {
            height: `${nav.getBoundingClientRect().height}px`,
            paddingTop: expandedStyle.paddingTop,
            paddingBottom: expandedStyle.paddingBottom,
            opacity: 1,
        } : { height: '0px', paddingTop: '0px', paddingBottom: '0px', opacity: 0 };
        nav.style.overflow = 'hidden';
        menuAnimation = nav.animate([start, end], {
            duration: 450,
            easing: 'cubic-bezier(.4, 0, .2, 1)',
        });
        menuAnimation.onfinish = () => {
            nav.hidden = !desktop.matches && !open;
            nav.inert = !desktop.matches && !open;
            nav.style.removeProperty('overflow');
            menuAnimation = null;
        };
    };

    // 初期状態と操作イベントを設定する。
    toggle.hidden = false;
    setOpen(false, true);
    toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    header.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            toggle.focus();
        }
    });
    document.addEventListener('click', (event) => {
        if (!header.contains(event.target)) setOpen(false);
    });
    nav.addEventListener('click', (event) => {
        if (event.target.closest('a')) setOpen(false);
    });
    header.addEventListener('focusout', (event) => {
        if (event.relatedTarget && !header.contains(event.relatedTarget)) setOpen(false);
    });
    // 画面幅の変更や履歴からの復帰時に表示状態を整える。
    desktop.addEventListener('change', () => setOpen(false, true));
    window.addEventListener('pageshow', () => setOpen(false, true));
})();
