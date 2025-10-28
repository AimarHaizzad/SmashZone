# ☁️ Azure Free Tier Deployment Guide for SmashZone

## 🆓 Azure Free Services You Can Use

### **✅ What's FREE on Azure:**
- **App Service (Linux)** - 1 F1 instance (1 GB RAM, 1 GB storage)
- **MySQL Database** - 32 GB storage, 250 connections
- **Storage Account** - 5 GB LRS storage
- **Application Insights** - 5 GB data ingestion/month
- **Azure CDN** - 5 GB data transfer/month
- **Custom Domain** - Free SSL certificate

**Total Value: ~$200/month for FREE! 🎉**

---

## 🚀 Method 1: Azure App Service (Recommended)

### **Step 1: Prepare Your Application**

First, let's prepare your Laravel app for Azure:

```bash
# Run the deployment script
./deploy.sh
```

### **Step 2: Create Azure Resources**

#### **A. Create Resource Group**
```bash
# Install Azure CLI first: https://docs.microsoft.com/en-us/cli/azure/install-azure-cli
az login
az group create --name smashzone-rg --location "Southeast Asia"
```

#### **B. Create MySQL Database**
```bash
# Create MySQL server
az mysql server create \
  --resource-group smashzone-rg \
  --name smashzone-mysql \
  --location "Southeast Asia" \
  --admin-user smashzoneadmin \
  --admin-password "YourSecurePassword123!" \
  --sku-name B_Gen5_1 \
  --storage-size 32

# Create database
az mysql db create \
  --resource-group smashzone-rg \
  --server-name smashzone-mysql \
  --name smashzone_db

# Configure firewall (allow Azure services)
az mysql server firewall-rule create \
  --resource-group smashzone-rg \
  --server smashzone-mysql \
  --name AllowAzureServices \
  --start-ip-address 0.0.0.0 \
  --end-ip-address 0.0.0.0
```

#### **C. Create App Service Plan**
```bash
az appservice plan create \
  --name smashzone-plan \
  --resource-group smashzone-rg \
  --location "Southeast Asia" \
  --is-linux \
  --sku F1
```

#### **D. Create Web App**
```bash
az webapp create \
  --resource-group smashzone-rg \
  --plan smashzone-plan \
  --name smashzone-app \
  --runtime "PHP|8.1"
```

### **Step 3: Configure Application Settings**

```bash
# Set environment variables
az webapp config appsettings set \
  --resource-group smashzone-rg \
  --name smashzone-app \
  --settings \
    APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY="base64:YOUR_APP_KEY_HERE" \
    DB_CONNECTION=mysql \
    DB_HOST="smashzone-mysql.mysql.database.azure.com" \
    DB_PORT=3306 \
    DB_DATABASE=smashzone_db \
    DB_USERNAME="smashzoneadmin@smashzone-mysql" \
    DB_PASSWORD="YourSecurePassword123!" \
    MAIL_MAILER=smtp \
    MAIL_HOST=smtp.gmail.com \
    MAIL_PORT=587 \
    MAIL_USERNAME=your_email@gmail.com \
    MAIL_PASSWORD=your_app_password \
    MAIL_ENCRYPTION=tls \
    MAIL_FROM_ADDRESS=noreply@smashzone.com \
    MAIL_FROM_NAME="SmashZone"
```

### **Step 4: Deploy Your Code**

#### **Option A: Deploy via Git (Recommended)**
```bash
# Initialize git repository
git init
git add .
git commit -m "Initial commit for Azure deployment"

# Add Azure remote
az webapp deployment source config-local-git \
  --resource-group smashzone-rg \
  --name smashzone-app

# Get deployment URL
DEPLOYMENT_URL=$(az webapp deployment source show \
  --resource-group smashzone-rg \
  --name smashzone-app \
  --query url --output tsv)

# Add remote and push
git remote add azure $DEPLOYMENT_URL
git push azure main
```

#### **Option B: Deploy via ZIP**
```bash
# Create deployment package
zip -r smashzone-azure.zip . -x "node_modules/*" ".git/*" "*.md" "test_*" "cookies*" "deploy.sh"

# Deploy to Azure
az webapp deployment source config-zip \
  --resource-group smashzone-rg \
  --name smashzone-app \
  --src smashzone-azure.zip
```

### **Step 5: Configure PHP and Laravel**

