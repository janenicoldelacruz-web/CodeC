@echo off
title SIATRACK Auto Launcher
cd /d D:\xamp\htdocs\CodeCommanders

echo ==================================================
echo       Starting SIATRACK System & NFC Reader       
echo ==================================================

:: 1. Patakbuhin ang Laravel Server sa background window
start "SIATRACK Server" cmd /k "title Laravel Server && php artisan serve"

:: 2. I-activate ang .venv at patakbuhin ang NFC Bridge
start "SIATRACK NFC Bridge" cmd /k "title NFC Bridge && call .venv\Scripts\activate && python nfc_bridge.py"

:: 3. Maghintay ng 2 segundo at kusang buksan ang browser
timeout /t 2 /nobreak >nul
start http://127.0.0.1:8000/admin/users/create

exit