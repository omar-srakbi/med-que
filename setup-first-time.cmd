@echo off
setlocal EnableDelayedExpansion

REM ###############################################################################
REM Med-Que First-Time Setup Script (Windows CMD/Batch)
REM
REM This script automates the initial setup for a fresh clone of the Med-Que
REM Laravel project. It handles:
REM - PHP version check (requires 8.4+)
REM - Composer dependencies installation
REM - Environment file setup
REM - Application key generation
REM - Database creation and migrations
REM - Default data seeding
REM - Cache clearing
REM ###############################################################################

REM Colors for output (ANSI escape codes - works on Windows 10+)
set "BLUE=[94m"
set "GREEN=[92m"
set "YELLOW=[93m"
set "RED=[91m"
set "NC=[0m"

REM Enable ANSI support on Windows 10+
for /F "tokens=4-5 delims=. " %%i in ('ver') do set VERSION=%%i.%%j
if "%VERSION%" == "10." (
    reg add HKCU\Console /v VirtualTerminalLevel /t REG_DWORD /d 1 /f >nul 2>&1
)

REM Helper functions (using labels and goto)
:print_header
echo.
echo ========================================
echo %~1
echo ========================================
echo.
goto :eof

:print_success
echo %GREEN%✓ %~1%NC%
goto :eof

:print_warning
echo %YELLOW%⚠ %~1%NC%
goto :eof

:print_error
echo %RED%✗ %~1%NC%
goto :eof

REM Start setup
call :print_header "Med-Que First-Time Setup"

REM Step 1: Check PHP version
call :print_header "Step 1: Checking PHP Version"
where php >nul 2>&1
if %errorlevel% neq 0 (
    call :print_error "PHP is not installed or not in PATH"
    exit /b 1
)

for /f "tokens=2 delims= " %%a in ('php -v ^| findstr /R "^[0-9]"') do set "PHP_FULL=%%a"
for /f "tokens=1 delims=." %%a in ("%PHP_FULL%") do set "PHP_MAJOR=%%a"
for /f "tokens=2 delims=." %%a in ("%PHP_FULL%") do set "PHP_MINOR=%%a"

echo Current PHP version: %PHP_MAJOR%.%PHP_MINOR%

if %PHP_MAJOR% LSS 8 (
    call :print_error "PHP 8.4+ is required. You have PHP %PHP_MAJOR%.%PHP_MINOR%"
    goto :php_install_help
)
if %PHP_MAJOR% EQU 8 if %PHP_MINOR% LSS 4 (
    call :print_error "PHP 8.4+ is required. You have PHP %PHP_MAJOR%.%PHP_MINOR%"
    goto :php_install_help
)

call :print_success "PHP version is compatible (%PHP_MAJOR%.%PHP_MINOR%)"
goto :step2

:php_install_help
echo.
echo To install PHP 8.4, download from: https://windows.php.net/download/
echo Or use a package manager like:
echo   winget install PHP.PHP.8.4
echo   choco install php --version=8.4.0
echo   scoop install php
exit /b 1

:step2
REM Step 2: Check Composer
call :print_header "Step 2: Checking Composer"
where composer >nul 2>&1
if %errorlevel% neq 0 (
    call :print_error "Composer is not installed"
    echo Install Composer from: https://getcomposer.org/download/
    exit /b 1
)

for /f "tokens=3" %%a in ('composer --version') do set "COMPOSER_VERSION=%%a"
call :print_success "Composer is installed (%COMPOSER_VERSION%)"

REM Step 3: Install Composer dependencies
call :print_header "Step 3: Installing Composer Dependencies"
if exist "vendor" (
    call :print_warning "Vendor directory exists. Re-installing dependencies..."
)
call composer install --no-interaction
if %errorlevel% neq 0 exit /b %errorlevel%
call :print_success "Composer dependencies installed"

REM Step 4: Setup Environment File
call :print_header "Step 4: Setting Up Environment File"
if exist ".env" (
    call :print_warning ".env file already exists. Skipping..."
    goto :step5
)
if not exist ".env.example" (
    call :print_error ".env.example not found!"
    exit /b 1
)
copy ".env.example" ".env" >nul
call :print_success ".env file created from .env.example"

:step5
REM Step 5: Generate Application Key
call :print_header "Step 5: Generating Application Key"
findstr /R "^APP_KEY=base64:" .env >nul 2>&1
if %errorlevel% equ 0 (
    call :print_warning "Application key already set. Skipping..."
    goto :step6
)
call php artisan key:generate
if %errorlevel% neq 0 exit /b %errorlevel%
call :print_success "Application key generated"

