export const validationMessages = {
    email: 'メールアドレスの形式を確認してください。（例：name@example.com）',
    emailMismatch: 'メールアドレスと確認用メールアドレスが一致していません。',
    consent: 'プライバシーポリシーへの同意が必要です。',
    url: 'https://などを含むURLを入力してください。（例：https://example.com）',
};
export function telephoneIsValid(value) {
    let number = value.replace(/[#*].*$/, '').replace(/[()/.*#\s-]+/g, '');
    if (number.startsWith('+') || number.startsWith('00')) number = '+' + number.replace(/^[+0]+/, '');
    return /^[+]?[0-9]+$/.test(number) && number.length > 5 && number.length < 16;
}
