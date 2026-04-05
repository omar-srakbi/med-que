#!/bin/bash

###############################################################################
# Med-Que First-Time Setup Script
#
# This script automates the initial setup for a fresh clone of the Med-Que
# Laravel project. It handles:
# - Auto-installation of dependencies (PHP, Composer, Node.js) on Debian/Ubuntu
# - PHP version check (requires 8.4+)
# - Composer dependencies installation
# - Environment file setup
# - Application key generation
# - Database configuration verification
# - Database creation and migrations
# - Default data seeding
# - Cache clearing
###############################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper functions
print_header() {
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}\n"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_progress() {
    echo -e "${BLUE}➜ $1${NC}"
}

# Detect package manager
detect_package_manager() {
    if command -v apt-get &> /dev/null; then
        echo "apt-get"
    elif command -v dnf &> /dev/null; then
        echo "dnf"
    elif command -v yum &> /dev/null; then
        echo "yum"
    elif command -v pacman &> /dev/null; then
        echo "pacman"
    else
        echo "unknown"
    fi
}

# Check if running as root
is_root() {
    [ "$(id -u)" -eq 0 ]
}

# Start setup
print_header "Med-Que First-Time Setup"

# Step 1: Check/Install PHP
print_header "Step 1: Checking/Installing PHP"

install_php() {
    local pkg_mgr=$(detect_package_manager)
    print_warning "Installing PHP 8.4 via $pkg_mgr..."

    case "$pkg_mgr" in
        apt-get)
            if is_root; then
                apt-get update
                apt-get install -y ca-certificates curl gnupg
                curl -sSLo /usr/share/keyrings/deb-php.gpg https://packages.sury.org/php/apt.gpg
                echo "deb [signed-by=/usr/share/keyrings/deb-php.gpg] https://packages.sury.org/php/ bookworm main" | tee /etc/apt/sources.list.d/php.list
                apt-get update
                apt-get install -y php8.4 php8.4-cli php8.4-common php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-sqlite3 php8.4-mysql php8.4-bcmath
            else
                echo "  sudo apt-get update"
                echo "  sudo apt-get install -y ca-certificates curl gnupg"
                echo "  sudo curl -sSLo /usr/share/keyrings/deb-php.gpg https://packages.sury.org/php/apt.gpg"
                echo "  echo 'deb [signed-by=/usr/share/keyrings/deb-php.gpg] https://packages.sury.org/php/ bookworm main' | sudo tee /etc/apt/sources.list.d/php.list"
                echo "  sudo apt-get update"
                echo "  sudo apt-get install -y php8.4 php8.4-cli php8.4-common php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-sqlite3 php8.4-mysql php8.4-bcmath"
                print_warning "Please run these commands manually, then re-run this script."
                exit 1
            fi
            ;;
        *)
            print_error "Unsupported package manager: $pkg_mgr"
            print_error "Please install PHP 8.4+ manually, then re-run this script."
            exit 1
            ;;
    esac
}

if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1 | cut -d ' ' -f 2 | cut -d '.' -f 1,2)
    PHP_MAJOR=$(echo $PHP_VERSION | cut -d '.' -f 1)
    PHP_MINOR=$(echo $PHP_VERSION | cut -d '.' -f 2)

    echo "Current PHP version: $PHP_VERSION"

    if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 4 ]); then
        print_warning "PHP 8.4+ is required. You have PHP $PHP_VERSION"
        read -p "Would you like to install PHP 8.4 now? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            install_php
        else
            print_error "PHP 8.4+ is required. Exiting."
            exit 1
        fi
    else
        print_success "PHP version is compatible ($PHP_VERSION)"
    fi
else
    print_warning "PHP is not installed"
    read -p "Would you like to install PHP 8.4 now? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        install_php
    else
        print_error "PHP is required. Please install PHP 8.4+ and re-run this script."
        exit 1
    fi
fi

print_success "PHP is ready!"

# Step 2: Check/Install Composer
print_header "Step 2: Checking/Installing Composer"

install_composer() {
    print_warning "Installing Composer..."
    echo "Downloading Composer installer..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
    print_success "Composer installed."
}

if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | cut -d ' ' -f 3)
    print_success "Composer is installed ($COMPOSER_VERSION)"
else
    print_warning "Composer is not installed"
    read -p "Would you like to install Composer now? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        if is_root; then
            install_composer
        else
            print_warning "Root privileges required to install Composer to /usr/local/bin"
            print_warning "Please run this script with sudo, or install Composer manually."
            exit 1
        fi
    else
        print_error "Composer is required. Please install Composer and re-run this script."
        exit 1
    fi
fi

# Step 3: Check/Install Node.js (Optional)
print_header "Step 3: Checking Node.js (Optional)"

if command -v node &> /dev/null; then
    NODE_VERSION=$(node -v)
    print_success "Node.js is installed ($NODE_VERSION)"
