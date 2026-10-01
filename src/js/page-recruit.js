import '../scss/page-recruit.scss';

const slider = document.querySelector('[data-recruit-slider]');

if (slider) {
  const track = slider.querySelector('.p-page-recruit__voice-track');
  const slides = Array.from(track.children);
  const dots = Array.from(slider.parentElement.querySelectorAll('.p-page-recruit__voice-dots button'));
  const mobileQuery = window.matchMedia('(max-width: 767px)');
  const desktopIndex = Math.min(1, slides.length - 1);
  let index = desktopIndex;
  let mobile = mobileQuery.matches;
  let timer;
  let frame;
  let drag;

  const slideCenter = (slide) => {
    const bounds = slide.getBoundingClientRect();
    return bounds.left + bounds.width / 2;
  };
  const trackCenter = () => track.getBoundingClientRect().left + track.clientLeft + track.clientWidth / 2;
  const nearestIndex = () => {
    const center = trackCenter();
    return slides.reduce((nearest, slide, slideIndex) => (
      Math.abs(slideCenter(slide) - center) < Math.abs(slideCenter(slides[nearest]) - center)
        ? slideIndex : nearest
    ), 0);
  };
  const move = (animate = true) => {
    const target = track.scrollLeft + slideCenter(slides[index]) - trackCenter();
    track.style.scrollBehavior = animate ? 'smooth' : 'auto';
    track.scrollLeft = Math.max(0, Math.min(track.scrollWidth - track.clientWidth, target));
  };
  const update = (activeIndex) => {
    slides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === activeIndex));
    dots.forEach((dot, dotIndex) => {
      const active = dotIndex === activeIndex;
      dot.classList.toggle('is-active', active);
      dot.setAttribute('aria-pressed', String(active));
    });
  };
  const endDrag = (animate = true) => {
    if (!drag) return;
    drag = null;
    track.classList.remove('is-dragging');
    track.style.scrollSnapType = '';
    if (mobileQuery.matches) {
      index = nearestIndex();
      update(index);
      move(animate);
    }
  };
  const syncLayout = () => {
    clearTimeout(timer);
    endDrag(false);
    mobile = mobileQuery.matches;
    update(mobile ? index : desktopIndex);
    if (mobile) {
      move(false);
    } else {
      track.style.scrollBehavior = 'auto';
      track.scrollLeft = 0;
    }
  };
  const scheduleLayout = () => {
    clearTimeout(timer);
    cancelAnimationFrame(frame);
    frame = requestAnimationFrame(syncLayout);
  };

  scheduleLayout();
  window.addEventListener('resize', scheduleLayout);
  window.addEventListener('pageshow', scheduleLayout);
  if (mobileQuery.addEventListener) {
    mobileQuery.addEventListener('change', scheduleLayout);
  } else {
    mobileQuery.addListener(scheduleLayout);
  }

  track.addEventListener('scroll', () => {
    clearTimeout(timer);
    if (drag || !mobile || !mobileQuery.matches) return;
    timer = setTimeout(() => {
      if (!mobileQuery.matches) return;
      index = nearestIndex();
      update(index);
    }, 90);
  }, { passive: true });

  track.addEventListener('mousedown', (event) => {
    if (!mobileQuery.matches || event.button !== 0) return;
    event.preventDefault();
    clearTimeout(timer);
    drag = { startX: event.clientX, scrollLeft: track.scrollLeft };
    track.style.scrollBehavior = 'auto';
    track.style.scrollSnapType = 'none';
    track.classList.add('is-dragging');
  });
  window.addEventListener('mousemove', (event) => {
    if (!drag) return;
    track.scrollLeft = drag.scrollLeft + drag.startX - event.clientX;
  });
  window.addEventListener('mouseup', () => endDrag());
  window.addEventListener('blur', () => endDrag(false));

  const keyboardFocus = (active) => dots.forEach(dot => dot.classList.toggle('is-keyboard-focus', active));
  window.addEventListener('keydown', (event) => {
    if (event.key === 'Tab') keyboardFocus(true);
  });
  dots.forEach((dot) => {
    dot.addEventListener('mousedown', () => keyboardFocus(false));
    dot.addEventListener('touchstart', () => keyboardFocus(false), { passive: true });
  });

  dots.forEach((dot, dotIndex) => dot.addEventListener('click', () => {
    if (!mobileQuery.matches) return;
    index = dotIndex;
    move();
    update(index);
  }));
}
