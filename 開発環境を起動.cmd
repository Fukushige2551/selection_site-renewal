@echo off
chcp 65001 > nul
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0dev-start.ps1"
if errorlevel 1 (
  echo.
  echo 起動中にエラーが発生しました。
  pause
)
