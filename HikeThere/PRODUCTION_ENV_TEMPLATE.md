# Production Environment Configuration Template

## Required Environment Variables

### API Keys
```env
# Google Maps API (for directions and geocoding)
GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here

# OpenWeather API (for weather data)
OPENWEATHER_API_KEY=your_openweather_api_key_here

# Image APIs (optional)
UNSPLASH_ACCESS_KEY=your_unsplash_access_key_here
PEXELS_API_KEY=your_pexels_api_key_here
PIXABAY_API_KEY=your_pixabay_api_key_here
```

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=your_database_host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### App Configuration
```env
APP_NAME="HikeThere"
APP_ENV=production
APP_KEY=your_app_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Manila
```

### Security
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIES=true
SESSION_SAME_SITE=strict
```

## Setup Instructions

1. **Copy this template** to your production server
2. **Fill in your actual values** for each environment variable
3. **Save as `.env`** in your project root directory
4. **Restart your web server** after making changes
5. **Verify configuration** by checking the application logs

## API Key Setup

### Google Maps API
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Maps JavaScript API and Directions API
3. Create credentials (API Key)
4. Restrict the key to your domain

### OpenWeather API
1. Go to [OpenWeather](https://openweathermap.org/api)
2. Sign up for a free account
3. Get your API key
4. The free tier includes 1000 calls/day

## Security Notes

- **Never commit** `.env` files to version control
- **Restrict API keys** to your production domain only
- **Use HTTPS** in production for all external API calls
- **Monitor API usage** to stay within free tier limits 