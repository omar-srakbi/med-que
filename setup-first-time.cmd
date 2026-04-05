@echo off
setlocal
:: الحصول على مسار المجلد الحالي
set "SCRIPT_DIR=%~dp0"
cd /d "%SCRIPT_DIR%"

echo ======================================================
echo           Med-Que Automated Setup (User Mode)
echo ======================================================
echo.

:: تشغيل الباورشيل بدون طلب صلاحيات مدير ومع تجاوز سياسة الحماية للجلسة فقط
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "setup-first-time.ps1"

echo.
echo ======================================================
echo Setup Process Finished.
echo ======================================================
pause