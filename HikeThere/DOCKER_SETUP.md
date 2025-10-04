# 🐳 Complete Docker Setup for HikeThere

This Docker Compose configuration provides a complete local development environment with all services containerized.

## 📦 Services Included

1. **Laravel Application** (Port 8080)
   - PHP 8.2-FPM + Nginx + Supervisor
   - Hot reload for development
   - Auto-runs migrations on startup

2. **MySQL Database** (Port 3306)
   - MySQL 8.0
   - Persistent data storage
   - Health checks

3. **ML Recommender Service** (Port 8001)
   - Python 3.10 + FastAPI
   - Model artifacts storage
   - Health endpoint

4. **Redis** (Port 6379) - Optional
   - For caching and sessions
   - Persistent storage

## 🚀 Quick Start

### 1. Prerequisites

- Docker Desktop installed (with Docker Compose)
- At least 4GB RAM available for Docker
- Ports 8080, 3306, 8001, 6379 available

### 2. First Time Setup

```bash
# 1. Copy environment file and add your API keys
cp .env.docker .env
# Edit .env with your actual API keys

# 2. Generate application key
docker-compose run --rm app php artisan key:generate

# 3. Build and start all services
docker-compose up -d

# 4. Wait for services to be healthy (check with)
docker-compose ps

# 5. Your app is now running at http://localhost:8080
```

### 3. Daily Development

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f app

# Stop all services
docker-compose down

# Stop and remove volumes (fresh start)
docker-compose down -v
```

## 📋 Common Commands

### Application Management

```bash
# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan tinker

# Install composer dependencies
docker-compose exec app composer install

# Install npm dependencies and build assets
docker-compose exec app npm install
docker-compose exec app npm run build

# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

### Database Management

```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u hikethere_user -phikethere_password hikethere

# Create database backup
docker-compose exec mysql mysqldump -u hikethere_user -phikethere_password hikethere > backup.sql

# Restore database from backup
docker-compose exec -T mysql mysql -u hikethere_user -phikethere_password hikethere < backup.sql

# Reset database (WARNING: Deletes all data)
docker-compose exec app php artisan migrate:fresh --seed
```

### ML Service Management

```bash
# View ML service logs
docker-compose logs -f ml-service

# Restart ML service
docker-compose restart ml-service

# Test ML recommendations
curl http://localhost:8001/health
curl http://localhost:8001/api/recommender/user/1?k=5
```

### Container Management

```bash
# View running containers
docker-compose ps

# View logs for all services
docker-compose logs -f

# View logs for specific service
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f ml-service

# Restart a specific service
docker-compose restart app

# Rebuild a service (after Dockerfile changes)
docker-compose build app
docker-compose up -d app

# Access container shell
docker-compose exec app bash
docker-compose exec mysql bash
docker-compose exec ml-service bash
```

## 🔧 Configuration

### Environment Variables

All environment variables are defined in `docker-compose.yml` under each service's `environment` section.

**Important variables to configure:**

```env
# In .env.docker file:
APP_KEY=base64:your_generated_key_here
GOOGLE_MAPS_API_KEY=your_key
ORS_API_KEY=your_key
OPENWEATHER_API_KEY=your_key
PAYMONGO_PUBLIC_KEY=pk_test_your_key
PAYMONGO_SECRET_KEY=sk_test_your_key
```

### Database Connection

The Laravel app automatically connects to MySQL using these credentials:

- **Host:** `mysql` (service name)
- **Port:** `3306`
- **Database:** `hikethere`
- **Username:** `hikethere_user`
- **Password:** `hikethere_password`

### Service URLs

From within containers, services can communicate using:

- Laravel App: `http://app:8080`
- MySQL: `mysql:3306`
- ML Service: `http://ml-service:8001`
- Redis: `redis:6379`

From your host machine:

- Laravel App: `http://localhost:8080`
- MySQL: `localhost:3306`
- ML Service: `http://localhost:8001`
- Redis: `localhost:6379`

## 📁 Volume Mounts

### Development (Hot Reload)

