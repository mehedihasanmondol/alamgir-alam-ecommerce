@echo off
echo ====================================
echo Building Frontend Assets
echo ====================================
echo.

echo Installing npm dependencies...
call npm install

if %errorlevel% neq 0 (
    echo Error: npm install failed!
    pause
    exit /b 1
)

echo.
echo Building assets with Vite...
call npm run build

if %errorlevel% neq 0 (
    echo Error: npm run build failed!
    pause
    exit /b 1
)

echo.
echo ====================================
echo Assets built successfully!
echo ====================================
echo.
echo You can now access the application at:
echo http://127.0.0.1:8000/login
echo.
pause
