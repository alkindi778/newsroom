# ===================================
# Newsroom Optimization Setup Script
# ===================================

Write-Host "Starting optimization setup..." -ForegroundColor Green
Write-Host ""

# Navigate to Backend folder
Set-Location "c:\xampp\htdocs\newsroom\backend"

Write-Host "Step 1: Installing Composer Dependencies..." -ForegroundColor Yellow
composer install
Write-Host "Dependencies installed successfully" -ForegroundColor Green
Write-Host ""

Write-Host "Step 2: Clearing Cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
Write-Host "Cache cleared successfully" -ForegroundColor Green
Write-Host ""

Write-Host "Step 3: Creating Storage Link..." -ForegroundColor Yellow
php artisan storage:link
Write-Host "Storage link created successfully" -ForegroundColor Green
Write-Host ""

Write-Host "Step 4: Convert existing images to WebP (optional)..." -ForegroundColor Yellow
$convertImages = Read-Host "Do you want to convert existing images now? (y/n)"
if ($convertImages -eq "y") {
    php artisan images:convert-webp --quality=85
    Write-Host "Images converted successfully" -ForegroundColor Green
} else {
    Write-Host "Skipping image conversion" -ForegroundColor Gray
}
Write-Host ""

Write-Host "Setup completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "Important Links:" -ForegroundColor Cyan
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Sitemap:" -ForegroundColor White
Write-Host "   http://192.168.1.101:3000/sitemap.xml" -ForegroundColor Blue
Write-Host ""
Write-Host "Robots.txt:" -ForegroundColor White
Write-Host "   http://192.168.1.101:3000/robots.txt" -ForegroundColor Blue
Write-Host ""
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "Useful Commands:" -ForegroundColor Cyan
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "# Convert images:" -ForegroundColor White
Write-Host "  php artisan images:convert-webp" -ForegroundColor Gray
Write-Host ""
Write-Host "# View Sitemap:" -ForegroundColor White
Write-Host "  curl http://192.168.1.101:3000/sitemap.xml" -ForegroundColor Gray
Write-Host ""
Write-Host "Read OPTIMIZATION_GUIDE.md for more details" -ForegroundColor Yellow
Write-Host ""