:step6
REM Step 6: Setup Database
call :print_header "Step 6: Setting Up Database"
echo Note: SQLite uses WAL mode for better multi-user concurrency
echo.

REM Get database connection type from .env
set "DB_CONNECTION="
for /f "tokens=2 delims==" %%a in ('findstr /R "^DB_CONNECTION=" .env') do set "DB_CONNECTION=%%a"

if "%DB_CONNECTION%"=="sqlite" (
    goto :setup_sqlite
) else if "%DB_CONNECTION%"=="mysql" (
    call :print_warning "MySQL database detected. Please ensure database is created and configured manually."
    goto :step7
) else if "%DB_CONNECTION%"=="pgsql" (
    call :print_warning "PostgreSQL database detected. Please ensure database is created and configured manually."
    goto :step7
) else if "%DB_CONNECTION%"=="sqlsrv" (
    call :print_warning "SQL Server database detected. Please ensure database is created and configured manually."
    goto :step7
) else (
    call :print_error "Unknown database connection: %DB_CONNECTION%"
    exit /b 1
)

:setup_sqlite
set "DB_DATABASE="
for /f "tokens=2 delims==" %%a in ('findstr /R "^DB_DATABASE=" .env') do set "DB_DATABASE=%%a"

if "%DB_DATABASE%"=="" (
    set "DB_DATABASE=database/database.sqlite"
    echo DB_DATABASE=%DB_DATABASE%>> .env
)
if "%DB_DATABASE%"==":memory:" (
    set "DB_DATABASE=database/database.sqlite"
    echo DB_DATABASE=%DB_DATABASE%>> .env
)

REM Create database directory if it doesn't exist
for %%I in ("%DB_DATABASE%") do set "DB_DIR=%%~dpI"
if not exist "%DB_DIR%" (
    mkdir "%DB_DIR%"
)

REM Create database file if it doesn't exist
if not exist "%DB_DATABASE%" (
    type nul > "%DB_DATABASE%"
    call :print_success "SQLite database created at: %DB_DATABASE%"
) else (
    call :print_warning "SQLite database already exists at: %DB_DATABASE%"
)
goto :step7

:step7
REM Step 7: Run Migrations
call :print_header "Step 7: Running Database Migrations"
call php artisan migrate --force
if %errorlevel% neq 0 exit /b %errorlevel%
call :print_success "Database migrations completed"

REM Step 8: Run Seeders
call :print_header "Step 8: Running Database Seeders"
call php artisan db:seed --force
if %errorlevel% neq 0 exit /b %errorlevel%
call :print_success "Database seeders completed"

REM Step 9: Setup Department Prefixes
call :print_header "Step 9: Configuring Department Prefixes"
call php artisan tinker --execute="$depts = [1 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q1'], 2 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q2'], 3 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q3'], 4 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q4'], 5 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q5'], 6 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q6']]; foreach($depts as $id => $data) { App\Models\Department::find($id)?->update($data); } echo 'Done';"
if %errorlevel% neq 0 exit /b %errorlevel%
call :print_success "Department prefixes configured (shared ticket sequence + unique queue prefixes)"

REM Step 10: Clear Caches
call :print_header "Step 10: Clearing Caches"
call php artisan cache:clear
call php artisan config:clear
call php artisan view:clear
call php artisan route:clear
call :print_success "Caches cleared"

REM Final Summary
call :print_header "Setup Complete!"

echo %GREEN%Med-Que is ready to run!%NC%
echo.
echo To start the development server:
echo   %YELLOW%php artisan serve%NC%
echo.
echo Then access the application at:
echo   %BLUE%http://127.0.0.1:8000%NC%
echo.
echo Default login credentials:
echo   Email:    %YELLOW%admin@example.com%NC%
echo   Password: %YELLOW%password%NC%
echo.
echo Additional demo accounts:
echo   Cashier:      %YELLOW%cashier@example.com%NC% / %YELLOW%cashier123%NC%
echo   Head Cashier: %YELLOW%headcashier@example.com%NC% / %YELLOW%head123%NC%
echo   Doctor:       %YELLOW%doctor@example.com%NC% / %YELLOW%doctor123%NC%
echo   Receptionist: %YELLOW%receptionist@example.com%NC% / %YELLOW%receptionist123%NC%
echo.
echo %YELLOW%Note: Change the default passwords after first login!%NC%
echo.

endlocal
exit /b 0