Create `.user.ini` file in your project root:
```ini
; Azure PHP Configuration
upload_max_filesize = 10M
post_max_size = 10M
memory_limit = 256M
max_execution_time = 300
max_input_vars = 3000
```

### **Step 6: Run Laravel Commands**

```bash
# Enable SSH for your web app
az webapp ssh enable --resource-group smashzone-rg --name smashzone-app

# Connect via SSH and run commands
az webapp ssh --resource-group smashzone-rg --name smashzone-app

# Inside SSH session:
cd /home/site/wwwroot
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🚀 Method 2: Azure Container Instances (Alternative)

### **Step 1: Create Dockerfile**

Create `Dockerfile` in your project root:
```dockerfile
FROM php:8.1-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install --production && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Expose port
EXPOSE 80

# Start services
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
```

### **Step 2: Create nginx.conf**

Create `docker/nginx.conf`:
```nginx
events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    server {
        listen 80;
        server_name _;
        root /var/www/public;
        index index.php;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }
    }
}
```

### **Step 3: Deploy to Container Instances**

```bash
# Build and push to Azure Container Registry
az acr create --resource-group smashzone-rg --name smashzoneacr --sku Basic
az acr login --name smashzoneacr

# Build and push image
docker build -t smashzoneacr.azurecr.io/smashzone:latest .
docker push smashzoneacr.azurecr.io/smashzone:latest

# Create container instance
az container create \
  --resource-group smashzone-rg \
  --name smashzone-container \
  --image smashzoneacr.azurecr.io/smashzone:latest \
  --cpu 1 \
  --memory 1 \
  --registry-login-server smashzoneacr.azurecr.io \
  --registry-username smashzoneacr \
  --registry-password $(az acr credential show --name smashzoneacr --query passwords[0].value -o tsv) \
  --dns-name-label smashzone-app \
  --ports 80
```

---

## 🔧 Azure-Specific Configuration

### **1. Update Laravel for Azure**

Create `config/azure.php`:
```php
<?php

return [
    'app_service' => env('WEBSITE_SITE_NAME'),
    'instance_id' => env('WEBSITE_INSTANCE_ID'),
    'resource_group' => env('WEBSITE_RESOURCE_GROUP'),
    'subscription_id' => env('WEBSITE_SUBSCRIPTION_ID'),
];
```

### **2. Azure Storage Integration**

Update `config/filesystems.php`:
```php
'azure' => [
    'driver' => 'azure',
    'name' => env('AZURE_STORAGE_NAME'),
    'key' => env('AZURE_STORAGE_KEY'),
    'container' => env('AZURE_STORAGE_CONTAINER', 'public'),
    'url' => env('AZURE_STORAGE_URL'),
    'endpoint' => env('AZURE_STORAGE_ENDPOINT'),
],
```

### **3. Environment Variables for Azure**

Add to your `.env`:
```env
# Azure specific
WEBSITE_SITE_NAME=smashzone-app
WEBSITE_RESOURCE_GROUP=smashzone-rg
AZURE_STORAGE_NAME=smashzonestorage
AZURE_STORAGE_KEY=your_storage_key
AZURE_STORAGE_CONTAINER=public
```

---

## 📊 Monitoring & Scaling

### **1. Application Insights**

```bash
# Create Application Insights
az monitor app-insights component create \
  --app smashzone-insights \
  --location "Southeast Asia" \
  --resource-group smashzone-rg

# Get instrumentation key
INSTRUMENTATION_KEY=$(az monitor app-insights component show \
  --app smashzone-insights \
  --resource-group smashzone-rg \
  --query instrumentationKey --output tsv)

# Add to app settings
az webapp config appsettings set \
  --resource-group smashzone-rg \
  --name smashzone-app \
  --settings APPINSIGHTS_INSTRUMENTATIONKEY=$INSTRUMENTATION_KEY
```

### **2. Log Analytics**

```bash
# Create Log Analytics workspace
az monitor log-analytics workspace create \
  --resource-group smashzone-rg \
  --workspace-name smashzone-logs \
  --location "Southeast Asia"
```

### **3. Auto-scaling (When you upgrade)**

```bash
# Create auto-scale rule (requires S1 plan or higher)
az monitor autoscale create \
  --resource-group smashzone-rg \
  --resource smashzone-plan \
  --resource-type Microsoft.Web/serverfarms \
  --name smashzone-autoscale \
  --min-count 1 \
  --max-count 3 \
  --count 1
