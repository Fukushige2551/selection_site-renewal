$ErrorActionPreference = 'Stop'

$projectDir = $PSScriptRoot
$viteEntry = Join-Path $projectDir 'node_modules\vite\bin\vite.js'
$bundledNode = 'C:\Users\k.fukushige\.cache\codex-runtimes\codex-primary-runtime\dependencies\node\bin\node.exe'

$nodeCommand = Get-Command 'node.exe' -ErrorAction SilentlyContinue
if ($nodeCommand) {
    $nodeExecutable = $nodeCommand.Source
}
elseif (Test-Path -LiteralPath $bundledNode) {
    $nodeExecutable = $bundledNode
}
else {
    throw 'Node.js was not found. Install Node.js or make node.exe available in PATH.'
}

if (-not (Test-Path -LiteralPath $viteEntry)) {
    throw 'Vite was not found. Run npm install in the theme directory first.'
}

Set-Location -LiteralPath $projectDir
Write-Host 'Vite development server' -ForegroundColor Cyan
Write-Host "Project: $projectDir"
Write-Host 'Stop: press Ctrl+C'
Write-Host ''

& $nodeExecutable $viteEntry

if ($LASTEXITCODE -ne 0) {
    Write-Host ''
    Write-Host "Vite stopped with exit code $LASTEXITCODE." -ForegroundColor Red
    Read-Host 'Press Enter to close this window'
}
