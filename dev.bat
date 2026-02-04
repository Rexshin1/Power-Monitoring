@echo off
set "PATH=%PATH%;C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64"
echo Environment setup for this session.
echo.
echo Starting Laravel Server...
php artisan serve
pause
