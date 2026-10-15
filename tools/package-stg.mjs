import { cp, mkdir, readdir, readFile, stat } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { execFileSync } from 'node:child_process';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const dist = path.join(root, 'dist');
const manifest = JSON.parse(await readFile(path.join(dist, '.vite', 'manifest.json'), 'utf8'));

// PHP references source paths as manifest keys; every referenced entry must exist.
for (const phpFile of ['functions.php', 'inc/cf7-forms.php']) {
  const source = await readFile(path.join(root, phpFile), 'utf8');
  for (const [, key] of source.matchAll(/['"](src\/(?:js|css)\/[^'"]+\.(?:js|css))['"]/g)) {
    if (!manifest[key]?.isEntry) throw new Error(`Missing build entry referenced by ${phpFile}: ${key}`);
  }
}

// Stop before packaging if the build manifest references missing output.
if (!Object.keys(manifest).length) throw new Error('Build manifest is empty.');
for (const entry of Object.values(manifest)) {
  for (const dependency of [...(entry.imports ?? []), ...(entry.dynamicImports ?? [])]) {
    if (!manifest[dependency]) throw new Error(`Missing manifest dependency: ${dependency}`);
  }
  for (const file of [entry.file, ...(entry.css ?? []), ...(entry.assets ?? [])].filter(Boolean)) {
    const resolved = path.resolve(dist, file);
    if (!resolved.startsWith(dist + path.sep)) throw new Error(`Invalid build path: ${file}`);
    if (!(await stat(resolved)).isFile()) throw new Error(`Missing build file: ${file}`);
  }
}

// Validate actual PHP enqueue behavior, not just the existence of build files.
execFileSync(process.env.PHP_BINARY || 'php', [path.join(root, 'tools', 'test-vite-assets.php')], { cwd: root, stdio: 'inherit' });

const entries = await readdir(root, { withFileTypes: true });
const files = entries.filter(entry => entry.isFile() && entry.name.endsWith('.php')).map(entry => entry.name);
const required = ['dist', 'img', 'inc', 'template-parts', 'style.css', ...files];
for (const name of ['functions.php', 'index.php', 'style.css', 'dist', 'img', 'inc', 'template-parts']) {
  await stat(path.join(root, name));
}

// Unique folders preserve previous packages and avoid stale files from older builds.
const stamp = new Date().toISOString().replace(/[:.]/g, '-');
const output = path.join(root, 'stg-upload', stamp);
await mkdir(output, { recursive: true });
for (const name of required) {
  await cp(path.join(root, name), path.join(output, name), { recursive: true, errorOnExist: true, force: false });
}
console.log(`STG upload folder: ${output}`);
console.log('Upload ALL CONTENTS of this folder to the remote theme directory.');
console.log('Include dist/.vite/manifest.json. Do not upload the timestamp folder itself.');
