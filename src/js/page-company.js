import './main.js';
import '../scss/page-company.scss';

/**
 * ヒーロースライダーの自動切り替え
 */
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.querySelector('.js-company-hero-slider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.p-page-company__hero__slide');
    if (slides.length <= 1) return;

    let currentIndex = 0;

    setInterval(() => {
        slides[currentIndex].classList.remove('is-active');

        currentIndex = (currentIndex + 1) % slides.length;

        slides[currentIndex].classList.add('is-active');
    }, 4000);
});

/**
 * 写真のループ表示とドラッグ操作
 */
const aboutSlider = document.querySelector('.js-about-slider');
const aboutTrack = document.querySelector('.js-about-track');

if (aboutSlider && aboutTrack) {
    const originals = [...aboutTrack.children];
    // 写真が奇数枚の場合は二巡分を一周期にし、上下の配置を連続させる。
    const cycleCount = originals.length % 2 ? originals.length * 2 : originals.length;
    for (let i = originals.length; i < cycleCount * 3; i++) {
        const clone = originals[i % originals.length].cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        aboutTrack.appendChild(clone);
    }
    aboutTrack.querySelectorAll('img').forEach((img) => {
        img.draggable = false;
    });

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const mobile = window.matchMedia('(max-width: 767px)');
    let cycleWidth = 0;
    let position = 0;
    let pointerId = null;
    let lastX = 0;
    let lastTime = 0;

    const renderPosition = () => {
        if (!cycleWidth) return;
        // 前後に一周期ずつ余裕を残し、中央の複製範囲内で位置を循環させる。
        position = cycleWidth + ((position - cycleWidth) % cycleWidth + cycleWidth) % cycleWidth;
        aboutSlider.scrollLeft = position;
    };

    // 画面幅の変更後も、周期内の相対位置を維持する。
    const measure = () => {
        const phase = cycleWidth ? (position - cycleWidth) / cycleWidth : 0;
        const items = aboutTrack.children;
        cycleWidth = items[cycleCount].getBoundingClientRect().left - items[0].getBoundingClientRect().left;
        position = cycleWidth * (1 + phase);
        renderPosition();
    };

    const stopDrag = () => {
        const id = pointerId;
        pointerId = null;
        aboutSlider.classList.remove('is-dragging');
        if (id !== null && aboutSlider.hasPointerCapture(id)) {
            aboutSlider.releasePointerCapture(id);
        }
    };

    // ドラッグ中は自動移動を止め、指やマウスの移動量を反映する。
    aboutSlider.addEventListener('pointerdown', (event) => {
        if (!event.isPrimary || event.button !== 0 || pointerId !== null) return;
        pointerId = event.pointerId;
        lastX = event.clientX;
        aboutSlider.classList.add('is-dragging');
        aboutSlider.setPointerCapture(pointerId);
    });
    aboutSlider.addEventListener('pointermove', (event) => {
        if (event.pointerId !== pointerId) return;
        position -= event.clientX - lastX;
        lastX = event.clientX;
        renderPosition();
    });
    for (const type of ['pointerup', 'pointercancel', 'lostpointercapture']) {
        aboutSlider.addEventListener(type, (event) => {
            if (event.pointerId === pointerId) stopDrag();
        });
    }
    aboutSlider.addEventListener('dragstart', (event) => event.preventDefault());

    const tick = (time) => {
        const elapsed = lastTime ? Math.min(time - lastTime, 50) : 0;
        lastTime = time;
        // パソコンの配置は維持し、スマートフォンのみ自動で移動する。
        if (pointerId === null && mobile.matches && !reducedMotion.matches && !document.hidden) {
            position += elapsed * 0.018;
            renderPosition();
        }
        requestAnimationFrame(tick);
    };

    window.addEventListener('resize', () => {
        stopDrag();
        measure();
    });
    new ResizeObserver(measure).observe(aboutSlider);
    measure();
    requestAnimationFrame(tick);
}
