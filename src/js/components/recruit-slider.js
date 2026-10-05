export function initRecruitSliders() {
  document.querySelectorAll('[data-recruit-slider]').forEach((slider) => {
    const track = slider.querySelector('.p-page-recruit__voice-track');
    if (!track) return;
    const originals = Array.from(track.children);
    const loop = Boolean(slider.closest('.p-interview__related'));
    const count = originals.length;
    if (loop && count) {
      const clone = (slide) => {
        const copy = slide.cloneNode(true);
        copy.dataset.loopClone = 'true';
        return copy;
      };
      track.prepend(...originals.map(clone));
      track.append(...originals.map(clone));
    }
    const slides = Array.from(track.children);
    if (!slides.length) return;
    const dots = Array.from(slider.parentElement.querySelectorAll('.p-page-recruit__voice-dots button'));
    const mobileQuery = window.matchMedia(loop ? '(max-width: 1023px)' : '(max-width: 767px)');
    const offset = loop ? count : 0;
    const desktopCard = originals.findIndex(card => card.dataset.interviewNumber === '03');
    const desktopIndex = offset + (desktopCard >= 0 ? desktopCard : Math.min(1, count - 1));
    let index = offset + Math.max(0, Math.min(count - 1, Number(slider.dataset.initialIndex ?? 1)));
    let suppressClick = false;
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
        const active = dot.dataset.interviewNumber
          ? dot.dataset.interviewNumber === slides[activeIndex]?.dataset.interviewNumber
          : Number(dot.dataset.slideIndex ?? dotIndex) === activeIndex % count;
        dot.classList.toggle('is-active', active);
        dot.setAttribute('aria-pressed', String(active));
      });
    };
    const settle = () => {
      if (!mobileQuery.matches || drag) return;
      index = nearestIndex();
      if (loop && (index < count || index >= count * 2)) {
        // Rebase equivalent cards atomically; this is not a new slide selection.
        track.classList.add('is-loop-rebasing');
        index = count + index % count;
        update(index);
        move(false);
        // Commit the final heights before restoring ordinary slide transitions.
        void track.offsetHeight;
        track.classList.remove('is-loop-rebasing');
        return;
      }
      update(index);
    };
    const endDrag = (animate = true) => {
      if (!drag) return;
      suppressClick = drag.moved;
      drag = null;
      track.classList.remove('is-dragging');
      track.style.scrollSnapType = '';
      if (mobileQuery.matches) {
        settle();
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
        settle();
      }, 90);
    }, { passive: true });
  
    track.addEventListener('mousedown', (event) => {
      if (!mobileQuery.matches || event.button !== 0) return;
      event.preventDefault();
      clearTimeout(timer);
      suppressClick = false;
      drag = { startX: event.clientX, scrollLeft: track.scrollLeft, moved: false };
      track.style.scrollBehavior = 'auto';
      track.style.scrollSnapType = 'none';
      track.classList.add('is-dragging');
    });
    window.addEventListener('mousemove', (event) => {
      if (!drag) return;
      drag.moved ||= Math.abs(drag.startX - event.clientX) > 6;
      track.scrollLeft = drag.scrollLeft + drag.startX - event.clientX;
    });
    window.addEventListener('mouseup', () => endDrag());
    track.addEventListener('click', (event) => {
      if (suppressClick) { event.preventDefault(); event.stopPropagation(); suppressClick = false; return; }
      if (!loop || !mobileQuery.matches) return;
      const card = event.target.closest('.p-page-recruit__voice-card');
      const targetIndex = slides.indexOf(card);
      if (targetIndex < 0) return;
      // Only a card physically at the center can navigate, including during animation.
      if (targetIndex !== index || Math.abs(slideCenter(card) - trackCenter()) > 2) {
        event.preventDefault();
        event.stopPropagation();
        index = targetIndex;
        update(index);
        move();
      }
    }, true);
    track.addEventListener('keydown', () => { suppressClick = false; });
    track.addEventListener('touchstart', () => { suppressClick = false; }, { passive: true });
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
      const target = dot.dataset.interviewNumber
        ? originals.findIndex(card => card.dataset.interviewNumber === dot.dataset.interviewNumber)
        : Number(dot.dataset.slideIndex ?? dotIndex);
      if (target < 0) return;
      index = offset + target;
      move();
      update(index);
    }));
  });
}