```

---

## 💰 Cost Breakdown (Free Tier)

| Service | Free Tier | Monthly Cost |
|---------|-----------|--------------|
| **App Service F1** | 1 instance, 1GB RAM | $0 |
| **MySQL Database** | 32GB storage | $0 |
| **Storage Account** | 5GB LRS | $0 |
| **Application Insights** | 5GB data | $0 |
| **Azure CDN** | 5GB transfer | $0 |
| **Custom Domain** | SSL included | $0 |
| **Total** | | **$0/month** |

---

## 🚀 Quick Deploy Script for Azure

Create `deploy-azure.sh`:

```bash
#!/bin/bash

echo "☁️ Deploying SmashZone to Azure..."

# Variables
RESOURCE_GROUP="smashzone-rg"
APP_NAME="smashzone-app"
MYSQL_SERVER="smashzone-mysql"
DATABASE_NAME="smashzone_db"

# Login to Azure
az login

# Create resource group
az group create --name $RESOURCE_GROUP --location "Southeast Asia"

# Create MySQL server
az mysql server create \
  --resource-group $RESOURCE_GROUP \
  --name $MYSQL_SERVER \
  --location "Southeast Asia" \
  --admin-user smashzoneadmin \
  --admin-password "SecurePassword123!" \
  --sku-name B_Gen5_1 \
  --storage-size 32

# Create database
az mysql db create \
  --resource-group $RESOURCE_GROUP \
  --server-name $MYSQL_SERVER \
  --name $DATABASE_NAME

# Configure firewall
az mysql server firewall-rule create \
  --resource-group $RESOURCE_GROUP \
  --server $MYSQL_SERVER \
  --name AllowAzureServices \
  --start-ip-address 0.0.0.0 \
  --end-ip-address 0.0.0.0

# Create App Service plan
az appservice plan create \
  --name smashzone-plan \
  --resource-group $RESOURCE_GROUP \
  --location "Southeast Asia" \
  --is-linux \
  --sku F1

# Create web app
az webapp create \
  --resource-group $RESOURCE_GROUP \
  --plan smashzone-plan \
  --name $APP_NAME \
  --runtime "PHP|8.1"

# Configure app settings
az webapp config appsettings set \
  --resource-group $RESOURCE_GROUP \
  --name $APP_NAME \
  --settings \
    APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=mysql \
    DB_HOST="$MYSQL_SERVER.mysql.database.azure.com" \
    DB_PORT=3306 \
    DB_DATABASE=$DATABASE_NAME \
    DB_USERNAME="smashzoneadmin@$MYSQL_SERVER" \
    DB_PASSWORD="SecurePassword123!"

echo "✅ Azure resources created!"
echo "🌐 Your app will be available at: https://$APP_NAME.azurewebsites.net"
echo "📋 Next steps:"
echo "1. Deploy your code: git push azure main"
echo "2. Run: az webapp ssh --resource-group $RESOURCE_GROUP --name $APP_NAME"
echo "3. Inside SSH: php artisan migrate --force"
```

---

## 🎯 **Recommended Azure Deployment Steps:**

### **1. Quick Start (5 minutes):**
```bash
# Make script executable
chmod +x deploy-azure.sh

# Run deployment
./deploy-azure.sh
```

### **2. Deploy Your Code:**
```bash
# Initialize git and deploy
git init
git add .
git commit -m "Deploy to Azure"
git push azure main
```

### **3. Configure Database:**
```bash
# SSH into your app
az webapp ssh --resource-group smashzone-rg --name smashzone-app

# Run migrations
php artisan migrate --force
php artisan storage:link
```

## 🎉 **Result:**
- ✅ **Free hosting** on Azure
- ✅ **Custom domain** support
- ✅ **SSL certificate** included
- ✅ **MySQL database** included
- ✅ **Monitoring** with Application Insights
- ✅ **Auto-scaling** when you upgrade

**Your SmashZone app will be live at: `https://smashzone-app.azurewebsites.net`** 🚀

---

## 📞 **Need Help?**

If you encounter any issues:
1. **Check Azure logs:** `az webapp log tail --resource-group smashzone-rg --name smashzone-app`
2. **SSH into app:** `az webapp ssh --resource-group smashzone-rg --name smashzone-app`
3. **Check app settings:** `az webapp config appsettings list --resource-group smashzone-rg --name smashzone-app`

**Azure free tier gives you everything you need to run SmashZone professionally! 🎉**
