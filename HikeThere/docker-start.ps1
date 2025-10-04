# Windows PowerShell script to quickly start HikeThere Docker environment

Write-Host "🐳 Starting HikeThere Complete Docker Environment..." -ForegroundColor Cyan
Write-Host ""

# Check if Docker is running
$dockerRunning = docker info 2>&1 | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Docker is not running. Please start Docker Desktop first." -ForegroundColor Red
    exit 1
}

# Check if .env file exists
if (-not (Test-Path ".env")) {
    Write-Host "📝 Creating .env file from .env.docker..." -ForegroundColor Yellow
    Copy-Item ".env.docker" ".env"
    Write-Host "⚠️  Please edit .env and add your API keys!" -ForegroundColor Yellow
    Write-Host ""
}

# Check if APP_KEY is set
$envContent = Get-Content ".env" -Raw
if ($envContent -match "APP_KEY=base64:your_generated_key_here" -or $envContent -notmatch "APP_KEY=base64:") {
    Write-Host "🔑 Generating APP_KEY..." -ForegroundColor Yellow
    docker-compose run --rm app php artisan key:generate
    Write-Host ""
}

# Build and start services
Write-Host "🏗️  Building and starting services..." -ForegroundColor Cyan
docker-compose up -d --build

Write-Host ""
Write-Host "⏳ Waiting for services to be healthy..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Check status
Write-Host ""
Write-Host "📊 Service Status:" -ForegroundColor Cyan
docker-compose ps

Write-Host ""
Write-Host "✅ HikeThere Docker Environment is starting!" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Access your application at: http://localhost:8080" -ForegroundColor Cyan
Write-Host "🤖 ML Service available at: http://localhost:8001" -ForegroundColor Cyan
Write-Host "🗄️  MySQL running on: localhost:3306" -ForegroundColor Cyan
Write-Host ""
Write-Host "📋 Useful commands:" -ForegroundColor Yellow
Write-Host "   View logs:        docker-compose logs -f" -ForegroundColor White
Write-Host "   Stop services:    docker-compose down" -ForegroundColor White
Write-Host "   Run migrations:   docker-compose exec app php artisan migrate" -ForegroundColor White
Write-Host "   Access shell:     docker-compose exec app bash" -ForegroundColor White
Write-Host ""
Write-Host "📖 Read DOCKER_SETUP.md for complete documentation" -ForegroundColor Cyan
