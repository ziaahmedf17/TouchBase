# build-deploy.ps1 — Builds Hostinger deployment package for TouchBase
# Output: C:\Users\HP\Downloads\touchbase-hostinger.zip
# Upload zip contents to: public_html/touchbase/ on Hostinger

$ProjectRoot = $PSScriptRoot
$DeployDir   = "C:\Users\HP\Downloads\touchbase-deploy"
$ZipPath     = "C:\Users\HP\Downloads\touchbase-hostinger.zip"

Write-Host "Cleaning previous build..."
if (Test-Path $DeployDir) { Remove-Item $DeployDir -Recurse -Force }
if (Test-Path $ZipPath)   { Remove-Item $ZipPath -Force }
New-Item -ItemType Directory -Path $DeployDir | Out-Null

# ── Copy project files (exclude dev/runtime artifacts) ──────────────────────
$ExcludedNames = @(
    '.git', '.env', 'node_modules', 'tests',
    'build-deploy.ps1', '.phpunit.result.cache',
    'phpunit.xml', '.editorconfig'
)

Write-Host "Copying project files..."
Get-ChildItem -Path $ProjectRoot | Where-Object { $_.Name -notin $ExcludedNames } | ForEach-Object {
    Copy-Item $_.FullName -Destination $DeployDir -Recurse -Force
}

# ── Copy vendor from Laragon (composer not in PATH) ──────────────────────────
$LaravelVendor = "C:\laragon\www\TouchBaseNew\vendor"
if (-not (Test-Path "$DeployDir\vendor") -and (Test-Path $LaravelVendor)) {
    Write-Host "Copying vendor from Laragon (this may take a moment)..."
    Copy-Item $LaravelVendor -Destination "$DeployDir\vendor" -Recurse -Force
}

# ── Wipe runtime-generated storage files ────────────────────────────────────
$WipePaths = @(
    "$DeployDir\storage\framework\cache\data",
    "$DeployDir\storage\framework\views",
    "$DeployDir\storage\framework\sessions",
    "$DeployDir\storage\logs"
)
foreach ($p in $WipePaths) {
    if (Test-Path $p) { Remove-Item "$p\*" -Recurse -Force -ErrorAction SilentlyContinue }
    New-Item -ItemType Directory -Force -Path $p | Out-Null
    Set-Content -Path "$p\.gitkeep" -Value "" -Encoding utf8
}

# Keep storage/app/public for symlink target
New-Item -ItemType Directory -Force -Path "$DeployDir\storage\app\public" | Out-Null

# ── Root index.php (basePath = project root, not public/) ───────────────────
Write-Host "Writing root index.php..."
Set-Content -Path "$DeployDir\index.php" -Encoding utf8 -Value @'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = __DIR__;

if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
'@

# ── Root .htaccess (rewrites assets to public/, PHP to index.php) ───────────
Write-Host "Writing root .htaccess..."
Set-Content -Path "$DeployDir\.htaccess" -Encoding utf8 -Value @'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Serve static assets directly from public/ subfolder
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(css|js|images|storage|favicon\.svg|robots\.txt)(.*)$ public/$1$2 [L,QSA]

    # Redirect trailing slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send all requests to front controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
'@

# ── Create ZIP ───────────────────────────────────────────────────────────────
Write-Host "Creating zip..."
Compress-Archive -Path "$DeployDir\*" -DestinationPath $ZipPath -Force

$ZipSize = [math]::Round((Get-Item $ZipPath).Length / 1MB, 1)
Write-Host ""
Write-Host "Done! $ZipPath ($ZipSize MB)"
Write-Host ""
Write-Host "=== POST-DEPLOY SSH COMMANDS ==="
Write-Host "cd ~/public_html/touchbase"
Write-Host "php artisan migrate --force"
Write-Host "php artisan db:seed --class=RolesPermissionsSeeder --force"
Write-Host "php artisan storage:link"
Write-Host "php artisan config:cache"
Write-Host "php artisan route:cache"
Write-Host "php artisan view:cache"
Write-Host "chmod -R 775 storage bootstrap/cache"
