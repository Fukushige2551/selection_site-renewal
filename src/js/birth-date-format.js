export function formatBirthDate(value, progressive = false) {
    const normalized = value.replace(/[０-９]/g, c => String.fromCharCode(c.charCodeAt(0) - 0xfee0)).replace(/／/g, '/');
    if (progressive && /^[0-9/-]*$/.test(normalized)) {
        const digits = normalized.replace(/[^0-9]/g, '');
        if (digits.length < 4) return digits;
        const year = digits.slice(0, 4);
        let month = '';
        let day = '';
        for (const digit of digits.slice(4)) {
            if (month.length < 2) {
                if (!month) month = Number(digit) > 1 ? '0' + digit : digit;
                else if (Number(month + digit) >= 1 && Number(month + digit) <= 12) month += digit;
                else if (month === '1') { month = '01'; day = Number(digit) > 3 ? '0' + digit : digit; }
                continue;
            }
            const y = Number(year);
            const leap = y % 4 === 0 && (y % 100 !== 0 || y % 400 === 0);
            const maxDay = [31, leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][Number(month) - 1];
            if (!day) day = Number(digit) > 3 ? '0' + digit : digit;
            else if (day.length === 1 && Number(day + digit) >= 1 && Number(day + digit) <= maxDay) day += digit;
        }
        return year + '/' + month + (month.length === 2 ? '/' + day : '');
    }
    if (/^\d{8}$/.test(normalized)) return `${normalized.slice(0, 4)}/${normalized.slice(4, 6)}/${normalized.slice(6)}`;
    if (/^\d{4,7}$/.test(normalized)) {
        const year = normalized.slice(0, 4);
        const month = normalized.slice(4, 6);
        const day = normalized.slice(6);
        return `${year}/${month}${month.length === 2 ? '/' + day : ''}`;
    }
    if (/^\d{4}\/\d{2}$/.test(normalized)) return normalized + '/';
    const parts = /^(\d{4})[/-](\d{1,2})[/-](\d{1,2})$/.exec(normalized);
    if (progressive && parts && parts[3].length < 2) return normalized;
    return parts ? `${parts[1]}/${parts[2].padStart(2, '0')}/${parts[3].padStart(2, '0')}` : normalized;
}
export function birthDateBackspaceRange(value, start, end) {
    return start === end && start >= 2 && value[start - 1] === '/' ? [start - 2, start] : null;
}
export function insertBirthDateDigits(value, position, digits) {
    if (!/^[0-9]+$/.test(digits)) return null;
    return formatBirthDate(value.slice(0, position) + digits, true);
}
export function birthDateIsValid(value, today) {
    const parts = /^(\d{4})\/(\d{2})\/(\d{2})$/.exec(value);
    if (!parts) return false;
    const [, y, m, d] = parts.map(Number);
    const date = new Date(0);
    date.setFullYear(y, m - 1, d);
    date.setHours(0, 0, 0, 0);
    return y > 0 && date.getFullYear() === y && date.getMonth() === m - 1 && date.getDate() === d && value <= today;
}