else
    print_warning "Node.js is not installed"
    echo "Note: Node.js is only needed for frontend asset compilation."
    echo "You can skip this if you only need the backend."
    read -p "Would you like to install Node.js now? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        pkg_mgr=$(detect_package_manager)
        case "$pkg_mgr" in
            apt-get)
                if is_root; then
                    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
                    apt-get install -y nodejs
                else
                    print_warning "Please run: curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash - && sudo apt-get install -y nodejs"
                fi
                ;;
            *)
                print_warning "Please install Node.js manually from https://nodejs.org/"
                ;;
        esac
    else
        print_warning "Node.js skipped. Run 'npm install' later if needed."
    fi
fi

# Step 4: Install Composer dependencies
print_header "Step 4: Installing Composer Dependencies"
if [ -d "vendor" ]; then
    print_warning "Vendor directory exists. Re-installing dependencies..."
    composer install --no-interaction
else
    composer install --no-interaction
fi
print_success "Composer dependencies installed"

# Step 5: Install NPM dependencies (if Node.js available)
print_header "Step 5: Installing NPM Dependencies (Optional)"
if command -v npm &> /dev/null && [ -f "package.json" ]; then
    print_progress "Installing NPM dependencies..."
    npm install
    print_success "NPM dependencies installed"
else
    print_warning "Node.js not available or package.json not found. Skipping."
fi

# Step 6: Setup Environment File
print_header "Step 6: Setting Up Environment File"
if [ -f ".env" ]; then
    print_warning ".env file already exists. Skipping..."
else
    if [ -f ".env.example" ]; then
        cp .env.example .env
        print_success ".env file created from .env.example"
    else
        print_error ".env.example not found!"
        exit 1
    fi
fi

# Step 7: Generate Application Key
print_header "Step 7: Generating Application Key"
APP_KEY_VALUE=$(grep "^APP_KEY=" .env | cut -d '=' -f 2)

if [ -z "$APP_KEY_VALUE" ] || [ "$APP_KEY_VALUE" = "" ]; then
    php artisan key:generate --force
    print_success "Application key generated"
else
    print_warning "Application key already set. Skipping..."
fi

# Step 8: Setup Database
print_header "Step 8: Setting Up Database"
echo "Note: migrate:fresh will automatically create the SQLite database file"
echo ""

# Verify database configuration
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f 2)

case "$DB_CONNECTION" in
    sqlite)
        print_success "SQLite database configured"
        print_progress "WAL mode enabled in config/database.php"
        ;;
    mysql|pgsql|sqlsrv)
        print_warning "$DB_CONNECTION database detected."
        echo ""
        echo "Please ensure the following:"
        echo "  - Database server is running"
        echo "  - Database exists and is accessible"
        echo "  - DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD are set in .env"
        echo ""
        read -p "Is your database configured and ready? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            print_error "Please configure your database in .env and re-run this script."
            exit 1
        fi
        ;;
    *)
        print_error "Unknown database connection: $DB_CONNECTION"
        exit 1
        ;;
esac

# Step 9: Run Migrations and Seeders
print_header "Step 9: Running Migrations and Seeders"
echo "Running fresh migrations with seeders..."
migrateOutput=$(php artisan migrate:fresh --seed --force 2>&1)
migrateExitCode=$?

if [ $migrateExitCode -ne 0 ]; then
    print_error "Migration or seeding failed!"
    echo "$migrateOutput"
    exit 1
fi

# Show last few lines of success
echo "$migrateOutput" | tail -n 5
print_success "Database migrations and seeders completed successfully"

# Final Summary
print_header "Setup Complete!"

echo -e "${GREEN}Med-Que is ready to run!${NC}"
echo ""
echo "To start the development server:"
echo -e "  ${YELLOW}php artisan serve${NC}"
echo ""
echo "Then access the application at:"
echo -e "  ${BLUE}http://127.0.0.1:8000${NC}"
echo ""
echo "Default login credentials:"
echo -e "  Email:    ${YELLOW}admin@example.com${NC}"
echo -e "  Password: ${YELLOW}password${NC}"
echo ""
echo "Additional demo accounts:"
echo -e "  Cashier:      ${YELLOW}cashier@example.com${NC} / ${YELLOW}cashier123${NC}"
echo -e "  Head Cashier: ${YELLOW}headcashier@example.com${NC} / ${YELLOW}head123${NC}"
echo -e "  Doctor:       ${YELLOW}doctor@example.com${NC} / ${YELLOW}doctor123${NC}"
echo -e "  Receptionist: ${YELLOW}receptionist@example.com${NC} / ${YELLOW}receptionist123${NC}"
echo ""
echo -e "${YELLOW}Note: Change the default passwords after first login!${NC}"
echo ""
