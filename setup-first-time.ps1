###############################################################################
# Med-Que First-Time Setup Script (Windows PowerShell)
#
# This script automates the initial setup for a fresh clone of the Med-Que
# Laravel project. It handles:
# - PHP version check (requires 8.4+)
# - Composer dependencies installation
# - Environment file setup
# - Application key generation
# - Database creation and migrations
# - Default data seeding
# - Cache clearing
###############################################################################

$ErrorActionPreference = "Stop"

# Helper functions
function Print-Header {
    param([string]$Message)
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Blue
    Write-Host $Message -ForegroundColor Blue
    Write-Host "========================================" -ForegroundColor Blue
    Write-Host ""
}

function Print-Success {
    param([string]$Message)
    Write-Host "✓ $Message" -ForegroundColor Green
}

function Print-Warning {
    param([string]$Message)
    Write-Host "⚠ $Message" -ForegroundColor Yellow
}

function Print-Error {
    param([string]$Message)
    Write-Host "✗ $Message" -ForegroundColor Red
}

function Get-PHPVersion {
    try {
        $phpOutput = php -v 2>&1
        $versionLine = $phpOutput | Select-Object -First 1
        $version = ($versionLine -split ' ')[1]
        $versionParts = $version -split '\.'
        return @{
            Major = [int]$versionParts[0]
            Minor = [int]$versionParts[1]
            Full = "$($versionParts[0]).$($versionParts[1])"
        }
    } catch {
        return $null
    }
}

function Get-ComposerVersion {
    try {
        $composerOutput = composer --version 2>&1
        if ($composerOutput -match 'version\s+(\d+\.\d+\.\d+)') {
            return $matches[1]
        }
        return "unknown"
    } catch {
        return $null
    }
}

function Get-EnvValue {
    param([string]$Key)
    if (Test-Path ".env") {
        $line = Get-Content ".env" | Where-Object { $_ -match "^$Key=" }
        if ($line) {
            return ($line -split '=', 2)[1]
        }
    }
    return ""
}

# Start setup
Print-Header "Med-Que First-Time Setup"

# Step 1: Check PHP version
Print-Header "Step 1: Checking PHP Version"
$phpVersion = Get-PHPVersion

if ($phpVersion) {
    Write-Host "Current PHP version: $($phpVersion.Full)"

    if ($phpVersion.Major -lt 8 -or ($phpVersion.Major -eq 8 -and $phpVersion.Minor -lt 4)) {
        Print-Error "PHP 8.4+ is required. You have PHP $($phpVersion.Full)"
        Write-Host ""
        Write-Host "To install PHP 8.4, download from: https://windows.php.net/download/"
        Write-Host "Or use a package manager like:"
        Write-Host "  winget install PHP.PHP.8.4"
        Write-Host "  choco install php --version=8.4.0"
        Write-Host "  scoop install php"
        exit 1
    }

    Print-Success "PHP version is compatible ($($phpVersion.Full))"
} else {
    Print-Error "PHP is not installed or not in PATH"
    exit 1
}

# Step 2: Check Composer
Print-Header "Step 2: Checking Composer"
$composerVersion = Get-ComposerVersion

if ($composerVersion) {
    Print-Success "Composer is installed ($composerVersion)"
} else {
    Print-Error "Composer is not installed"
    Write-Host "Install Composer from: https://getcomposer.org/download/"
    exit 1
}

# Step 3: Install Composer dependencies
Print-Header "Step 3: Installing Composer Dependencies"
if (Test-Path "vendor") {
    Print-Warning "Vendor directory exists. Re-installing dependencies..."
    composer install --no-interaction
} else {
    composer install --no-interaction
}
Print-Success "Composer dependencies installed"

# Step 4: Setup Environment File
Print-Header "Step 4: Setting Up Environment File"
if (Test-Path ".env") {
    Print-Warning ".env file already exists. Skipping..."
} else {
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Print-Success ".env file created from .env.example"
    } else {
        Print-Error ".env.example not found!"
        exit 1
    }
}

# Step 5: Generate Application Key
Print-Header "Step 5: Generating Application Key"
$envContent = Get-Content ".env" -Raw
$appKeyLine = $envContent | Select-String "^APP_KEY="

