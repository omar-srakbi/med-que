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
function Print-Header { param($Msg) Write-Host "`n=== $Msg ===" -ForegroundColor Blue }
function Print-Success { param($Msg) Write-Host "✔ $Msg" -ForegroundColor Green }
function Print-Progress { param($Msg) Write-Host "➜ $Msg" -ForegroundColor White }
function Print-Error { param($Msg) Write-Host "✖ $Msg" -ForegroundColor Red }

function Update-SessionPath {
    $userScoop = "$env:USERPROFILE\scoop"
    $addPath = "$userScoop\shims;$userScoop\apps\scoop\current\bin"
    if ($env:Path -notlike "*$addPath*") {
        $env:Path += ";$addPath"
    }
}

Print-Header "Step 1: Scoop Package Manager"

if (!(Get-Command scoop -ErrorAction SilentlyContinue)) {
    Print-Progress "Checking Execution Policy..."
    
    # التحقق مما إذا كانت السياسة الحالية تسمح بالتشغيل أصلاً
    $currentPolicy = Get-ExecutionPolicy
    if ($currentPolicy -eq 'Bypass' -or $currentPolicy -eq 'Unrestricted' -or $currentPolicy -eq 'RemoteSigned') {
        Print-Success "Current Policy ($currentPolicy) is sufficient. Skipping change."
    } else {
        try {
            Print-Progress "Attempting to set Execution Policy to RemoteSigned..."
            Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser -Force -ErrorAction SilentlyContinue
        } catch {
            Write-Host "Note: Could not change policy, but will try to proceed." -ForegroundColor Gray
        }
    }

    try {
        Print-Progress "Downloading and Installing Scoop..."
        $installScript = Invoke-RestMethod -Uri https://get.scoop.sh
        Invoke-Expression "& {$installScript}"
        Print-Success "Scoop installed successfully."
    } catch {
        Print-Error "Failed to install Scoop: $_"
        exit 1
    }
} else {
    Print-Success "Scoop is already present."
}
Update-SessionPath

Print-Header "Step 2: Core Dependencies (Git, PHP, Composer, Nodejs)"

# First: Install Git (required for buckets)
if (!(Get-Command git -ErrorAction SilentlyContinue)) {
    Print-Progress "Installing Git..."
    & scoop install git
    Update-SessionPath
} else {
    Print-Success "Git is already installed."
}

Print-Progress "Adding PHP bucket..."

# Check if buckets already exist before adding
$buckets = @("php", "versions", "extras")
foreach ($bucket in $buckets) {
    # Check if bucket directory exists (most reliable method)
    $bucketPath = "$env:USERPROFILE\scoop\buckets\$bucket"
    if (Test-Path $bucketPath) {
        Print-Success "Bucket '$bucket' already exists."
    } else {
        Print-Progress "Adding bucket '$bucket'..."
        $addOutput = & scoop bucket add $bucket 2>&1
        if ($LASTEXITCODE -eq 0) {
            Print-Success "Bucket '$bucket' added."
        } else {
            Print-Warning "Failed to add bucket '$bucket'."
            Write-Host ($addOutput -join "`n") -ForegroundColor Yellow
        }
    }
}
Print-Success "PHP bucket configuration complete."

# Third: Install remaining dependencies
$apps = @(
    @{ name="extras/vcredist2022"; check="vcredist"; label="VCrun2022" },
    @{ name="versions/php84"; check="versions"; label="PHP 8.4" },
    @{ name="versions/python314"; check="versions"; label="python3.14" },
    @{ name="composer"; check="composer"; label="Composer" },
    @{ name="nodejs-lts"; check="node"; label="Node.js" }
)

foreach ($app in $apps) {
    if (!(Get-Command $app.check -ErrorAction SilentlyContinue)) {
        Print-Progress "Installing $($app.label)..."
        & scoop install $($app.name)
        Update-SessionPath
    } else {
        Print-Success "$($app.label) is already installed."
    }
}

