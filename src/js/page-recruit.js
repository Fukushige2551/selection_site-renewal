import '../scss/page-recruit.scss';
import { initRecruitSliders } from './components/recruit-slider';

initRecruitSliders();

const heroVideo = document.querySelector('.js-recruit-hero-video');
if (heroVideo) {
  const desktop = window.matchMedia('(min-width: 768px)');
  const syncHeroVideo = () => {
    heroVideo.src = desktop.matches ? heroVideo.dataset.srcPc : heroVideo.dataset.srcSp;
    heroVideo.load();
    heroVideo.play().catch(() => {});
  };
  desktop.addEventListener('change', syncHeroVideo);
  syncHeroVideo();
}
