import assert from 'node:assert/strict';
import test from 'node:test';
import { inspectFormat } from './check-format.mjs';

test('accept LF, CRLF, four spaces and block comment alignment', () => {
    for (const eol of ['\n', '\r\n']) {
        assert.deepEqual(inspectFormat(['function example() {', '    /**', '     * Comment', '     */', '    return true;', '}', ''].join(eol)), []);
    }
});

for (const [source, rule, line] of [
    ['const value = 1; \n', 'trailing whitespace', 1],
    ['\tvalue();\n', 'tab indentation', 1],
    ['  value();\n', 'indentation must be a multiple of 4 spaces', 1],
    ['value();\n\n\nnext();\n', 'consecutive blank lines', 3],
    ['value();', 'missing final newline', 1],
]) {
    test(`reject ${rule} with line number`, () => {
        assert.ok(inspectFormat(source).some(error => error.rule === rule && error.line === line));
    });
}