# Enable required PHP extensions (handles Scoop's php.ini automatically)
Print-Progress "Enabling required PHP extensions..."

function Enable-PhpExtension {
    param([string]$Extension)

    $scoopPhpDir = "$env:USERPROFILE\scoop\apps\php84\current"

    # Ensure php.ini exists from production template
    if (Test-Path $scoopPhpDir) {
        $phpIniProd = Join-Path $scoopPhpDir "php.ini-production"
        $phpIniDest = Join-Path $scoopPhpDir "php.ini"

        if (-not (Test-Path $phpIniDest)) {
            if (Test-Path $phpIniProd) {
                Copy-Item $phpIniProd $phpIniDest
                Print-Progress "Created php.ini from php.ini-production"
            }
        }
    }

    # Only modify the main php.ini (cli/php.ini is managed separately by Scoop)
    $iniPath = Join-Path $scoopPhpDir "php.ini"
    if (-not (Test-Path $iniPath)) { return $false }

    $extLine = "extension=$Extension"
    $lines = Get-Content $iniPath

    # Check current state
    $enabledCount = 0
    $commentedIndices = @()

    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i].Trim()
        if ($line -eq $extLine) {
            $enabledCount++
        }
        if ($line -match "^;\s*extension\s*=\s*$Extension\s*$") {
            $commentedIndices += $i
        }
    }

    if ($enabledCount -gt 0) {
        # Already enabled - remove any duplicates (keep only first)
        if ($enabledCount -gt 1) {
            $found = 0
            for ($i = 0; $i -lt $lines.Count; $i++) {
                if ($lines[$i].Trim() -eq $extLine) {
                    $found++
                    if ($found -gt 1) {
                        $lines[$i] = ";$extLine" # re-comment duplicate
                    }
                }
            }
            $lines | Set-Content $iniPath -Encoding UTF8
        }
        return $true
    }

    if ($commentedIndices.Count -gt 0) {
        # Uncomment all commented instances
        foreach ($idx in $commentedIndices) {
            $lines[$idx] = $extLine
        }
        $lines | Set-Content $iniPath -Encoding UTF8
        Print-Progress "$extLine enabled in php.ini"
        return $true
    }

    # No matching line found, add it
    Add-Content -Path $iniPath -Value "`n$extLine" -Encoding UTF8
    Print-Progress "$extLine added to php.ini"
    return $true
}

$extsNeeded = @("openssl", "fileinfo", "sqlite3", "pdo_sqlite", "gd", "zip", "mbstring")
foreach ($ext in $extsNeeded) {
    Enable-PhpExtension -Extension $ext | Out-Null
}
Print-Success "PHP extensions configured."

# Reset PHP to apply changes
Print-Progress "Resetting PHP environment..."
& scoop reset php84 2>$null | Out-Null

# Verify extensions using php -m
Print-Progress "Verifying PHP extensions..."
$loadedModules = cmd /c "php -m 2>nul" | Where-Object { $_ -match '^\[' -or $_ -match '^\w' }
$loadedList = @()
foreach ($line in $loadedModules) {
    if ($line -match '^\[') { continue } # skip section headers
    $loadedList += $line.Trim().ToLower()
}

foreach ($ext in $extsNeeded) {
    $extLower = $ext.ToLower()
    # Handle mapping: pdo_sqlite -> pdo_sqlite, sqlite3 -> sqlite3
    $found = $loadedList -contains $extLower
    if ($found) {
        Print-Success "$ext extension is working!"
    } else {
        # Try alternative names
        $altNames = @{
            "sqlite3" = @("sqlite3", "pdo_sqlite")
            "pdo_sqlite" = @("pdo_sqlite", "sqlite3")
            "gd" = @("gd", "gd2")
        }
        $altFound = $false
        if ($altNames.ContainsKey($extLower)) {
            foreach ($alt in $altNames[$extLower]) {
                if ($loadedList -contains $alt.ToLower()) { $altFound = $true; break }
            }
        }
        if ($altFound) {
            Print-Success "$ext extension is working!"
        } else {
            Print-Error "$ext extension is NOT working. Composer may fail."
        }
    }
}

