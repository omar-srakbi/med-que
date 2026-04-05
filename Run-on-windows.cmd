@echo off

echo %GREEN%Med-Que is ready to run!%NC%
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
php artisan serve