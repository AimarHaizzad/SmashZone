# 🚀 SmashZone Website Deployment Guide

## 📋 Pre-Deployment Checklist

Before deploying, ensure everything is ready:

### ✅ **1. Production Assets Built**
```bash
npm run build
```

### ✅ **2. Environment Configuration**
Create `.env.production` file:
```env
APP_NAME="SmashZone"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your_database_host
DB_PORT=3306
DB_DATABASE=smashzone_production
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SmashZone"

FIREBASE_PROJECT_ID=your_firebase_project_id
FIREBASE_PRIVATE_KEY_ID=your_private_key_id
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY\n-----END PRIVATE KEY-----\n"
FIREBASE_CLIENT_EMAIL=your_service_account_email
FIREBASE_CLIENT_ID=your_client_id
FIREBASE_AUTH_URI=https://accounts.google.com/o/oauth2/auth
FIREBASE_TOKEN_URI=https://oauth2.googleapis.com/token
FIREBASE_AUTH_PROVIDER_X509_CERT_URL=https://www.googleapis.com/oauth2/v1/certs
FIREBASE_CLIENT_X509_CERT_URL=your_cert_url

NEWS_API_KEY=your_news_api_key
```

### ✅ **3. Database Migration**
```bash
php artisan migrate --force
```

### ✅ **4. Storage Link**
```bash
php artisan storage:link
```

### ✅ **5. Cache Optimization**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🌐 Deployment Options

### **Option 1: Shared Hosting (cPanel/WHM)**

#### **Requirements:**
- PHP 8.1+ with extensions: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML, GD, MySQL
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js & NPM

#### **Steps:**

1. **Upload Files:**
   ```bash
   # Create deployment package
   tar -czf smashzone.tar.gz --exclude=node_modules --exclude=.git --exclude=storage/logs .
   ```

2. **Upload to Hosting:**
   - Upload `smashzone.tar.gz` to `public_html`
   - Extract files
   - Move contents of `public` folder to `public_html`
   - Move other files to parent directory

3. **Configure Database:**
   - Create MySQL database
   - Import your database dump
   - Update `.env` with production database credentials

4. **Set Permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