# --- الخطوة 3: إعداد المشروع (Laravel) ---
Print-Header "Step 3: Project Configuration"

# 1. مكتبات Composer
Print-Progress "Running 'composer install' (this may take a few minutes)..."
Write-Host "Installing dependencies..." -ForegroundColor DarkGray
composer install --no-interaction
$composerExitCode = $LASTEXITCODE

# Check both exit code AND vendor/autoload.php existence
if ($composerExitCode -eq 0 -and (Test-Path "vendor/autoload.php")) {
    Print-Success "Composer dependencies installed successfully."
} else {
    Print-Error "Composer install failed (exit code: $composerExitCode)."
    exit 1
}

# 2. ملف البيئة
if (!(Test-Path ".env")) {
    Print-Progress "Creating .env file..."
    Copy-Item ".env.example" ".env"
}

# 3. مفتاح التطبيق
Print-Progress "Generating App Key..."
$keyOutput = cmd /c "php artisan key:generate --force 2>&1"
$keyExitCode = $LASTEXITCODE

if ($keyExitCode -ne 0) {
    Print-Error "Failed to generate application key!"
    Write-Host ($keyOutput -join "`n") -ForegroundColor Red
    exit 1
}
Print-Success "Application key generated."

# 4. اعداد بنى قواعد البيانات (Migrations & Seeders)
# Note: migrate:fresh will automatically create the SQLite database file

Print-Progress "Running migrations..."
$migrateOutput = cmd /c "php artisan migrate:fresh --force 2>&1"
$migrateExitCode = $LASTEXITCODE

if ($migrateExitCode -ne 0) {
    Print-Error "Migration failed!"
    Write-Host ($migrateOutput -join "`n") -ForegroundColor Red
    exit 1
}
Print-Success "Migrations complete."

# Run seeders - Use DatabaseSeeder which calls all seeders in order
Print-Progress "Running database seeders..."
$seedOutput = cmd /c "php artisan db:seed --force 2>&1"
$seedExitCode = $LASTEXITCODE

if ($seedExitCode -ne 0) {
    Print-Error "Database seeding failed!"
    $seedErrors = $seedOutput | Where-Object { $_ -match "Error|Problem|Exception" }
    if ($seedErrors) { Write-Host ($seedErrors -join "`n") -ForegroundColor Red }
    exit 1
}
Print-Success "Database seeded successfully (roles, departments with services, admin, employee credentials)."

Print-Header "🚀 SETUP COMPLETE!"
Write-Host ""
Write-Host "To start the development server:"
Write-Host "  php artisan serve" -ForegroundColor Yellow
Write-Host ""
Write-Host "Then access the application at:"
Write-Host "  http://127.0.0.1:8000" -ForegroundColor Blue
Write-Host ""
Write-Host "TAKE A PICTURE" -ForegroundColor green
Write-Host ""
Write-Host "Default login credentials:"
Write-Host "  Email:    admin@example.com" -ForegroundColor Yellow
Write-Host "  Password: password" -ForegroundColor Yellow
Write-Host ""
Write-Host "demo accounts:"
Write-Host "  Cashier:      cashier@example.com / cashier123" -ForegroundColor Yellow
Write-Host "  Head Cashier: headcashier@example.com / head123" -ForegroundColor Yellow
Write-Host "  Doctor:       doctor@example.com / doctor123" -ForegroundColor Yellow
Write-Host "  Receptionist: receptionist@example.com / receptionist123" -ForegroundColor Yellow
Write-Host ""
Write-Host "Note: Change the default passwords after first login!" -ForegroundColor Yellow
Write-Host ""
