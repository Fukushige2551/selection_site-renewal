import '../scss/page-recruit.scss';

const slider = document.querySelector('[data-recruit-slider]');

if (slider && window.matchMedia('(max-width: 767px)').matches) {
  const track = slider.querySelector('.p-page-recruit__voice-track');
  const slides = Array.from(track.children);
  const dots = Array.from(document.querySelectorAll('.p-page-recruit__voice-dots > *'));
  let index = 1;
  let timer;

  const slideWidth = () => {
    const gap = parseFloat(getComputedStyle(track).gap) || 0;
    return slides[0].getBoundingClientRect().width + gap;
  };
  const move = (animate = true) => {
    track.style.scrollBehavior = animate ? 'smooth' : 'auto';
    track.scrollLeft = index * slideWidth();
  };
  const update = () => {
    slides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === index));
    dots.forEach((dot, dotIndex) => dot.classList.toggle('is-active', dotIndex === index));
  };

  requestAnimationFrame(() => { move(false); update(); });
  track.addEventListener('scroll', () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      index = Math.max(0, Math.min(slides.length - 1, Math.round(track.scrollLeft / slideWidth())));
      update();
    }, 90);
  }, { passive: true });

  dots.forEach((dot, dotIndex) => dot.addEventListener('click', () => {
    index = dotIndex;
    move();
    update();
  }));
}
