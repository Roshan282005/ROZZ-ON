# ℝ𝕀ℤℤ - Dynamic Website Deployment Guide

## Platform-Specific Deployment Instructions

### 1. Shared Hosting (cPanel/Plesk)

**Steps:**
1. **Upload Files**: Use FTP/SFTP or file manager to upload all files to your hosting account
2. **Create Database**:
   - Go to MySQL Databases in cPanel
   - Create database: `rizz_db`
   - Create user and assign to database
   - Note credentials for configuration

3. **Configure Database**:
   - Edit `login/config.php` with your hosting database credentials
   - Or set environment variables if your host supports them

4. **Run Setup**:
   - Access `login/setup-database.php` via browser to create tables
   - Or use phpMyAdmin to import the SQL schema manually

5. **Configure .htaccess** (Apache):
   ```apache
   # Force HTTPS
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   
   # Security headers
   Header always set X-Content-Type-Options nosniff
   Header always set X-Frame-Options DENY
   ```

### 2. VPS/Dedicated Server

**Ubuntu/Debian with LEMP Stack:**

```bash
# Install dependencies
sudo apt update
sudo apt install nginx mysql-server php-fpm php-mysql

# Create database
sudo mysql -e "CREATE DATABASE rizz_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'rizz_user'@'localhost' IDENTIFIED BY 'secure_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON rizz_db.* TO 'rizz_user'@'localhost';"

# Configure Nginx
sudo nano /etc/nginx/sites-available/rizz

# Nginx configuration
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/rizz;
    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
}

# Enable site
sudo ln -s /etc/nginx/sites-available/rizz /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# Deploy files
sudo cp -r . /var/www/rizz/
sudo chown -R www-data:www-data /var/www/rizz/
```

### 3. Cloud Platforms

**AWS (EC2 + RDS):**
1. Launch EC2 instance with LAMP/LEMP stack
2. Create RDS MySQL instance
3. Update `config.php` with RDS endpoint and credentials
4. Deploy files to EC2
5. Configure security groups for database access

**Google Cloud Platform:**
1. Create Compute Engine instance
2. Set up Cloud SQL MySQL instance
3. Configure database connection using Cloud SQL proxy
4. Deploy application files

**Azure:**
1. Create App Service with PHP runtime
2. Set up Azure Database for MySQL
3. Configure application settings for database connection
4. Deploy via FTP or Git

### 4. Platform-as-a-Service (PaaS)

**Heroku:**
1. Create `Procfile`:
   ```
   web: vendor/bin/heroku-php-apache2 ./
   ```

2. Set environment variables:
   ```bash
   heroku config:set DB_HOST=your_mysql_host
   heroku config:set DB_USER=your_user
   heroku config:set DB_PASS=your_password
   heroku config:set DB_NAME=your_database
   ```

3. Deploy via Git:
   ```bash
   git push heroku main
   ```

**Vercel** (for frontend + API routes):
- Requires separate database hosting
- Use serverless functions for PHP endpoints

### 5. Docker Deployment

**Docker Compose:**
```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "80:80"
    volumes:
      - .:/var/www/html
    environment:
      - DB_HOST=db
      - DB_USER=rizz_user
      - DB_PASS=secure_password
      - DB_NAME=rizz_db

  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=root_password
      - MYSQL_DATABASE=rizz_db
      - MYSQL_USER=rizz_user
      - MYSQL_PASSWORD=secure_password
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

### 6. Database Setup for All Platforms

**Manual Setup:**
1. Create database and user
2. Run setup script:
   ```bash
   php login/setup-database.php
   ```
3. Or import schema manually via phpMyAdmin/Adminer

**Environment Variables:**
Set these in your hosting control panel or server configuration:
```
DB_HOST=your_database_host
DB_USER=your_database_user
DB_PASS=your_database_password
DB_NAME=your_database_name
APP_URL=your_website_url
```

### 7. Post-Deployment Checklist

1. **Test Functionality**:
   - Register new user
   - Test login with correct credentials
   - Test failed login attempts (should track and lockout)
   - Verify dashboard security information displays correctly

2. **Security Verification**:
   - HTTPS is enforced
   - Database credentials are secure
   - File permissions are correct
   - Error reporting is disabled in production

3. **Performance**:
   - Enable PHP opcache
   - Configure appropriate caching headers
   - Monitor database performance

### 8. Monitoring and Maintenance

**Log Monitoring:**
- Check PHP error logs regularly
- Monitor database connection errors
- Track failed login attempts for security analysis

**Backup Strategy:**
- Regular database backups
- File system backups
- Test restoration process periodically

### Support

For platform-specific issues, consult:
- Your hosting provider's documentation
- Platform-specific deployment guides
- The `DEPLOYMENT.md` file for general guidance

The application is designed to be platform-agnostic and should work on any standard PHP/MySQL hosting environment.
