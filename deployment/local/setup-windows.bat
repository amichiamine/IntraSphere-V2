@echo off
title IntraSphere - Installation Windows
echo.
echo ========================================
echo   IntraSphere - Installation Windows
echo ========================================
echo.

echo [1/4] Verification Node.js...
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Node.js non trouve. Installez Node.js 18+ depuis nodejs.org
    pause
    exit /b 1
)
echo ✅ Node.js detecte

echo.
echo [2/4] Installation des dependances...
call npm install
if %errorlevel% neq 0 (
    echo ❌ Erreur lors de l'installation
    pause
    exit /b 1
)

echo.
echo [3/4] Configuration base de donnees...
call npm run db:push
if %errorlevel% neq 0 (
    echo ⚠️ Attention: Erreur base de donnees (non critique)
)

echo.
echo [4/4] Demarrage de l'application...
echo ✅ Installation terminee!
echo.
echo Ouvrez votre navigateur sur: http://localhost:5173
echo Pour arreter: Ctrl+C
echo.
call npm run dev

pause