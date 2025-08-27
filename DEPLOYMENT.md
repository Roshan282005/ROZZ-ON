# ℝ𝕀ℤℤ - Deployment Guide

## Prerequisites
- Web server with PHP 7.4+ and MySQL/MariaDB
- MySQL database server
- SSL certificate (recommended for production)

## Database Setup

1. **Create Database User**:
   ```sql
   CREATE USER 'rizz_user'@'localhost' IDENTIFIED BY 'secure_password';
   CREATE DATABASE rizz_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   GRANT ALL PRIVILEGES ON rizz_db.* TO 'rizz_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. **Import Schema**:
   ```bash
   php login/setup-database.php
   ```

## Environment Configuration

Create a `.env` file in the project root or set environment variables:

```bash
# Database Configuration
DB_HOST=localhost
DB_USER=rizz_user
DB_PASS=secure_password
DB_NAME=rizz_db
DB_PORT=3306

# Application Configuration
APP_URL=https://yourdomain.com
APP_TIMEZONE=UTC
REQUIRE_HTTPS=true
```

## Web Server Configuration

### Apache (.htaccess)
```apache
RewriteEngine On

# Force HTTPS in production
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

### Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    root /path/to/rizz;
    index index.html index.php;
    
    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection "1; mode=block";
    
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## Security Considerations

1. **File Permissions**:
   ```bash
   chmod 644 *.php *.html *.css *.js
   chmod 755 login/ uploads/
   ```

2. **Database Security**:
   - Use strong, unique passwords
   - Restrict database user privileges
   - Enable MySQL SSL connections if possible

3. **PHP Security**:
   - Disable error display in production
   - Set appropriate file upload limits
   - Enable PHP opcache for performance

## Monitoring

1. **Error Logging**:
   ```php
   // In config.php
   ini_set('log_errors', 1);
   ini_set('error_log', '/path/to/php-error.log');
   ```

2. **Database Monitoring**:
   - Monitor login_attempts table for suspicious activity
   - Set up alerts for multiple failed attempts

## Backup Strategy

1. **Database Backups**:
   ```bash
   # Daily backup script
   mysqldump -u rizz_user -p rizz_db > /backups/rizz_db_$(date +%Y%m%d).sql
   ```

2. **File Backups**:
   ```bash
   tar -czf /backups/rizz_files_$(date +%Y%m%d).tar.gz /path/to/rizz
   ```

## Performance Optimization

1. **Database Indexing**:
   - Ensure proper indexes on frequently queried columns
   - Regularly optimize tables

2. **Caching**:
   - Enable PHP opcache
   - Consider implementing Redis/Memcached for session storage

## Troubleshooting

Common issues and solutions:

1. **Database Connection Issues**:
   - Verify database credentials in config.php
   - Check MySQL server is running
   - Ensure database user has proper permissions

2. **File Permission Issues**:
   - Verify web server has read access to files
   - Check write permissions for upload directories

3. **HTTPS Issues**:
   - Verify SSL certificate is valid
   - Check web server SSL configuration
```

## Updates

To update the application:

1. Backup database and files
2. Deploy new code
3. Run database migrations if needed
4. Test thoroughly
5. Clear any caches