5. **Install Dependencies:**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install --production
   npm run build
   ```

6. **Laravel Setup:**
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

### **Option 2: VPS/Cloud Server (DigitalOcean, AWS, Linode)**

#### **Server Setup (Ubuntu 22.04):**

1. **Update System:**
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

2. **Install PHP 8.1:**
   ```bash
   sudo apt install software-properties-common
   sudo add-apt-repository ppa:ondrej/php
   sudo apt update
   sudo apt install php8.1-fpm php8.1-mysql php8.1-xml php8.1-gd php8.1-curl php8.1-zip php8.1-mbstring php8.1-bcmath php8.1-tokenizer php8.1-dom php8.1-fileinfo
   ```

3. **Install Nginx:**
   ```bash
   sudo apt install nginx
   ```

4. **Install MySQL:**
   ```bash
   sudo apt install mysql-server
   sudo mysql_secure_installation
   ```

5. **Install Composer:**
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

6. **Install Node.js:**
   ```bash
   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
   sudo apt-get install -y nodejs
   ```

7. **Deploy Application:**
   ```bash
   # Clone your repository
   git clone https://github.com/yourusername/smashzone.git /var/www/smashzone
   cd /var/www/smashzone
   
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   npm install --production
   npm run build
   
   # Set permissions
   sudo chown -R www-data:www-data /var/www/smashzone
   sudo chmod -R 755 /var/www/smashzone/storage
   sudo chmod -R 755 /var/www/smashzone/bootstrap/cache
   ```

8. **Configure Nginx:**
   ```nginx
   server {
       listen 80;
       server_name yourdomain.com www.yourdomain.com;
       root /var/www/smashzone/public;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

9. **SSL Certificate (Let's Encrypt):**
   ```bash
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
   ```

---

### **Option 3: Laravel Forge (Recommended)**

#### **Why Laravel Forge:**
- ✅ One-click deployment
- ✅ Automatic SSL certificates
- ✅ Server monitoring
- ✅ Easy database management
- ✅ Queue workers
- ✅ Cron job management

#### **Steps:**

1. **Sign up at [Laravel Forge](https://forge.laravel.com)**
2. **Connect your server** (DigitalOcean, AWS, etc.)
3. **Create site:**
   - Domain: `yourdomain.com`
   - Directory: `/home/forge/yourdomain.com`
   - PHP version: 8.1

4. **Deploy from Git:**
   - Repository: `https://github.com/yourusername/smashzone.git`
   - Branch: `main`
   - Composer: `composer install --no-dev --optimize-autoloader`
   - NPM: `npm install --production && npm run build`

5. **Environment Variables:**
   - Add all your `.env` variables in Forge dashboard

6. **Database:**
   - Create MySQL database
   - Run migrations: `php artisan migrate --force`

7. **SSL:**
   - Click "Secure" to get free SSL certificate

---

### **Option 4: Heroku (Quick Deploy)**

#### **Requirements:**
- Heroku account
- Heroku CLI installed

#### **Steps:**

1. **Create Heroku App:**
   ```bash
   heroku create smashzone-app
   ```

2. **Add Buildpacks:**
   ```bash
   heroku buildpacks:add heroku/php
   heroku buildpacks:add heroku/nodejs
   ```

3. **Set Environment Variables:**
   ```bash
   heroku config:set APP_KEY=$(php artisan key:generate --show)
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   # Add all other .env variables
   ```

4. **Add Database:**
   ```bash
   heroku addons:create cleardb:ignite
   ```

5. **Deploy:**
   ```bash
   git push heroku main
   heroku run php artisan migrate --force
   heroku run php artisan storage:link
   ```

---

## 🔧 Post-Deployment Tasks

### **1. Database Setup:**
```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder
```

### **2. Storage Setup:**
```bash
php artisan storage:link
```

### **3. Cache Optimization:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

### **4. Cron Jobs Setup:**
Add to your server's crontab:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### **5. Queue Workers:**
```bash
# For production, use supervisor to manage queue workers
php artisan queue:work --daemon
```

---

## 🛡️ Security Checklist

### **✅ Server Security:**
- [ ] Firewall configured (only ports 80, 443, 22)
- [ ] SSH key authentication only
- [ ] Regular security updates
- [ ] Fail2ban installed

### **✅ Application Security:**
- [ ] `APP_DEBUG=false` in production
- [ ] Strong database passwords
- [ ] HTTPS enabled
- [ ] File upload restrictions
- [ ] Rate limiting enabled

### **✅ Database Security:**
- [ ] Database user has minimal privileges
- [ ] Regular backups
- [ ] Connection encryption

---

## 📊 Monitoring & Maintenance

### **1. Log Monitoring:**
```bash
tail -f storage/logs/laravel.log
```

### **2. Performance Monitoring:**
- Use Laravel Telescope (development only)
- Consider New Relic or DataDog for production

### **3. Backup Strategy:**
```bash
# Database backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# File backup
tar -czf files_backup_$(date +%Y%m%d).tar.gz storage/app/public
```

### **4. Updates:**
```bash
# Update dependencies
composer update
npm update

# Rebuild assets
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🚀 Quick Deploy Script

Create `deploy.sh`:

```bash
#!/bin/bash

echo "🚀 Starting SmashZone deployment..."

# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm install --production
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
php artisan queue:restart

echo "✅ Deployment complete!"
```

Make it executable:
```bash
chmod +x deploy.sh
```

---

## 💰 Cost Comparison

| Option | Monthly Cost | Difficulty | Features |
|--------|-------------|------------|----------|
| **Shared Hosting** | $5-20 | Easy | Basic |
| **VPS** | $10-50 | Medium | Full control |
| **Laravel Forge** | $12-39 | Easy | Professional |
| **Heroku** | $7-25 | Easy | Quick setup |

---

## 🎯 Recommended Approach

**For SmashZone, I recommend:**

1. **Start with Laravel Forge** (easiest, most reliable)
2. **Use DigitalOcean droplet** ($12/month)
3. **Add domain and SSL** (free with Forge)
4. **Set up monitoring** and backups

**Total setup time: ~30 minutes**
**Monthly cost: ~$15-20**

---

## 📞 Need Help?

If you encounter any issues during deployment:

1. **Check logs:** `storage/logs/laravel.log`
2. **Verify permissions:** `storage/` and `bootstrap/cache/`
3. **Test database connection:** `php artisan tinker`
4. **Check web server configuration**

**Your SmashZone app is ready for production! 🎉**
