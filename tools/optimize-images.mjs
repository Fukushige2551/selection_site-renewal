import { copyFile, mkdir, readFile, readdir, stat, writeFile } from 'node:fs/promises'
import path from 'node:path'
import sharp from 'sharp'
import { optimize } from 'svgo'

const sourceRoot = path.resolve('img')
const outputRoot = path.resolve('dist/img')
const rasterExtensions = new Set(['.jpg', '.jpeg', '.png', '.webp'])

let sourceBytes = 0
let outputBytes = 0
let optimizedCount = 0
let copiedCount = 0

async function optimizeRaster(sourcePath, outputPath, extension) {
  let pipeline = sharp(sourcePath)

  if (extension === '.jpg' || extension === '.jpeg') {
    pipeline = pipeline.jpeg({ quality: 82, mozjpeg: true })
  } else if (extension === '.png') {
    pipeline = pipeline.png({ compressionLevel: 9, effort: 10 })
  } else if (extension === '.webp') {
    pipeline = pipeline.webp({ quality: 82, effort: 6 })
  }

  await pipeline.toFile(outputPath)
}

async function optimizeSvg(sourcePath, outputPath) {
  const source = await readFile(sourcePath, 'utf8')
  const result = optimize(source, {
    path: sourcePath,
    multipass: true,
  })

  await writeFile(outputPath, result.data, 'utf8')
}

async function processDirectory(sourceDirectory, outputDirectory) {
  await mkdir(outputDirectory, { recursive: true })

  const entries = await readdir(sourceDirectory, { withFileTypes: true })

  for (const entry of entries) {
    const sourcePath = path.join(sourceDirectory, entry.name)
    const outputPath = path.join(outputDirectory, entry.name)

    if (entry.isDirectory()) {
      await processDirectory(sourcePath, outputPath)
      continue
    }

    if (!entry.isFile()) continue

    const extension = path.extname(entry.name).toLowerCase()
    sourceBytes += (await stat(sourcePath)).size

    if (rasterExtensions.has(extension)) {
      await optimizeRaster(sourcePath, outputPath, extension)
      optimizedCount += 1
    } else if (extension === '.svg') {
      await optimizeSvg(sourcePath, outputPath)
      optimizedCount += 1
    } else {
      await copyFile(sourcePath, outputPath)
      copiedCount += 1
    }

    outputBytes += (await stat(outputPath)).size
  }
}

await processDirectory(sourceRoot, outputRoot)

const reduction = sourceBytes === 0
  ? 0
  : ((sourceBytes - outputBytes) / sourceBytes) * 100

console.log(
  `Images: ${optimizedCount} optimized, ${copiedCount} copied; ` +
  `${(sourceBytes / 1024 / 1024).toFixed(2)} MB -> ` +
  `${(outputBytes / 1024 / 1024).toFixed(2)} MB ` +
  `(${reduction.toFixed(1)}% smaller)`,
)
