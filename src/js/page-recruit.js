import '../scss/page-recruit.scss';
import { initRecruitSliders } from './components/recruit-slider';

initRecruitSliders();

const heroNavigation = document.querySelector('[data-recruit-navigation]');
const message = document.querySelector('.p-page-recruit__message');
const companyHeader = document.querySelector('[data-company-header]');
if (heroNavigation && message && companyHeader) {
  const toggle = heroNavigation.querySelector('button');
  const panel = heroNavigation.querySelector('nav');
  const desktopHeader = window.matchMedia('(min-width: 1024px)');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let panelAnimation;
  const setOpen = (open, immediate = false) => {
    if (!immediate && toggle.getAttribute('aria-expanded') === String(open)) return;
    const startTransform = panel.hidden ? 'translateX(100%)' : getComputedStyle(panel).transform;
    panelAnimation?.cancel();
    panelAnimation = null;
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', `採用ページのメニューを${open ? '閉じる' : '開く'}`);
    panel.inert = !open;
    if (immediate || reducedMotion.matches) {
      panel.hidden = !open;
      return;
    }
    panel.hidden = false;
    panelAnimation = panel.animate([
      { transform: startTransform },
      { transform: open ? 'translateX(0)' : 'translateX(100%)' },
    ], { duration: 400, easing: 'cubic-bezier(.4, 0, .2, 1)' });
    panelAnimation.onfinish = () => {
      panel.hidden = !open;
      panelAnimation = null;
    };
  };
  toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
  heroNavigation.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') { setOpen(false); toggle.focus(); }
  });
  panel.addEventListener('click', (event) => { if (event.target.closest('a')) setOpen(false); });
  document.addEventListener('click', (event) => { if (!heroNavigation.contains(event.target)) setOpen(false); });
  heroNavigation.addEventListener('focusout', (event) => {
    if (event.relatedTarget && !heroNavigation.contains(event.relatedTarget)) setOpen(false);
  });
  let wasPastMessage;
  let wasDesktop;
  const syncHeader = () => {
    const isDesktop = desktopHeader.matches;
    const pastMessage = message.getBoundingClientRect().top <= 0;
    if (pastMessage === wasPastMessage && isDesktop === wasDesktop) return;
    const focusInMenu = heroNavigation.contains(document.activeElement);
    setOpen(false, !isDesktop || wasDesktop === undefined || wasDesktop !== isDesktop);
    heroNavigation.hidden = !isDesktop;
    heroNavigation.classList.toggle('is-header-visible', pastMessage);
    heroNavigation.inert = !isDesktop || pastMessage;
    companyHeader.classList.toggle('is-recruit-hero', isDesktop && !pastMessage);
    companyHeader.inert = isDesktop && !pastMessage;
    if (isDesktop || wasDesktop) companyHeader.dispatchEvent(new Event('recruit-header-change'));
    if (pastMessage && focusInMenu) companyHeader.querySelector('a')?.focus({ preventScroll: true });
    wasPastMessage = pastMessage;
    wasDesktop = isDesktop;
  };
  window.addEventListener('scroll', syncHeader, { passive: true });
  window.addEventListener('resize', syncHeader);
  window.addEventListener('pageshow', syncHeader);
  syncHeader();
}

const heroVideo = document.querySelector('.js-recruit-hero-video');
if (heroVideo) {
  const hero = heroVideo.closest('.p-page-recruit__hero');
  const syncHeroHeight = () => {
    hero.closest('main').style.setProperty('--recruit-hero-height', `${hero.getBoundingClientRect().height}px`);
  };
  new ResizeObserver(syncHeroHeight).observe(hero);
  syncHeroHeight();
  const desktop = window.matchMedia('(min-width: 768px)');
  const syncHeroVideo = () => {
    heroVideo.src = desktop.matches ? heroVideo.dataset.srcPc : heroVideo.dataset.srcSp;
    heroVideo.load();
    heroVideo.play().catch(() => {});
  };
  desktop.addEventListener('change', syncHeroVideo);
  syncHeroVideo();
}
