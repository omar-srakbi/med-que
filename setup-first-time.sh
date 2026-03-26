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
if grep -q "^APP_KEY=" .env && ! grep -q "^APP_KEY=base64:" .env 2>/dev/null || grep -q "^APP_KEY=$" .env 2>/dev/null; then
    php artisan key:generate
    print_success "Application key generated"
else
    print_warning "Application key already set. Skipping..."
fi

# Step 6: Setup Database
print_header "Step 6: Setting Up Database"

# Get database configuration from .env
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f 2)

case "$DB_CONNECTION" in
    sqlite)
        DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f 2)
        
        # If DB_DATABASE is empty or :memory:, use default
        if [ -z "$DB_DATABASE" ] || [ "$DB_DATABASE" = ":memory:" ]; then
            DB_DATABASE="database/database.sqlite"
            echo "DB_DATABASE=$DB_DATABASE" >> .env
        fi
        
        # Create database directory if it doesn't exist
        mkdir -p "$(dirname "$DB_DATABASE")"
        
        # Create database file if it doesn't exist
        if [ ! -f "$DB_DATABASE" ]; then
            touch "$DB_DATABASE"
            print_success "SQLite database created at: $DB_DATABASE"
        else
            print_warning "SQLite database already exists at: $DB_DATABASE"
        fi
        ;;
    mysql|pgsql|sqlsrv)
        print_warning "$DB_CONNECTION database detected. Please ensure database is created and configured manually."
        ;;
    *)
        print_error "Unknown database connection: $DB_CONNECTION"
        exit 1
        ;;
esac

# Step 7: Run Migrations
print_header "Step 7: Running Database Migrations"
php artisan migrate --force
print_success "Database migrations completed"

# Step 8: Run Seeders
print_header "Step 8: Running Database Seeders"
php artisan db:seed --force
print_success "Database seeders completed"

# Step 9: Fix Department Ticket Prefixes (Prevent Duplicate Ticket Numbers)
print_header "Step 9: Setting Unique Department Ticket Prefixes"
php artisan tinker --execute="
\$depts = [
    1 => ['prefix' => 'CLN'],
    2 => ['prefix' => 'KID'],
    3 => ['prefix' => 'LAB'],
    4 => ['prefix' => 'RAD'],
    5 => ['prefix' => 'MRI'],
    6 => ['prefix' => 'PHY'],
];
foreach(\$depts as \$id => \$data) {
    App\Models\Department::find(\$id)?->update(\$data);
}
echo 'Done';
"
print_success "Department ticket prefixes configured"

# Step 10: Clear Caches
print_header "Step 10: Clearing Caches"
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
print_success "Caches cleared"

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
echo -e "${YELLOW}Note: Change the default password after first login!${NC}"
echo ""