These directories are mounted for live development:

```yaml
- ./app → /app/app
- ./resources → /app/resources
- ./routes → /app/routes
- ./config → /app/config
- ./database → /app/database
- ./public → /app/public
- ./ml-prototype → /app (ML service)
```

Changes to these files are immediately reflected in the container.

### Persistent Data

These volumes persist data across container restarts:

- `mysql_data` - Database files
- `storage_data` - Laravel storage (uploads, logs)
- `ml_artifacts` - ML model artifacts
- `redis_data` - Redis data

## 🐛 Troubleshooting

### Services Won't Start

```bash
# Check logs for errors
docker-compose logs

# Check if ports are in use
netstat -ano | findstr "8080"
netstat -ano | findstr "3306"

# Rebuild from scratch
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

### Database Connection Failed

```bash
# Wait for MySQL to be healthy
docker-compose ps

# Check MySQL logs
docker-compose logs mysql

# Test connection manually
docker-compose exec app php artisan db:show
```

### Permission Errors

```bash
# Fix storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### ML Service Not Responding

```bash
# Check if service is running
docker-compose ps ml-service

# View logs
docker-compose logs ml-service

# Test health endpoint
curl http://localhost:8001/health

# Restart service
docker-compose restart ml-service
```

### "Port Already in Use" Error

```bash
# Find and stop the process using the port (Windows)
netstat -ano | findstr "8080"
taskkill /PID <process_id> /F

# Or change the port in docker-compose.yml
ports:
  - "8081:8080"  # Use 8081 instead
```

## 🔄 Development Workflow

### 1. Start Your Day

```bash
# Start all services
docker-compose up -d

# Check everything is running
docker-compose ps

# View logs
docker-compose logs -f
```

### 2. Make Changes

- Edit files in your IDE (changes auto-reflect in containers)
- Access app at http://localhost:8080
- View logs in real-time

### 3. Run Migrations/Seeds

```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### 4. Clear Caches When Needed

```bash
docker-compose exec app php artisan optimize:clear
```

### 5. End Your Day

```bash
# Stop services (keeps data)
docker-compose stop

# Or completely shut down
docker-compose down
```

## 🚀 Production Deployment

**Important:** This Docker Compose setup is for **local development only**.

For production, use:
- The `Dockerfile` in root (production-ready)
- Railway deployment (as documented)
- Managed database (Railway MySQL)
- Google Cloud Storage (not local storage)

See `RAILWAY_DEPLOYMENT_GUIDE.md` for production deployment.

## 📊 Resource Usage

Typical resource usage:

- **MySQL:** ~400MB RAM
- **Laravel App:** ~300-500MB RAM
- **ML Service:** ~200-400MB RAM
- **Redis:** ~20-50MB RAM
- **Total:** ~1-1.5GB RAM

Ensure Docker Desktop has at least 4GB RAM allocated.

## ✅ Verification Checklist

After starting services:

- [ ] All containers running: `docker-compose ps`
- [ ] MySQL healthy: `docker-compose exec mysql mysql -u root -phikethere_root_password -e "SELECT 1"`
- [ ] App accessible: Open http://localhost:8080
- [ ] Migrations ran: `docker-compose exec app php artisan migrate:status`
- [ ] ML service responding: `curl http://localhost:8001/health`
- [ ] Storage writable: `docker-compose exec app touch storage/test.txt`

## 🆘 Getting Help

If you encounter issues:

1. Check the troubleshooting section above
2. View logs: `docker-compose logs -f`
3. Check Docker Desktop dashboard
4. Ensure you have latest Docker Desktop version
5. Try rebuilding: `docker-compose build --no-cache`

## 📚 Additional Resources

- Docker Compose Docs: https://docs.docker.com/compose/
- Laravel Docker: https://laravel.com/docs/deployment#docker
- MySQL Docker: https://hub.docker.com/_/mysql
- Redis Docker: https://hub.docker.com/_/redis

---

**Happy Developing! 🐳**

Your complete HikeThere stack is now containerized and ready for local development!
