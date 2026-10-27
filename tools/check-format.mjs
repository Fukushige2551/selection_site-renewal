import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

export function inspectFormat(source) {
    const errors = [];
    const lines = source.replace(/^\uFEFF/, '').split(/\r\n|\n|\r/);
    let empty = 0;
    for (let index = 0; index < lines.length; index++) {
        const line = lines[index];
        if (/[\t ]+$/.test(line)) errors.push({ line: index + 1, rule: 'trailing whitespace' });
        const indent = line.match(/^[\t ]*/)[0];
        if (/\t/.test(indent)) errors.push({ line: index + 1, rule: 'tab indentation' });
        // Block comment stars may be offset by one space from their containing code.
        if (line.trim() && !line.trimStart().startsWith('*') && indent.length % 4 !== 0) {
            errors.push({ line: index + 1, rule: 'indentation must be a multiple of 4 spaces' });
        }
        if (!line.trim()) {
            empty++;
            // The final split entry represents the final newline, not another empty line.
            if (empty > 1 && index < lines.length - 1) errors.push({ line: index + 1, rule: 'consecutive blank lines' });
        } else empty = 0;
    }
    if (source && !/[\r\n]$/.test(source)) errors.push({ line: lines.length, rule: 'missing final newline' });
    return errors;
}

function collect(directory) {
    return fs.readdirSync(directory, { withFileTypes: true }).flatMap(entry => {
        const name = path.join(directory, entry.name);
        if (entry.isDirectory()) return collect(name);
        return /\.(js|scss)$/.test(entry.name) ? [name] : [];
    });
}

if (process.argv[1] && path.resolve(process.argv[1]) === fileURLToPath(import.meta.url)) {
    const root = fileURLToPath(new URL('../', import.meta.url));
    const files = ['src/js', 'src/scss'].flatMap(name => collect(path.join(root, name)));
    let failures = 0;
    for (const file of files) {
        for (const error of inspectFormat(fs.readFileSync(file, 'utf8'))) {
            console.error(`${path.relative(root, file)}:${error.line}: ${error.rule}`);
            failures++;
        }
    }
    if (failures) {
        console.error(`FAIL: ${failures} formatting violations (no files changed)`);
        process.exitCode = 1;
    } else console.log(`PASS: formatting of ${files.length} JS/SCSS files`);
}
