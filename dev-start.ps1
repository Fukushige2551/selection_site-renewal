[CmdletBinding()]
param(
    [switch]$NoCode,
    [switch]$NoBrowser
)

$ErrorActionPreference = 'Stop'

$projectDir = $PSScriptRoot
$siteUrl = 'http://foods-selectioncojp.local'
$localShortcut = 'C:\ProgramData\Microsoft\Windows\Start Menu\Programs\Local.lnk'
$localExecutable = 'C:\Program Files (x86)\Local\Local.exe'
$viteUrl = 'http://localhost:5173'

function Test-Url {
    param(
        [Parameter(Mandatory)]
        [string]$Url,
        [int]$TimeoutSeconds = 3
    )

    try {
        $response = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec $TimeoutSeconds
        return $response.StatusCode -ge 200 -and $response.StatusCode -lt 500
    }
    catch {
        return $false
    }
}

function Test-TcpPort {
    param(
        [Parameter(Mandatory)]
        [string]$HostName,
        [Parameter(Mandatory)]
        [int]$Port,
        [int]$TimeoutMilliseconds = 1000
    )

    $client = [System.Net.Sockets.TcpClient]::new()
    try {
        $task = $client.ConnectAsync($HostName, $Port)
        return $task.Wait($TimeoutMilliseconds) -and $client.Connected
    }
    catch {
        return $false
    }
    finally {
        $client.Dispose()
    }
}

function Start-LocalApp {
    if (Test-Path -LiteralPath $localShortcut) {
        Start-Process -FilePath $localShortcut
        return
    }

    if (Test-Path -LiteralPath $localExecutable) {
        Start-Process -FilePath $localExecutable
        return
    }

    throw 'Local was not found. Please start Local manually.'
}

Write-Host ''
Write-Host 'Preparing the local development environment.' -ForegroundColor Cyan

if (-not (Test-Url -Url $siteUrl)) {
    Write-Host 'Starting Local.' -ForegroundColor Yellow
    Start-LocalApp
    Write-Host ''
    Write-Host 'In Local, select foods-selection.co.jp and click "Start site".' -ForegroundColor Yellow
    Write-Host 'Waiting for the site. Keep this window open.'

    while (-not (Test-Url -Url $siteUrl)) {
        Start-Sleep -Seconds 3
    }
}

Write-Host 'The WordPress site is running.' -ForegroundColor Green

if (-not (Test-TcpPort -HostName 'localhost' -Port 5173)) {
    $viteLauncher = Join-Path $projectDir 'dev-vite.ps1'
    Write-Host 'Starting the Vite development server.' -ForegroundColor Cyan
    Start-Process -FilePath 'powershell.exe' -ArgumentList @(
        '-NoProfile',
        '-ExecutionPolicy',
        'Bypass',
        '-File',
        "`"$viteLauncher`""
    )
}
else {
    Write-Host 'The Vite development server is already running.' -ForegroundColor Green
}

if (-not $NoCode) {
    $codeCommand = Get-Command 'code.cmd' -ErrorAction SilentlyContinue
    if ($codeCommand) {
        Start-Process -FilePath $codeCommand.Source -ArgumentList @('--reuse-window', $projectDir)
    }
    else {
        Write-Warning 'The VS Code command was not found. VS Code was not opened.'
    }
}

if (-not $NoBrowser) {
    Start-Process $siteUrl
}

Write-Host ''
Write-Host 'The development environment is ready.' -ForegroundColor Green
Write-Host "WordPress: $siteUrl"
Write-Host "Vite:     $viteUrl"