if ($appKeyLine -and $appKeyLine.Line -match "^APP_KEY=base64:") {
    Print-Warning "Application key already set. Skipping..."
} else {
    php artisan key:generate
    Print-Success "Application key generated"
}

# Step 6: Setup Database
Print-Header "Step 6: Setting Up Database"
Write-Host "Note: SQLite uses WAL mode for better multi-user concurrency"
Write-Host ""

$DB_CONNECTION = Get-EnvValue "DB_CONNECTION"

switch ($DB_CONNECTION) {
    "sqlite" {
        $DB_DATABASE = Get-EnvValue "DB_DATABASE"

        # If DB_DATABASE is empty or :memory:, use default
        if ([string]::IsNullOrEmpty($DB_DATABASE) -or $DB_DATABASE -eq ":memory:") {
            $DB_DATABASE = "database/database.sqlite"
            Add-Content ".env" "`nDB_DATABASE=$DB_DATABASE"
        }

        # Create database directory if it doesn't exist
        $dbDir = Split-Path -Path $DB_DATABASE -Parent
        if ($dbDir -and -not (Test-Path $dbDir)) {
            New-Item -ItemType Directory -Force -Path $dbDir | Out-Null
        }

        # Create database file if it doesn't exist
        if (-not (Test-Path $DB_DATABASE)) {
            New-Item -ItemType File -Force -Path $DB_DATABASE | Out-Null
            Print-Success "SQLite database created at: $DB_DATABASE"
        } else {
            Print-Warning "SQLite database already exists at: $DB_DATABASE"
        }
    }
    "mysql" {
        Print-Warning "MySQL database detected. Please ensure database is created and configured manually."
    }
    "pgsql" {
        Print-Warning "PostgreSQL database detected. Please ensure database is created and configured manually."
    }
    "sqlsrv" {
        Print-Warning "SQL Server database detected. Please ensure database is created and configured manually."
    }
    default {
        Print-Error "Unknown database connection: $DB_CONNECTION"
        exit 1
    }
}

# Step 7: Run Migrations
Print-Header "Step 7: Running Database Migrations"
php artisan migrate --force
Print-Success "Database migrations completed"

# Step 8: Run Seeders
Print-Header "Step 8: Running Database Seeders"
php artisan db:seed --force
Print-Success "Database seeders completed"

# Step 9: Setup Department Prefixes (Shared Ticket Sequence + Unique Queue Prefixes)
Print-Header "Step 9: Configuring Department Prefixes"
php artisan tinker --execute=@"
`$depts = [
    1 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q1'],
    2 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q2'],
    3 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q3'],
    4 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q4'],
    5 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q5'],
    6 => ['sequence_prefix' => 'TK', 'queue_prefix' => 'Q6'],
];
foreach(`$depts as `$id => `$data) {
    App\Models\Department::find(`$id)?->update(`$data);
}
echo 'Done';
"@
Print-Success "Department prefixes configured (shared ticket sequence + unique queue prefixes)"

# Step 10: Clear Caches
Print-Header "Step 10: Clearing Caches"
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
Print-Success "Caches cleared"

# Final Summary
Print-Header "Setup Complete!"

Write-Host "Med-Que is ready to run!" -ForegroundColor Green
Write-Host ""
Write-Host "To start the development server:"
Write-Host "  php artisan serve" -ForegroundColor Yellow
Write-Host ""
Write-Host "Then access the application at:"
Write-Host "  http://127.0.0.1:8000" -ForegroundColor Blue
Write-Host ""
Write-Host "Default login credentials:"
Write-Host "  Email:    admin@example.com" -ForegroundColor Yellow
Write-Host "  Password: password" -ForegroundColor Yellow
Write-Host ""
Write-Host "Additional demo accounts:"
Write-Host "  Cashier:      cashier@example.com / cashier123" -ForegroundColor Yellow
Write-Host "  Head Cashier: headcashier@example.com / head123" -ForegroundColor Yellow
Write-Host "  Doctor:       doctor@example.com / doctor123" -ForegroundColor Yellow
Write-Host "  Receptionist: receptionist@example.com / receptionist123" -ForegroundColor Yellow
Write-Host ""
Write-Host "Note: Change the default passwords after first login!" -ForegroundColor Yellow
Write-Host ""
