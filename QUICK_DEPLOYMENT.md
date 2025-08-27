# ℝ𝕀ℤℤ - Quick Deployment Process

## For Your Current XAMPP Setup

### Step 1: Create Security Tables
Run this command to create the login_attempts table:
```bash
php login/setup-database.php
```

### Step 2: Test Locally
1. Start XAMPP Apache and MySQL services
2. Open browser and go to: `http://localhost/rizz/login/login.html`
3. Test login functionality with sample users:
   - Email: `user1@example.com`
   - Password: `password` (hashed password in database)

### Step 3: Production Deployment Options

#### Option A: Shared Hosting (cPanel)
1. **Upload Files**: Use FTP to upload all files to public_html
2. **Create Database**: 
   - Go to MySQL Databases in cPanel
   - Create database and user
   - Update `login/config.php` with new credentials
3. **Run Setup**: Access `yoursite.com/login/setup-database.php`

#### Option B: VPS/Cloud Server
```bash
# Install LAMP stack
sudo apt update
sudo apt install apache2 mysql-server php libapache2-mod-php

# Create database
sudo mysql -e "CREATE DATABASE rizz_db;"
sudo mysql -e "CREATE USER 'rizz_user'@'localhost' IDENTIFIED BY 'password';"
sudo mysql -e "GRANT ALL ON rizz_db.* TO 'rizz_user'@'localhost';"

# Deploy files
sudo cp -r . /var/www/html/
sudo chown -R www-data:www-data /var/www/html/
```

#### Option C: Docker (Quickest)
```bash
# Create docker-compose.yml
version: '3.8'
services:
  app:
    image: php:8.1-apache
    ports: ["80:80"]
    volumes: [".:/var/www/html"]
    environment:
      - DB_HOST=db
      - DB_USER=rizz_user
      - DB_PASS=password
      - DB_NAME=rizz_db

  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=rootpass
      - MYSQL_DATABASE=rizz_db
      - MYSQL_USER=rizz_user
      - MYSQL_PASSWORD=password

# Run
docker-compose up -d
```

### Step 4: Configuration
Edit `login/config.php` for production:
```php
$db_config = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'username' => getenv('DB_USER') ?: 'rizz_user',
    'password' => getenv('DB_PASS') ?: 'secure_password',
    'database' => getenv('DB_NAME') ?: 'rizz_db',
    'port' => getenv('DB_PORT') ?: 3306
];
```

### Step 5: Security Checklist
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions
- [ ] Update database passwords
- [ ] Disable error display in production
- [ ] Set up regular backups

### Quick Test Commands
```bash
# Test database connection
php test_db_connection.php

# Test login functionality
curl -X POST http://localhost/rizz/login/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"user1@example.com","password":"password"}'
```

### Support
- Check `DEPLOYMENT.md` for detailed instructions
- Check `DEPLOYMENT_GUIDE.md` for platform-specific guides
- The application is ready for any PHP/MySQL hosting environment
