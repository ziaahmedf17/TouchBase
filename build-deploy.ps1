# build-deploy.ps1 -- Builds Hostinger deployment package for TouchBase
# Output: C:\Users\HP\Downloads\touchbase-hostinger.zip
# Upload zip contents to: public_html/touchbase/ on Hostinger

$ProjectRoot = $PSScriptRoot
$DeployDir   = "C:\Users\HP\Downloads\touchbase-deploy"
$ZipPath     = "C:\Users\HP\Downloads\touchbase-hostinger.zip"

Write-Host "Cleaning previous build..."
if (Test-Path $DeployDir) { Remove-Item $DeployDir -Recurse -Force }
if (Test-Path $ZipPath)   { Remove-Item $ZipPath -Force }
New-Item -ItemType Directory -Path $DeployDir | Out-Null

# -- Copy project files (exclude dev/runtime artifacts) --
$ExcludedNames = @(
    '.git', '.env', 'node_modules', 'tests',
    'build-deploy.ps1', '.phpunit.result.cache',
    'phpunit.xml', '.editorconfig'
)

Write-Host "Copying project files..."
Get-ChildItem -Path $ProjectRoot | Where-Object { $_.Name -notin $ExcludedNames } | ForEach-Object {
    Copy-Item $_.FullName -Destination $DeployDir -Recurse -Force
}

# -- Copy Laravel skeleton files from Laragon (not tracked in git) --
$LaravelRoot   = "C:\laragon\www\TouchBaseNew"
$LaravelVendor = "$LaravelRoot\vendor"

$SkeletonItems = @('bootstrap', 'config', 'storage', 'artisan', 'composer.lock')
foreach ($item in $SkeletonItems) {
    $src = "$LaravelRoot\$item"
    if (Test-Path $src) {
        Write-Host "Copying $item from Laragon..."
        Copy-Item $src -Destination "$DeployDir\$item" -Recurse -Force
    } else {
        Write-Warning "$item not found in Laragon project - skipping"
    }
}

if (-not (Test-Path "$DeployDir\vendor") -and (Test-Path $LaravelVendor)) {
    Write-Host "Copying vendor from Laragon (this may take a moment)..."
    Copy-Item $LaravelVendor -Destination "$DeployDir\vendor" -Recurse -Force
}

# -- Wipe runtime-generated storage files --
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

# -- Create ZIP --
Write-Host "Creating zip..."
Compress-Archive -Path "$DeployDir\*" -DestinationPath $ZipPath -Force

$ZipSize = [math]::Round((Get-Item $ZipPath).Length / 1MB, 1)
Write-Host ""
Write-Host "Done! $ZipPath ($ZipSize MB)"
Write-Host ""
Write-Host "=== POST-DEPLOY SSH COMMANDS ==="
Write-Host "cd ~/public_html/touchbase"
Write-Host "php artisan key:generate"
Write-Host "php artisan migrate --force"
Write-Host "php artisan db:seed --class=RolesPermissionsSeeder --force"
Write-Host "php artisan storage:link"
Write-Host "php artisan config:cache"
Write-Host "php artisan route:cache"
Write-Host "php artisan view:cache"
Write-Host "chmod -R 775 storage bootstrap/cache"
