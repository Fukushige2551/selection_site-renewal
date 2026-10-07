import { cp, mkdir, readdir, readFile, stat } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const dist = path.join(root, 'dist');
const manifest = JSON.parse(await readFile(path.join(dist, '.vite', 'manifest.json'), 'utf8'));

// Stop before packaging if the build manifest references missing output.
if (!Object.keys(manifest).length) throw new Error('Build manifest is empty.');
for (const entry of Object.values(manifest)) {
  for (const file of [entry.file, ...(entry.css ?? []), ...(entry.assets ?? [])].filter(Boolean)) {
    const resolved = path.resolve(dist, file);
    if (!resolved.startsWith(dist + path.sep)) throw new Error(`Invalid build path: ${file}`);
    if (!(await stat(resolved)).isFile()) throw new Error(`Missing build file: ${file}`);
  }
}

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
