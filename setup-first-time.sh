#!/bin/bash

###############################################################################
# Med-Que First-Time Setup Script
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

# Start setup
print_header "Med-Que First-Time Setup"

# Step 1: Check PHP version
print_header "Step 1: Checking PHP Version"
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1 | cut -d ' ' -f 2 | cut -d '.' -f 1,2)
    PHP_MAJOR=$(echo $PHP_VERSION | cut -d '.' -f 1)
    PHP_MINOR=$(echo $PHP_VERSION | cut -d '.' -f 2)
    
    echo "Current PHP version: $PHP_VERSION"
    
    if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 4 ]); then
        print_error "PHP 8.4+ is required. You have PHP $PHP_VERSION"
        echo ""
        echo "To install PHP 8.4 on Debian/Ubuntu, run:"
        echo "  sudo apt-get update"
        echo "  sudo apt-get install -y ca-certificates curl gnupg"
        echo "  sudo curl -sSLo /usr/share/keyrings/deb-php.gpg https://packages.sury.org/php/apt.gpg"
        echo "  echo 'deb [signed-by=/usr/share/keyrings/deb-php.gpg] https://packages.sury.org/php/ bookworm main' | sudo tee /etc/apt/sources.list.d/php.list"
        echo "  sudo apt-get update"
        echo "  sudo apt-get install -y php8.4 php8.4-cli php8.4-common php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-sqlite3 php8.4-mysql php8.4-bcmath"
        exit 1
    fi
    
    print_success "PHP version is compatible ($PHP_VERSION)"
else
    print_error "PHP is not installed"
    exit 1
fi

# Step 2: Check Composer
print_header "Step 2: Checking Composer"
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | cut -d ' ' -f 3)
    print_success "Composer is installed ($COMPOSER_VERSION)"
else
    print_error "Composer is not installed"
    echo "Install Composer from: https://getcomposer.org/download/"
    exit 1
fi

# Step 3: Install Composer dependencies
print_header "Step 3: Installing Composer Dependencies"
if [ -d "vendor" ]; then
    print_warning "Vendor directory exists. Re-installing dependencies..."
    composer install --no-interaction
else
    composer install --no-interaction
fi
print_success "Composer dependencies installed"

# Step 4: Setup Environment File
print_header "Step 4: Setting Up Environment File"
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

# Step 5: Generate Application Key
print_header "Step 5: Generating Application Key"
APP_KEY_VALUE=$(grep "^APP_KEY=" .env | cut -d '=' -f 2)

if [ -z "$APP_KEY_VALUE" ] || [ "$APP_KEY_VALUE" = "" ]; then
    php artisan key:generate --force
    print_success "Application key generated"
else
    print_warning "Application key already set. Skipping..."
fi

# Step 6: Setup Database
print_header "Step 6: Setting Up Database"
echo "Note: migrate:fresh will automatically create the SQLite database file"
echo ""

# Verify database configuration
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f 2)

case "$DB_CONNECTION" in
    sqlite)
        print_success "SQLite database configured"
        ;;
    mysql|pgsql|sqlsrv)
        print_warning "$DB_CONNECTION database detected. Please ensure database is created and configured manually."
        ;;
    *)
        print_error "Unknown database connection: $DB_CONNECTION"
        exit 1
        ;;
esac

# Step 7: Run Migrations and Seeders
print_header "Step 7: Running Migrations and Seeders"
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
