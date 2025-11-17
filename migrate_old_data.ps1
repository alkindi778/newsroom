# سكريبت دمج البيانات القديمة
# Migration Script for Old Database

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   دمج البيانات من القاعدة القديمة" -ForegroundColor Cyan
Write-Host "   Old Database Migration Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# المتغيرات
$projectPath = "c:\xampp\htdocs\newsroom"
$backendPath = "$projectPath\backend"
$oldDbFile = "$projectPath\adenstc_db.sql\adenstc_db.sql"
$oldDbName = "adenstc_db"

# التحقق من وجود ملف SQL القديم
if (-not (Test-Path $oldDbFile)) {
    Write-Host "❌ خطأ: ملف قاعدة البيانات القديمة غير موجود!" -ForegroundColor Red
    Write-Host "   المسار المتوقع: $oldDbFile" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ تم العثور على ملف قاعدة البيانات القديمة" -ForegroundColor Green
Write-Host ""

# سؤال المستخدم عن بيانات الاتصال
Write-Host "📝 إعدادات قاعدة البيانات:" -ForegroundColor Yellow
$dbUsername = Read-Host "اسم المستخدم لـ MySQL (افتراضي: root)"
if ([string]::IsNullOrWhiteSpace($dbUsername)) {
    $dbUsername = "root"
}

$dbPassword = Read-Host "كلمة المرور لـ MySQL (اتركه فارغاً إذا لم يكن هناك كلمة مرور)" -AsSecureString
$dbPasswordPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($dbPassword))

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "الخطوة 1: إنشاء قاعدة البيانات القديمة" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# إنشاء قاعدة البيانات
$createDbCommand = "CREATE DATABASE IF NOT EXISTS $oldDbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if ([string]::IsNullOrWhiteSpace($dbPasswordPlain)) {
    $mysqlCreateCmd = "mysql -u $dbUsername -e `"$createDbCommand`""
} else {
    $mysqlCreateCmd = "mysql -u $dbUsername -p$dbPasswordPlain -e `"$createDbCommand`""
}

Write-Host "🔄 إنشاء قاعدة البيانات $oldDbName..." -ForegroundColor Yellow

try {
    Invoke-Expression $mysqlCreateCmd
    Write-Host "✅ تم إنشاء قاعدة البيانات بنجاح" -ForegroundColor Green
} catch {
    Write-Host "❌ فشل إنشاء قاعدة البيانات: $_" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "الخطوة 2: استيراد البيانات القديمة" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# استيراد البيانات
if ([string]::IsNullOrWhiteSpace($dbPasswordPlain)) {
    $mysqlImportCmd = "mysql -u $dbUsername $oldDbName < `"$oldDbFile`""
} else {
    $mysqlImportCmd = "mysql -u $dbUsername -p$dbPasswordPlain $oldDbName < `"$oldDbFile`""
}

Write-Host "🔄 استيراد البيانات... (قد يستغرق بضع دقائق)" -ForegroundColor Yellow

try {
    cmd /c $mysqlImportCmd
    Write-Host "✅ تم استيراد البيانات بنجاح" -ForegroundColor Green
} catch {
    Write-Host "❌ فشل استيراد البيانات: $_" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "الخطوة 3: تحديث ملف .env" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

$envFile = "$backendPath\.env"

if (Test-Path $envFile) {
    Write-Host "🔄 إضافة إعدادات القاعدة القديمة إلى .env..." -ForegroundColor Yellow
    
    # قراءة محتوى .env
    $envContent = Get-Content $envFile -Raw
    
    # التحقق من عدم وجود الإعدادات مسبقاً
    if ($envContent -notmatch "OLD_DB_HOST") {
        $oldDbConfig = @"

# Old Database Configuration
OLD_DB_HOST=127.0.0.1
OLD_DB_PORT=3306
OLD_DB_DATABASE=$oldDbName
OLD_DB_USERNAME=$dbUsername
OLD_DB_PASSWORD=$dbPasswordPlain
"@
        Add-Content -Path $envFile -Value $oldDbConfig
        Write-Host "✅ تم تحديث ملف .env" -ForegroundColor Green
    } else {
        Write-Host "ℹ️  إعدادات القاعدة القديمة موجودة بالفعل في .env" -ForegroundColor Yellow
    }
} else {
    Write-Host "⚠️  تحذير: ملف .env غير موجود!" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "الخطوة 4: إنشاء مجلد الصور القديمة" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

$oldPhotosPath = "$backendPath\storage\app\public\old_photos"

if (-not (Test-Path $oldPhotosPath)) {
    New-Item -ItemType Directory -Path $oldPhotosPath -Force | Out-Null
    Write-Host "✅ تم إنشاء مجلد: $oldPhotosPath" -ForegroundColor Green
} else {
    Write-Host "ℹ️  مجلد الصور موجود بالفعل" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "الخطوة 5: تنفيذ عملية الدمج" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

Write-Host ""
Write-Host "اختر نوع الدمج:" -ForegroundColor Yellow
Write-Host "1. دمج كل شيء (الكتاب + مقالات الرأي + الأخبار)" -ForegroundColor White
Write-Host "2. دمج الكتاب فقط" -ForegroundColor White
Write-Host "3. دمج مقالات الرأي فقط" -ForegroundColor White
Write-Host "4. دمج الأخبار فقط" -ForegroundColor White
Write-Host "5. تخطي عملية الدمج (يمكنك تشغيلها لاحقاً)" -ForegroundColor White

$choice = Read-Host "اختيارك (1-5)"

Set-Location $backendPath

switch ($choice) {
    "1" {
        Write-Host "🔄 بدء دمج جميع البيانات..." -ForegroundColor Yellow
        php artisan migrate:old-database
    }
    "2" {
        Write-Host "🔄 بدء دمج الكتاب..." -ForegroundColor Yellow
        php artisan migrate:old-database --step=writers
    }
    "3" {
        Write-Host "🔄 بدء دمج مقالات الرأي..." -ForegroundColor Yellow
        php artisan migrate:old-database --step=opinions
    }
    "4" {
        Write-Host "🔄 بدء دمج الأخبار..." -ForegroundColor Yellow
        php artisan migrate:old-database --step=articles
    }
    "5" {
        Write-Host "⏭️  تم تخطي عملية الدمج" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "يمكنك تشغيل الدمج لاحقاً باستخدام:" -ForegroundColor Cyan
        Write-Host "  cd $backendPath" -ForegroundColor White
        Write-Host "  php artisan migrate:old-database" -ForegroundColor White
    }
    default {
        Write-Host "❌ اختيار غير صحيح" -ForegroundColor Red
        exit 1
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ اكتملت العملية!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "📋 ملاحظات هامة:" -ForegroundColor Yellow
Write-Host "1. إذا كانت لديك صور قديمة، انسخها إلى:" -ForegroundColor White
Write-Host "   $oldPhotosPath" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. للتحقق من نتائج الدمج، استخدم:" -ForegroundColor White
Write-Host "   cd $backendPath" -ForegroundColor Cyan
Write-Host "   php artisan tinker" -ForegroundColor Cyan
Write-Host "   ثم نفذ: App\Models\Writer::count()" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. راجع دليل الدمج الكامل في:" -ForegroundColor White
Write-Host "   $projectPath\MIGRATION_GUIDE.md" -ForegroundColor Cyan
Write-Host ""

Read-Host "اضغط Enter للخروج"
