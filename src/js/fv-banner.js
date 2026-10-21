import QRCode from 'qrcode';
import '../scss/fv-banner.scss';

export function getAppPlatform(info) {
    const ua = info.userAgent || '';
    if (/iPhone|iPad|iPod/i.test(ua) || (/Macintosh/i.test(ua) && info.maxTouchPoints > 1)) return 'ios';
    if (/Android/i.test(ua)) return 'android';
    return null;
}

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[data-fv-app]');
    if (!link || event.defaultPrevented) return;
    event.preventDefault();
    const platform = getAppPlatform(navigator);
    const url = platform === 'ios' ? link.dataset.iosUrl : platform === 'android' ? link.dataset.androidUrl : '';
    if (url) { window.location.assign(url); return; }
    const dialog = document.getElementById(link.getAttribute('aria-controls'));
    if (!dialog || dialog.open) return;
    dialog.showModal();
    dialog.addEventListener('close', () => link.focus(), { once: true });
});

document.querySelectorAll('.fv-app-dialog').forEach((dialog) => {
    dialog.querySelector('.fv-app-dialog__close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target !== dialog) return;
        const r = dialog.getBoundingClientRect();
        if (event.clientX < r.left || event.clientX > r.right || event.clientY < r.top || event.clientY > r.bottom) dialog.close();
    });
    dialog.querySelectorAll('[data-qr-url]').forEach((canvas) => {
        QRCode.toCanvas(canvas, canvas.dataset.qrUrl, { width: 200, margin: 4, errorCorrectionLevel: 'M' })
            .catch(() => { canvas.hidden = true; });
    });
});
