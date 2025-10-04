#!/bin/bash
# Bash script to quickly start HikeThere Docker environment (for Mac/Linux)

echo "🐳 Starting HikeThere Complete Docker Environment..."
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Check if .env file exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.docker..."
    cp .env.docker .env
    echo "⚠️  Please edit .env and add your API keys!"
    echo ""
fi

# Check if APP_KEY is set
if grep -q "APP_KEY=base64:your_generated_key_here" .env || ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating APP_KEY..."
    docker-compose run --rm app php artisan key:generate
    echo ""
fi

# Build and start services
echo "🏗️  Building and starting services..."
docker-compose up -d --build

echo ""
echo "⏳ Waiting for services to be healthy..."
sleep 10

# Check status
echo ""
echo "📊 Service Status:"
docker-compose ps

echo ""
echo "✅ HikeThere Docker Environment is starting!"
echo ""
echo "🌐 Access your application at: http://localhost:8080"
echo "🤖 ML Service available at: http://localhost:8001"
echo "🗄️  MySQL running on: localhost:3306"
echo ""
echo "📋 Useful commands:"
echo "   View logs:        docker-compose logs -f"
echo "   Stop services:    docker-compose down"
echo "   Run migrations:   docker-compose exec app php artisan migrate"
echo "   Access shell:     docker-compose exec app bash"
echo ""
echo "📖 Read DOCKER_SETUP.md for complete documentation"
