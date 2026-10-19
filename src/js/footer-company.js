/**
 * 企業ページ共通フッター
 * 各項目は標準の details 要素で開閉する。
 * 初期状態は閉じた状態とし、画面幅の変更時も利用者の選択を維持する。
 */

const companyFooterDetails = document.querySelectorAll('.l-company-footer__nav details');

companyFooterDetails.forEach((detail) => {
    detail.addEventListener('toggle', () => {
        if (!detail.open) return;

        companyFooterDetails.forEach((otherDetail) => {
            if (otherDetail !== detail) otherDetail.open = false;
        });
    });
});
