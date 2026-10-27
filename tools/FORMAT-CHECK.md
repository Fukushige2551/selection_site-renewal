# Source formatting checks

- Run `npm run check:format` for a read-only check of every `.js` file in `src/js` and `.scss` file in `src/scss`.
- `npm run build` and `npm run build:stg` run the check automatically before building. Direct Vite commands do not run npm lifecycle checks.
- Use spaces, with indentation in multiples of four. Block comment stars may have a one-space alignment offset.
- Avoid trailing spaces and tabs, and keep at most one blank line between sections. End files with a newline. Both LF and CRLF are accepted.
- The check reports file paths and line numbers and exits unsuccessfully on violations. It never rewrites files.
- Indentation and blank-line checks also inspect multiline literals. Preserve intentional literal content; review any diagnostic there rather than blindly reformatting it.
- PHP/HTML, plain CSS, generated `dist` files, images and dependencies are outside this check's scope. Their existing formatting is preserved.
- `.editorconfig` helps compatible editors apply the same JS/SCSS settings.
- Run `npm run test:format` to test valid examples and rejection of each violation type.
